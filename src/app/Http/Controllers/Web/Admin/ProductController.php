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
        $product->update($request->validated());

        alert()->success('Product has been updated successfully');
        return redirect()->route('admin.product.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        alert()->success('Product has been deleted successfully');
        return redirect()->route('admin.product.index');
    }
}
