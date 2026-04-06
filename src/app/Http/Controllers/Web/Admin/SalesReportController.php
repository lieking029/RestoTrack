<?php

namespace App\Http\Controllers\Web\Admin;

use App\DataTables\SalesReportDataTable;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SalesReportService;

class SalesReportController extends Controller
{
    public function __construct(
        protected SalesReportService $salesReportService
    ) {}

    /**
     * Display the sales report dashboard.
     */
    public function index(SalesReportDataTable $dataTable)
    {
        $quickStats = $this->salesReportService->getQuickStats();
        $todaySales = (float) $quickStats->today_sales;
        $weeklySales = (float) $quickStats->weekly_sales;
        $monthlySales = (float) $quickStats->monthly_sales;
        $totalOrders = (int) $quickStats->total_orders;
        $totalDiscounts = (float) $quickStats->total_discounts;

        $topSellingItems = $this->salesReportService->getTopSellingItems(5);
        $leastSellingItems = $this->salesReportService->getLeastSellingItems(5);
        $dailySalesChart = $this->salesReportService->getDailySalesChart(7);
        $weekComparison = $this->salesReportService->getWeekComparison();

        return $dataTable->render('admin.sales-report.index', compact(
            'todaySales',
            'weeklySales',
            'monthlySales',
            'totalOrders',
            'totalDiscounts',
            'topSellingItems',
            'leastSellingItems',
            'dailySalesChart',
            'weekComparison'
        ));
    }

    /**
     * Display the specified order details.
     */
    public function show(string $id)
    {
        $order = Order::with(['items', 'cashier', 'creator', 'payments'])->findOrFail($id);

        return view('admin.sales-report.show', compact('order'));
    }
}
