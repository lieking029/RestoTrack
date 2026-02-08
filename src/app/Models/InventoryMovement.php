<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryMovementFactory> */
    use HasFactory, HasUuids;

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

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope to get waste movements only
     */
    public function scopeWaste($query)
    {
        return $query->where('reason', 'WASTE');
    }

    /**
     * Get parsed waste reason from note
     */
    public function getWasteReasonAttribute(): ?string
    {
        if (!$this->note || $this->reason !== 'WASTE') {
            return null;
        }

        if (preg_match('/Reason:\s*(\w+)/', $this->note, $matches)) {
            return $matches[1];
        }

        return 'Other';
    }

    /**
     * Get parsed notes from note field
     */
    public function getParsedNotesAttribute(): ?string
    {
        if (!$this->note) {
            return null;
        }

        if (preg_match('/Notes:\s*(.+)$/', $this->note, $matches)) {
            return $matches[1];
        }

        return $this->note;
    }
}
