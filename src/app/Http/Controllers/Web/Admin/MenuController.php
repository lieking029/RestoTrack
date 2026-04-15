<?php

namespace App\Http\Controllers\Web\Admin;

use App\DataTables\MenuDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Menu\StoreMenuRequest;
use App\Http\Requests\Web\Admin\Menu\UpdateMenuRequest;
use App\Models\InventoryItem;
use App\Models\Menu;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MenuDataTable $dataTable)
    {
        return $dataTable->render('admin.menu.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('admin.menu.create', [
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('dish_picture')) {
            $imagePath = $request->file('dish_picture')->store('menu', 'public');
            $validated['dish_picture'] = $imagePath;
        }

        $requestedAvailable = $request->has('status');
        $hasStock = $this->ingredientsHaveSufficientStock($validated['ingredients'] ?? []);
        $validated['status'] = ($requestedAvailable && $hasStock) ? 0 : 1;

        $menu = Menu::create($validated);

        if (!empty($validated['ingredients'])) {
            $ingredients = [];
            foreach ($validated['ingredients'] as $ingredient) {
                $ingredients[$ingredient['product_id']] = [
                    'quantity_needed' => $ingredient['quantity_needed']
                ];
            }
            $menu->products()->attach($ingredients);
        }

        if ($requestedAvailable && !$hasStock) {
            alert()->warning('Menu item created but marked as Unavailable because one or more ingredients have insufficient stock.');
        } else {
            alert()->success('Menu item has been created successfully!');
        }
        return redirect()->route('admin.menu.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        $menu->load('products');
        
        return view('admin.menu.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $menu->load('products');
        $products = Product::orderBy('name')->get();
        
        return view('admin.menu.edit', compact('menu', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('dish_picture')) {
            // Delete old image
            if ($menu->dish_picture && Storage::disk('public')->exists($menu->dish_picture)) {
                Storage::disk('public')->delete($menu->dish_picture);
            }
            
            $imagePath = $request->file('dish_picture')->store('menu', 'public');
            $validated['dish_picture'] = $imagePath;
        }

        // Set status — force Unavailable if any ingredient has insufficient stock
        $requestedAvailable = $request->has('status');
        $hasStock = $this->ingredientsHaveSufficientStock($validated['ingredients'] ?? []);
        $validated['status'] = ($requestedAvailable && $hasStock) ? 0 : 1;

        // Update menu
        $menu->update($validated);

        // Sync ingredients
        if (!empty($validated['ingredients'])) {
            $ingredients = [];
            foreach ($validated['ingredients'] as $ingredient) {
                $ingredients[$ingredient['product_id']] = [
                    'quantity_needed' => $ingredient['quantity_needed']
                ];
            }
            $menu->products()->sync($ingredients);
        } else {
            $menu->products()->detach();
        }

        if ($requestedAvailable && !$hasStock) {
            alert()->warning('Menu item updated but marked as Unavailable because one or more ingredients have insufficient stock.');
        } else {
            alert()->success('Menu item has been updated successfully!');
        }
        return redirect()->route('admin.menu.index');
    }

    /**
     * Check whether every ingredient in the submitted list has enough stock
     * (using InventoryItem.stock_quantity as the source of truth) to satisfy
     * the quantity_needed for one serving of the dish.
     */
    private function ingredientsHaveSufficientStock(array $ingredients): bool
    {
        if (empty($ingredients)) {
            return false;
        }

        foreach ($ingredients as $ingredient) {
            $productId = $ingredient['product_id'] ?? null;
            $needed = (float) ($ingredient['quantity_needed'] ?? 0);

            if (!$productId || $needed <= 0) {
                return false;
            }

            $product = Product::find($productId);
            if (!$product) {
                return false;
            }

            $productStock = (float) ($product->remaining_stock ?? 0);
            $inventory = InventoryItem::where('product_id', $productId)->first();
            $inventoryStock = $inventory ? (float) $inventory->stock_quantity : $productStock;

            // Use the lower of the two to fail-safe when sources drift out of sync.
            $available = min($productStock, $inventoryStock);

            if ($available < $needed) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        if (auth()->user()->isManager()) {
            abort(403, 'Managers are not allowed to delete menu items.');
        }

        // Delete image
        if ($menu->dish_picture && Storage::disk('public')->exists($menu->dish_picture)) {
            Storage::disk('public')->delete($menu->dish_picture);
        }

        // Delete menu (ingredients will be auto-deleted due to cascade)
        $menu->delete();

        alert()->success('Menu item has been deleted successfully!');
        return redirect()->route('admin.menu.index');
    }
}