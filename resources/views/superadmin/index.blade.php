@extends('layouts.app')

@section('title', 'Dashboard SuperAdmin - Pymora SaaS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-2xl border border-slate-800 backdrop-blur-xl">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                📊 Dashboard SuperAdmin
            </h1>
            <p class="text-sm text-slate-400 mt-1">Resumen ejecutivo de comercios activos, facturación recurrente, salud financiera y parámetros globales.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.finanzas') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Mis Finanzas</span>
            </a>
        </div>
    </div>

    <!-- Executive KPI Cards Grid (3 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- 1. Total Empresas -->
        <a href="{{ route('superadmin.empresas') }}" class="glass-card p-5 rounded-2xl border border-slate-800 hover:border-indigo-500/40 transition-all group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-1">
                <span>Empresas Registradas</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-white font-mono">{{ $totalTenants }}</div>
            <div class="text-xs text-indigo-400 font-semibold mt-2 flex items-center justify-between">
                <span>{{ $activeTenants }} Activas en SaaS</span>
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </div>
        </a>

        <!-- 2. MRR Ingresos Mensuales -->
        <a href="{{ route('superadmin.finanzas') }}" class="glass-card p-5 rounded-2xl border border-slate-800 hover:border-emerald-500/40 transition-all group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-1">
                <span>Ingresos Recurrentes (MRR)</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-400 font-mono">${{ number_format($totalMrrUsd, 2) }}</div>
            <div class="text-xs text-emerald-400/80 font-semibold mt-2 flex items-center justify-between">
                <span>Cobranza Mensual SaaS</span>
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </div>
        </a>

        <!-- 3. Tasa BCV Oficial -->
        <a href="{{ route('superadmin.configuracion') }}" class="glass-card p-5 rounded-2xl border border-slate-800 hover:border-amber-500/40 transition-all group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-1">
                <span>Tasa Oficial Dólar BCV</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-amber-300 font-mono">{{ number_format($bcvUsdRate, 2) }} <span class="text-xs text-slate-400 font-normal">VES</span></div>
            <div class="text-xs text-amber-400/80 font-mono mt-2 flex items-center justify-between">
                <span>Euro: {{ number_format($bcvEurRate, 2) }} VES</span>
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </div>
        </a>
    </div>

    <!-- Modular Navigation Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <!-- Module 1: Empresas -->
        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-3 flex flex-col justify-between hover:border-indigo-500/50 transition-all group">
            <div class="space-y-2">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="font-bold text-white text-base group-hover:text-indigo-400 transition-colors">🏬 Módulo Empresas</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Crea, edita, elimina, suspende, reactiva suscripciones y realiza auditorías en tiempo real.</p>
            </div>
            <a href="{{ route('superadmin.empresas') }}" class="w-full text-center bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white font-bold py-2 rounded-lg text-xs border border-indigo-500/30 transition-all flex items-center justify-center gap-1.5">
                <span>Gestionar Empresas</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Module 2: Mis Finanzas & Planes -->
        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-3 flex flex-col justify-between hover:border-emerald-500/50 transition-all group">
            <div class="space-y-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-white text-base group-hover:text-emerald-400 transition-colors">💰 Mis Finanzas & Tarifas</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Reporte de ganancias, comprobantes de pago recibidos y configuración de Planes & Tarifas SaaS.</p>
            </div>
            <a href="{{ route('superadmin.finanzas') }}" class="w-full text-center bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white font-bold py-2 rounded-lg text-xs border border-emerald-500/30 transition-all flex items-center justify-center gap-1.5">
                <span>Ver Finanzas & Planes</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Module 3: Usuarios y Roles -->
        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-3 flex flex-col justify-between hover:border-purple-500/50 transition-all group">
            <div class="space-y-2">
                <div class="w-10 h-10 rounded-xl bg-purple-600/20 border border-purple-500/30 flex items-center justify-center text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="font-bold text-white text-base group-hover:text-purple-400 transition-colors">👥 Usuarios y Roles</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Administra las cuentas de usuario de Super Admin, administradores y personal técnico.</p>
            </div>
            <a href="{{ route('superadmin.users') }}" class="w-full text-center bg-purple-600/20 hover:bg-purple-600 text-purple-300 hover:text-white font-bold py-2 rounded-lg text-xs border border-purple-500/30 transition-all flex items-center justify-center gap-1.5">
                <span>Administrar Usuarios</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Module 4: Configuración -->
        <div class="glass-card p-6 rounded-2xl border border-slate-800 space-y-3 flex flex-col justify-between hover:border-amber-500/50 transition-all group">
            <div class="space-y-2">
                <div class="w-10 h-10 rounded-xl bg-amber-600/20 border border-amber-500/30 flex items-center justify-center text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-bold text-white text-base group-hover:text-amber-400 transition-colors">⚙️ Configuración</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Sincronización BCV DolarApi, correo de soporte, trial por defecto y avisos broadcast globales.</p>
            </div>
            <a href="{{ route('superadmin.configuracion') }}" class="w-full text-center bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-white font-bold py-2 rounded-lg text-xs border border-amber-500/30 transition-all flex items-center justify-center gap-1.5">
                <span>Parámetros & BCV</span>
                <span>&rarr;</span>
            </a>
        </div>

    </div>

    <!-- Summary Table of Active Companies -->
    <div class="glass-card p-6 rounded-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-white text-base flex items-center gap-2">
                <span>Resumen de Empresas Recientes</span>
                <span class="bg-indigo-500/20 text-indigo-300 text-xs px-2.5 py-0.5 rounded-full font-mono">{{ $tenants->count() }}</span>
            </h3>
            <a href="{{ route('superadmin.empresas') }}" class="text-xs text-indigo-400 hover:underline font-semibold flex items-center gap-1">
                <span>Ver Módulo Completo de Empresas</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase font-mono text-[10px]">
                    <tr>
                        <th class="p-3">Empresa / RIF</th>
                        <th class="p-3">Subdominio</th>
                        <th class="p-3">Plan</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3 text-right">Acción Rápida</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                    @forelse($tenants->take(5) as $t)
                    @php
                        $bMeta = $businessTypes[$t->business_type] ?? $businessTypes['abasto'];
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">{{ $bMeta['icon'] }}</span>
                                <div>
                                    <div class="font-bold text-white">{{ $t->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $t->rif_tax_id ?: 'J-12345678-9' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3 font-mono text-indigo-400">{{ $t->subdomain }}.pymora.com</td>
                        <td class="p-3 font-mono font-bold uppercase text-indigo-300">{{ $t->plan_tier }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $t->is_active ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                {{ $t->is_active ? 'ACTIVO' : 'SUSPENDIDO' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <a href="{{ route('superadmin.impersonate', $t->id) }}" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-bold rounded-lg transition-colors shadow">
                                🔍 Auditar Empresa
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400">No hay empresas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
