<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Pymora SaaS</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Outfit', sans-serif; }
        .glass-card {
            background: rgba(30, 41, 59, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex items-center justify-center p-4 relative overflow-hidden antialiased">

    <!-- Background Ambient Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div x-data="{ demoRole: '' }" class="w-full max-w-md space-y-6 relative z-10">

        <!-- Logo Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-400 font-bold text-white text-3xl shadow-xl shadow-indigo-500/20 font-display">
                P
            </div>
            <h1 class="text-3xl font-extrabold text-white font-display">Pymora SaaS</h1>
            <p class="text-xs text-slate-400">Sistema Administrativo & Financiero Multimoneda para Venezuela</p>
        </div>

        <!-- Login Form Glass Card -->
        <div class="glass-card p-6 rounded-2xl shadow-2xl space-y-6">

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="demo_role" x-model="demoRole">

                @if($errors->any())
                    <div class="p-3 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400 text-xs">
                        {{ session('success') }}
                    </div>
                @endif

                <div>
                    <label class="block text-slate-300 font-medium mb-1.5">Correo Electrónico</label>
                    <input type="email" name="email" required placeholder="tu@empresa.com" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-slate-300 font-medium">Contraseña</label>
                        <a href="#" class="text-[11px] text-indigo-400 hover:underline">¿Olvidaste tu contraseña?</a>
                    </div>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0">
                        <span>Recordar mi sesión</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/25 transition-all font-display">
                    INICIAR SESIÓN
                </button>
            </form>

            <!-- Quick Demo Role Switcher -->
            <div class="pt-4 border-t border-slate-800 space-y-3">
                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center">Acceso Rápido Demo por Rol</div>
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="admin@pymora.com">
                        <input type="hidden" name="password" value="password">
                        <input type="hidden" name="demo_role" value="super_admin">
                        <button type="submit" class="w-full p-2 bg-indigo-600/20 hover:bg-indigo-600/40 border border-indigo-500/30 text-indigo-300 rounded-lg text-left transition-all">
                            <div class="font-bold">👑 Super Admin</div>
                            <div class="text-[9px] text-slate-400">Owner del SaaS</div>
                        </button>
                    </form>

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="carlos@elsol.com">
                        <input type="hidden" name="password" value="password">
                        <input type="hidden" name="demo_role" value="owner">
                        <button type="submit" class="w-full p-2 bg-emerald-600/20 hover:bg-emerald-600/40 border border-emerald-500/30 text-emerald-300 rounded-lg text-left transition-all">
                            <div class="font-bold">🏢 Owner (Dueño)</div>
                            <div class="text-[9px] text-slate-400">Bodega El Sol</div>
                        </button>
                    </form>

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="pedro@elsol.com">
                        <input type="hidden" name="password" value="password">
                        <input type="hidden" name="demo_role" value="cashier">
                        <button type="submit" class="w-full p-2 bg-amber-600/20 hover:bg-amber-600/40 border border-amber-500/30 text-amber-300 rounded-lg text-left transition-all">
                            <div class="font-bold">🛒 Cajero POS</div>
                            <div class="text-[9px] text-slate-400">Punto de Venta</div>
                        </button>
                    </form>

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="luis@elsol.com">
                        <input type="hidden" name="password" value="password">
                        <input type="hidden" name="demo_role" value="warehouse_manager">
                        <button type="submit" class="w-full p-2 bg-purple-600/20 hover:bg-purple-600/40 border border-purple-500/30 text-purple-300 rounded-lg text-left transition-all">
                            <div class="font-bold">📦 Almacenista</div>
                            <div class="text-[9px] text-slate-400">Inventario</div>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Registration Footer Link -->
        <div class="text-center text-xs text-slate-400">
            ¿No tienes una empresa registrada?
            <a href="{{ route('register') }}" class="font-bold text-indigo-400 hover:underline">Registra tu empresa en Pymora (15 días gratis)</a>
        </div>

    </div>
</body>
</html>
