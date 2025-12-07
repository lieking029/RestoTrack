<?php

namespace App\Http\Controllers\Web\Admin;

use App\DataTables\MenuDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Menu\StoreMenuRequest;
use App\Http\Requests\Web\Admin\Menu\UpdateMenuRequest;
use App\Models\Menu;
use App\Models\Product;
use Illuminate\Http\Request;
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

        $validated['status'] = $request->has('status') ? 0 : 1;

        $menu = Menu::create($validated);

        if ($request->has('ingredients')) {
            $ingredients = [];
            foreach ($request->ingredients as $ingredient) {
                if (!empty($ingredient['product_id']) && !empty($ingredient['quantity_needed'])) {
                    $ingredients[$ingredient['product_id']] = [
                        'quantity_needed' => $ingredient['quantity_needed']
                    ];
                }
            }
            
            if (!empty($ingredients)) {
                $menu->products()->attach($ingredients);
            }
        }

        alert()->success('Menu item has been created successfully!');
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

        // Set status
        $validated['status'] = $request->has('status') ? 0 : 1;

        // Update menu
        $menu->update($validated);

        // Sync ingredients
        if ($request->has('ingredients')) {
            $ingredients = [];
            foreach ($request->ingredients as $ingredient) {
                if (!empty($ingredient['product_id']) && !empty($ingredient['quantity_needed'])) {
                    $ingredients[$ingredient['product_id']] = [
                        'quantity_needed' => $ingredient['quantity_needed']
                    ];
                }
            }
            
            $menu->products()->sync($ingredients);
        } else {
            // Remove all ingredients if none provided
            $menu->products()->detach();
        }

        alert()->success('Menu item has been updated successfully!');
        return redirect()->route('admin.menu.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
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