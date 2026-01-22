<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryMovementFactory> */
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'inventory_item_id',
        'order_id',
        'performed_by',
        'type',
        'reason',
        'quantity',
        'note',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
