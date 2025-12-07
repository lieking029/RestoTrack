<?php

namespace App\Models;

use App\Enums\UnitOfMeasurement;
use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'initial_stock',
        'unit_of_measurement',
        'status',
        'stock_out',
        'remaining_stock',
        'expiration_date',
    ];

    protected $casts = [
        'unit_of_measurement' => UnitOfMeasurement::class,
        'status' => InventoryStatus::class,
        'expiration_date' => 'date',
        'initial_stock' => 'integer',
        'stock_out' => 'integer',
        'remaining_stock' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get the formatted stock display with unit.
     */
    public function getFormattedStockAttribute(): string
    {
        return $this->remaining_stock . ' ' . $this->unit_of_measurement;
    }

    /**
     * Get the stock percentage remaining.
     */
    public function getStockPercentageAttribute(): float
    {
        if ($this->initial_stock == 0) {
            return 0;
        }

        return round(($this->remaining_stock / $this->initial_stock) * 100, 2);
    }

    /**
     * Get the status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            InventoryStatus::OnStock => 'badge-success',
            InventoryStatus::LowOnStock => 'badge-warning',
            InventoryStatus::NoStock => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get days until expiration.
     */
    public function getDaysUntilExpirationAttribute(): int
    {
        return now()->diffInDays($this->expiration_date, false);
    }

    /**
     * Check if product is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date->isPast();
    }

    /**
     * Check if product is expiring soon (within 7 days).
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->days_until_expiration <= 7 && $this->days_until_expiration > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if product is low on stock.
     */
    public function isLowOnStock(): bool
    {
        return $this->status === InventoryStatus::LowOnStock;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->status === InventoryStatus::NoStock;
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->status === InventoryStatus::OnStock;
    }

    /**
     * Update the stock and recalculate status.
     */
    public function updateStock(int $quantity, bool $isAddition = false): void
    {
        if ($isAddition) {
            $this->remaining_stock += $quantity;
        } else {
            $this->stock_out += $quantity;
            $this->remaining_stock = max(0, $this->initial_stock - $this->stock_out);
        }

        // Recalculate status
        $this->status = $this->calculateStatus();
        $this->save();
    }

    /**
     * Calculate the status based on remaining stock.
     */
    protected function calculateStatus(): InventoryStatus
    {
        $lowStockThreshold = (int) ($this->initial_stock * 0.2);

        if ($this->remaining_stock == 0) {
            return InventoryStatus::NoStock();
        } elseif ($this->remaining_stock <= $lowStockThreshold) {
            return InventoryStatus::LowOnStock();
        } else {
            return InventoryStatus::OnStock();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->where('status', InventoryStatus::LowOnStock);
    }

    /**
     * Scope a query to only include out of stock products.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('status', InventoryStatus::NoStock);
    }

    /**
     * Scope a query to only include in stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('status', InventoryStatus::OnStock);
    }

    /**
     * Scope to get products that need attention (low or out of stock).
     */
    public function scopeNeedsAttention($query)
    {
        return $query->whereIn('status', [
            InventoryStatus::LowOnStock,
            InventoryStatus::NoStock
        ]);
    }

    /**
     * Get products expiring soon (within 7 days).
     */
    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->whereDate('expiration_date', '<=', now()->addDays($days))
            ->whereDate('expiration_date', '>=', now());
    }

    /**
     * Get expired products.
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('expiration_date', '<', now());
    }

    /**
     * Order by stock level (lowest first).
     */
    public function scopeLowestStock($query)
    {
        return $query->orderBy('remaining_stock', 'asc');
    }

    /**
     * Order by expiration date (soonest first).
     */
    public function scopeSoonestExpiring($query)
    {
        return $query->orderBy('expiration_date', 'asc');
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_product')
            ->withPivot('quantity_needed');
    }
}