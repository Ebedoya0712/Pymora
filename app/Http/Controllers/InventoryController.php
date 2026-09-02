<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Branch;
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
use Exception;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenant = Tenant::current();
        } catch (Exception $e) {
            $tenant = null;
        }

        if (!$tenant) {
            $tenant = (object) [
                'id' => 1,
                'name' => 'Bodega & Abasto El Sol C.A.',
                'business_type' => 'abasto'
            ];
        }

        // Live Exchange Rates
        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

        $filter = $request->input('filter', 'all');
        $categoryId = $request->input('category_id', 'all');
        $search = trim((string) $request->input('search', ''));

        $query = Product::where('tenant_id', $tenant->id)
            ->with(['category', 'stocks.branch'])
            ->orderBy('name', 'asc');

        if ($categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $allProducts = $query->get();

        // Calculate total stock and low stock flag for each product
        $allProducts->each(function ($product) {
            $product->total_stock = (float) $product->stocks->sum('quantity');
            $product->min_alert = (float) ($product->min_stock_alert ?? 10);
            $product->is_low_stock = $product->total_stock <= $product->min_alert;
        });

        // Filter products based on tab
        if ($filter === 'low_stock') {
            $products = $allProducts->filter(fn($p) => $p->is_low_stock)->values();
        } elseif ($filter === 'normal') {
            $products = $allProducts->filter(fn($p) => !$p->is_low_stock)->values();
        } else {
            $products = $allProducts;
        }

        // All low stock products for modal alert
        $lowStockProducts = $allProducts->filter(fn($p) => $p->is_low_stock)->values();
        $lowStockCount = $lowStockProducts->count();
        $totalProductsCount = $allProducts->count();
        $totalStockUnits = $allProducts->sum('total_stock');
        $totalInventoryValueUsd = $allProducts->sum(fn($p) => $p->total_stock * (float)$p->price_usd);
        $totalInventoryValueVes = $totalInventoryValueUsd * $bcvUsdRate;

        $categories = Category::where('tenant_id', $tenant->id)->get();
        $branches = Branch::where('tenant_id', $tenant->id)->get();

        $productsJson = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku ?? '',
                'barcode' => $p->barcode ?? '',
                'category_id' => (string)($p->category_id ?? ''),
                'category_name' => $p->category->name ?? 'General',
                'image_url' => $p->image_url ?? '',
            ];
        })->values()->toJson();

        return view('inventory.index', compact(
            'tenant',
            'products',
            'productsJson',
            'lowStockProducts',
            'lowStockCount',
            'totalProductsCount',
            'totalStockUnits',
            'totalInventoryValueUsd',
            'totalInventoryValueVes',
            'categories',
            'branches',
            'filter',
            'categoryId',
            'search',
            'bcvUsdRate',
            'bcvEurRate'
        ));
    }

    public function store(Request $request)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'image_url' => 'nullable|string',
            'description' => 'nullable|string',
            'cost_usd' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0.01',
            'min_stock_alert' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
            'unit' => 'nullable|string|max:50',
            'has_lots' => 'nullable|boolean',
        ]);

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?: 'SKU-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']), 0, 3)) . '-' . rand(100, 999),
            'barcode' => $validated['barcode'] ?: null,
            'image_url' => $validated['image_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'cost_usd' => $validated['cost_usd'],
            'price_usd' => $validated['price_usd'],
            'min_stock_alert' => $validated['min_stock_alert'] ?? 10.00,
            'unit' => $validated['unit'] ?? 'Unidad',
            'has_lots' => $request->has('has_lots'),
            'is_active' => true,
        ]);

        $initialStock = (float) ($request->input('stock_quantity') ?? 0);
        $branchId = $request->input('branch_id') ?: 1;

        InventoryStock::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchId,
            'product_id' => $product->id,
            'quantity' => $initialStock,
        ]);

        return redirect()->route('inventory.index')->with('success', '¡Producto "' . $product->name . '" registrado exitosamente con ' . $initialStock . ' unidades en stock!');
    }

    public function updateStock(Request $request)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'nullable|exists:branches,id',
            'quantity' => 'required|numeric|min:0',
            'min_stock_alert' => 'nullable|numeric|min:0',
            'operation' => 'nullable|string|in:set,add',
        ]);

        $branchId = $validated['branch_id'] ?? 1;
        $stock = InventoryStock::firstOrCreate([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchId,
            'product_id' => $validated['product_id'],
        ], [
            'quantity' => 0
        ]);

        if (($validated['operation'] ?? 'set') === 'add') {
            $stock->quantity += (float) $validated['quantity'];
        } else {
            $stock->quantity = (float) $validated['quantity'];
        }
        $stock->save();

        if (isset($validated['min_stock_alert'])) {
            Product::where('id', $validated['product_id'])->update([
                'min_stock_alert' => $validated['min_stock_alert']
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Stock de producto actualizado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];
        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'image_url' => 'nullable|string',
            'description' => 'nullable|string',
            'cost_usd' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0.01',
            'min_stock_alert' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'has_lots' => 'nullable|boolean',
        ]);

        $product->update([
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?: $product->sku,
            'barcode' => $validated['barcode'] ?: null,
            'image_url' => $validated['image_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'cost_usd' => $validated['cost_usd'],
            'price_usd' => $validated['price_usd'],
            'min_stock_alert' => $validated['min_stock_alert'] ?? 10.00,
            'unit' => $validated['unit'] ?? 'Unidad',
            'has_lots' => $request->has('has_lots'),
        ]);

        return redirect()->route('inventory.index')->with('success', '¡Producto "' . $product->name . '" actualizado correctamente!');
    }

    public function destroy($id)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];
        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);
        $productName = $product->name;
        $product->delete();

        return redirect()->route('inventory.index')->with('success', 'Producto "' . $productName . '" eliminado del inventario.');
    }
}
