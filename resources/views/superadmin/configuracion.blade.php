@extends('layouts.app')

@section('title', 'Configuración Global - Super Admin Pymora')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-2xl border border-slate-800 backdrop-blur-xl">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                ⚙️ Configuración & Parámetros del Sistema
            </h1>
            <p class="text-sm text-slate-400 mt-1">Administra la sincronización de divisas BCV en vivo, días de prueba gratis, avisos globales y correo de soporte.</p>
        </div>
        <form action="{{ route('superadmin.sync-dolarapi') }}" method="POST">
            @csrf
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Sincronizar Tasas DolarApi (En Vivo)</span>
            </button>
        </form>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div x-data="{ showSuccess: true }" x-show="showSuccess" x-transition class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="showSuccess = false" title="Cerrar notificación" class="text-emerald-400/80 hover:text-white hover:bg-emerald-500/20 p-1.5 rounded-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ showError: true }" x-show="showError" x-transition class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-medium flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="showError = false" title="Cerrar notificación" class="text-rose-400/80 hover:text-white hover:bg-rose-500/20 p-1.5 rounded-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- 1. Parámetros SaaS -->
        <div class="glass-card p-6 rounded-2xl space-y-4">
            <h3 class="font-bold text-white text-lg flex items-center gap-2 border-b border-slate-800 pb-3">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Parámetros del Sistema</span>
            </h3>

            <form action="{{ route('superadmin.settings.update') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-300">Correo Oficial de Soporte Técnico</label>
                    <input type="email" name="support_email" value="{{ $supportEmail }}" required class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-amber-500 focus:outline-none">
                    <p class="text-[10px] text-slate-500">Recibe las solicitudes de soporte y comprobantes adjuntados.</p>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-300">Días de Prueba Gratis (Nuevas Empresas)</label>
                    <div class="relative">
                        <input type="number" name="trial_days" min="1" max="365" value="{{ $trialDays }}" required class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-amber-500 focus:outline-none font-mono">
                        <span class="absolute right-3 top-2.5 text-[10px] text-slate-500 font-mono">Días</span>
                    </div>
                    <p class="text-[10px] text-slate-500">Tiempo de gracia asignado automáticamente al registrar un comercio.</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-2.5 rounded-lg shadow-lg transition-colors">
                        Guardar Parámetros del Sistema
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Tasas de Cambio Oficiales BCV (Sincronizadas Automáticas) -->
        <div class="glass-card p-6 rounded-2xl space-y-4 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-white text-lg flex items-center justify-between border-b border-slate-800 pb-3">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Tasas de Cambio Oficiales BCV</span>
                    </span>
                    <span class="text-[10px] font-mono bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded border border-emerald-500/30 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        API EN VIVO
                    </span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <!-- Dólar BCV -->
                    <div class="bg-slate-950 p-4 rounded-xl border border-slate-800/80 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-400 font-medium">Tasa Oficial Dólar BCV</div>
                            <div class="text-2xl font-extrabold text-emerald-400 font-mono mt-1">
                                {{ number_format($bcvUsdRate, 2, ',', '.') }} <span class="text-xs text-slate-500 font-normal">VES</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold font-mono text-xs">
                            USD
                        </div>
                    </div>

                    <!-- Euro BCV -->
                    <div class="bg-slate-950 p-4 rounded-xl border border-slate-800/80 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-400 font-medium">Tasa Oficial Euro BCV</div>
                            <div class="text-2xl font-extrabold text-sky-400 font-mono mt-1">
                                {{ number_format($bcvEurRate, 2, ',', '.') }} <span class="text-xs text-slate-500 font-normal">VES</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 font-bold font-mono text-xs">
                            EUR
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3.5 bg-slate-950/80 rounded-xl border border-slate-800 text-xs text-slate-400 flex items-center justify-between gap-3 mt-4">
                <div class="flex items-center gap-2 text-[11px]">
                    <span class="text-emerald-400">🤖</span>
                    <span>Sincronización automática de divisas oficiales BCV integrada vía DolarApi.</span>
                </div>
                <form action="{{ route('superadmin.sync-dolarapi') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 rounded-lg text-xs font-semibold transition-all">
                        Forzar Sincronización
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- 3. Aviso Global (Broadcast Banner) -->
    <div class="glass-card p-6 rounded-2xl space-y-4">
        <h3 class="font-bold text-white text-lg flex items-center gap-2 border-b border-slate-800 pb-3">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            <span>📢 Mensaje Institucional / Aviso Global Broadcast</span>
        </h3>

        <form action="{{ route('superadmin.broadcast.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-slate-300">Mensaje Visible Superior (Global Alert Banner)</label>
                <textarea name="broadcast_message" rows="3" placeholder="Ej: 🚀 Mantenimiento programado el día Domingo a las 2:00 AM. La plataforma estará activa normalmente..." class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl p-3 focus:border-indigo-500 focus:outline-none">{{ $broadcastMessage }}</textarea>
                <p class="text-[10px] text-slate-500">Dejar en blanco para desactivar el aviso superior en todas las empresas.</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-2.5 rounded-lg shadow-lg transition-colors">
                    Publicar Mensaje Global
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
