<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
use Exception;

class ScannerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenant = Tenant::current();
        } catch (Exception $e) {
            $tenant = null;
        }

        // Live Exchange Rates from DolarApi
        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

        if (!$tenant) {
            $tenant = (object) [
                'id' => 1,
                'name' => 'Bodega & Abasto El Sol C.A.',
                'bcv_rate' => $bcvUsdRate,
                'business_type' => 'abasto'
            ];
        }

        try {
            $branches = Branch::where('tenant_id', $tenant->id)->get();
            $categories = Category::where('tenant_id', $tenant->id)->get();
            $products = Product::where('tenant_id', $tenant->id)
                ->with(['category', 'stocks.branch'])
                ->get();
        } catch (Exception $e) {
            $branches = collect([
                (object) ['id' => 1, 'name' => 'Sucursal Principal Altamira'],
                (object) ['id' => 2, 'name' => 'Almacén Central Las Mercedes']
            ]);
            $categories = collect([
                (object) ['id' => 1, 'name' => 'Bebidas y Refrescos'],
                (object) ['id' => 2, 'name' => 'Víveres y Granos'],
                (object) ['id' => 3, 'name' => 'Charcutería y Lácteos']
            ]);
            $products = collect([
                (object) ['id' => 1, 'name' => 'Refresco Coca-Cola 2L', 'sku' => 'BEB-001', 'barcode' => '7591001001234', 'price_usd' => 2.50, 'stocks' => collect([(object)['branch' => (object)['name' => 'Altamira'], 'quantity' => 120]])],
                (object) ['id' => 2, 'name' => 'Harina PAN Blanca 1kg', 'sku' => 'VIV-001', 'barcode' => '7591002005678', 'price_usd' => 1.35, 'stocks' => collect([(object)['branch' => (object)['name' => 'Altamira'], 'quantity' => 250]])],
                (object) ['id' => 3, 'name' => 'Queso Paisa Blanco (Kg)', 'sku' => 'CHA-001', 'barcode' => '7591003009012', 'price_usd' => 7.80, 'stocks' => collect([(object)['branch' => (object)['name' => 'Altamira'], 'quantity' => 35.5]])],
                (object) ['id' => 4, 'name' => 'Arroz Primor Supremo 1kg', 'sku' => 'VIV-002', 'barcode' => '7591004003456', 'price_usd' => 1.50, 'stocks' => collect([(object)['branch' => (object)['name' => 'Altamira'], 'quantity' => 180]])]
            ]);
        }

        $totalProductsCount = $products->count();
        $barcodeProductsCount = $products->whereNotNull('barcode')->where('barcode', '!=', '')->count();

        return view('scanner.index', compact(
            'tenant',
            'branches',
            'categories',
            'products',
            'bcvUsdRate',
            'bcvEurRate',
            'totalProductsCount',
            'barcodeProductsCount'
        ));
    }

    public function lookup(Request $request)
    {
        $code = trim((string) $request->input('code'));
        if (empty($code)) {
            return response()->json(['success' => false, 'message' => 'Código no proporcionado']);
        }

        $tenant = Tenant::current() ?? (object)['id' => 1];
        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

        $product = Product::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($code) {
                $q->where('barcode', $code)
                  ->orWhere('sku', $code)
                  ->orWhere('name', 'like', "%{$code}%");
            })
            ->with(['category', 'stocks.branch'])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado con el código o nombre: ' . $code
            ], 404);
        }

        $priceUsd = (float) $product->price_usd;
        $priceVes = $priceUsd * $bcvUsdRate;
        $priceEur = $bcvEurRate > 0 ? ($priceVes / $bcvEurRate) : ($priceUsd * 0.92);

        $totalStock = (float) $product->stocks->sum('quantity');

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'category' => $product->category ? $product->category->name : 'General',
                'price_usd' => $priceUsd,
                'price_ves' => round($priceVes, 2),
                'price_eur' => round($priceEur, 2),
                'total_stock' => $totalStock,
                'stocks' => $product->stocks->map(function ($s) {
                    return [
                        'branch_name' => $s->branch ? $s->branch->name : 'Principal',
                        'quantity' => (float) $s->quantity
                    ];
                })
            ],
            'rates' => [
                'bcv_usd' => $bcvUsdRate,
                'bcv_eur' => $bcvEurRate
            ]
        ]);
    }

    public function updateStock(Request $request)
    {
        $productId = $request->input('product_id');
        $branchId = $request->input('branch_id', 1);
        $newQuantity = (float) $request->input('quantity', 0);

        try {
            $stock = InventoryStock::updateOrCreate(
                ['product_id' => $productId, 'branch_id' => $branchId],
                ['quantity' => $newQuantity]
            );

            return response()->json([
                'success' => true,
                'message' => 'Inventario actualizado correctamente',
                'new_quantity' => $stock->quantity
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function quickStore(Request $request)
    {
        $tenant = Tenant::current() ?? (object)['id' => 1];
        $barcode = trim((string) $request->input('barcode'));
        $name = trim((string) $request->input('name', 'Producto ' . $barcode));
        $priceUsd = (float) $request->input('price_usd', 1.50);
        $categoryId = (int) $request->input('category_id', 1);
        $initialStock = (float) $request->input('initial_stock', 10);
        $sku = trim((string) $request->input('sku', 'SKU-' . strtoupper(substr(md5($barcode . time()), 0, 6))));

        if (empty($barcode)) {
            return response()->json(['success' => false, 'message' => 'El código de barras es requerido'], 422);
        }

        try {
            $product = Product::create([
                'tenant_id' => $tenant->id,
                'category_id' => $categoryId,
                'name' => $name,
                'sku' => $sku,
                'barcode' => $barcode,
                'price_usd' => $priceUsd,
                'cost_usd' => $priceUsd * 0.7,
                'is_active' => true,
            ]);

            // Create initial stock in main branch
            $branch = Branch::where('tenant_id', $tenant->id)->first();
            $branchId = $branch ? $branch->id : 1;

            InventoryStock::create([
                'tenant_id' => $tenant->id,
                'product_id' => $product->id,
                'branch_id' => $branchId,
                'quantity' => $initialStock,
            ]);

            $rates = DolarApiService::getRates();
            $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
            $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

            $product->load(['category', 'stocks.branch']);

            return response()->json([
                'success' => true,
                'message' => '¡Producto registrado y agregado al catálogo exitosamente!',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'price_usd' => (float) $product->price_usd,
                    'price_ves' => round($product->price_usd * $bcvUsdRate, 2),
                    'price_eur' => round(($product->price_usd * $bcvUsdRate) / $bcvEurRate, 2),
                    'total_stock' => $initialStock,
                    'category' => $product->category ? $product->category->name : 'General',
                    'stocks' => [
                        ['branch_name' => $branch ? $branch->name : 'Principal', 'quantity' => $initialStock]
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el producto: ' . $e->getMessage()
            ], 500);
        }
    }
}
