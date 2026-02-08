<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\V1\EmployeeProfileController;
use App\Http\Controllers\Api\V1\CashierOrderController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\KitchenController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\SalesReportController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::post('/inventory/{inventoryItem}/receive', [InventoryController::class, 'receive']);
        Route::post('/inventory/{inventoryItem}/waste', [InventoryController::class, 'waste']);
        Route::post('/inventory/{inventoryItem}/adjust', [InventoryController::class, 'adjust']);

        Route::post('/logout', [LoginController::class, 'logout']);
        Route::get('/me', [LoginController::class, 'me']);


        Route::middleware('user.type:Employee')->group(function () {

            Route::get('/profile', EmployeeProfileController::class);

            Route::middleware('role:server|barista')->group(function () {
                Route::get('/menus', [MenuController::class, 'index']);
                Route::get('/order/my', [OrderController::class, 'my']);
                Route::post('/order', [OrderController::class, 'store']);
                Route::put('/order/{orderId}', [OrderController::class, 'complete']);
                Route::put('/order/{orderId}/cancel', [OrderController::class, 'cancel']);
            });

            Route::middleware('role:cashier')->group(function () {
                Route::get('/cashier/orders', [CashierOrderController::class, 'index']);
                Route::post('/payments', PaymentController::class);
                Route::get('/transactions', TransactionController::class);
            });

            Route::middleware('role:cook|chef')->group(function () {
                Route::get('/kitchen/orders', [KitchenController::class, 'index']);
                Route::patch('/kitchen/orders/{order}', [KitchenController::class, 'updateStatus']);
            });

            Route::middleware('role_or_permission:manager|view_sales_reports')->group(function () {
                Route::get('/reports/sales', SalesReportController::class);
            });

        });
    });
});