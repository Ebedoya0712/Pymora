<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TaxRetention;
use App\Models\SellerCommission;
use Illuminate\Http\Request;
use Exception;

class ReportController extends Controller
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
            $retentions = collect([
                (object) [
                    'retention_number' => '2026080000001',
                    'supplier_name' => 'Distribuidora Polar C.A.',
                    'supplier_tax_id' => 'J-00001234-5',
                    'base_amount_usd' => 500.00,
                    'retained_amount_usd' => 60.00
                ],
                (object) [
                    'retention_number' => '2026080000002',
                    'supplier_name' => 'Monaca C.A.',
                    'supplier_tax_id' => 'J-00005678-9',
                    'base_amount_usd' => 1200.00,
                    'retained_amount_usd' => 144.00
                ]
            ]);
            $commissions = collect([
                (object) [
                    'user' => (object) ['name' => 'Pedro Gómez (Cajero)'],
                    'commission_rate' => 3.00,
                    'commission_amount_usd' => 13.52
                ],
                (object) [
                    'user' => (object) ['name' => 'María Rodríguez (Gerente)'],
                    'commission_rate' => 5.00,
                    'commission_amount_usd' => 22.54
                ]
            ]);
        } else {
            $retentions = TaxRetention::where('tenant_id', $tenant->id)->latest()->get();
            $commissions = SellerCommission::where('tenant_id', $tenant->id)->with('user')->latest()->get();
        }

        return view('reports.index', compact('tenant', 'retentions', 'commissions'));
    }
}
