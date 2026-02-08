<?php

namespace App\Services;

use App\Enums\InventoryStatus;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InventoryAlertService
{
    /**
     * Get all inventory alerts
     */
    public function getAllAlerts(): Collection
    {
        $alerts = collect();

        // Get low stock alerts
        $lowStockAlerts = $this->getLowStockAlerts();
        $alerts = $alerts->merge($lowStockAlerts);

        // Get out of stock alerts
        $outOfStockAlerts = $this->getOutOfStockAlerts();
        $alerts = $alerts->merge($outOfStockAlerts);

        // Get expiring soon alerts
        $expiringAlerts = $this->getExpiringAlerts();
        $alerts = $alerts->merge($expiringAlerts);

        // Get expired alerts
        $expiredAlerts = $this->getExpiredAlerts();
        $alerts = $alerts->merge($expiredAlerts);

        // Sort by priority (critical first) then by date
        return $alerts->sortByDesc(function ($alert) {
            $priorityScore = match ($alert['priority']) {
                'critical' => 100,
                'high' => 75,
                'medium' => 50,
                'low' => 25,
                default => 0
            };
            return $priorityScore;
        })->values();
    }

    /**
     * Get low stock alerts
     */
    public function getLowStockAlerts(): Collection
    {
        return Product::lowStock()
            ->lowestStock()
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'type' => 'low_stock',
                    'priority' => 'medium',
                    'icon' => 'fa-exclamation-triangle',
                    'icon_class' => 'text-warning',
                    'title' => 'Low Stock Alert',
                    'message' => "{$product->name} is running low ({$product->remaining_stock} {$product->unit_of_measurement->value} remaining)",
                    'product_name' => $product->name,
                    'remaining_stock' => $product->remaining_stock,
                    'unit' => $product->unit_of_measurement->value ?? '',
                    'percentage' => $product->stock_percentage,
                    'created_at' => $product->updated_at,
                    'time_ago' => $product->updated_at->diffForHumans(),
                    'action_url' => route('admin.product.edit', $product->id),
                    'action_label' => 'Restock',
                ];
            });
    }

    /**
     * Get out of stock alerts
     */
    public function getOutOfStockAlerts(): Collection
    {
        return Product::outOfStock()
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'type' => 'out_of_stock',
                    'priority' => 'critical',
                    'icon' => 'fa-times-circle',
                    'icon_class' => 'text-danger',
                    'title' => 'Out of Stock',
                    'message' => "{$product->name} is completely out of stock!",
                    'product_name' => $product->name,
                    'remaining_stock' => 0,
                    'unit' => $product->unit_of_measurement->value ?? '',
                    'percentage' => 0,
                    'created_at' => $product->updated_at,
                    'time_ago' => $product->updated_at->diffForHumans(),
                    'action_url' => route('admin.product.edit', $product->id),
                    'action_label' => 'Restock Now',
                ];
            });
    }

    /**
     * Get expiring soon alerts (within 7 days)
     */
    public function getExpiringAlerts(int $days = 7): Collection
    {
        return Product::expiringSoon($days)
            ->soonestExpiring()
            ->get()
            ->map(function ($product) {
                $daysLeft = $product->days_until_expiration;
                $priority = $daysLeft <= 3 ? 'high' : 'medium';

                return [
                    'id' => $product->id,
                    'type' => 'expiring_soon',
                    'priority' => $priority,
                    'icon' => 'fa-clock',
                    'icon_class' => $daysLeft <= 3 ? 'text-danger' : 'text-warning',
                    'title' => 'Expiring Soon',
                    'message' => "{$product->name} expires in {$daysLeft} day(s) ({$product->expiration_date->format('M d, Y')})",
                    'product_name' => $product->name,
                    'expiration_date' => $product->expiration_date->format('M d, Y'),
                    'days_left' => $daysLeft,
                    'created_at' => now(),
                    'time_ago' => "{$daysLeft} days left",
                    'action_url' => route('admin.product.edit', $product->id),
                    'action_label' => 'View Product',
                ];
            });
    }

    /**
     * Get expired product alerts
     */
    public function getExpiredAlerts(): Collection
    {
        return Product::expired()
            ->get()
            ->map(function ($product) {
                $daysExpired = abs($product->days_until_expiration);

                return [
                    'id' => $product->id,
                    'type' => 'expired',
                    'priority' => 'critical',
                    'icon' => 'fa-skull-crossbones',
                    'icon_class' => 'text-danger',
                    'title' => 'Product Expired',
                    'message' => "{$product->name} expired {$daysExpired} day(s) ago! Remove from inventory.",
                    'product_name' => $product->name,
                    'expiration_date' => $product->expiration_date->format('M d, Y'),
                    'days_expired' => $daysExpired,
                    'created_at' => $product->expiration_date,
                    'time_ago' => "Expired {$daysExpired} days ago",
                    'action_url' => route('admin.product.edit', $product->id),
                    'action_label' => 'Remove/Dispose',
                ];
            });
    }

    /**
     * Get alert counts by type
     */
    public function getAlertCounts(): array
    {
        return [
            'total' => $this->getTotalAlertCount(),
            'critical' => $this->getCriticalAlertCount(),
            'low_stock' => Product::lowStock()->count(),
            'out_of_stock' => Product::outOfStock()->count(),
            'expiring_soon' => Product::expiringSoon()->count(),
            'expired' => Product::expired()->count(),
        ];
    }

    /**
     * Get total alert count
     */
    public function getTotalAlertCount(): int
    {
        return Product::lowStock()->count()
            + Product::outOfStock()->count()
            + Product::expiringSoon()->count()
            + Product::expired()->count();
    }

    /**
     * Get critical alert count (out of stock + expired)
     */
    public function getCriticalAlertCount(): int
    {
        return Product::outOfStock()->count()
            + Product::expired()->count();
    }

    /**
     * Get alerts for navbar (limited)
     */
    public function getNavbarAlerts(int $limit = 5): Collection
    {
        return $this->getAllAlerts()->take($limit);
    }

    /**
     * Check if there are any critical alerts
     */
    public function hasCriticalAlerts(): bool
    {
        return $this->getCriticalAlertCount() > 0;
    }
}
