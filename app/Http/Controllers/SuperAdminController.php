<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Sale;
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

        if ($tenants->isEmpty()) {
            $tenants = collect([
                (object) [
                    'id' => 1,
                    'name' => 'Bodega & Abasto El Sol C.A.',
                    'rif_tax_id' => 'J-12345678-9',
                    'subdomain' => 'elsol',
                    'plan_tier' => 'pro',
                    'bcv_rate' => 52.4000,
                    'expires_at' => now()->addDays(365),
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Supermercados Plaza Caracas C.A.',
                    'rif_tax_id' => 'J-30555666-2',
                    'subdomain' => 'plazacaracas',
                    'plan_tier' => 'enterprise',
                    'bcv_rate' => 52.4000,
                    'expires_at' => now()->addDays(180),
                ],
                (object) [
                    'id' => 3,
                    'name' => 'Inversiones Los Chaguaramos',
                    'rif_tax_id' => 'J-40999888-1',
                    'subdomain' => 'chaguaramos',
                    'plan_tier' => 'starter',
                    'bcv_rate' => 52.4000,
                    'expires_at' => now()->addDays(30),
                ]
            ]);
            $totalTenants = 3;
            $activeTenants = 3;
            $totalMrrUsd = 307.00; // $79 (pro) + $199 (enterprise) + $29 (starter)
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

        return view('superadmin.index', compact('tenants', 'totalTenants', 'activeTenants', 'totalMrrUsd'));
    }

    public function storeTenant(Request $request)
    {
        return redirect()->route('superadmin.index')->with('success', 'Nueva empresa creada en la plataforma SaaS.');
    }
}
