<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SalesReportService;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function __construct(public SalesReportService $salesReportService)
    {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $quickStats = $this->salesReportService->getQuickStats();

        return response()->json([
            'summary' => $this->salesReportService->getSalesSummary($startDate, $endDate),
            'today_sales' => (float) $quickStats->today_sales,
            'weekly_sales' => (float) $quickStats->weekly_sales,
            'monthly_sales' => (float) $quickStats->monthly_sales,
            'total_orders' => (int) $quickStats->total_orders,
            'total_discounts' => (float) $quickStats->total_discounts,
            'top_selling_items' => $this->salesReportService->getTopSellingItems(5, $startDate, $endDate),
            'least_selling_items' => $this->salesReportService->getLeastSellingItems(5, $startDate, $endDate),
            'daily_chart' => $this->salesReportService->getDailySalesChart(),
        ]);
    }
}
