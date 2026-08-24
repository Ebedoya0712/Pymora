<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Exception;

class CxcCxpController extends Controller
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
            $customers = collect([
                (object) ['name' => 'Inversiones Los Chaguaramos C.A.', 'tax_id' => 'J-30987654-1', 'customer_type' => 'b2b', 'phone' => '+584123334455', 'credit_limit_usd' => 1000.00, 'current_debt_usd' => 150.00],
                (object) ['name' => 'Distribuidora Central Caracas C.A.', 'tax_id' => 'J-40111222-9', 'customer_type' => 'wholesale', 'phone' => '+584149998877', 'credit_limit_usd' => 2500.00, 'current_debt_usd' => 420.00],
                (object) ['name' => 'Juan Pérez (Cliente Detal)', 'tax_id' => 'V-18234567', 'customer_type' => 'retail', 'phone' => '+584241112233', 'credit_limit_usd' => 0.00, 'current_debt_usd' => 0.00]
            ]);
            $pendingSales = collect();
        } else {
            $customers = Customer::where('tenant_id', $tenant->id)->get();
            $pendingSales = Sale::where('tenant_id', $tenant->id)->where('payment_status', '!=', 'paid')->get();
        }

        return view('cxc.index', compact('tenant', 'customers', 'pendingSales'));
    }
}
