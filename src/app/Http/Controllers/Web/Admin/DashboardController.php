<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\SalesReportService;

class DashboardController extends Controller
{
    public function __construct(
        protected SalesReportService $salesReportService
    ) {}

    /**
     * Display the admin dashboard with analytics.
     */
    public function index()
    {
        // Sales Metrics
        $todaySales = $this->salesReportService->getTodaySales();
        $weeklySales = $this->salesReportService->getWeeklySales();
        $monthlySales = $this->salesReportService->getMonthlySales();
        $totalOrders = $this->salesReportService->getTotalOrders();

        // Chart Data
        $weekComparison = $this->salesReportService->getWeekComparison();
        $dailySalesChart = $this->salesReportService->getDailySalesChart(7);

        // Inventory Alerts
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();
        $stockAlerts = Product::needsAttention()
            ->lowestStock()
            ->limit(5)
            ->get();

        // Expiring Soon
        $expiringSoonCount = Product::expiringSoon()->count();
        $expiringProducts = Product::expiringSoon()
            ->soonestExpiring()
            ->limit(5)
            ->get();

        // Top Selling Items
        $topSellingItems = $this->salesReportService->getTopSellingItems(5);

        // Recent Orders
        $recentOrders = Order::with(['cashier', 'items'])
            ->latest()
            ->limit(5)
            ->get();

        // Summary for quick stats
        $summary = $this->salesReportService->getSalesSummary();

        return view('admin.dashboard.index', compact(
            'todaySales',
            'weeklySales',
            'monthlySales',
            'totalOrders',
            'weekComparison',
            'dailySalesChart',
            'lowStockCount',
            'outOfStockCount',
            'stockAlerts',
            'expiringSoonCount',
            'expiringProducts',
            'topSellingItems',
            'recentOrders',
            'summary'
        ));
    }
}
