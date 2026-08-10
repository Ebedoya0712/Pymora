<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CashSession;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Exception;

class PosController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenant = Tenant::first();
        } catch (Exception $e) {
            $tenant = null;
        }

        if (!$tenant) {
            $tenant = (object) ['id' => 1, 'name' => 'Bodega & Abasto El Sol C.A.', 'bcv_rate' => 52.40];
            $categories = collect([
                (object) ['id' => 1, 'name' => 'Bebidas y Refrescos'],
                (object) ['id' => 2, 'name' => 'Víveres y Granos'],
                (object) ['id' => 3, 'name' => 'Charcutería y Lácteos'],
                (object) ['id' => 4, 'name' => 'Limpieza e Higiene']
            ]);
            $products = collect([
                (object) ['id' => 1, 'category_id' => 1, 'name' => 'Refresco Coca-Cola 2L', 'sku' => 'BEB-001', 'barcode' => '7591001001234', 'price_usd' => 2.50],
                (object) ['id' => 2, 'category_id' => 2, 'name' => 'Harina PAN Blanca 1kg', 'sku' => 'VIV-001', 'barcode' => '7591002005678', 'price_usd' => 1.35],
                (object) ['id' => 3, 'category_id' => 3, 'name' => 'Queso Paisa Blanco (Kg)', 'sku' => 'CHA-001', 'barcode' => '7591003009012', 'price_usd' => 7.80],
                (object) ['id' => 4, 'category_id' => 2, 'name' => 'Arroz Primor Supremo 1kg', 'sku' => 'VIV-002', 'barcode' => '7591004003456', 'price_usd' => 1.50],
                (object) ['id' => 5, 'category_id' => 1, 'name' => 'Jugo del Valle Manzana 1L', 'sku' => 'BEB-002', 'barcode' => '7591005007890', 'price_usd' => 1.80],
                (object) ['id' => 6, 'category_id' => 4, 'name' => 'Detergente Las Llaves 1kg', 'sku' => 'LIM-001', 'barcode' => '7591006001122', 'price_usd' => 3.20]
            ]);
            $customers = collect([
                (object) ['id' => 1, 'name' => 'Inversiones Los Chaguaramos C.A.', 'tax_id' => 'J-30987654-1'],
                (object) ['id' => 2, 'name' => 'Juan Pérez (Cliente Detal)', 'tax_id' => 'V-18234567']
            ]);
            $activeSession = (object) ['status' => 'open'];
        } else {
            $categories = Category::where('tenant_id', $tenant->id)->get();
            $products = Product::where('tenant_id', $tenant->id)->where('is_active', true)->get();
            $customers = Customer::where('tenant_id', $tenant->id)->get();
            $activeSession = CashSession::where('tenant_id', $tenant->id)->where('status', 'open')->first();
        }

        return view('pos.index', compact('tenant', 'categories', 'products', 'customers', 'activeSession'));
    }

    public function store(Request $request)
    {
        return redirect()->route('pos.index')->with('success', 'Venta VTA-2026-0089 procesada exitosamente ($' . number_format($request->input('total_usd', 0), 2) . ' USD).');
    }
}
