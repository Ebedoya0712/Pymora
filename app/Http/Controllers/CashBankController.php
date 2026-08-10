<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\CashSession;
use App\Models\BankAccount;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Exception;

class CashBankController extends Controller
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
            $bankAccounts = collect([
                (object) ['name' => 'Banesco Bolívares Principal', 'bank_name' => 'Banesco', 'account_number' => '0134-0001-00-1234567890', 'currency' => 'VES', 'balance' => 45000.00],
                (object) ['name' => 'Zelle Dólares Empresa', 'bank_name' => 'Chase Bank', 'account_number' => 'pagos@elsol.com', 'currency' => 'USD', 'balance' => 3200.00],
                (object) ['name' => 'Efectivo Caja USD Altamira', 'bank_name' => 'Caja Chica', 'account_number' => 'CAJA-USD', 'currency' => 'USD', 'balance' => 850.00]
            ]);
            $cashSessions = collect([
                (object) ['status' => 'open', 'opened_at' => now()->startOfDay(), 'expected_cash_usd' => 250.00, 'initial_cash_usd' => 50.00],
                (object) ['status' => 'closed', 'opened_at' => now()->subDay()->startOfDay(), 'expected_cash_usd' => 620.00, 'initial_cash_usd' => 50.00]
            ]);
            $activeSession = $cashSessions->first();
        } else {
            $bankAccounts = BankAccount::where('tenant_id', $tenant->id)->get();
            $cashSessions = CashSession::where('tenant_id', $tenant->id)->latest()->get();
            $activeSession = CashSession::where('tenant_id', $tenant->id)->where('status', 'open')->first();
        }

        return view('cashbank.index', compact('tenant', 'bankAccounts', 'cashSessions', 'activeSession'));
    }
}
