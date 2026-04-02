<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::available()
            ->with('products')
            ->latest()
            ->get()
            ->filter(fn ($menu) => $menu->hasIngredientsInStock());

        return MenuResource::collection($menus);
    }
}
