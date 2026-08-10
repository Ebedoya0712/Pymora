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
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterTenantController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterTenantController::class, 'register'])->name('register.post');

// Main SaaS Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Super Admin SaaS Owner Routes
Route::get('/superadmin', [SuperAdminController::class, 'index'])->name('superadmin.index');
Route::post('/superadmin/tenants', [SuperAdminController::class, 'storeTenant'])->name('superadmin.tenants.store');

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
