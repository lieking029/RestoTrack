<?php

use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\InventoryAlertController;
use App\Http\Controllers\Web\Admin\WasteManagementController;
use App\Http\Controllers\Web\Admin\MenuController;
use App\Http\Controllers\Web\Admin\ProductController;
use App\Http\Controllers\Web\Admin\SalesReportController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
});
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => 'auth',
], function() {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('product', ProductController::class);
    Route::resource('user', UserController::class);
    Route::resource('menu', MenuController::class);
    Route::resource('sales-report', SalesReportController::class)->only(['index', 'show']);

    // Inventory Alerts
    Route::get('/inventory-alerts', [InventoryAlertController::class, 'index'])->name('inventory-alerts.index');
    Route::get('/inventory-alerts/data', [InventoryAlertController::class, 'data'])->name('inventory-alerts.data');

    // Waste Management
    Route::get('/waste-management', [WasteManagementController::class, 'index'])->name('waste-management.index');
    Route::get('/waste-management/expiry', [WasteManagementController::class, 'expiry'])->name('waste-management.expiry');
    Route::get('/waste-management/logs', [WasteManagementController::class, 'logs'])->name('waste-management.logs');
    Route::get('/waste-management/create', [WasteManagementController::class, 'create'])->name('waste-management.create');
    Route::post('/waste-management', [WasteManagementController::class, 'store'])->name('waste-management.store');
    Route::post('/waste-management/dispose/{product}', [WasteManagementController::class, 'dispose'])->name('waste-management.dispose');
    Route::post('/waste-management/bulk-dispose', [WasteManagementController::class, 'bulkDispose'])->name('waste-management.bulk-dispose');
});