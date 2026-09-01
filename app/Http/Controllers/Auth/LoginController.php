<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $role = $request->input('demo_role');

        // Handle Demo Quick Logins for testing
        if ($role) {
            $firstTenant = \App\Models\Tenant::first();
            session([
                'user_role' => $role,
                'user_email' => $request->input('email'),
                'user_name' => match($role) {
                    'super_admin' => 'Eliecer (Super Admin)',
                    'owner' => 'Carlos Mendoza (Owner)',
                    'cashier' => 'Pedro Gómez (Cajero)',
                    'warehouse_manager' => 'Luis Almacén',
                    default => 'Usuario Pymora'
                },
                'tenant_id' => $firstTenant->id ?? 1,
                'company_name' => $firstTenant->name ?? 'Bodega & Abasto El Sol C.A.',
                'business_type' => $firstTenant->business_type ?? 'abasto',
            ]);

            return match($role) {
                'super_admin' => redirect()->route('superadmin.index')->with('success', 'Bienvenido al Portal Super Admin SaaS.'),
                'cashier' => redirect()->route('pos.index')->with('success', 'Turno POS iniciado correctamente.'),
                'warehouse_manager' => redirect()->route('inventory.index')->with('success', 'Bienvenido al Módulo de Inventario.'),
                default => redirect()->route('dashboard')->with('success', 'Sesión iniciada correctamente.')
            };
        }

        // Standard Laravel Authentication
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $tenant = $user->tenant;

            session([
                'user_role' => $user->role,
                'user_email' => $user->email,
                'user_name' => $user->name,
                'tenant_id' => $user->tenant_id,
                'company_name' => $tenant?->name ?? 'Bodega & Abasto El Sol C.A.',
                'business_type' => $tenant?->business_type ?? 'abasto',
            ]);

            return match($user->role ?? 'owner') {
                'super_admin' => redirect()->route('superadmin.index'),
                'cashier' => redirect()->route('pos.index'),
                'warehouse_manager' => redirect()->route('inventory.index'),
                default => redirect()->route('dashboard')
            };
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
}
