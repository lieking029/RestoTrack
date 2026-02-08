<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WasteManagementService
{
    public function __construct(
        protected InventoryOpsService $inventoryOpsService
    ) {}

    /**
     * Log waste for a product
     */
    public function logWaste(
        Product $product,
        int $quantity,
        string $reason,
        ?string $userId = null,
        ?string $notes = null
    ): void {
        // Get or create inventory item for this product
        $inventoryItem = InventoryItem::firstOrCreate(
            ['product_id' => $product->id],
            ['stock_quantity' => $product->remaining_stock, 'reorder_level' => 10]
        );

        // Build detailed note
        $detailedNote = "Reason: {$reason}";
        if ($notes) {
            $detailedNote .= " | Notes: {$notes}";
        }

        // Log the waste movement
        $this->inventoryOpsService->waste($inventoryItem, $quantity, $userId, $detailedNote);

        // Also update the Product's remaining stock
        $product->decrement('remaining_stock', $quantity);
        $product->increment('stock_out', $quantity);

        // Recalculate product status
        $product->status = $this->calculateProductStatus($product);
        $product->save();
    }

    /**
     * Dispose expired products
     */
    public function disposeExpired(Product $product, ?string $userId = null, ?string $notes = null): void
    {
        $quantity = $product->remaining_stock;

        if ($quantity <= 0) {
            return;
        }

        $this->logWaste(
            $product,
            $quantity,
            'EXPIRED',
            $userId,
            $notes ?? "Product expired on {$product->expiration_date->format('M d, Y')}"
        );
    }

    /**
     * Get waste statistics
     */
    public function getWasteStats(?string $startDate = null, ?string $endDate = null): array
    {
        $query = InventoryMovement::where('reason', 'WASTE');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $totalWaste = $query->sum('quantity');
        $wasteCount = $query->count();

        // Get waste by reason from notes
        $wasteByReason = InventoryMovement::where('reason', 'WASTE')
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->select(DB::raw("
                CASE
                    WHEN note LIKE '%EXPIRED%' THEN 'Expired'
                    WHEN note LIKE '%DAMAGED%' THEN 'Damaged'
                    WHEN note LIKE '%SPOILED%' THEN 'Spoiled'
                    WHEN note LIKE '%CONTAMINATED%' THEN 'Contaminated'
                    ELSE 'Other'
                END as waste_reason
            "), DB::raw('SUM(quantity) as total'))
            ->groupBy('waste_reason')
            ->pluck('total', 'waste_reason')
            ->toArray();

        return [
            'total_waste' => $totalWaste,
            'waste_count' => $wasteCount,
            'by_reason' => $wasteByReason,
            'today' => $this->getTodayWaste(),
            'this_week' => $this->getWeeklyWaste(),
            'this_month' => $this->getMonthlyWaste(),
        ];
    }

    /**
     * Get today's waste
     */
    public function getTodayWaste(): int
    {
        return InventoryMovement::where('reason', 'WASTE')
            ->whereDate('created_at', Carbon::today())
            ->sum('quantity');
    }

    /**
     * Get this week's waste
     */
    public function getWeeklyWaste(): int
    {
        return InventoryMovement::where('reason', 'WASTE')
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('quantity');
    }

    /**
     * Get this month's waste
     */
    public function getMonthlyWaste(): int
    {
        return InventoryMovement::where('reason', 'WASTE')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('quantity');
    }

    /**
     * Get recent waste logs
     */
    public function getRecentWasteLogs(int $limit = 10): Collection
    {
        return InventoryMovement::with(['inventoryItem.product', 'performer'])
            ->where('reason', 'WASTE')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get all waste logs with filters
     */
    public function getWasteLogs(?string $startDate = null, ?string $endDate = null): Collection
    {
        return InventoryMovement::with(['inventoryItem.product', 'performer'])
            ->where('reason', 'WASTE')
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->latest()
            ->get();
    }

    /**
     * Get expiring products
     */
    public function getExpiringProducts(int $days = 7): Collection
    {
        return Product::where('remaining_stock', '>', 0)
            ->whereDate('expiration_date', '<=', now()->addDays($days))
            ->whereDate('expiration_date', '>=', now())
            ->orderBy('expiration_date')
            ->get();
    }

    /**
     * Get expired products
     */
    public function getExpiredProducts(): Collection
    {
        return Product::where('remaining_stock', '>', 0)
            ->whereDate('expiration_date', '<', now())
            ->orderBy('expiration_date')
            ->get();
    }

    /**
     * Get expiry summary
     */
    public function getExpirySummary(): array
    {
        return [
            'expired' => Product::expired()->where('remaining_stock', '>', 0)->count(),
            'expiring_today' => Product::whereDate('expiration_date', Carbon::today())
                ->where('remaining_stock', '>', 0)->count(),
            'expiring_3_days' => Product::expiringSoon(3)->count(),
            'expiring_7_days' => Product::expiringSoon(7)->count(),
            'expiring_30_days' => Product::expiringSoon(30)->count(),
        ];
    }

    /**
     * Get daily waste chart data
     */
    public function getDailyWasteChart(int $days = 7): array
    {
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');

            $waste = InventoryMovement::where('reason', 'WASTE')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('quantity');

            $data[] = (int) $waste;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get waste by category/product
     */
    public function getWasteByProduct(int $limit = 10): Collection
    {
        return InventoryMovement::with('inventoryItem.product')
            ->where('reason', 'WASTE')
            ->whereMonth('created_at', Carbon::now()->month)
            ->select('inventory_item_id', DB::raw('SUM(quantity) as total_waste'))
            ->groupBy('inventory_item_id')
            ->orderByDesc('total_waste')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate product status based on remaining stock
     */
    protected function calculateProductStatus(Product $product): \App\Enums\InventoryStatus
    {
        $lowStockThreshold = (int) ($product->initial_stock * 0.2);

        if ($product->remaining_stock == 0) {
            return \App\Enums\InventoryStatus::NoStock();
        } elseif ($product->remaining_stock <= $lowStockThreshold) {
            return \App\Enums\InventoryStatus::LowOnStock();
        } else {
            return \App\Enums\InventoryStatus::OnStock();
        }
    }

    /**
     * Get waste reasons for dropdown
     */
    public static function getWasteReasons(): array
    {
        return [
            'EXPIRED' => 'Expired',
            'SPOILED' => 'Spoiled',
            'DAMAGED' => 'Damaged',
            'CONTAMINATED' => 'Contaminated',
            'QUALITY_ISSUE' => 'Quality Issue',
            'OVERSTOCK' => 'Overstock/Excess',
            'OTHER' => 'Other',
        ];
    }
}
