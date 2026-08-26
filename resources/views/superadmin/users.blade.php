@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Pymora Super Admin')

@section('content')
<div x-data="{ 
    showCreateModal: false, 
    showEditModal: false, 
    editUserId: null, 
    editUserName: '', 
    editUserEmail: '', 
    editUserRole: 'owner', 
    editUserTenantId: '', 
    editUserPhone: '',
    openEdit(user) {
        this.editUserId = user.id;
        this.editUserName = user.name;
        this.editUserEmail = user.email;
        this.editUserRole = user.role;
        this.editUserTenantId = user.tenant_id || '';
        this.editUserPhone = user.phone || '';
        this.showEditModal = true;
    }
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    Control Global de Cuentas
                </span>
                <span class="text-slate-500">•</span>
                <span class="text-xs font-mono text-slate-400">Pymora Identity & Roles</span>
            </div>
            <h1 class="text-2xl font-bold text-white font-display mt-1">Gestión de Usuarios & Credenciales</h1>
            <p class="text-slate-400 text-sm">Alta de usuarios, asignación de permisos, cambio de claves y suspensión de cuentas.</p>
        </div>

        <button @click="showCreateModal = true" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>Crear Nuevo Usuario</span>
        </button>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Users -->
        <div class="glass-card p-5 rounded-2xl border border-slate-800 relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                <span>Total de Usuarios</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">{{ $totalUsers }} <span class="text-xs text-slate-400 font-normal">Cuentas</span></div>
            <div class="text-xs text-indigo-400 flex items-center gap-1 mt-2">
                <span>Registradas en la plataforma</span>
            </div>
        </div>

        <!-- Active Users -->
        <div class="glass-card p-5 rounded-2xl border border-slate-800 relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                <span>Cuentas Activas</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-400 font-display">{{ $activeUsers }} <span class="text-xs text-slate-400 font-normal">Activos</span></div>
            <div class="text-xs text-emerald-400 flex items-center gap-1 mt-2">
                <span>Acceso permitido</span>
            </div>
        </div>

        <!-- Super Admins -->
        <div class="glass-card p-5 rounded-2xl border border-slate-800 relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                <span>Super Administradores</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-purple-400 font-display">{{ $superAdminCount }} <span class="text-xs text-slate-400 font-normal">Globales</span></div>
            <div class="text-xs text-purple-400 flex items-center gap-1 mt-2">
                <span>Acceso root Plataforma</span>
            </div>
        </div>

        <!-- Tenant Users -->
        <div class="glass-card p-5 rounded-2xl border border-slate-800 relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                <span>Usuarios de Empresas</span>
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 flex items-center justify-center text-sky-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">{{ $tenantUsersCount }} <span class="text-xs text-slate-400 font-normal">Empresas</span></div>
            <div class="text-xs text-sky-400 flex items-center gap-1 mt-2">
                <span>Empresarios, Cajeros & Almacén</span>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar & Users Data Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
        <!-- Toolbar Filters -->
        <form method="GET" action="{{ route('superadmin.users') }}" class="p-4 border-b border-slate-800 flex flex-col md:flex-row items-center justify-between gap-3 bg-slate-900/60">
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, email o teléfono..." class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Tenant Filter Select -->
                <select name="tenant_id" onchange="this.form.submit()" class="w-full sm:w-56 bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    <option value="">-- Todas las Empresas --</option>
                    @foreach($tenants as $t)
                        <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                @if(request('search') || request('tenant_id'))
                    <a href="{{ route('superadmin.users') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-xl transition-colors">Limpiar Filtros</a>
                @endif
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md transition-all">Filtrar</button>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 font-semibold uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5">Usuario</th>
                        <th class="px-4 py-3.5">Rol de Sistema</th>
                        <th class="px-4 py-3.5">Empresa / Comercio</th>
                        <th class="px-4 py-3.5">Teléfono</th>
                        <th class="px-4 py-3.5">Estado</th>
                        <th class="px-4 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center font-bold text-white text-xs shadow-md">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-xs">{{ $user->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @php
                                    $roleBadge = match($user->role) {
                                        'super_admin' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                        'owner' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
                                        'branch_manager' => 'bg-sky-500/20 text-sky-300 border-sky-500/30',
                                        'cashier' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                        'warehouse_manager' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                        default => 'bg-slate-800 text-slate-300 border-slate-700',
                                    };

                                    $roleLabel = match($user->role) {
                                        'super_admin' => 'Super Admin',
                                        'owner' => 'Empresario / Dueño',
                                        'branch_manager' => 'Gerente Sucursal',
                                        'cashier' => 'Cajero POS',
                                        'warehouse_manager' => 'Encargado Almacén',
                                        'accountant' => 'Contador',
                                        default => strtoupper($user->role),
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg border text-[10px] font-semibold uppercase font-mono {{ $roleBadge }}">
                                    {{ $roleLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($user->tenant)
                                    <div class="font-medium text-slate-200">{{ $user->tenant->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $user->tenant->rif_tax_id ?? 'RIF Registrado' }}</div>
                                @else
                                    <span class="text-purple-400 font-semibold font-mono text-[11px]">Plataforma Global</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-300">
                                {{ $user->phone ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if($user->is_active ?? true)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">ACTIVO</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">SUSPENDIDO</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit Button -->
                                    <button @click="openEdit({{ json_encode($user) }})" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 text-[10px] rounded-lg border border-slate-700 transition-colors">
                                        Editar
                                    </button>

                                    <!-- Toggle Status Form -->
                                    <form action="{{ route('superadmin.users.toggle', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-[10px] font-semibold rounded-lg border {{ ($user->is_active ?? true) ? 'bg-amber-500/10 text-amber-400 border-amber-500/30 hover:bg-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30 hover:bg-emerald-500/20' }}">
                                            {{ ($user->is_active ?? true) ? 'Suspender' : 'Activar' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-500 text-sm">
                                No se encontraron usuarios con los criterios especificados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Crear Nuevo Usuario -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="showCreateModal = false" class="glass-card w-full max-w-lg rounded-2xl border border-slate-800 p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-white font-display">Crear Nuevo Usuario</h3>
                    <p class="text-xs text-slate-400">Asigna credenciales de acceso y permisos de sistema.</p>
                </div>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nombre Completo</label>
                    <input type="text" name="name" required placeholder="Ej: Pedro Pérez" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Correo Electrónico (Login)</label>
                        <input type="email" name="email" required placeholder="pedro@empresa.com" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Contraseña</label>
                        <input type="password" name="password" required placeholder="******" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Rol de Acceso</label>
                        <select name="role" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                            <option value="owner">Empresario / Admin Empresa</option>
                            <option value="super_admin">Super Admin (Global)</option>
                            <option value="branch_manager">Gerente de Sucursal</option>
                            <option value="cashier">Cajero POS</option>
                            <option value="warehouse_manager">Encargado de Almacén</option>
                            <option value="accountant">Contador</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Empresa / Comercio</label>
                        <select name="tenant_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                            <option value="">-- Ninguna (Super Admin Global) --</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Teléfono / WhatsApp (Opcional)</label>
                    <input type="text" name="phone" placeholder="+58 412 1234567" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white rounded-xl">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/20">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Editar Usuario -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="showEditModal = false" class="glass-card w-full max-w-lg rounded-2xl border border-slate-800 p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-white font-display">Editar Usuario</h3>
                    <p class="text-xs text-slate-400">Modifica datos personales, roles o restablece la clave.</p>
                </div>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'/superadmin/users/' + editUserId + '/update'" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nombre Completo</label>
                    <input type="text" name="name" x-model="editUserName" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Correo Electrónico</label>
                        <input type="email" name="email" x-model="editUserEmail" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Nueva Clave (Opcional)</label>
                        <input type="password" name="password" placeholder="Dejar en blanco para conservar" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Rol de Acceso</label>
                        <select name="role" x-model="editUserRole" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                            <option value="owner">Empresario / Admin Empresa</option>
                            <option value="super_admin">Super Admin (Global)</option>
                            <option value="branch_manager">Gerente de Sucursal</option>
                            <option value="cashier">Cajero POS</option>
                            <option value="warehouse_manager">Encargado de Almacén</option>
                            <option value="accountant">Contador</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Empresa / Comercio</label>
                        <select name="tenant_id" x-model="editUserTenantId" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                            <option value="">-- Ninguna (Super Admin Global) --</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Teléfono / WhatsApp</label>
                    <input type="text" name="phone" x-model="editUserPhone" placeholder="+58 412 1234567" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white rounded-xl">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/20">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
