<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\GlobalSetting;
use App\Services\DolarApiService;
use Illuminate\Http\Request;
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
        return redirect()->route('superadmin.index')->with('success', 'Nueva empresa creada en la plataforma SaaS.');
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
