<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\V1\EmployeeProfileController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\KitchenController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\OnlinePaymentController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PaymongoWebhookController;
use App\Http\Controllers\Api\V1\SalesReportController;
use App\Http\Controllers\Api\V1\CashierOrderController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);


// PayMongo Webhook (no auth — called by PayMongo)
Route::post('/paymongo/webhook', [PaymongoWebhookController::class, 'handle']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/me', [LoginController::class, 'me']);

    Route::middleware('role_or_permission:manager|manage_inventory')->group(function () {
        Route::post('/inventory/{inventoryItem}/receive', [InventoryController::class, 'receive']);
        Route::post('/inventory/{inventoryItem}/waste', [InventoryController::class, 'waste']);
        Route::post('/inventory/{inventoryItem}/adjust', [InventoryController::class, 'adjust']);
    });

    Route::middleware('role_or_permission:manager|view_sales_reports')->group(function () {
        Route::get('/reports/sales', SalesReportController::class);
    });

    Route::middleware('user.type:Employee')->group(function () {

        Route::get('/profile', EmployeeProfileController::class);

        Route::middleware('role:server|barista')->prefix('server')->group(function () {
            Route::get('/menus', [MenuController::class, 'index']);
            Route::get('/order/my', [OrderController::class, 'my']);
            Route::post('/order', [OrderController::class, 'store']);
            Route::put('/order/{order}', [OrderController::class, 'complete']);
            Route::put('/order/{order}/cancel', [OrderController::class, 'cancel']);
        });

        Route::middleware('role:cashier')->prefix('cashier')->group(function () {
            Route::get('/orders', [CashierOrderController::class, 'index']);
            Route::post('/payments', PaymentController::class);
            Route::post('/payments/online', [OnlinePaymentController::class, 'createCheckoutSession']);
            Route::get('/orders/{order}/payment-status', [OnlinePaymentController::class, 'checkStatus']);
            Route::get('/transactions', TransactionController::class);
        });

        Route::middleware('role:cook|chef')->prefix('kitchen')->group(function () {
            Route::get('/orders', [KitchenController::class, 'index']);
            Route::patch('/orders/{order}', [KitchenController::class, 'updateStatus']);
        });

    });
});
