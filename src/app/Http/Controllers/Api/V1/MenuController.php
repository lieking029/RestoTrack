<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('products.inventoryItem')
            ->latest()
            ->get();

        return MenuResource::collection($menus);
    }
}
