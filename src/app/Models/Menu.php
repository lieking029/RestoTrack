<?php

namespace App\Models;

use App\Enums\MenuStatus;
use App\Enums\MenuType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dish_picture',
        'name',
        'description',
        'price',
        'category',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'category' => MenuType::class,
            'status' => MenuStatus::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The products (ingredients) that belong to the menu item.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'menu_product')
                    ->withPivot('quantity_needed');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get the full image path.
     */
    public function getDishPictureUrlAttribute(): string
    {
        return asset('storage/' . $this->dish_picture);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status->value) {
            MenuStatus::Available => 'success',
            MenuStatus::Unavailable => 'danger',
            default => 'secondary'
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if menu item is available.
     */
    public function isAvailable(): bool
    {
        return $this->status->value === MenuStatus::Available;
    }

    /**
     * Check if menu item has sufficient ingredients in stock.
     */
    public function hasIngredientsInStock(): bool
    {
        foreach ($this->products as $product) {
            $quantityNeeded = $product->pivot->quantity_needed;
            
            // Check if there's enough stock
            if ($product->remaining_stock < $quantityNeeded) {
                return false;
            }
            
            // Also check if product is out of stock
            if ($product->isOutOfStock()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get low stock ingredients for this menu item.
     */
    public function getLowStockIngredients()
    {
        return $this->products()->where(function($query) {
            $query->where('status', \App\Enums\InventoryStatus::LowOnStock)
                  ->orWhere('status', \App\Enums\InventoryStatus::NoStock);
        })->get();
    }

    /**
     * Get missing ingredients (ingredients that don't have enough stock).
     */
    public function getMissingIngredients()
    {
        $missing = [];
        
        foreach ($this->products as $product) {
            $quantityNeeded = $product->pivot->quantity_needed;
            
            if ($product->remaining_stock < $quantityNeeded) {
                $missing[] = [
                    'product' => $product,
                    'needed' => $quantityNeeded,
                    'available' => $product->remaining_stock,
                    'shortage' => $quantityNeeded - $product->remaining_stock,
                ];
            }
        }
        
        return collect($missing);
    }

    /**
     * Auto-update availability based on ingredient stock.
     */
    public function updateAvailability(): void
    {
        if ($this->hasIngredientsInStock()) {
            $this->status = MenuStatus::Available();
        } else {
            $this->status = MenuStatus::Unavailable();
        }
        $this->save();
    }

    /**
     * Deduct ingredients when menu item is ordered.
     */
    public function deductIngredients(int $quantity = 1): bool
    {
        if (!$this->hasIngredientsInStock()) {
            return false;
        }

        foreach ($this->products as $product) {
            $quantityNeeded = $product->pivot->quantity_needed * $quantity;
            $newStock = $product->remaining_stock - $quantityNeeded;
            
            if ($newStock < 0) {
                return false; // Not enough stock
            }
            
            $product->updateStock($newStock);
        }

        // Update menu availability after deducting
        $this->updateAvailability();
        
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include available menu items.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', MenuStatus::Available);
    }

    /**
     * Scope a query to only include unavailable menu items.
     */
    public function scopeUnavailable($query)
    {
        return $query->where('status', MenuStatus::Unavailable);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, int $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to search menu items.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
}