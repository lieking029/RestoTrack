<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
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

        return [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'total_tax' => $query->sum('tax'),
            'average_order_value' => $query->avg('total') ?? 0,
        ];
    }

    /**
     * Get today's sales
     */
    public function getTodaySales(): float
    {
        return Order::where('status', OrderStatus::COMPLETED)
            ->whereDate('created_at', Carbon::today())
            ->sum('total');
    }

    /**
     * Get this week's sales
     */
    public function getWeeklySales(): float
    {
        return Order::where('status', OrderStatus::COMPLETED)
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('total');
    }

    /**
     * Get this month's sales
     */
    public function getMonthlySales(): float
    {
        return Order::where('status', OrderStatus::COMPLETED)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total');
    }

    /**
     * Get total completed orders count
     */
    public function getTotalOrders(): int
    {
        return Order::where('status', OrderStatus::COMPLETED)->count();
    }

    /**
     * Get top selling items
     */
    public function getTopSellingItems(int $limit = 5, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = OrderItem::select(
            'name',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total) as total_revenue')
        )
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('status', OrderStatus::COMPLETED);

                if ($startDate) {
                    $q->whereDate('created_at', '>=', $startDate);
                }

                if ($endDate) {
                    $q->whereDate('created_at', '<=', $endDate);
                }
            })
            ->groupBy('name')
            ->orderByDesc('total_quantity')
            ->limit($limit);

        return $query->get()->toArray();
    }

    /**
     * Get least selling items
     */
    public function getLeastSellingItems(int $limit = 5, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = OrderItem::select(
            'name',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total) as total_revenue')
        )
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('status', OrderStatus::COMPLETED);

                if ($startDate) {
                    $q->whereDate('created_at', '>=', $startDate);
                }

                if ($endDate) {
                    $q->whereDate('created_at', '<=', $endDate);
                }
            })
            ->groupBy('name')
            ->orderBy('total_quantity', 'asc')
            ->limit($limit);

        return $query->get()->toArray();
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
                DB::raw('SUM(total) as total'),
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
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(total) as total'),
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
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisWeekEnd = Carbon::now()->endOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekSales = Order::where('status', OrderStatus::COMPLETED)
            ->whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])
            ->select(
                DB::raw('DAYOFWEEK(created_at) as day'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $lastWeekSales = Order::where('status', OrderStatus::COMPLETED)
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->select(
                DB::raw('DAYOFWEEK(created_at) as day'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $thisWeekData = [];
        $lastWeekData = [];

        for ($i = 1; $i <= 7; $i++) {
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
