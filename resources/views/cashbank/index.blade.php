@extends('layouts.app')

@section('title', 'Caja & Bancos Multimoneda - Pymora')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div>
        <h2 class="text-2xl font-bold text-white font-display">Caja & Bancos (Tesorería Multimoneda)</h2>
        <p class="text-xs text-slate-400">Control de apertura/cierre de cajas de turno, arqueos y saldos en cuentas bancarias nacionales e internacionales.</p>
    </div>

    <!-- Active Cash Sessions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Multi-Account Cards -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="font-bold text-white text-base">Cuentas Bancarias & Wallets</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($bankAccounts as $acc)
                <div class="glass-card p-4 rounded-xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-300">{{ $acc->bank_name ?? 'Caja Chica' }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-emerald-500/20 text-emerald-300">
                            {{ $acc->currency }}
                        </span>
                    </div>
                    <div class="text-xl font-extrabold text-white font-display">
                        {{ $acc->currency === 'USD' ? '$' : 'Bs ' }}{{ number_format($acc->balance, 2) }}
                    </div>
                    <div class="text-[11px] text-slate-400 font-mono flex items-center justify-between border-t border-slate-800 pt-2">
                        <span>{{ $acc->name }}</span>
                        <span>{{ $acc->account_number }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Cash Sessions History -->
        <div class="space-y-4">
            <h3 class="font-bold text-white text-base">Historial de Turnos de Caja</h3>
            <div class="glass-card p-4 rounded-xl space-y-3">
                @foreach($cashSessions as $s)
                <div class="p-3 bg-slate-900/60 rounded-lg border border-slate-800 space-y-1 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-indigo-400">Caja POS 01</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold uppercase bg-emerald-500/20 text-emerald-300">
                            {{ strtoupper($s->status) }}
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-400">Apertura: {{ $s->opened_at->format('d/m/Y H:i') }}</div>
                    <div class="flex justify-between font-mono font-bold text-white pt-1">
                        <span>Esperado: ${{ number_format($s->expected_cash_usd, 2) }}</span>
                        <span class="text-emerald-400">Inicial: ${{ number_format($s->initial_cash_usd, 2) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
