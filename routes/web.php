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
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\BatchController;
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
Route::get('/superadmin/empresas', [SuperAdminController::class, 'empresas'])->name('superadmin.empresas');
Route::get('/superadmin/finanzas', [SuperAdminController::class, 'finanzas'])->name('superadmin.finanzas');
Route::get('/superadmin/comprobantes', [SuperAdminController::class, 'comprobantes'])->name('superadmin.comprobantes');
Route::get('/superadmin/planes', [SuperAdminController::class, 'planes'])->name('superadmin.planes');
Route::get('/superadmin/configuracion', [SuperAdminController::class, 'configuracion'])->name('superadmin.configuracion');
Route::post('/superadmin/finanzas/payments', [SuperAdminController::class, 'storePayment'])->name('superadmin.payments.store');

// Super Admin SaaS User Management Routes
Route::get('/superadmin/users', [SuperAdminController::class, 'users'])->name('superadmin.users');
Route::post('/superadmin/users', [SuperAdminController::class, 'storeUser'])->name('superadmin.users.store');
Route::post('/superadmin/users/{id}/update', [SuperAdminController::class, 'updateUser'])->name('superadmin.users.update');
Route::post('/superadmin/users/{id}/toggle-status', [SuperAdminController::class, 'toggleUserStatus'])->name('superadmin.users.toggle');
Route::post('/superadmin/users/{id}/delete', [SuperAdminController::class, 'deleteUser'])->name('superadmin.users.delete');

// Super Admin SaaS Tenant Actions
Route::post('/superadmin/tenants', [SuperAdminController::class, 'storeTenant'])->name('superadmin.tenants.store');
Route::post('/superadmin/tenants/{id}/update', [SuperAdminController::class, 'updateTenant'])->name('superadmin.tenants.update');
Route::post('/superadmin/tenants/{id}/delete', [SuperAdminController::class, 'deleteTenant'])->name('superadmin.tenants.delete');
Route::post('/superadmin/tenants/{id}/toggle-status', [SuperAdminController::class, 'toggleTenantStatus'])->name('superadmin.tenants.toggle');
Route::post('/superadmin/tenants/{id}/renew', [SuperAdminController::class, 'renewTenant'])->name('superadmin.tenants.renew');
Route::get('/superadmin/impersonate/{id}', [SuperAdminController::class, 'impersonate'])->name('superadmin.impersonate');
Route::post('/superadmin/tenants/{id}/impersonate', [SuperAdminController::class, 'impersonate'])->name('superadmin.tenants.impersonate');
Route::get('/superadmin/stop-impersonating', [SuperAdminController::class, 'stopImpersonating'])->name('superadmin.stop-impersonating');
Route::post('/superadmin/stop-impersonation', [SuperAdminController::class, 'stopImpersonating'])->name('superadmin.stop-impersonation');

Route::post('/superadmin/settings', [SuperAdminController::class, 'updateSettings'])->name('superadmin.settings.update');
Route::post('/superadmin/plans/update', [SuperAdminController::class, 'updatePlan'])->name('superadmin.plans.update');
Route::post('/superadmin/broadcast', [SuperAdminController::class, 'storeBroadcast'])->name('superadmin.broadcast.store');
Route::post('/superadmin/sync-dolarapi', [SuperAdminController::class, 'syncDolarApi'])->name('superadmin.sync-dolarapi');

// Core Business Modules
Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
Route::post('/pos/customers', [PosController::class, 'storeCustomer'])->name('pos.customers.store');

Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');
Route::post('/scanner/lookup', [ScannerController::class, 'lookup'])->name('scanner.lookup');
Route::post('/scanner/update-stock', [ScannerController::class, 'updateStock'])->name('scanner.updateStock');
Route::post('/scanner/quick-store', [ScannerController::class, 'quickStore'])->name('scanner.quickStore');

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
Route::post('/inventory/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.updateStock');
Route::post('/inventory/{id}/delete', [InventoryController::class, 'destroy'])->name('inventory.destroy');

Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
Route::post('/batches/{id}/delete', [BatchController::class, 'destroy'])->name('batches.destroy');

Route::get('/cash-bank', [CashBankController::class, 'index'])->name('cashbank.index');
Route::get('/cxc-cxp', [CxcCxpController::class, 'index'])->name('cxc.index');

// High Value Advanced Modules
Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
