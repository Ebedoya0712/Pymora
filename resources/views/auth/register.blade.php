<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empresa - Pymora SaaS</title>
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
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div x-data="{ subdomain: '' }" class="w-full max-w-lg space-y-6 relative z-10 my-8">

        <!-- Logo Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-indigo-600 font-bold text-white text-3xl shadow-xl shadow-emerald-500/20 font-display">
                P
            </div>
            <h1 class="text-3xl font-extrabold text-white font-display">Registra tu Empresa en Pymora</h1>
            <p class="text-xs text-slate-400">Comienza tus 15 días de prueba gratis. Sin necesidad de tarjeta de crédito.</p>
        </div>

        <!-- Registration Form Glass Card -->
        <div class="glass-card p-6 rounded-2xl shadow-2xl space-y-6">

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                @if($errors->any())
                    <div class="p-3 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Section 1: Business Details -->
                <div class="space-y-3">
                    <div class="text-[11px] font-semibold text-indigo-400 uppercase tracking-wider">1. Datos de tu Empresa</div>

                    <div>
                        <label class="block text-slate-300 font-medium mb-1">Nombre Comercial de la Empresa</label>
                        <input type="text" name="company_name" required placeholder="Ej: Comercializadora Valera C.A." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-medium mb-1">RIF / Cédula Fiscal</label>
                            <input type="text" name="rif_tax_id" required placeholder="J-12345678-0" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>

                        <div>
                            <label class="block text-slate-300 font-medium mb-1">Subdominio deseado</label>
                            <div class="relative">
                                <input x-model="subdomain" type="text" name="subdomain" required placeholder="valera" class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-4 pr-24 py-2.5 text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                                <span class="absolute right-3 top-3 text-[10px] text-slate-500 font-mono font-semibold">.pymora.com</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Owner Details -->
                <div class="space-y-3 pt-2 border-t border-slate-800">
                    <div class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider">2. Datos del Administrador (Owner)</div>

                    <div>
                        <label class="block text-slate-300 font-medium mb-1">Nombre Completo del Dueño</label>
                        <input type="text" name="owner_name" required placeholder="Ej: Carlos Mendoza" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-medium mb-1">Correo Electrónico</label>
                            <input type="email" name="email" required placeholder="carlos@empresa.com" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>

                        <div>
                            <label class="block text-slate-300 font-medium mb-1">Teléfono (WhatsApp)</label>
                            <input type="text" name="phone" required placeholder="+584121234567" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-medium mb-1">Contraseña</label>
                            <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>

                        <div>
                            <label class="block text-slate-300 font-medium mb-1">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/25 transition-all font-display mt-2">
                    CREAR MI EMPRESA & INICIAR PROBA GRATIS
                </button>
            </form>

        </div>

        <!-- Login Link -->
        <div class="text-center text-xs text-slate-400">
            ¿Ya tienes una cuenta en Pymora?
            <a href="{{ route('login') }}" class="font-bold text-indigo-400 hover:underline">Inicia sesión aquí</a>
        </div>

    </div>
</body>
</html>
