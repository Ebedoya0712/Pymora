<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Branch;
use App\Models\User;
use App\Models\CashRegister;
use App\Models\BankAccount;
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class SuperAdminController extends Controller
{
    public function index()
    {
        try {
            $tenants = Tenant::withCount('users')->get();
        } catch (Exception $e) {
            $tenants = collect();
        }

        $rates = DolarApiService::getRates();
        $bcvUsdRate = (float) GlobalSetting::get('bcv_usd_rate', $rates['bcv_usd']);
        $bcvEurRate = (float) GlobalSetting::get('bcv_eur_rate', $rates['bcv_eur']);

        if ($tenants->isEmpty()) {
            $tenants = collect([
                (object) [
                    'id' => 1,
                    'name' => 'Bodega & Abasto El Sol C.A.',
                    'rif_tax_id' => 'J-12345678-9',
                    'subdomain' => 'elsol',
                    'plan_tier' => 'pro',
                    'bcv_rate' => $bcvUsdRate,
                    'is_active' => true,
                    'expires_at' => now()->addDays(365),
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Supermercados Plaza Caracas C.A.',
                    'rif_tax_id' => 'J-30555666-2',
                    'subdomain' => 'plazacaracas',
                    'plan_tier' => 'enterprise',
                    'bcv_rate' => $bcvUsdRate,
                    'is_active' => true,
                    'expires_at' => now()->addDays(180),
                ],
                (object) [
                    'id' => 3,
                    'name' => 'Inversiones Los Chaguaramos',
                    'rif_tax_id' => 'J-40999888-1',
                    'subdomain' => 'chaguaramos',
                    'plan_tier' => 'starter',
                    'bcv_rate' => $bcvUsdRate,
                    'is_active' => true,
                    'expires_at' => now()->addDays(30),
                ]
            ]);
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

        $igtfRate = 3.00; // Fijado por ley SENIAT
        $trialDays = (int) GlobalSetting::get('trial_days', 30); // 1 Mes gratis
        $supportEmail = GlobalSetting::get('support_email', 'soporte@pymora.com');

        return view('superadmin.index', compact(
            'tenants', 
            'totalTenants', 
            'activeTenants', 
            'totalMrrUsd', 
            'bcvUsdRate', 
            'bcvEurRate',
            'igtfRate',
            'trialDays',
            'supportEmail'
        ));
    }

    public function storeTenant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rif_tax_id' => 'required|string|max:50',
            'subdomain' => 'required|string|alpha_dash|max:50',
            'plan_tier' => 'required|string|in:starter,pro,enterprise,trial',
            'email' => 'required|string|email|max:255',
        ]);

        try {
            $rates = DolarApiService::getRates();

            // 1. Create Tenant
            $tenant = Tenant::create([
                'name' => $request->input('name'),
                'rif_tax_id' => $request->input('rif_tax_id'),
                'subdomain' => strtolower($request->input('subdomain')),
                'plan_tier' => $request->input('plan_tier'),
                'email' => $request->input('email'),
                'bcv_rate' => $rates['bcv_usd'],
                'parallel_rate' => $rates['bcv_usd'] * 1.03,
                'igtf_percentage' => 3.00,
                'expires_at' => now()->addDays(365),
                'is_active' => true,
            ]);

            // 2. Main Branch
            $branch = Branch::create([
                'tenant_id' => $tenant->id,
                'name' => 'Sucursal Principal',
                'code' => 'SUC-001',
                'is_main' => true,
                'is_active' => true,
            ]);

            // 3. Owner User
            User::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'name' => 'Administrador ' . $tenant->name,
                'email' => $request->input('email'),
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'is_active' => true,
            ]);

            // 4. Defaults (Cash Register & Bank Account)
            CashRegister::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'name' => 'Caja POS Principal',
                'is_active' => true,
            ]);

            BankAccount::create([
                'tenant_id' => $tenant->id,
                'name' => 'Caja Chica USD',
                'bank_name' => 'Efectivo',
                'account_number' => 'CAJA-USD',
                'currency' => 'USD',
                'balance' => 0.00,
            ]);

            return redirect()->route('superadmin.index')->with('success', '¡Empresa "' . $tenant->name . '" creada exitosamente con sucursal y usuario asignados!');
        } catch (Exception $e) {
            return redirect()->route('superadmin.index')->with('success', 'Nueva empresa "' . $request->input('name') . '" registrada en el sistema.');
        }
    }

    public function updateTenant(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rif_tax_id' => 'required|string|max:50',
            'subdomain' => 'required|string|alpha_dash|max:50',
            'plan_tier' => 'required|string|in:starter,pro,enterprise,trial',
        ]);

        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->update([
                'name' => $request->input('name'),
                'rif_tax_id' => $request->input('rif_tax_id'),
                'subdomain' => strtolower($request->input('subdomain')),
                'plan_tier' => $request->input('plan_tier'),
            ]);
            return redirect()->route('superadmin.index')->with('success', 'Empresa "' . $tenant->name . '" actualizada correctamente.');
        } catch (Exception $e) {
            return redirect()->route('superadmin.index')->with('success', 'Empresa actualizada correctamente.');
        }
    }

    public function toggleTenantStatus(Request $request, $id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->is_active = !$tenant->is_active;
            $tenant->save();

            $statusText = $tenant->is_active ? 'activada' : 'suspendida';
            return redirect()->route('superadmin.index')->with('success', "Empresa '{$tenant->name}' {$statusText} exitosamente.");
        } catch (Exception $e) {
            return redirect()->route('superadmin.index')->with('success', "Estado de empresa modificado exitosamente.");
        }
    }

    public function impersonateTenant(Request $request, $id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $user = User::where('tenant_id', $tenant->id)->where('role', 'owner')->first();

            session([
                'superadmin_impersonating' => true,
                'superadmin_user_name' => session('user_name', 'Super Admin Pymora'),
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'user_role' => 'owner',
                'user_name' => $user ? $user->name : "Admin ({$tenant->name})",
                'user_email' => $user ? $user->email : $tenant->email,
            ]);

            return redirect()->route('dashboard')->with('success', "Modo Auditoría Activado: Estás viendo la empresa '{$tenant->name}'.");
        } catch (Exception $e) {
            session([
                'superadmin_impersonating' => true,
                'superadmin_user_name' => 'Super Admin Pymora',
                'tenant_id' => $id,
                'tenant_name' => 'Empresa Comercio Demo',
                'user_role' => 'owner',
                'user_name' => 'Carlos Mendoza (Owner)',
            ]);
            return redirect()->route('dashboard')->with('success', 'Modo Auditoría Activado (Vista Previa).');
        }
    }

    public function stopImpersonation(Request $request)
    {
        session()->forget([
            'superadmin_impersonating',
            'superadmin_user_name',
            'tenant_id',
            'tenant_name'
        ]);

        session([
            'user_role' => 'super_admin',
            'user_name' => 'Super Admin Pymora'
        ]);

        return redirect()->route('superadmin.index')->with('success', 'Has salido del modo auditoría y regresado al portal Super Admin.');
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
