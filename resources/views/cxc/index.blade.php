@extends('layouts.app')

@section('title', 'Cuentas por Cobrar (CXC) & WhatsApp - Pymora')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div>
        <h2 class="text-2xl font-bold text-white font-display">Cuentas por Cobrar (CXC) & Gestión de Cobranza</h2>
        <p class="text-xs text-slate-400">Control de clientes, límites de crédito, facturas fiadas y recordatorios automáticos por WhatsApp.</p>
    </div>

    <!-- Customers & Debts Table -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-sm">Estado de Cuenta de Clientes</h3>
            <span class="text-xs text-slate-400 font-mono">Tasa de Conversión: 52.40 VES/USD</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="p-3">Cliente / RIF / Cédula</th>
                        <th class="p-3">Tipo</th>
                        <th class="p-3">Teléfono</th>
                        <th class="p-3">Límite Crédito USD</th>
                        <th class="p-3">Deuda Actual USD</th>
                        <th class="p-3">Deuda Equiv. VES</th>
                        <th class="p-3">Acciones de Cobro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($customers as $c)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3">
                            <div class="font-bold text-white">{{ $c->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $c->tax_id }}</div>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-mono font-semibold bg-indigo-500/20 text-indigo-300">
                                {{ $c->customer_type }}
                            </span>
                        </td>
                        <td class="p-3 font-mono text-slate-300">{{ $c->phone ?? 'Sin Teléfono' }}</td>
                        <td class="p-3 font-mono text-slate-400">${{ number_format($c->credit_limit_usd, 2) }}</td>
                        <td class="p-3 font-mono font-bold text-amber-400">${{ number_format($c->current_debt_usd, 2) }}</td>
                        <td class="p-3 font-mono text-emerald-400">Bs {{ number_format($c->current_debt_usd * 52.40, 2) }}</td>
                        <td class="p-3">
                            @if($c->current_debt_usd > 0)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $c->phone) }}?text={{ urlencode('Hola ' . $c->name . ', le recordamos de Bodega & Abasto El Sol su saldo pendiente de $' . number_format($c->current_debt_usd, 2) . ' USD (Bs ' . number_format($c->current_debt_usd * 52.40, 2) . ' a tasa BCV). ¡Gracias!') }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] rounded-lg shadow-md flex items-center gap-1.5 inline-flex transition-all">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/></svg>
                                Recordar por WhatsApp
                            </a>
                            @else
                            <span class="text-[10px] text-slate-500 font-mono">SIN DEUDA</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
