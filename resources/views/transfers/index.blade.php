@extends('layouts.app')

@section('title', 'Traslados Multi-Sucursal - Pymora')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white font-display">Traslados & Transferencias entre Sucursales</h2>
            <p class="text-xs text-slate-400">Control de inventario en tránsito, depósitos centrales y recepción de mercancía.</p>
        </div>
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-lg shadow-indigo-500/20 flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Solicitar Traslado
        </button>
    </div>

    <!-- Transfers Table -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-sm">Transferencias de Stock Registradas</h3>
            <span class="text-xs text-slate-400 font-mono">Multi-Branch System</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="p-3">Nº Guía Traslado</th>
                        <th class="p-3">Sucursal Origen</th>
                        <th class="p-3">Sucursal Destino</th>
                        <th class="p-3">Fecha Solicitud</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($transfers as $t)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3 font-mono font-bold text-indigo-400">{{ $t->transfer_number }}</td>
                        <td class="p-3 text-slate-300 font-semibold">{{ $t->fromBranch->name ?? 'Almacén Central' }}</td>
                        <td class="p-3 text-slate-300 font-semibold">{{ $t->toBranch->name ?? 'Sucursal Altamira' }}</td>
                        <td class="p-3 font-mono text-slate-400">{{ is_object($t->created_at) ? $t->created_at->format('Y-m-d H:i') : ($t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('Y-m-d H:i') : 'Hoy') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                {{ strtoupper($t->status) }}
                            </span>
                        </td>
                        <td class="p-3">
                            <button class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded shadow transition-all">Confirmar Recepción</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
