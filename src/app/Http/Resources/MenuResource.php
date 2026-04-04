<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isAvailable = $this->isAvailable() && $this->hasIngredientsInStock();

        $missingIngredients = [];
        if (!$this->hasIngredientsInStock()) {
            $missingIngredients = $this->getMissingIngredients()->map(fn ($item) => [
                'product_name' => $item['product']->name,
                'needed' => $item['needed'],
                'available' => $item['available'],
                'shortage' => $item['shortage'],
            ])->values()->toArray();
        }

        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'category' => $this->category->value ?? $this->category,
            'status' => $this->status->value ?? $this->status,
            'is_available' => $isAvailable,
            'missing_ingredients' => $missingIngredients,
            'dish_picture' => $this->dish_picture_url,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
