<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    /**
     * Get sales summary for a given period
     */
    public function getSalesSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Order::where('status', OrderStatus::COMPLETED);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $result = $query->selectRaw('
            COUNT(*) as total_orders,
            COALESCE(SUM(total), 0) as total_revenue,
            COALESCE(SUM(tax), 0) as total_tax,
            COALESCE(AVG(total), 0) as average_order_value,
            COALESCE(SUM(COALESCE(discount_amount, 0)), 0) as total_discounts,
            COALESCE(SUM(COALESCE(discount_total, total)), 0) as total_amount_paid
        ')->first();

        return [
            'total_orders' => (int) $result->total_orders,
            'total_revenue' => (float) $result->total_revenue,
            'total_tax' => (float) $result->total_tax,
            'average_order_value' => (float) $result->average_order_value,
            'total_discounts' => (float) $result->total_discounts,
            'total_amount_paid' => (float) $result->total_amount_paid,
        ];
    }

    /**
     * Get today's sales, weekly sales, monthly sales, and total orders in a single query.
     */
    public function getQuickStats(): object
    {
        $today = Carbon::today()->toDateString();
        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY)->toDateTimeString();
        $weekEnd = Carbon::now()->endOfWeek(Carbon::SATURDAY)->toDateTimeString();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        return Order::where('status', OrderStatus::COMPLETED)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN DATE(created_at) = ? THEN COALESCE(discount_total, total) ELSE 0 END), 0) as today_sales,
                COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? THEN COALESCE(discount_total, total) ELSE 0 END), 0) as weekly_sales,
                COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = ? AND EXTRACT(YEAR FROM created_at) = ? THEN COALESCE(discount_total, total) ELSE 0 END), 0) as monthly_sales,
                COUNT(*) as total_orders,
                COALESCE(SUM(COALESCE(discount_amount, 0)), 0) as total_discounts
            ", [$today, $weekStart, $weekEnd, $month, $year])
            ->first();
    }

    /**
     * Get top selling items
     */
    public function getTopSellingItems(int $limit = 5, ?string $startDate = null, ?string $endDate = null): array
    {
        return $this->getSellingItems($limit, 'desc', $startDate, $endDate);
    }

    /**
     * Get least selling items
     */
    public function getLeastSellingItems(int $limit = 5, ?string $startDate = null, ?string $endDate = null): array
    {
        return $this->getSellingItems($limit, 'asc', $startDate, $endDate);
    }

    private function getSellingItems(int $limit, string $direction, ?string $startDate, ?string $endDate): array
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', OrderStatus::COMPLETED)
            ->select(
                'order_items.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total) as total_revenue')
            )
            ->groupBy('order_items.name')
            ->orderBy('total_quantity', $direction)
            ->limit($limit);

        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        return $query->get()->map(fn ($item) => (array) $item)->toArray();
    }

    /**
     * Get daily sales for the past N days
     */
    public function getDailySalesChart(int $days = 7): array
    {
        $sales = Order::where('status', OrderStatus::COMPLETED)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(COALESCE(discount_total, total)) as total'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];
        $orderCounts = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('M d');

            $dayData = $sales->firstWhere('date', $date);
            $data[] = $dayData ? (float) $dayData->total : 0;
            $orderCounts[] = $dayData ? (int) $dayData->orders : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'orders' => $orderCounts,
        ];
    }

    /**
     * Get sales by hour for today
     */
    public function getHourlySales(): array
    {
        $sales = Order::where('status', OrderStatus::COMPLETED)
            ->whereDate('created_at', Carbon::today())
            ->select(
                DB::raw('EXTRACT(HOUR FROM created_at) as hour'),
                DB::raw('SUM(COALESCE(discount_total, total)) as total'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 0; $i < 24; $i++) {
            $labels[] = sprintf('%02d:00', $i);
            $hourData = $sales->firstWhere('hour', $i);
            $data[] = $hourData ? (float) $hourData->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Compare this week vs last week sales
     */
    public function getWeekComparison(): array
    {
        $thisWeekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $thisWeekEnd = Carbon::now()->endOfWeek(Carbon::SATURDAY);
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::SUNDAY);
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek(Carbon::SATURDAY);

        $thisWeekSales = Order::where('status', OrderStatus::COMPLETED)
            ->whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])
            ->select(
                DB::raw('EXTRACT(DOW FROM created_at) as day'),
                DB::raw('SUM(COALESCE(discount_total, total)) as total')
            )
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $lastWeekSales = Order::where('status', OrderStatus::COMPLETED)
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->select(
                DB::raw('EXTRACT(DOW FROM created_at) as day'),
                DB::raw('SUM(COALESCE(discount_total, total)) as total')
            )
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $thisWeekData = [];
        $lastWeekData = [];

        for ($i = 0; $i <= 6; $i++) {
            $thisWeekData[] = isset($thisWeekSales[$i]) ? (float) $thisWeekSales[$i] : 0;
            $lastWeekData[] = isset($lastWeekSales[$i]) ? (float) $lastWeekSales[$i] : 0;
        }

        return [
            'labels' => $days,
            'thisWeek' => $thisWeekData,
            'lastWeek' => $lastWeekData,
        ];
    }

    /**
     * Get sales by payment method
     */
    public function getSalesByPaymentMethod(?string $startDate = null, ?string $endDate = null): array
    {
        $query = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('orders.status', OrderStatus::COMPLETED)
            ->select(
                'payments.method',
                DB::raw('SUM(payments.amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('payments.method');

        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        return $query->get()->toArray();
    }
}
