@extends('layouts.app')

@section('title', 'Control Global Pymora SaaS - Super Admin')

@section('content')
<div x-data="{ openTenantModal: false, activeTab: 'tenants' }" class="space-y-6">

    <!-- Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800/80 pb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 text-[10px] font-mono font-bold uppercase border border-indigo-500/30">
                    👑 SUPER ADMIN PLATFORM
                </span>
                <h2 class="text-2xl font-bold text-white font-display">Control Global Pymora SaaS</h2>
            </div>
            <p class="text-xs text-slate-400 mt-1">Gestión de inquilinos (tenants), suscripciones activas, planes de cobro y parámetros globales de la plataforma.</p>
        </div>
        
        <button @click="openTenantModal = true" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-500/20 flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Registrar Nueva Empresa (Tenant)
        </button>
    </div>

    <!-- Global SaaS Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Tenants -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1 relative overflow-hidden">
            <div class="absolute -right-2 -bottom-2 w-16 h-16 bg-indigo-500/10 rounded-full blur-xl"></div>
            <div class="text-slate-400 text-xs font-medium">Empresas Registradas</div>
            <div class="text-3xl font-extrabold text-white font-display">{{ $totalTenants }}</div>
            <div class="text-[11px] text-emerald-400 font-mono flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                {{ $activeTenants }} Activas con suscripción
            </div>
        </div>

        <!-- Card 2: MRR -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1 relative overflow-hidden">
            <div class="absolute -right-2 -bottom-2 w-16 h-16 bg-emerald-500/10 rounded-full blur-xl"></div>
            <div class="text-slate-400 text-xs font-medium">Ingresos Recurrentes (MRR)</div>
            <div class="text-3xl font-extrabold text-emerald-400 font-display">${{ number_format($totalMrrUsd, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">USD/mes</span></div>
            <div class="text-[11px] text-slate-400">Facturación recurrente mensual</div>
        </div>

        <!-- Card 3: Tasa BCV Global -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1 relative overflow-hidden">
            <div class="absolute -right-2 -bottom-2 w-16 h-16 bg-amber-500/10 rounded-full blur-xl"></div>
            <div class="text-slate-400 text-xs font-medium">Tasa BCV por Defecto</div>
            <div class="text-3xl font-extrabold text-amber-300 font-display">52.40 <span class="text-xs font-normal text-slate-400 font-mono">VES</span></div>
            <div class="text-[11px] text-indigo-300">Sincronizada con BCV Oficial</div>
        </div>

        <!-- Card 4: Infraestructura -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1 relative overflow-hidden">
            <div class="absolute -right-2 -bottom-2 w-16 h-16 bg-purple-500/10 rounded-full blur-xl"></div>
            <div class="text-slate-400 text-xs font-medium">Estado de Infraestructura</div>
            <div class="text-3xl font-extrabold text-indigo-400 font-display">100%</div>
            <div class="text-[11px] text-emerald-400 font-mono">Base de Datos PostgreSQL/MySQL Activa</div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 text-xs font-medium">
        <button @click="activeTab = 'tenants'" :class="activeTab === 'tenants' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Inquilinos & Empresas ({{ count($tenants) }})
        </button>
        <button @click="activeTab = 'plans'" :class="activeTab === 'plans' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Planes & Tarifas SaaS
        </button>
        <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Parámetros Globales
        </button>
    </div>

    <!-- TAB 1: Tenants List Table -->
    <div x-show="activeTab === 'tenants'" class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-white text-sm font-display">Empresas Inquilinas en Pymora</h3>
                <p class="text-[11px] text-slate-400">Listado de empresas con subdominios dedicados y su estado de suscripción.</p>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" placeholder="Buscar empresa o RIF..." class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 w-60">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="p-3">Empresa / RIF</th>
                        <th class="p-3">Subdominio</th>
                        <th class="p-3">Plan SaaS</th>
                        <th class="p-3">Tasa BCV</th>
                        <th class="p-3">Vencimiento</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3 text-right">Acciones Super Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($tenants as $t)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3">
                            <div class="font-bold text-white">{{ $t->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $t->rif_tax_id }}</div>
                        </td>
                        <td class="p-3 font-mono text-indigo-400 font-medium">
                            {{ $t->subdomain }}.pymora.com
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                {{ strtoupper($t->plan_tier) }}
                            </span>
                        </td>
                        <td class="p-3 font-mono text-emerald-400 font-semibold">{{ number_format($t->bcv_rate, 4) }} VES</td>
                        <td class="p-3 text-slate-400 font-mono">{{ $t->expires_at ? $t->expires_at->format('Y-m-d') : 'Licencia Vitalicia' }}</td>
                        <td class="p-3">
                            @if($t->is_active ?? true)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">ACTIVO</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">SUSPENDIDO</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 text-[10px] rounded-lg border border-slate-700 transition-colors">
                                    Editar Plan
                                </button>
                                <a href="{{ route('dashboard') }}" class="px-2.5 py-1 bg-indigo-600/30 hover:bg-indigo-600 text-indigo-200 text-[10px] font-semibold rounded-lg border border-indigo-500/30 transition-colors">
                                    🔑 Impersonar (Entrar)
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: Plans & Pricing -->
    <div x-show="activeTab === 'plans'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Starter Plan -->
        <div class="glass-card rounded-xl p-6 border border-slate-800 space-y-4 relative">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Plan Starter</div>
            <div class="text-3xl font-extrabold text-white font-display">$29 <span class="text-xs font-normal text-slate-400">/ mes</span></div>
            <p class="text-xs text-slate-400">Ideal para pequeños comercios o bodegas únicas.</p>
            <ul class="text-xs text-slate-300 space-y-2 pt-2 border-t border-slate-800">
                <li class="flex items-center gap-2">✓ 1 Sucursal</li>
                <li class="flex items-center gap-2">✓ 1 Caja POS Táctil</li>
                <li class="flex items-center gap-2">✓ Hasta 3 Usuarios</li>
                <li class="flex items-center gap-2">✓ Facturación Multimoneda USD/VES</li>
            </ul>
        </div>

        <!-- Pro Plan -->
        <div class="glass-card rounded-xl p-6 border-2 border-indigo-500/50 space-y-4 relative bg-indigo-950/20">
            <span class="absolute -top-3 right-4 px-2.5 py-0.5 bg-indigo-500 text-white text-[10px] font-bold rounded-full uppercase">MÁS POPULAR</span>
            <div class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Plan Pro Multi-Sucursal</div>
            <div class="text-3xl font-extrabold text-white font-display">$79 <span class="text-xs font-normal text-slate-400">/ mes</span></div>
            <p class="text-xs text-slate-400">Para empresas medianas con múltiples sucursales y depósitos.</p>
            <ul class="text-xs text-slate-300 space-y-2 pt-2 border-t border-slate-800">
                <li class="flex items-center gap-2">✓ Hasta 5 Sucursales</li>
                <li class="flex items-center gap-2">✓ Cajas POS Ilimitadas</li>
                <li class="flex items-center gap-2">✓ Usuarios Ilimitados con Roles</li>
                <li class="flex items-center gap-2">✓ Traslados de Inventario & Cotizaciones</li>
                <li class="flex items-center gap-2">✓ Módulo SENIAT IVA / Comisiones</li>
            </ul>
        </div>

        <!-- Enterprise Plan -->
        <div class="glass-card rounded-xl p-6 border border-slate-800 space-y-4 relative">
            <div class="text-xs font-bold text-purple-400 uppercase tracking-wider">Plan Enterprise</div>
            <div class="text-3xl font-extrabold text-white font-display">$199 <span class="text-xs font-normal text-slate-400">/ mes</span></div>
            <p class="text-xs text-slate-400">Para cadenas de supermercados y distribuidoras masivas.</p>
            <ul class="text-xs text-slate-300 space-y-2 pt-2 border-t border-slate-800">
                <li class="flex items-center gap-2">✓ Sucursales Ilimitadas</li>
                <li class="flex items-center gap-2">✓ Base de Datos Dedicada Supabase</li>
                <li class="flex items-center gap-2">✓ Soporte Prioritario 24/7 en WhatsApp</li>
                <li class="flex items-center gap-2">✓ API personalizada e Integraciones BI</li>
            </ul>
        </div>
    </div>

    <!-- TAB 3: Global Settings -->
    <div x-show="activeTab === 'settings'" class="glass-card rounded-xl p-6 border border-slate-800 max-w-2xl space-y-4">
        <div>
            <h3 class="font-bold text-white text-base font-display">Parámetros Globales de Pymora SaaS</h3>
            <p class="text-xs text-slate-400">Configuración general por defecto para nuevas empresas que se registren en la plataforma.</p>
        </div>

        <form class="space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-400 mb-1">Tasa de Cambio BCV Oficial (VES/USD)</label>
                    <input type="number" step="0.0001" value="52.4000" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Porcentaje IGTF (%)</label>
                    <input type="number" step="0.01" value="3.00" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-slate-400 mb-1">Días de Prueba Gratis (Free Trial)</label>
                <input type="number" value="15" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
            </div>

            <div class="pt-2 flex justify-end">
                <button type="button" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow-lg shadow-indigo-500/20">
                    Guardar Configuración Global
                </button>
            </div>
        </form>
    </div>

    <!-- Modal for registering new Tenant -->
    <div x-show="openTenantModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-card w-full max-w-lg rounded-xl p-6 space-y-4 shadow-2xl border border-slate-700">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-base font-display">Registrar Nueva Empresa (Tenant)</h3>
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
                        <label class="block text-slate-400 mb-1">RIF / Identificación Fiscal</label>
                        <input type="text" name="rif_tax_id" required placeholder="J-12345678-0" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Subdominio Asignado</label>
                        <input type="text" name="subdomain" required placeholder="valera" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Plan SaaS Inicial</label>
                        <select name="plan_tier" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="trial">Trial (15 Días Gratis)</option>
                            <option value="starter">Starter ($29/mes)</option>
                            <option value="pro" selected>Pro Multi-Sucursal ($79/mes)</option>
                            <option value="enterprise">Enterprise ($199/mes)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Correo del Administrador</label>
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
