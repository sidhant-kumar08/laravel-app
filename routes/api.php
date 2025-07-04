<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckUserId;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('categories', App\Http\Controllers\CategoryController::class)->middleware(CheckUserId::class);

// Route::apiResource('products', App\Http\Controllers\ProductController::class)->middleware(CheckUserId::class);

Route::apiResource('categories.products', App\Http\Controllers\NewProductController::class)->middleware(CheckUserId::class);



Route::post('user', [UserController::class, 'store']);




