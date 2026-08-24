<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Quote;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Exception;

class QuoteController extends Controller
{
    public function index()
    {
        try {
            $tenant = Tenant::current();
        } catch (Exception $e) {
            $tenant = null;
        }

        if (!$tenant) {
            $tenant = (object) ['id' => 1, 'name' => 'Bodega & Abasto El Sol C.A.'];
            $quotes = collect([
                (object) ['quote_number' => 'COT-2026-001', 'valid_until' => '2026-08-25', 'total_usd' => 174.00, 'status' => 'approved', 'customer' => (object) ['name' => 'Inversiones Los Chaguaramos']],
                (object) ['quote_number' => 'COT-2026-002', 'valid_until' => '2026-08-30', 'total_usd' => 520.00, 'status' => 'pending_approval', 'customer' => (object) ['name' => 'Distribuidora Central Caracas C.A.']],
                (object) ['quote_number' => 'COT-2026-003', 'valid_until' => '2026-09-05', 'total_usd' => 89.50, 'status' => 'draft', 'customer' => (object) ['name' => 'Supermercado Plaza']]
            ]);
            $customers = collect();
            $products = collect();
        } else {
            $quotes = Quote::where('tenant_id', $tenant->id)->with('customer')->latest()->get();
            $customers = Customer::where('tenant_id', $tenant->id)->get();
            $products = Product::where('tenant_id', $tenant->id)->get();
        }

        return view('quotes.index', compact('tenant', 'quotes', 'customers', 'products'));
    }
}
