@extends('layouts.app')

@section('title', 'Retenciones Fiscales SENIAT & Comisiones - Pymora')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div>
        <h2 class="text-2xl font-bold text-white font-display">Retenciones Fiscales SENIAT & Comisiones de Ventas</h2>
        <p class="text-xs text-slate-400">Comprobantes de retención de IVA (75% / 100%) e ISLR para contribuyentes especiales, y liquidación de vendedores.</p>
    </div>

    <!-- Grid: Retentions & Commissions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Retenciones SENIAT Card -->
        <div class="glass-card p-5 rounded-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-white text-base">Comprobantes de Retención IVA / ISLR</h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500/20 text-amber-300">SENIAT OK</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px]">
                        <tr>
                            <th class="p-2">Comprobante Nº</th>
                            <th class="p-2">Proveedor / RIF</th>
                            <th class="p-2">Base $</th>
                            <th class="p-2">Retenido $</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($retentions as $r)
                        <tr>
                            <td class="p-2 font-mono font-bold text-amber-400">{{ $r->retention_number }}</td>
                            <td class="p-2">
                                <div class="font-semibold text-white">{{ $r->supplier_name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $r->supplier_tax_id }}</div>
                            </td>
                            <td class="p-2 font-mono text-slate-300">${{ number_format($r->base_amount_usd, 2) }}</td>
                            <td class="p-2 font-mono font-bold text-emerald-400">${{ number_format($r->retained_amount_usd, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Comisiones Vendedores Card -->
        <div class="glass-card p-5 rounded-xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-white text-base">Comisiones de Vendedores</h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-indigo-500/20 text-indigo-300">POS Sales</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px]">
                        <tr>
                            <th class="p-2">Vendedor</th>
                            <th class="p-2">Venta Ref.</th>
                            <th class="p-2">Tasa %</th>
                            <th class="p-2">Comisión USD</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($commissions as $c)
                        <tr>
                            <td class="p-2 font-semibold text-white">{{ $c->user->name ?? 'Pedro Gómez' }}</td>
                            <td class="p-2 font-mono text-indigo-400">VTA-2026-0001</td>
                            <td class="p-2 font-mono text-slate-400">{{ number_format($c->commission_rate, 2) }}%</td>
                            <td class="p-2 font-mono font-bold text-emerald-400">${{ number_format($c->commission_amount_usd, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
