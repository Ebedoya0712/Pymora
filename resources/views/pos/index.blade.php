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
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input x-ref="posSearchInput" 
                       x-model="searchQuery" 
                       @keydown.enter.prevent="handleScanEnter()"
                       type="text" 
                       placeholder="Buscar por nombre, código de barras o SKU..." 
                       class="w-full bg-slate-900 border border-slate-700 rounded-lg pl-9 pr-4 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 font-mono">
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

        <!-- Product Cards Grid with Rich Product Images -->
        <div class="flex-1 overflow-y-auto grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 pr-1">
            <template x-for="p in filteredProducts" :key="p.id">
                <button @click="addToCart(p)" class="glass-card glass-card-hover rounded-xl flex flex-col justify-between text-left overflow-hidden transition-all group border border-slate-800 hover:border-indigo-500 h-[210px]">
                    <!-- Product Image Container -->
                    <div class="relative w-full h-28 bg-slate-900 overflow-hidden shrink-0">
                        <img :src="p.image_url || 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&auto=format&fit=crop&q=80'" 
                             :alt="p.name" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded bg-slate-950/80 backdrop-blur-md border border-slate-700 text-[9px] font-mono text-indigo-300 font-semibold" x-text="p.sku || 'PROD'"></div>
                    </div>
                    <!-- Product Details -->
                    <div class="p-2.5 flex-1 flex flex-col justify-between">
                        <div class="text-xs font-bold text-slate-100 line-clamp-1 group-hover:text-indigo-300 transition-colors" x-text="p.name"></div>
                        <div class="mt-1 flex items-end justify-between">
                            <div>
                                <div class="text-sm font-extrabold text-white font-display" x-text="'$' + parseFloat(p.price_usd).toFixed(2)"></div>
                                <div class="text-[10px] font-mono text-emerald-400 font-semibold" x-text="'Bs ' + formatNumber(p.price_usd * bcvUsdRate)"></div>
                            </div>
                            <span class="p-1 rounded-lg bg-indigo-600/20 text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </span>
                        </div>
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
            <div class="space-y-1 text-xs">
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
                <!-- Converted Totals Display -->
                <div class="pt-2 border-t border-slate-800 space-y-1">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-300 font-semibold">TOTAL VES (Bs):</span>
                        <span class="font-mono font-extrabold text-emerald-400" x-text="'Bs ' + formatNumber(totalUsd * bcvUsdRate)"></span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-300 font-semibold">TOTAL EUR (€):</span>
                        <span class="font-mono font-extrabold text-sky-400" x-text="'€' + ((totalUsd * bcvUsdRate) / bcvEurRate).toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-xs font-bold text-slate-100 uppercase">Total Dólares:</span>
                        <span class="text-2xl font-extrabold text-white font-display" x-text="'$' + totalUsd.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <!-- Open Multi-Currency Checkout Modal Button -->
            <button @click="openCheckoutModal()" 
                    :disabled="cart.length === 0" 
                    class="w-full py-3.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-indigo-600 hover:from-emerald-600 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition-all font-display uppercase tracking-wider flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Procesar Cobro Multimoneda</span>
            </button>
        </div>
    </div>

    <!-- Multi-Currency Checkout Modal (Cobro Multimoneda) -->
    <div x-show="showCheckoutModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         style="display: none;">
        
        <div class="glass-card w-full max-w-xl rounded-2xl border border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <!-- Modal Header -->
            <div class="p-4 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold">
                        💳
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white font-display">Cobro Multimoneda POS</h3>
                        <p class="text-[10px] text-slate-400">Selecciona la moneda y método de pago al cambio oficial BCV</p>
                    </div>
                </div>
                <button @click="showCheckoutModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Content Body -->
            <form action="{{ route('pos.store') }}" method="POST" class="p-5 overflow-y-auto space-y-4 text-xs">
                @csrf
                <input type="hidden" name="customer_id" :value="selectedCustomerId">
                <input type="hidden" name="total_usd" :value="totalUsd">
                <input type="hidden" name="items_json" :value="JSON.stringify(cart)">
                <input type="hidden" name="currency" :value="payCurrency">
                <input type="hidden" name="payment_method" :value="payMethod">
                <input type="hidden" name="amount_received_native" :value="amountReceived">
                <input type="hidden" name="change_due_ves" :value="changeDueVes">
                <input type="hidden" name="change_due_usd" :value="changeDueUsd">

                <!-- Totals Breakdown Widget -->
                <div class="grid grid-cols-3 gap-2 p-3 bg-slate-950 rounded-xl border border-slate-800 text-center">
                    <div class="p-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                        <div class="text-[10px] text-emerald-400 font-semibold uppercase">Total USD ($)</div>
                        <div class="text-lg font-extrabold text-white font-mono mt-0.5" x-text="'$' + totalUsd.toFixed(2)"></div>
                    </div>
                    <div class="p-2 rounded-lg bg-sky-500/10 border border-sky-500/20">
                        <div class="text-[10px] text-sky-400 font-semibold uppercase">Total VES (Bs)</div>
                        <div class="text-base font-extrabold text-white font-mono mt-0.5" x-text="'Bs ' + formatNumber(totalUsd * bcvUsdRate)"></div>
                        <div class="text-[9px] text-slate-400 font-mono">Tasa: <span x-text="bcvUsdRate.toFixed(2)"></span></div>
                    </div>
                    <div class="p-2 rounded-lg bg-indigo-500/10 border border-indigo-500/20">
                        <div class="text-[10px] text-indigo-400 font-semibold uppercase">Total EUR (€)</div>
                        <div class="text-base font-extrabold text-white font-mono mt-0.5" x-text="'€' + totalEurRequired.toFixed(2)"></div>
                        <div class="text-[9px] text-slate-400 font-mono">Tasa: <span x-text="bcvEurRate.toFixed(2)"></span></div>
                    </div>
                </div>

                <!-- 1. Currency Selector -->
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-200">Moneda de Pago</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" 
                                @click="selectCurrency('USD')" 
                                :class="payCurrency === 'USD' ? 'bg-emerald-600 text-white border-emerald-400' : 'bg-slate-900 text-slate-300 border-slate-800 hover:border-slate-700'" 
                                class="p-3 rounded-xl border font-bold flex flex-col items-center gap-1 transition-all cursor-pointer">
                            <span class="text-lg">💵</span>
                            <span>Dólares ($ USD)</span>
                        </button>

                        <button type="button" 
                                @click="selectCurrency('VES')" 
                                :class="payCurrency === 'VES' ? 'bg-sky-600 text-white border-sky-400' : 'bg-slate-900 text-slate-300 border-slate-800 hover:border-slate-700'" 
                                class="p-3 rounded-xl border font-bold flex flex-col items-center gap-1 transition-all cursor-pointer">
                            <span class="text-lg">🇻🇪</span>
                            <span>Bolívares (Bs VES)</span>
                        </button>

                        <button type="button" 
                                @click="selectCurrency('EUR')" 
                                :class="payCurrency === 'EUR' ? 'bg-indigo-600 text-white border-indigo-400' : 'bg-slate-900 text-slate-300 border-slate-800 hover:border-slate-700'" 
                                class="p-3 rounded-xl border font-bold flex flex-col items-center gap-1 transition-all cursor-pointer">
                            <span class="text-lg">💶</span>
                            <span>Euros (€ EUR)</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Payment Method Selector -->
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-200">Método de Pago</label>
                    <select x-model="payMethod" class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl p-2.5 focus:border-indigo-500 focus:outline-none font-semibold">
                        <template x-if="payCurrency === 'USD'">
                            <g>
                                <option value="cash_usd">Efectivo USD ($)</option>
                                <option value="zelle">Zelle (USD)</option>
                                <option value="paypal">PayPal (USD)</option>
                            </g>
                        </template>
                        <template x-if="payCurrency === 'VES'">
                            <g>
                                <option value="pago_movil">Pago Móvil (VES)</option>
                                <option value="pos_ves">Punto de Venta / Tarjeta (VES)</option>
                                <option value="cash_ves">Efectivo Bolívares (Bs)</option>
                                <option value="transfer_ves">Transferencia Bancaria (VES)</option>
                            </g>
                        </template>
                        <template x-if="payCurrency === 'EUR'">
                            <g>
                                <option value="cash_eur">Efectivo EUR (€)</option>
                                <option value="transfer_eur">Transferencia Bancaria EUR (€)</option>
                            </g>
                        </template>
                    </select>
                </div>

                <!-- 3. Amount Received & Change Calculator -->
                <div class="space-y-2 p-3 bg-slate-950 rounded-xl border border-slate-800">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-slate-200">Monto Recibido del Cliente</label>
                        <span class="text-[10px] text-slate-400 font-mono" x-text="'Requerido: ' + getRequiredFormatted()"></span>
                    </div>

                    <div class="relative">
                        <input type="number" 
                               step="0.01" 
                               x-model.number="amountReceived" 
                               placeholder="Ej: 20.00" 
                               class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl p-3 font-mono text-base font-bold focus:border-emerald-500 focus:outline-none">
                        <span class="absolute right-3 top-3.5 font-bold font-mono text-emerald-400" x-text="payCurrency"></span>
                    </div>

                    <!-- Fast Denomination Quick Buttons -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pt-1">
                        <span class="text-[10px] text-slate-500 font-semibold mr-1">Rápido:</span>
                        <template x-for="denom in getQuickDenominations()" :key="denom">
                            <button type="button" 
                                    @click="amountReceived = denom" 
                                    class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-700 text-slate-200 text-xs font-mono font-bold transition-all">
                                <span x-text="payCurrency === 'VES' ? 'Bs ' + denom : (payCurrency === 'EUR' ? '€' + denom : '$' + denom)"></span>
                            </button>
                        </template>
                        <button type="button" 
                                @click="amountReceived = getExactRequiredAmount()" 
                                class="px-2.5 py-1 rounded-lg bg-emerald-600/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold transition-all ml-auto">
                            Monto Exacto
                        </button>
                    </div>

                    <!-- Calculated Change / Vuelto Box -->
                    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-300">Vuelto / Cambio a Entregar</div>
                            <div class="text-[10px] text-slate-500 font-mono" x-text="'Equivalente: $' + changeDueUsd.toFixed(2) + ' USD'"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-extrabold text-emerald-400 font-mono" x-text="'Bs ' + formatNumber(changeDueVes) + ' VES'"></div>
                        </div>
                    </div>
                </div>

                <!-- 4. Reference Code / Memo (Optional) -->
                <div class="space-y-1">
                    <label class="font-semibold text-slate-300">Código de Referencia / Comprobante (Opcional)</label>
                    <input type="text" 
                           name="reference_code" 
                           placeholder="Ej: PM-998822 / Ref 4321..." 
                           class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl p-2.5 focus:border-indigo-500 focus:outline-none font-mono">
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex gap-3">
                    <button type="button" @click="showCheckoutModal = false" class="w-1/3 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition-all">
                        Cancelar
                    </button>
                    <button type="submit" 
                            :disabled="amountReceived < getExactRequiredAmount() - 0.05" 
                            class="w-2/3 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-extrabold rounded-xl shadow-lg shadow-emerald-500/20 transition-all font-display text-sm tracking-wider uppercase">
                        Confirmar Venta y Cobro
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function posSystem() {
    return {
        bcvUsdRate: {{ $bcvUsdRate }},
        bcvEurRate: {{ $bcvEurRate }},
        searchQuery: '',
        selectedCategory: 'all',
        selectedCustomerId: '',
        products: @json($products),
        cart: [
            { id: 1, name: 'Refresco Coca-Cola 2L', price: 2.50, qty: 2 },
            { id: 2, name: 'Harina PAN Blanca 1kg', price: 1.35, qty: 2 }
        ],

        // Modal States
        showCheckoutModal: false,
        payCurrency: 'USD',
        payMethod: 'cash_usd',
        amountReceived: 0,

        get filteredProducts() {
            return this.products.filter(p => {
                const matchesCat = this.selectedCategory === 'all' || p.category_id === this.selectedCategory;
                const matchesSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                     (p.sku && p.sku.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                                     (p.barcode && p.barcode.includes(this.searchQuery));
                return matchesCat && matchesSearch;
            });
        },

        formatNumber(val) {
            return parseFloat(val || 0).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

            let match = this.products.find(p => p.barcode && p.barcode.toLowerCase() === query);
            if (!match) {
                match = this.products.find(p => p.sku && p.sku.toLowerCase() === query);
            }
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
        },

        get totalEurRequired() {
            const totalVes = this.totalUsd * this.bcvUsdRate;
            return totalVes / this.bcvEurRate;
        },

        openCheckoutModal() {
            if (this.cart.length === 0) return;
            this.payCurrency = 'USD';
            this.payMethod = 'cash_usd';
            this.amountReceived = Math.ceil(this.totalUsd);
            this.showCheckoutModal = true;
        },

        selectCurrency(curr) {
            this.payCurrency = curr;
            if (curr === 'USD') {
                this.payMethod = 'cash_usd';
                this.amountReceived = Math.ceil(this.totalUsd);
            } else if (curr === 'VES') {
                this.payMethod = 'pago_movil';
                this.amountReceived = Math.ceil(this.totalUsd * this.bcvUsdRate);
            } else if (curr === 'EUR') {
                this.payMethod = 'cash_eur';
                this.amountReceived = Math.ceil(this.totalEurRequired);
            }
        },

        getExactRequiredAmount() {
            if (this.payCurrency === 'USD') {
                return parseFloat(this.totalUsd.toFixed(2));
            } else if (this.payCurrency === 'VES') {
                return parseFloat((this.totalUsd * this.bcvUsdRate).toFixed(2));
            } else if (this.payCurrency === 'EUR') {
                return parseFloat(this.totalEurRequired.toFixed(2));
            }
            return 0;
        },

        getRequiredFormatted() {
            if (this.payCurrency === 'USD') {
                return '$' + this.totalUsd.toFixed(2) + ' USD';
            } else if (this.payCurrency === 'VES') {
                return 'Bs ' + this.formatNumber(this.totalUsd * this.bcvUsdRate) + ' VES';
            } else if (this.payCurrency === 'EUR') {
                return '€' + this.totalEurRequired.toFixed(2) + ' EUR';
            }
            return '';
        },

        getQuickDenominations() {
            if (this.payCurrency === 'USD') {
                return [10, 20, 50, 100];
            } else if (this.payCurrency === 'VES') {
                const req = Math.ceil(this.totalUsd * this.bcvUsdRate);
                return [req, Math.ceil(req * 1.1 / 100) * 100, Math.ceil(req * 1.2 / 500) * 500];
            } else if (this.payCurrency === 'EUR') {
                return [10, 20, 50, 100];
            }
            return [];
        },

        get changeDueVes() {
            const reqNative = this.getExactRequiredAmount();
            const rec = parseFloat(this.amountReceived || 0);
            if (rec <= reqNative) return 0;
            
            const diffNative = rec - reqNative;
            if (this.payCurrency === 'VES') {
                return diffNative;
            } else if (this.payCurrency === 'USD') {
                return diffNative * this.bcvUsdRate;
            } else if (this.payCurrency === 'EUR') {
                return diffNative * this.bcvEurRate;
            }
            return 0;
        },

        get changeDueUsd() {
            return this.changeDueVes / this.bcvUsdRate;
        }
    }
}
</script>
@endsection
