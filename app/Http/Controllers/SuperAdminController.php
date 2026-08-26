<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\User;
use App\Models\CashRegister;
use App\Models\BankAccount;
use App\Models\SaasPayment;
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public static function getPlans(): array
    {
        $defaults = [
            'trial' => [
                'id' => 'trial',
                'name' => 'Plan 1 Mes Gratis',
                'price' => 0,
                'features' => "✓ 1 Mes de acceso completo gratis\n✓ 1 Sucursal\n✓ 1 Caja POS\n✓ Acceso inicial completo para pruebas",
            ],
            'starter' => [
                'id' => 'starter',
                'name' => 'Plan Sencillo',
                'price' => 29,
                'features' => "✓ 1 Sucursal\n✓ 2 Cajas POS\n✓ 5 Usuarios\n✓ Control de Inventario & Ventas",
            ],
            'pro' => [
                'id' => 'pro',
                'name' => 'Plan Pro (Avanzado)',
                'price' => 79,
                'features' => "✓ Sucursales Ilimitadas\n✓ Cajas Ilimitadas\n✓ Usuarios Ilimitados\n✓ Cotizaciones & Traslados\n✓ Soporte Prioritario VIP",
            ],
        ];

        $stored = GlobalSetting::get('saas_plans');
        if ($stored) {
            $decoded = json_decode($stored, true);
            if (is_array($decoded)) {
                return array_merge($defaults, $decoded);
            }
        }

        return $defaults;
    }

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

        $plans = self::getPlans();

        if ($tenants->isEmpty()) {
            $totalTenants = 3;
            $activeTenants = 3;
            $totalMrrUsd = (float) ($plans['starter']['price'] + $plans['pro']['price'] + $plans['enterprise']['price']);
        } else {
            $totalTenants = $tenants->count();
            $activeTenants = $tenants->where('is_active', true)->count();
            $totalMrrUsd = $tenants->where('is_active', true)->sum(function ($t) use ($plans) {
                return (float) ($plans[$t->plan_tier]['price'] ?? 0);
            });
        }

        $igtfRate = 3.00;
        $trialDays = (int) GlobalSetting::get('trial_days', 30);
        $supportEmail = GlobalSetting::get('support_email', 'soporte@pymora.com');
        $broadcastMessage = GlobalSetting::get('broadcast_message', '');
        $businessTypes = Tenant::getBusinessTypes();

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
            'broadcastMessage',
            'plans',
            'businessTypes'
        ));
    }

    public function empresas(Request $request)
    {
        if (!session('is_impersonating')) {
            session([
                'user_role' => 'super_admin',
                'user_name' => session('user_name', 'Eliecer (Super Admin)'),
            ]);
        }

        try {
            $query = Tenant::withCount('users')->latest('id');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('rif_tax_id', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $tenants = $query->paginate(10)->withQueryString();
            $totalTenants = Tenant::count();
            $activeTenants = Tenant::where('is_active', true)->count();
            $suspendedTenants = Tenant::where('is_active', false)->count();
        } catch (Exception $e) {
            $tenants = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $totalTenants = 0;
            $activeTenants = 0;
            $suspendedTenants = 0;
        }

        $plans = self::getPlans();
        $businessTypes = Tenant::getBusinessTypes();

        return view('superadmin.empresas', compact('tenants', 'totalTenants', 'activeTenants', 'suspendedTenants', 'plans', 'businessTypes'));
    }

    public function updateTenant($id, Request $request)
    {
        $tenant = Tenant::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'rif_tax_id' => 'required|string|max:50',
            'subdomain' => 'required|string|max:50|unique:tenants,subdomain,' . $id,
            'business_type' => 'required|string',
            'plan_tier' => 'required|string',
        ]);

        $tenant->update([
            'name' => $request->input('name'),
            'rif_tax_id' => $request->input('rif_tax_id'),
            'subdomain' => strtolower($request->input('subdomain')),
            'business_type' => $request->input('business_type'),
            'plan_tier' => $request->input('plan_tier'),
            'expires_at' => $request->input('expires_at') ? Carbon::parse($request->input('expires_at')) : $tenant->expires_at,
        ]);

        return redirect()->back()->with('success', "Empresa '{$tenant->name}' actualizada exitosamente.");
    }

    public function deleteTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $name = $tenant->name;
        $tenant->delete();

        return redirect()->back()->with('success', "Empresa '{$name}' eliminada correctamente.");
    }

    public function renewTenant($id, Request $request)
    {
        $tenant = Tenant::findOrFail($id);
        $months = (int) $request->input('months', 1);

        $currentExpire = ($tenant->expires_at && Carbon::parse($tenant->expires_at)->isFuture())
            ? Carbon::parse($tenant->expires_at)
            : now();

        $tenant->expires_at = $currentExpire->addMonths($months);
        $tenant->is_active = true;
        $tenant->save();

        return redirect()->back()->with('success', "Suscripción de '{$tenant->name}' renovada por {$months} mes(es) hasta " . $tenant->expires_at->format('d/m/Y') . ".");
    }

    public function configuracion()
    {
        if (!session('is_impersonating')) {
            session([
                'user_role' => 'super_admin',
                'user_name' => session('user_name', 'Eliecer (Super Admin)'),
            ]);
        }

        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);
        $trialDays = (int) GlobalSetting::get('trial_days', 30);
        $supportEmail = GlobalSetting::get('support_email', 'soporte@pymora.com');
        $broadcastMessage = GlobalSetting::get('broadcast_message', '');

        return view('superadmin.configuracion', compact(
            'bcvUsdRate',
            'bcvEurRate',
            'trialDays',
            'supportEmail',
            'broadcastMessage'
        ));
    }

    public function finanzas(Request $request)
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

        // Revenue Breakdown: Day, Week, Month, Total
        $todayPayments = $payments->filter(fn($p) => Carbon::parse($p->payment_date)->toDateString() === $today);
        $todayRevenueUsd = (float) $todayPayments->sum('amount_usd');
        $todayRevenueVes = (float) round($todayRevenueUsd * $bcvUsdRate, 2);

        $weekRevenueUsd = (float) $payments->filter(fn($p) => Carbon::parse($p->payment_date)->gte(now()->subDays(6)->startOfDay()))->sum('amount_usd');
        $thisMonthRevenueUsd = (float) $payments->filter(fn($p) => Carbon::parse($p->payment_date)->isCurrentMonth())->sum('amount_usd');
        $totalRevenueUsd = (float) $payments->sum('amount_usd');

        $activeTenantsCount = $tenants->where('is_active', true)->count();
        $expiringSoonCount = $tenants->filter(function ($t) {
            return $t->expires_at && Carbon::parse($t->expires_at)->diffInDays(now(), false) >= -30 && Carbon::parse($t->expires_at)->isFuture();
        })->count();

        // Dynamic Chart Period Filtering: 7days, months, years
        $period = $request->input('period', '7days');
        $dailyLabels = [];
        $dailyValues = [];
        $chartTitle = 'Evolución Diaria de Ganancias ($ USD)';
        $chartSubtitle = 'Ingresos registrados en los últimos 7 días.';

        if ($period === 'months') {
            $chartTitle = 'Evolución Mensual de Ganancias ($ USD)';
            $chartSubtitle = 'Ingresos acumulados mes a mes (últimos 12 meses).';
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthStr = $date->format('Y-m');
                $dailyLabels[] = ucfirst($date->translatedFormat('M Y'));
                $dailyValues[] = (float) $payments->filter(fn($p) => Carbon::parse($p->payment_date)->format('Y-m') === $monthStr)->sum('amount_usd');
            }
        } elseif ($period === 'years') {
            $chartTitle = 'Evolución Anual de Ganancias ($ USD)';
            $chartSubtitle = 'Ingresos acumulados por año fiscal.';
            $currentYear = (int) now()->format('Y');
            for ($year = $currentYear - 4; $year <= $currentYear; $year++) {
                $dailyLabels[] = (string) $year;
                $dailyValues[] = (float) $payments->filter(fn($p) => (int) Carbon::parse($p->payment_date)->format('Y') === $year)->sum('amount_usd');
            }
        } else { // 7days default
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateStr = $date->toDateString();
                $dailyLabels[] = $date->translatedFormat('D d/m');
                $dailyValues[] = (float) $payments->filter(fn($p) => Carbon::parse($p->payment_date)->toDateString() === $dateStr)->sum('amount_usd');
            }
        }

        // Revenue by Payment Method
        $methodsMap = [
            'pago_movil' => 'Pago Móvil VES',
            'paypal' => 'PayPal (USD)',
            'zinli' => 'Zinli Wallet',
            'binance' => 'Binance USDT',
            'binance_usdt' => 'Binance USDT',
            'zelle' => 'Zelle (USD)',
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
            $methodLabels = ['Pago Móvil VES', 'PayPal (USD)', 'Binance USDT', 'Zinli Wallet'];
            $methodValues = [158.00, 29.00, 79.00, 79.00];
        }

        $plans = self::getPlans();

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
            'methodValues',
            'plans',
            'period',
            'chartTitle',
            'chartSubtitle'
        ));
    }

    public function comprobantes(Request $request)
    {
        if (!session('is_impersonating')) {
            session([
                'user_role' => 'super_admin',
                'user_name' => session('user_name', 'Eliecer (Super Admin)'),
            ]);
        }

        $query = SaasPayment::with('tenant')->latest();

        if ($request->filled('method') && $request->input('method') !== 'all') {
            $query->where('payment_method', $request->input('method'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_code', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%")
                        ->orWhere('rif_tax_id', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(15)->withQueryString();
        $tenants = Tenant::orderBy('name')->get();
        $plans = self::getPlans();
        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);

        $totalAmountUsd = SaasPayment::sum('amount_usd');
        $countPaypal = SaasPayment::where('payment_method', 'paypal')->count();
        $countPagoMovil = SaasPayment::where('payment_method', 'pago_movil')->count();
        $countZinli = SaasPayment::where('payment_method', 'zinli')->count();
        $countBinance = SaasPayment::where('payment_method', 'binance')->count();

        return view('superadmin.comprobantes', compact(
            'payments',
            'tenants',
            'plans',
            'bcvUsdRate',
            'totalAmountUsd',
            'countPaypal',
            'countPagoMovil',
            'countZinli',
            'countBinance'
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
            $query = User::with('tenant')->where('role', '!=', 'owner')->latest();

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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'tenant_id' => $request->input('role') === 'super_admin' ? null : $request->input('tenant_id'),
            'phone' => $request->input('phone'),
            'avatar' => $avatarPath,
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
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

        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = null;
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
        return redirect()->route('superadmin.users')->with('success', "Usuario '{$user->name}' {$statusText} correctamente.");
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if (auth()->check() && auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta de usuario.');
        }

        if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('superadmin.users')->with('success', "Usuario '{$userName}' eliminado permanentemente.");
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'amount_usd' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'reference_code' => 'required|string',
            'payment_date' => 'required|date',
            'plan_tier' => 'required|string|in:trial,starter,pro,enterprise',
            'months_paid' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        $rates = DolarApiService::getRates();
        $bcvRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $amountVes = round($request->input('amount_usd') * $bcvRate, 2);

        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $proofImagePath = $request->file('proof_image')->store('payment_proofs', 'public');
        }

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
            'proof_image' => $proofImagePath,
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

        return redirect()->back()->with('success', "Comprobante de pago registrado exitosamente con foto/comprobante adjunto. Licencia de '{$tenant->name}' renovada hasta " . $newExpiry->format('d/m/Y') . ".");
    }

    public function storeTenant(Request $request)
    {
        $email = $request->input('email') ?: $request->input('admin_email');
        $request->merge(['email' => $email]);

        $validTypes = implode(',', array_keys(Tenant::getBusinessTypes()));
        $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'nullable|string|max:100',
            'rif_tax_id' => 'required|string|max:50',
            'plan_tier' => 'nullable|string|in:starter,pro,enterprise,trial',
            'business_type' => 'nullable|string|in:' . $validTypes,
            'email' => 'required|email',
            'phone' => 'nullable|string',
        ]);

        $subdomain = $request->input('subdomain') 
            ? strtolower($request->input('subdomain')) 
            : \Illuminate\Support\Str::slug($request->input('name')) . '-' . rand(100, 999);

        $rates = DolarApiService::getRates();
        $bcvRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);

        try {
            $tenant = Tenant::create([
                'name' => $request->input('name'),
                'rif_tax_id' => $request->input('rif_tax_id'),
                'subdomain' => $subdomain,
                'plan_tier' => $request->input('plan_tier', 'trial'),
                'business_type' => $request->input('business_type', 'abasto'),
                'email' => $email,
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

            $ownerEmail = User::where('email', $email)->exists() ? 'admin_' . $tenant->id . '_' . $email : $email;

            User::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'name' => 'Administrador ' . $tenant->name,
                'email' => $ownerEmail,
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'is_active' => true,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al registrar empresa: ' . $e->getMessage());
        }

        return redirect()->route('superadmin.empresas')->with('success', "Empresa '{$tenant->name}' registrada exitosamente en Pymora (1 Mes Gratis activado).");
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
            'tenant_id' => $tenant->id,
            'company_name' => $tenant->name,
            'business_type' => $tenant->business_type ?? 'abasto',
            'is_impersonating' => true,
        ]);

        return redirect()->route('dashboard')->with('success', "Modo Auditoría Activo: Tienes acceso total para operar e inspeccionar a '{$tenant->name}'.");
    }

    public function stopImpersonating()
    {
        session()->forget(['impersonated_tenant_id', 'is_impersonating', 'business_type']);
        $firstTenant = Tenant::first();
        session([
            'tenant_id' => $firstTenant->id ?? 1,
            'company_name' => $firstTenant->name ?? 'Bodega & Abasto El Sol C.A.',
        ]);

        return redirect()->route('superadmin.index')->with('success', 'Has finalizado la auditoría de empresa. Has vuelto al Panel Super Admin.');
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
        $rates = DolarApiService::getRates(true);
        $usd = $rates['bcv_usd'];
        $eur = $rates['bcv_eur'];

        GlobalSetting::set('bcv_usd_rate', $usd, 'exchange');
        GlobalSetting::set('bcv_eur_rate', $eur, 'exchange');

        try {
            Tenant::query()->update(['bcv_rate' => $usd]);
        } catch (Exception $e) {}

        return redirect()->back()->with('success', "Tasas BCV Oficiales (Dólar: {$usd} VES | Euro: {$eur} VES) sincronizadas en vivo desde DolarApi.");
    }

    public function planes()
    {
        if (!session('is_impersonating')) {
            session([
                'user_role' => 'super_admin',
                'user_name' => session('user_name', 'Eliecer (Super Admin)'),
            ]);
        }

        $plans = self::getPlans();
        return view('superadmin.planes', compact('plans'));
    }

    public function updatePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|string|in:trial,starter,pro',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'features' => 'required|string',
        ]);

        $plans = self::getPlans();
        $planId = $request->input('plan_id');

        $plans[$planId] = [
            'id' => $planId,
            'name' => $request->input('name'),
            'price' => (float) $request->input('price'),
            'features' => $request->input('features'),
        ];

        GlobalSetting::set('saas_plans', json_encode($plans), 'saas');

        return redirect()->route('superadmin.planes')->with('success', "Plan '{$plans[$planId]['name']}' actualizado correctamente.");
    }
}
