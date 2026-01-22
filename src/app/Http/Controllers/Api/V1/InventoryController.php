<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Services\InventoryOpsService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function receive(Request $request, InventoryItem $inventoryItem, InventoryOpsService $service)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'note'     => ['nullable', 'string', 'max:255'],
        ]);

        $service->receive($inventoryItem, $data['quantity'], auth()->id(), $data['note'] ?? null);

        return response()->json($inventoryItem->fresh('product'));
    }

    public function waste(Request $request, InventoryItem $inventoryItem, InventoryOpsService $service)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'note'     => ['nullable', 'string', 'max:255'],
        ]);

        $service->waste($inventoryItem, $data['quantity'], auth()->id(), $data['note'] ?? null);

        return response()->json($inventoryItem->fresh('product'));
    }

    public function adjust(Request $request, InventoryItem $inventoryItem, InventoryOpsService $service)
    {
        $data = $request->validate([
            'new_quantity' => ['required', 'integer', 'min:0'],
            'note'         => ['nullable', 'string', 'max:255'],
        ]);

        $service->adjustTo($inventoryItem, $data['new_quantity'], auth()->id(), $data['note'] ?? null);

        return response()->json($inventoryItem->fresh('product'));
    }
}