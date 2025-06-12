<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/test', function (Request $request){
    $name = $request->input('name');
    $email = $request->input('email');



    return response()->json(["name" => $name, "email" => $email],200);
});

Route::get('/test', function (Request $request){
    Log::info("Request Received");
    $name = "Sidhant";
    $email = "test@test.com";



    return response()->json(["name" => $name, "email" => $email],200);
});
