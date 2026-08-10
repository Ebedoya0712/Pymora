@extends('layouts.app')

@section('title', 'Inventario Inteligente Multialmacén - Pymora')

@section('content')
<div x-data="{ openModal: false }" class="space-y-6">
    <!-- Header Title & Add Product Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white font-display">Inventario Inteligente Multialmacén</h2>
            <p class="text-xs text-slate-400">Gestión de productos, existencias por sucursal, control de lotes y costos USD/VES.</p>
        </div>
        <button @click="openModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-lg shadow-indigo-500/20 flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Agregar Producto
        </button>
    </div>

    <!-- Products Table Card -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="text-xs font-semibold text-slate-300">Catálogo General de Productos</div>
            <div class="text-xs text-slate-400 font-mono">Tasa de Valoración: 52.40 VES/USD</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="p-3">SKU / Código</th>
                        <th class="p-3">Producto</th>
                        <th class="p-3">Categoría</th>
                        <th class="p-3">Costo USD</th>
                        <th class="p-3">Precio USD</th>
                        <th class="p-3">Precio VES</th>
                        <th class="p-3">Stock Altamira</th>
                        <th class="p-3">Stock Las Mercedes</th>
                        <th class="p-3">Estado Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($products as $p)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3 font-mono text-indigo-400 font-bold">{{ $p->sku }}</td>
                        <td class="p-3 font-semibold text-white">
                            {{ $p->name }}
                            @if($p->has_lots)
                                <span class="ml-1 bg-amber-500/20 text-amber-300 text-[9px] px-1.5 py-0.5 rounded font-mono">LOTE</span>
                            @endif
                        </td>
                        <td class="p-3 text-slate-400">{{ $p->category->name ?? 'General' }}</td>
                        <td class="p-3 font-mono text-slate-400">${{ number_format($p->cost_usd, 2) }}</td>
                        <td class="p-3 font-mono font-bold text-white">${{ number_format($p->price_usd, 2) }}</td>
                        <td class="p-3 font-mono text-emerald-400">Bs {{ number_format($p->price_usd * 52.40, 2) }}</td>
                        <td class="p-3 font-mono font-bold text-slate-200">
                            {{ $p->stocks->where('branch_id', 1)->first()->quantity ?? 0 }} {{ $p->unit }}
                        </td>
                        <td class="p-3 font-mono text-slate-400">
                            {{ $p->stocks->where('branch_id', 2)->first()->quantity ?? 0 }} {{ $p->unit }}
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                NORMAL
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Adding Product -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-card w-full max-w-lg rounded-xl p-6 space-y-4 shadow-2xl border border-slate-700">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-base font-display">Registrar Nuevo Producto</h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('inventory.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1">Nombre del Producto</label>
                    <input type="text" name="name" required placeholder="Ej: Arroz Mary Tradicional 1kg" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Categoría</label>
                        <select name="category_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">SKU / Código</label>
                        <input type="text" name="sku" placeholder="AUTO-GEN" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Costo ($ USD)</label>
                        <input type="number" step="0.01" name="cost_usd" required placeholder="0.00" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Precio Venta ($ USD)</label>
                        <input type="number" step="0.01" name="price_usd" required placeholder="0.00" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Stock Inicial Sucursal Principal</label>
                        <input type="number" name="initial_stock" value="10" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Nº de Lote / Vencimiento</label>
                        <input type="text" name="lot_number" placeholder="Opcional Ej: LOT-2026" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="pt-3 flex justify-end gap-3">
                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-lg hover:bg-slate-700">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg shadow-indigo-500/20">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
