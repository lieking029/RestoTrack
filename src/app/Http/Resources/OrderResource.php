<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'createdBy' => (string) $this->created_by,
            'processedBy' => $this->processed_by ? (string) $this->processed_by : null,
            'status' => $this->status->value ?? $this->status,
            'subtotal' => (float) $this->subtotal,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'processor' => new UserResource($this->whenLoaded('processor')),
        ];
    }
}
