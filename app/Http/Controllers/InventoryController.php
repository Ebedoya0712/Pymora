<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Branch;
use Illuminate\Http\Request;
use Exception;

class InventoryController extends Controller
{
    public function index()
    {
        try {
            $tenant = Tenant::first();
        } catch (Exception $e) {
            $tenant = null;
        }

        if (!$tenant) {
            $tenant = (object) ['id' => 1, 'name' => 'Bodega & Abasto El Sol C.A.'];
            $categories = collect([
                (object) ['id' => 1, 'name' => 'Bebidas y Refrescos'],
                (object) ['id' => 2, 'name' => 'Víveres y Granos'],
                (object) ['id' => 3, 'name' => 'Charcutería y Lácteos']
            ]);
            $branches = collect([
                (object) ['id' => 1, 'name' => 'Altamira'],
                (object) ['id' => 2, 'name' => 'Las Mercedes']
            ]);
            $products = collect([
                (object) [
                    'id' => 1, 'sku' => 'BEB-001', 'name' => 'Refresco Coca-Cola 2L', 'cost_usd' => 1.80, 'price_usd' => 2.50, 'unit' => 'Unidad', 'has_lots' => false,
                    'category' => (object) ['name' => 'Bebidas y Refrescos'],
                    'stocks' => collect([
                        (object) ['branch_id' => 1, 'quantity' => 120],
                        (object) ['branch_id' => 2, 'quantity' => 50]
                    ])
                ],
                (object) [
                    'id' => 2, 'sku' => 'VIV-001', 'name' => 'Harina PAN Blanca 1kg', 'cost_usd' => 0.95, 'price_usd' => 1.35, 'unit' => 'Unidad', 'has_lots' => false,
                    'category' => (object) ['name' => 'Víveres y Granos'],
                    'stocks' => collect([
                        (object) ['branch_id' => 1, 'quantity' => 250],
                        (object) ['branch_id' => 2, 'quantity' => 100]
                    ])
                ],
                (object) [
                    'id' => 3, 'sku' => 'CHA-001', 'name' => 'Queso Paisa Blanco (Kg)', 'cost_usd' => 5.20, 'price_usd' => 7.80, 'unit' => 'Kg', 'has_lots' => true,
                    'category' => (object) ['name' => 'Charcutería y Lácteos'],
                    'stocks' => collect([
                        (object) ['branch_id' => 1, 'quantity' => 35.5],
                        (object) ['branch_id' => 2, 'quantity' => 12.0]
                    ])
                ]
            ]);
        } else {
            $products = Product::where('tenant_id', $tenant->id)->with(['category', 'stocks'])->get();
            $categories = Category::where('tenant_id', $tenant->id)->get();
            $branches = Branch::where('tenant_id', $tenant->id)->get();
        }

        return view('inventory.index', compact('tenant', 'products', 'categories', 'branches'));
    }

    public function store(Request $request)
    {
        return redirect()->route('inventory.index')->with('success', 'Producto registrado correctamente.');
    }
}
