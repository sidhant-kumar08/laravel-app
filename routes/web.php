<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::resource('categories', App\Http\Controllers\CategoryController::class)->only('index');


Route::resource('categories', App\Http\Controllers\CategoryController::class)->only('index');
