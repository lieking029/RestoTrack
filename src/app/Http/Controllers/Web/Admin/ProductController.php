<?php

namespace App\Http\Controllers\Web\Admin;

use App\DataTables\ProductDataTable;
use App\Enums\UnitOfMeasurement;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Product\StoreProductRequest;
use App\Http\Requests\Web\Admin\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductDataTable $dataTable)
    {
        return $dataTable->render('admin.product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.product.create', [
            'unitOfMeasurements' => UnitOfMeasurement::getGroupedUnits(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated());

        alert()->success('Product has been added successfully');
        return redirect()->route('admin.product.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('admin.product.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.product.edit', [
            'product' => $product,
            'unitOfMeasurements' => UnitOfMeasurement::getGroupedUnits(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        $product->update($validated);

        // Recalculate status if stock fields were changed
        if (isset($validated['remaining_stock']) || isset($validated['initial_stock'])) {
            $product->update(['status' => $product->calculateStatus()]);
        }

        alert()->success('Product has been updated successfully');
        return redirect()->route('admin.product.index');
    }

    /**
     * Archive the specified product (soft delete).
     */
    public function destroy(Product $product)
    {
        if (auth()->user()->isManager()) {
            abort(403, 'Managers are not allowed to archive products.');
        }

        $product->delete();

        alert()->success('Product has been archived successfully');
        return redirect()->route('admin.product.index');
    }

    /**
     * Display archived products.
     */
    public function archived()
    {
        $archivedProducts = Product::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.product.archived', compact('archivedProducts'));
    }

    /**
     * Restore an archived product.
     */
    public function restore(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        alert()->success('Product has been restored successfully');
        return redirect()->route('admin.product.archived');
    }

    /**
     * Permanently delete an archived product.
     */
    public function forceDelete(string $id)
    {
        if (auth()->user()->isManager()) {
            abort(403, 'Managers are not allowed to permanently delete products.');
        }

        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();

        alert()->success('Product has been permanently deleted');
        return redirect()->route('admin.product.archived');
    }
}
