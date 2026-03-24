<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\OrderStatus;
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
        $quickStats = $this->salesReportService->getQuickStats();
        $todaySales = (float) $quickStats->today_sales;
        $weeklySales = (float) $quickStats->weekly_sales;
        $monthlySales = (float) $quickStats->monthly_sales;
        $totalOrders = (int) $quickStats->total_orders;

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

        // Expired Products
        $expiredCount = Product::expired()->where('remaining_stock', '>', 0)->count();

        // Expiring Soon
        $expiringSoonCount = Product::expiringSoon()->count();
        $expiringProducts = Product::expiringSoon()
            ->soonestExpiring()
            ->limit(5)
            ->get();

        // Top Selling Items
        $topSellingItems = $this->salesReportService->getTopSellingItems(5);

        // Recent Orders (completed only, consistent with sales stats)
        $recentOrders = Order::with(['cashier', 'items'])
            ->where('status', OrderStatus::COMPLETED)
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
            'expiredCount',
            'expiringSoonCount',
            'expiringProducts',
            'topSellingItems',
            'recentOrders',
            'summary'
        ));
    }
}
