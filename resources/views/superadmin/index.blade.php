@extends('layouts.app')

@section('title', 'Panel Super Admin SaaS - Pymora Owner')

@section('content')
<div x-data="{ openTenantModal: false }" class="space-y-6">
    <!-- Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-400 text-[10px] font-mono font-bold uppercase">SaaS Owner Control</span>
                <h2 class="text-2xl font-bold text-white font-display">Portal Super Admin Pymora</h2>
            </div>
            <p class="text-xs text-slate-400">Administración global de empresas (tenants), suscripciones SaaS, planes y facturación recurrente.</p>
        </div>
        <button @click="openTenantModal = true" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-xs font-semibold rounded-lg shadow-lg shadow-indigo-500/20 flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Registrar Nueva Empresa (Tenant)
        </button>
    </div>

    <!-- Global SaaS KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-card p-4 rounded-xl space-y-1">
            <div class="text-slate-400 text-xs font-medium">Empresas Registradas (Tenants)</div>
            <div class="text-3xl font-extrabold text-white font-display">{{ $totalTenants }}</div>
            <div class="text-xs text-emerald-400 font-mono">{{ $activeTenants }} Activas con suscripción</div>
        </div>

        <div class="glass-card p-4 rounded-xl space-y-1">
            <div class="text-slate-400 text-xs font-medium">Ingresos Mensuales SaaS (MRR)</div>
            <div class="text-3xl font-extrabold text-emerald-400 font-display">${{ number_format($totalMrrUsd, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">USD/mes</span></div>
            <div class="text-xs text-slate-400">Proyección de cobro recurrente</div>
        </div>

        <div class="glass-card p-4 rounded-xl space-y-1">
            <div class="text-slate-400 text-xs font-medium">Infraestructura & Servidores</div>
            <div class="text-3xl font-extrabold text-indigo-400 font-display">100%</div>
            <div class="text-xs text-emerald-400 font-mono">Render.com + Supabase Free Tier</div>
        </div>
    </div>

    <!-- Tenants List Table -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-sm">Inquilinos Registrados en la Plataforma</h3>
            <span class="text-xs text-slate-400 font-mono">Total: {{ count($tenants) }} Empresas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="p-3">Empresa / RIF</th>
                        <th class="p-3">Subdominio</th>
                        <th class="p-3">Plan SaaS</th>
                        <th class="p-3">Tasa BCV</th>
                        <th class="p-3">Vencimiento</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Acciones Super Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($tenants as $t)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3">
                            <div class="font-bold text-white">{{ $t->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $t->rif_tax_id }}</div>
                        </td>
                        <td class="p-3 font-mono text-indigo-400">{{ $t->subdomain }}.pymora.com</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                {{ $t->plan_tier }}
                            </span>
                        </td>
                        <td class="p-3 font-mono text-emerald-400">{{ number_format($t->bcv_rate, 4) }} VES</td>
                        <td class="p-3 text-slate-400 font-mono">{{ $t->expires_at ? $t->expires_at->format('Y-m-d') : 'Indefinido' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">ACTIVO</span>
                        </td>
                        <td class="p-3 flex items-center gap-2">
                            <button class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 text-[10px] rounded border border-slate-700">Editar Plan</button>
                            <button class="px-2 py-1 bg-indigo-600/30 hover:bg-indigo-600 text-indigo-200 text-[10px] rounded">Entrar como Admin</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for registering new Tenant -->
    <div x-show="openTenantModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-card w-full max-w-lg rounded-xl p-6 space-y-4 shadow-2xl border border-slate-700">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-base font-display">Registrar Nueva Empresa en Pymora SaaS</h3>
                <button @click="openTenantModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('superadmin.tenants.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1">Nombre Comercial de la Empresa</label>
                    <input type="text" name="name" required placeholder="Ej: Comercializadora Valera C.A." class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">RIF / Cédula Fiscal</label>
                        <input type="text" name="rif_tax_id" required placeholder="J-12345678-0" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Subdominio Único</label>
                        <input type="text" name="subdomain" required placeholder="valera" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Plan SaaS</label>
                        <select name="plan_tier" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="trial">Trial (15 Días Gratis)</option>
                            <option value="starter">Starter ($29/mes)</option>
                            <option value="pro" selected>Pro Multi-Sucursal ($79/mes)</option>
                            <option value="enterprise">Enterprise ($199/mes)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Correo Administrador</label>
                        <input type="email" name="email" required placeholder="admin@empresa.com" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="pt-3 flex justify-end gap-3">
                    <button type="button" @click="openTenantModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg hover:bg-slate-700">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-lg shadow-lg shadow-indigo-500/20">Crear Empresa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
