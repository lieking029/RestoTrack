<?php

use App\Http\Controllers\Web\Admin\ProductController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
});
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => 'auth',
], function() {
    
    Route::get('/dashboard', fn() => view('admin.dashboard.index'))->name('dashboard');
    Route::resource('product', ProductController::class);
    Route::resource('user', UserController::class);
});