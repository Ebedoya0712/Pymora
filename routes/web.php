<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\CxcCxpController;
use App\Http\Controllers\CashBankController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterTenantController;

// Auth Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterTenantController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterTenantController::class, 'register'])->name('register.post');

// Main SaaS Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Super Admin SaaS Owner Routes
Route::get('/superadmin', [SuperAdminController::class, 'index'])->name('superadmin.index');
Route::get('/superadmin/finanzas', [SuperAdminController::class, 'finanzas'])->name('superadmin.finanzas');
Route::post('/superadmin/finanzas/payments', [SuperAdminController::class, 'storePayment'])->name('superadmin.payments.store');

// Super Admin SaaS User Management Routes
Route::get('/superadmin/users', [SuperAdminController::class, 'users'])->name('superadmin.users');
Route::post('/superadmin/users', [SuperAdminController::class, 'storeUser'])->name('superadmin.users.store');
Route::post('/superadmin/users/{id}/update', [SuperAdminController::class, 'updateUser'])->name('superadmin.users.update');
Route::post('/superadmin/users/{id}/toggle-status', [SuperAdminController::class, 'toggleUserStatus'])->name('superadmin.users.toggle');

// Super Admin SaaS Tenant & System Routes
Route::post('/superadmin/tenants', [SuperAdminController::class, 'storeTenant'])->name('superadmin.tenants.store');
Route::post('/superadmin/tenants/{id}/toggle-status', [SuperAdminController::class, 'toggleTenantStatus'])->name('superadmin.tenants.toggle');
Route::get('/superadmin/impersonate/{id}', [SuperAdminController::class, 'impersonate'])->name('superadmin.impersonate');
Route::post('/superadmin/tenants/{id}/impersonate', [SuperAdminController::class, 'impersonate'])->name('superadmin.tenants.impersonate');
Route::get('/superadmin/stop-impersonating', [SuperAdminController::class, 'stopImpersonating'])->name('superadmin.stop-impersonating');
Route::post('/superadmin/stop-impersonation', [SuperAdminController::class, 'stopImpersonating'])->name('superadmin.stop-impersonation');

Route::post('/superadmin/settings', [SuperAdminController::class, 'updateSettings'])->name('superadmin.settings.update');
Route::post('/superadmin/broadcast', [SuperAdminController::class, 'storeBroadcast'])->name('superadmin.broadcast.store');
Route::post('/superadmin/sync-dolarapi', [SuperAdminController::class, 'syncDolarApi'])->name('superadmin.sync-dolarapi');

// Core Business Modules
Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos', [PosController::class, 'store'])->name('pos.store');

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');

Route::get('/cash-bank', [CashBankController::class, 'index'])->name('cashbank.index');
Route::get('/cxc-cxp', [CxcCxpController::class, 'index'])->name('cxc.index');

// High Value Advanced Modules
Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
