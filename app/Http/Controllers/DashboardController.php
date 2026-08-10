<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\CashSession;
use App\Models\BankAccount;
use App\Models\Quote;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Exception;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenant = Tenant::first();
        } catch (Exception $e) {
            $tenant = null;
        }

        if (!$tenant) {
            $tenant = (object) [
                'id' => 1,
                'name' => 'Bodega & Abasto El Sol C.A.',
                'rif_tax_id' => 'J-12345678-9',
                'subdomain' => 'elsol',
                'plan_tier' => 'pro',
                'bcv_rate' => 52.4000,
                'parallel_rate' => 54.1000,
                'igtf_percentage' => 3.00,
            ];
            $branches = collect([
                (object) ['id' => 1, 'name' => 'Sucursal Principal Altamira', 'code' => 'ALT-001'],
                (object) ['id' => 2, 'name' => 'Almacén Central Las Mercedes', 'code' => 'MER-002']
            ]);
            $activeBranch = $branches->first();
            $salesTodayUsd = 450.80;
            $salesTodayVes = $salesTodayUsd * 52.40;
            $totalProductsCount = 42;
            $totalDebtUsd = 350.00;
            $activeCashSession = (object) ['status' => 'open', 'initial_cash_usd' => 50.00, 'expected_cash_usd' => 250.00];
            $bankAccounts = collect([
                (object) ['name' => 'Banesco Bolívares Principal', 'account_number' => '0134-0001-00-1234567890', 'currency' => 'VES', 'balance' => 45000.00],
                (object) ['name' => 'Zelle Dólares Empresa', 'account_number' => 'pagos@elsol.com', 'currency' => 'USD', 'balance' => 3200.00],
                (object) ['name' => 'Efectivo Caja USD Altamira', 'account_number' => 'CAJA-USD', 'currency' => 'USD', 'balance' => 850.00]
            ]);
            $recentSales = collect([
                (object) ['sale_number' => 'VTA-2026-0001', 'total_usd' => 8.93, 'total_ves' => 467.93, 'customer' => (object) ['name' => 'Juan Pérez']],
                (object) ['sale_number' => 'VTA-2026-0002', 'total_usd' => 15.50, 'total_ves' => 812.20, 'customer' => (object) ['name' => 'Inversiones Los Chaguaramos']],
                (object) ['sale_number' => 'VTA-2026-0003', 'total_usd' => 4.20, 'total_ves' => 220.08, 'customer' => (object) ['name' => 'Cliente Detal']]
            ]);
            $pendingQuotesCount = 3;
            $transfersInTransitCount = 1;
        } else {
            $branches = Branch::where('tenant_id', $tenant->id)->get();
            $activeBranch = $branches->first();
            $salesTodayUsd = Sale::where('tenant_id', $tenant->id)->sum('total_usd');
            $salesTodayVes = $salesTodayUsd * $tenant->bcv_rate;
            $totalProductsCount = Product::where('tenant_id', $tenant->id)->count();
            $totalDebtUsd = Customer::where('tenant_id', $tenant->id)->sum('current_debt_usd');
            $activeCashSession = CashSession::where('tenant_id', $tenant->id)->where('status', 'open')->first();
            $bankAccounts = BankAccount::where('tenant_id', $tenant->id)->get();
            $recentSales = Sale::where('tenant_id', $tenant->id)->with('customer')->latest()->take(5)->get();
            $pendingQuotesCount = Quote::where('tenant_id', $tenant->id)->where('status', 'pending_approval')->count();
            $transfersInTransitCount = StockTransfer::where('tenant_id', $tenant->id)->where('status', 'in_transit')->count();
        }

        return view('dashboard.index', compact(
            'tenant',
            'branches',
            'activeBranch',
            'salesTodayUsd',
            'salesTodayVes',
            'totalProductsCount',
            'totalDebtUsd',
            'activeCashSession',
            'bankAccounts',
            'recentSales',
            'pendingQuotesCount',
            'transfersInTransitCount'
        ));
    }
}
