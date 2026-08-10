@extends('layouts.app')

@section('title', 'Cotizaciones & Presupuestos Workflow - Pymora')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white font-display">Módulo Cotizaciones con Workflow de Aprobación</h2>
            <p class="text-xs text-slate-400">Presupuestos formales para clientes B2B, aprobación gerencial y conversión a factura 1-click.</p>
        </div>
        <button class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-semibold rounded-lg shadow-lg shadow-amber-500/20 flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Crear Cotización
        </button>
    </div>

    <!-- Quotes Table -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-sm">Listado de Cotizaciones Emitidas</h3>
            <span class="text-xs text-slate-400 font-mono">Workflow B2B</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="p-3">Nº Cotización</th>
                        <th class="p-3">Cliente B2B</th>
                        <th class="p-3">Válido Hasta</th>
                        <th class="p-3">Total USD</th>
                        <th class="p-3">Total VES (BCV)</th>
                        <th class="p-3">Estado Workflow</th>
                        <th class="p-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($quotes as $q)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3 font-mono font-bold text-amber-400">{{ $q->quote_number }}</td>
                        <td class="p-3 font-semibold text-white">{{ $q->customer->name ?? 'Cliente General' }}</td>
                        <td class="p-3 font-mono text-slate-400">{{ $q->valid_until }}</td>
                        <td class="p-3 font-mono font-bold text-white">${{ number_format($q->total_usd, 2) }}</td>
                        <td class="p-3 font-mono text-emerald-400">Bs {{ number_format($q->total_usd * 52.40, 2) }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                {{ strtoupper($q->status) }}
                            </span>
                        </td>
                        <td class="p-3 flex items-center gap-2">
                            <button class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded shadow transition-all">Convertir a Venta</button>
                            <button class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 text-[10px] rounded border border-slate-700">Imprimir PDF</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
