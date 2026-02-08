<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\InventoryAlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryAlertController extends Controller
{
    public function __construct(
        protected InventoryAlertService $alertService
    ) {}

    /**
     * Display the inventory alerts page.
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $alerts = match ($filter) {
            'critical' => $this->alertService->getOutOfStockAlerts()
                ->merge($this->alertService->getExpiredAlerts()),
            'low_stock' => $this->alertService->getLowStockAlerts(),
            'out_of_stock' => $this->alertService->getOutOfStockAlerts(),
            'expiring' => $this->alertService->getExpiringAlerts(),
            'expired' => $this->alertService->getExpiredAlerts(),
            default => $this->alertService->getAllAlerts(),
        };

        $alertCounts = $this->alertService->getAlertCounts();

        return view('admin.inventory-alerts.index', compact('alerts', 'alertCounts', 'filter'));
    }

    /**
     * Get alerts data for AJAX refresh.
     */
    public function data(): JsonResponse
    {
        return response()->json([
            'alerts' => $this->alertService->getNavbarAlerts(5),
            'counts' => $this->alertService->getAlertCounts(),
            'hasCritical' => $this->alertService->hasCriticalAlerts(),
        ]);
    }
}
