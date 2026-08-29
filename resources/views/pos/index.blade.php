@extends('layouts.app')

@section('title', 'Punto de Venta POS Multimoneda - Pymora')

@section('content')
<div x-data="posSystem()" class="h-[calc(100vh-80px)] flex flex-col md:flex-row gap-4 overflow-hidden">

    <!-- Left Panel: Product Catalog & Search (65%) -->
    <div class="flex-1 flex flex-col gap-4 overflow-hidden">
        <!-- Search & Barcode Scanner Bar -->
        <div class="glass-card p-3 rounded-xl flex flex-wrap items-center justify-between gap-3">
            <div class="relative flex-1 min-w-[240px]">
                <div class="absolute left-3 top-2.5 flex items-center gap-1.5 text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <input x-ref="posSearchInput" 
                       x-model="searchQuery" 
                       @keydown.enter.prevent="handleScanEnter()"
                       type="text" 
                       placeholder="Escanea con el lector de barras o busca por nombre / SKU..." 
                       class="w-full bg-slate-900 border border-slate-700 rounded-lg pl-9 pr-24 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 font-mono">
                <span class="absolute right-2 top-1.5 px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono text-[10px] font-bold border border-indigo-500/30 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Lector USB</span>
                </span>
            </div>
            <!-- Category Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 max-w-full">
                <button @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700'" class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-all cursor-pointer">
                    Todos
                </button>
                @foreach($categories as $cat)
                <button @click="selectedCategory = {{ $cat->id }}" :class="selectedCategory === {{ $cat->id }} ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700'" class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-all cursor-pointer">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Product Cards Grid -->
        <div class="flex-1 overflow-y-auto grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 pr-1">
            <template x-for="p in filteredProducts" :key="p.id">
                <button @click="addToCart(p)" class="glass-card glass-card-hover p-3 rounded-xl flex flex-col justify-between text-left h-36 transition-all group border border-slate-800 hover:border-indigo-500">
                    <div class="space-y-1">
                        <div class="text-[10px] font-mono text-indigo-400 group-hover:text-indigo-300" x-text="p.sku"></div>
                        <div class="text-xs font-bold text-slate-100 line-clamp-2" x-text="p.name"></div>
                    </div>
                    <div class="mt-2 flex items-end justify-between">
                        <div>
                            <div class="text-base font-extrabold text-white font-display" x-text="'$' + p.price_usd.toFixed(2)"></div>
                            <div class="text-[10px] font-mono text-emerald-400" x-text="'Bs ' + (p.price_usd * bcvRate).toFixed(2)"></div>
                        </div>
                        <span class="p-1.5 rounded-lg bg-indigo-600/20 text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </span>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- Right Panel: Live Cart & Checkout (35%) -->
    <div class="w-full md:w-96 glass-card rounded-xl flex flex-col overflow-hidden">
        <!-- Cart Header -->
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                <h3 class="font-bold text-white text-sm font-display">Ticket de Venta POS</h3>
            </div>
            <button @click="clearCart()" class="text-xs text-rose-400 hover:underline">Vaciar</button>
        </div>

        <!-- Customer Selector -->
        <div class="px-4 py-2 bg-slate-900/60 border-b border-slate-800">
            <select x-model="selectedCustomerId" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                <option value="">-- Cliente Contado (General) --</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->tax_id }})</option>
                @endforeach
            </select>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 divide-y divide-slate-800">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="pt-2 flex items-center justify-between gap-2 text-xs">
                    <div class="flex-1">
                        <div class="font-semibold text-slate-200" x-text="item.name"></div>
                        <div class="text-[10px] text-slate-400 font-mono" x-text="'$' + item.price.toFixed(2) + ' c/u'"></div>
                    </div>
                    <!-- Quantity controls -->
                    <div class="flex items-center gap-1.5 bg-slate-900 border border-slate-700 rounded-lg p-1">
                        <button @click="updateQty(index, -1)" class="w-5 h-5 rounded bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center font-bold text-xs">-</button>
                        <span class="w-6 text-center font-mono font-bold text-white text-xs" x-text="item.qty"></span>
                        <button @click="updateQty(index, 1)" class="w-5 h-5 rounded bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center font-bold text-xs">+</button>
                    </div>
                    <div class="text-right font-mono font-bold text-white min-w-[50px]" x-text="'$' + (item.price * item.qty).toFixed(2)"></div>
                </div>
            </template>
            <template x-if="cart.length === 0">
                <div class="py-12 text-center text-slate-500 text-xs">
                    El carrito está vacío.<br>Haga clic en los productos para agregarlos.
                </div>
            </template>
        </div>

        <!-- Totals Summary & Checkout Button -->
        <div class="p-4 bg-slate-900/90 border-t border-slate-800 space-y-3">
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-400">
                    <span>Subtotal:</span>
                    <span class="font-mono text-slate-200" x-text="'$' + subtotalUsd.toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>IVA (16%):</span>
                    <span class="font-mono text-slate-200" x-text="'$' + taxUsd.toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>IGTF (3.0% Divisas):</span>
                    <span class="font-mono text-amber-400" x-text="'$' + igtfUsd.toFixed(2)"></span>
                </div>
                <div class="pt-2 border-t border-slate-800 flex justify-between items-end">
                    <div>
                        <div class="text-xs font-bold text-slate-300">TOTAL PAGAR</div>
                        <div class="text-[10px] font-mono text-emerald-400" x-text="'Bs ' + (totalUsd * bcvRate).toFixed(2) + ' VES'"></div>
                    </div>
                    <div class="text-2xl font-extrabold text-white font-display" x-text="'$' + totalUsd.toFixed(2)"></div>
                </div>
            </div>

            <!-- Checkout Form Button -->
            <form action="{{ route('pos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_id" :value="selectedCustomerId">
                <input type="hidden" name="total_usd" :value="totalUsd">
                <input type="hidden" name="items_json" :value="JSON.stringify(cart)">
                <input type="hidden" name="payment_method" value="cash_usd">
                <input type="hidden" name="currency" value="USD">

                <button type="submit" :disabled="cart.length === 0" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/20 transition-all font-display">
                    PROCESAR COBRO MULTIMONEDA
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function posSystem() {
    return {
        bcvRate: {{ $tenant->bcv_rate }},
        searchQuery: '',
        selectedCategory: 'all',
        selectedCustomerId: '',
        products: @json($products),
        cart: [
            { id: 1, name: 'Refresco Coca-Cola 2L', price: 2.50, qty: 2 },
            { id: 2, name: 'Harina PAN Blanca 1kg', price: 1.35, qty: 2 }
        ],

        get filteredProducts() {
            return this.products.filter(p => {
                const matchesCat = this.selectedCategory === 'all' || p.category_id === this.selectedCategory;
                const matchesSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                     (p.sku && p.sku.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                                     (p.barcode && p.barcode.includes(this.searchQuery));
                return matchesCat && matchesSearch;
            });
        },

        playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1800, ctx.currentTime);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.12);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.12);
            } catch (e) {}
        },

        handleScanEnter() {
            const query = (this.searchQuery || '').trim().toLowerCase();
            if (!query) return;

            // 1. Try exact barcode match first
            let match = this.products.find(p => p.barcode && p.barcode.toLowerCase() === query);
            
            // 2. Try exact SKU match
            if (!match) {
                match = this.products.find(p => p.sku && p.sku.toLowerCase() === query);
            }

            // 3. Try partial name match if only 1 result
            if (!match) {
                const results = this.filteredProducts;
                if (results.length === 1) {
                    match = results[0];
                }
            }

            if (match) {
                this.playBeep();
                this.addToCart(match);
                this.searchQuery = '';
            }
        },

        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price_usd),
                    qty: 1
                });
            }
        },

        updateQty(index, change) {
            this.cart[index].qty += change;
            if (this.cart[index].qty <= 0) {
                this.cart.splice(index, 1);
            }
        },

        clearCart() {
            this.cart = [];
        },

        get subtotalUsd() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        get taxUsd() {
            return this.subtotalUsd * 0.16;
        },

        get igtfUsd() {
            return this.subtotalUsd * 0.03;
        },

        get totalUsd() {
            return this.subtotalUsd + this.taxUsd + this.igtfUsd;
        }
    }
}
</script>
@endsection
