<?php

namespace App\Providers;

use App\Services\InventoryAlertService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share inventory alerts with navbar
        View::composer('layouts.components.nav', function ($view) {
            if (auth()->check()) {
                $alertService = app(InventoryAlertService::class);

                $view->with([
                    'inventoryAlerts' => $alertService->getNavbarAlerts(5),
                    'alertCounts' => $alertService->getAlertCounts(),
                    'hasCriticalAlerts' => $alertService->hasCriticalAlerts(),
                ]);
            }
        });

        // Share low stock count with sidebar
        View::composer('layouts.components.sidebar', function ($view) {
            if (auth()->check()) {
                $alertService = app(InventoryAlertService::class);
                $view->with('lowStockCount', $alertService->getAlertCounts()['low_stock'] + $alertService->getAlertCounts()['out_of_stock']);
            }
        });
    }
}
