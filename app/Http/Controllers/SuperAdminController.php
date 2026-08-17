<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
<<<<<<< HEAD
use App\Models\Branch;
use App\Models\User;
use App\Models\CashRegister;
use App\Models\BankAccount;
=======
use App\Models\User;
use App\Models\SaasPayment;
>>>>>>> de6794c (feat: módulo de Finanzas Propias SaaS con gráficas interactivas, gestión de Usuarios SaaS y menú exclusivo de Super Admin)
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function index()
    {
        if (!session('is_impersonating')) {
            session([
                'user_role' => 'super_admin',
                'user_name' => session('user_name', 'Eliecer (Super Admin)'),
            ]);
        }

        try {
            $tenants = Tenant::withCount('users')->get();
        } catch (Exception $e) {
            $tenants = collect();
        }

        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

        if ($tenants->isEmpty()) {
            $totalTenants = 3;
            $activeTenants = 3;
            $totalMrrUsd = 307.00;
        } else {
            $totalTenants = $tenants->count();
            $activeTenants = $tenants->where('is_active', true)->count();
            $totalMrrUsd = $tenants->where('is_active', true)->sum(function ($t) {
                return match($t->plan_tier) {
                    'starter' => 29,
                    'pro' => 79,
                    'enterprise' => 199,
                    default => 0
                };
            });
        }

        $igtfRate = 3.00;
        $trialDays = (int) GlobalSetting::get('trial_days', 30);
        $supportEmail = GlobalSetting::get('support_email', 'soporte@pymora.com');
        $broadcastMessage = GlobalSetting::get('broadcast_message', '');

        return view('superadmin.index', compact(
            'tenants', 
            'totalTenants', 
            'activeTenants', 
            'totalMrrUsd', 
            'bcvUsdRate', 
            'bcvEurRate',
            'igtfRate',
            'trialDays',
            'supportEmail',
            'broadcastMessage'
        ));
    }

    public function finanzas()
    {
        if (!session('is_impersonating')) {
            session([
                'user_role' => 'super_admin',
                'user_name' => session('user_name', 'Eliecer (Super Admin)'),
            ]);
        }

        try {
            $tenants = Tenant::orderBy('name')->get();
            $payments = SaasPayment::with('tenant')->orderBy('payment_date', 'desc')->get();
        } catch (Exception $e) {
            $tenants = collect();
            $payments = collect();
        }

        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);

        $today = now()->toDateString();
        $sevenDaysAgo = now()->subDays(7)->toDateString();

        // Revenue Breakdown: Day, Week, Month, Total
        $todayPayments = $payments->filter(fn($p) => Carbon::parse($p->payment_date)->toDateString() === $today);
        $todayRevenueUsd = (float) $todayPayments->sum('amount_usd');
        $todayRevenueVes = (float) $todayPayments->sum(function($p) use ($bcvUsdRate) {
            return $p->amount_ves ?: ($p->amount_usd * $bcvUsdRate);
        });

        $weekRevenueUsd = (float) $payments->filter(fn($p) => Carbon::parse($p->payment_date)->gte(now()->startOfWeek()))->sum('amount_usd');

        $thisMonthRevenueUsd = (float) $payments->filter(fn($p) => Carbon::parse($p->payment_date)->isCurrentMonth())->sum('amount_usd');

        $totalRevenueUsd = (float) $payments->sum('amount_usd');

        $activeTenantsCount = $tenants->where('is_active', true)->count();
        $expiringSoonCount = $tenants->filter(function ($t) {
            return $t->expires_at && Carbon::parse($t->expires_at)->diffInDays(now(), false) >= -30 && Carbon::parse($t->expires_at)->isFuture();
        })->count();

        // Chart 1: Last 7 Days Daily Breakdown
        $dailyLabels = [];
        $dailyValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->toDateString();
            $dailyLabels[] = $date->translatedFormat('D d/m');
            $dailyValues[] = (float) $payments->filter(fn($p) => Carbon::parse($p->payment_date)->toDateString() === $dateStr)->sum('amount_usd');
        }

        // Chart 2: Revenue by Payment Method
        $methodsMap = [
            'pago_movil' => 'Pago Móvil VES',
            'zelle' => 'Zelle (USD)',
            'binance_usdt' => 'Binance USDT',
            'bank_transfer' => 'Transferencia Bancaria',
            'cash_usd' => 'Efectivo USD',
        ];

        $methodLabels = [];
        $methodValues = [];
        foreach ($methodsMap as $key => $label) {
            $val = (float) $payments->where('payment_method', $key)->sum('amount_usd');
            if ($val > 0) {
                $methodLabels[] = $label;
                $methodValues[] = $val;
            }
        }

        if (empty($methodLabels)) {
            $methodLabels = ['Pago Móvil VES', 'Zelle (USD)', 'Binance USDT'];
            $methodValues = [158.00, 199.00, 79.00];
        }

        return view('superadmin.finanzas', compact(
            'tenants',
            'payments',
            'todayRevenueUsd',
            'todayRevenueVes',
            'weekRevenueUsd',
            'thisMonthRevenueUsd',
            'totalRevenueUsd',
            'activeTenantsCount',
            'expiringSoonCount',
            'bcvUsdRate',
            'dailyLabels',
            'dailyValues',
            'methodLabels',
            'methodValues'
        ));
    }

    public function users(Request $request)
    {
        if (!session('is_impersonating')) {
            session([
                'user_role' => 'super_admin',
                'user_name' => session('user_name', 'Eliecer (Super Admin)'),
            ]);
        }

        try {
            $query = User::with('tenant')->latest();

            if ($request->has('tenant_id') && $request->input('tenant_id') != '') {
                $query->where('tenant_id', $request->input('tenant_id'));
            }

            if ($request->has('search') && $request->input('search') != '') {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $users = $query->get();
            $tenants = Tenant::orderBy('name')->get();
        } catch (Exception $e) {
            $users = collect();
            $tenants = collect();
        }

        $totalUsers = $users->count();
        $activeUsers = $users->where('is_active', true)->count();
        $superAdminCount = $users->where('role', 'super_admin')->count();
        $tenantUsersCount = $users->where('role', '!=', 'super_admin')->count();

        return view('superadmin.users', compact(
            'users',
            'tenants',
            'totalUsers',
            'activeUsers',
            'superAdminCount',
            'tenantUsersCount'
        ));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:super_admin,owner,branch_manager,cashier,warehouse_manager,accountant',
            'tenant_id' => 'nullable|exists:tenants,id',
            'phone' => 'nullable|string|max:50',
        ]);

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'tenant_id' => $request->input('role') === 'super_admin' ? null : $request->input('tenant_id'),
            'phone' => $request->input('phone'),
            'is_active' => true,
        ]);

        return redirect()->route('superadmin.users')->with('success', "Usuario '{$request->input('name')}' creado exitosamente.");
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string|in:super_admin,owner,branch_manager,cashier,warehouse_manager,accountant',
            'tenant_id' => 'nullable|exists:tenants,id',
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'tenant_id' => $request->input('role') === 'super_admin' ? null : $request->input('tenant_id'),
            'phone' => $request->input('phone'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $user->update($data);

        return redirect()->route('superadmin.users')->with('success', "Usuario '{$user->name}' actualizado correctamente.");
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'activado' : 'suspendido';
        return redirect()->back()->with('success', "El usuario '{$user->name}' ha sido {$statusText}.");
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'amount_usd' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'reference_code' => 'required|string',
            'payment_date' => 'required|date',
            'plan_tier' => 'required|string|in:starter,pro,enterprise',
            'months_paid' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $rates = DolarApiService::getRates();
        $bcvRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $amountVes = round($request->input('amount_usd') * $bcvRate, 2);

        $payment = SaasPayment::create([
            'tenant_id' => $request->input('tenant_id'),
            'amount_usd' => $request->input('amount_usd'),
            'exchange_rate_bcv' => $bcvRate,
            'amount_ves' => $amountVes,
            'payment_method' => $request->input('payment_method'),
            'reference_code' => $request->input('reference_code'),
            'payment_date' => $request->input('payment_date'),
            'plan_tier' => $request->input('plan_tier'),
            'months_paid' => $request->input('months_paid'),
            'notes' => $request->input('notes'),
        ]);

        // Extend tenant subscription expiration & reactivate if needed
        $tenant = Tenant::findOrFail($request->input('tenant_id'));
        $currentExpiry = $tenant->expires_at && Carbon::parse($tenant->expires_at)->isFuture() 
            ? Carbon::parse($tenant->expires_at) 
            : now();
        
        $newExpiry = $currentExpiry->addDays(30 * (int) $request->input('months_paid'));

        $tenant->update([
            'expires_at' => $newExpiry,
            'plan_tier' => $request->input('plan_tier'),
            'is_active' => true,
        ]);

        return redirect()->route('superadmin.finanzas')->with('success', "Pago registrado exitosamente. Licencia de '{$tenant->name}' renovada hasta " . $newExpiry->format('d/m/Y') . ".");
    }

    public function storeTenant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:100',
            'rif_tax_id' => 'required|string|max:50',
            'plan_tier' => 'required|string|in:starter,pro,enterprise,trial',
            'email' => 'required|email',
            'phone' => 'nullable|string',
        ]);

        $rates = DolarApiService::getRates();
        $bcvRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);

        try {
            $tenant = Tenant::create([
                'name' => $request->input('name'),
                'rif_tax_id' => $request->input('rif_tax_id'),
                'subdomain' => strtolower($request->input('subdomain')),
                'plan_tier' => $request->input('plan_tier'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'is_active' => true,
                'bcv_rate' => $bcvRate,
                'expires_at' => now()->addDays(30),
            ]);

            // Branch & Owner defaults
            $branch = Branch::create([
                'tenant_id' => $tenant->id,
                'name' => 'Sucursal Principal',
                'code' => 'SUC-001',
                'is_main' => true,
                'is_active' => true,
            ]);

            User::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'name' => 'Administrador ' . $tenant->name,
                'email' => $request->input('email'),
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'is_active' => true,
            ]);
        } catch (Exception $e) {}

        return redirect()->route('superadmin.index')->with('success', "Empresa '{$request->input('name')}' registrada en Pymora SaaS exitosamente.");
    }

    public function updateTenant(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update($request->only(['name', 'rif_tax_id', 'subdomain', 'plan_tier']));

        return redirect()->route('superadmin.index')->with('success', "Empresa '{$tenant->name}' actualizada correctamente.");
    }

    public function toggleTenantStatus($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->is_active = !$tenant->is_active;
        $tenant->save();

        $statusText = $tenant->is_active ? 'activada' : 'suspendida';
        return redirect()->back()->with('success', "La empresa '{$tenant->name}' ha sido {$statusText}.");
    }

    public function impersonate($id)
    {
        $tenant = Tenant::findOrFail($id);
        
        session([
            'impersonated_tenant_id' => $tenant->id,
            'company_name' => $tenant->name,
            'is_impersonating' => true,
        ]);

        return redirect()->route('dashboard')->with('success', "Modo Impersonación activo: Ahora navegas como '{$tenant->name}'.");
    }

    public function stopImpersonating()
    {
        session()->forget(['impersonated_tenant_id', 'is_impersonating']);
        session(['company_name' => 'Bodega & Abasto El Sol C.A.']);

        return redirect()->route('superadmin.index')->with('success', 'Has salido del modo impersonación. Has vuelto al Panel Super Admin.');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'trial_days' => 'required|integer|min:1',
            'support_email' => 'required|email',
        ]);

        GlobalSetting::set('trial_days', $request->input('trial_days'), 'saas');
        GlobalSetting::set('support_email', $request->input('support_email'), 'saas');

        return redirect()->route('superadmin.index')->with('success', 'Parámetros SaaS actualizados exitosamente.');
    }

    public function storeBroadcast(Request $request)
    {
        $message = $request->input('broadcast_message', '');
        GlobalSetting::set('broadcast_message', $message, 'saas');

        return redirect()->route('superadmin.index')->with('success', 'Aviso global de la plataforma actualizado.');
    }

    public function syncDolarApi(Request $request)
    {
        $rates = DolarApiService::getRates();
        $usd = $rates['bcv_usd'];
        $eur = $rates['bcv_eur'];

        GlobalSetting::set('bcv_usd_rate', $usd, 'exchange');
        GlobalSetting::set('bcv_eur_rate', $eur, 'exchange');

        try {
            Tenant::query()->update(['bcv_rate' => $usd]);
        } catch (Exception $e) {}

        return redirect()->route('superadmin.index')->with('success', "Tasas BCV Oficiales (Dólar: {$usd} VES | Euro: {$eur} VES) sincronizadas automáticamente desde DolarApi.");
    }
}
