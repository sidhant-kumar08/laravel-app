<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


use Throwable;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse|ProductCollection
    {
        try {
            $userId = $request->query("user_id");

            $products = Product::where("user_id", $userId)->get();

            if ($products->isEmpty()) {
                return response()->json(["message" => "No data found"], 404); //user_id is not exist in that product table
            }

            return new ProductCollection($products);
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal Server Error"], 500);
        }
    }

    public function store(ProductStoreRequest $request): JsonResponse|ProductResource
    {

        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            $product = Product::create($request->validated() + ["user_id" => $userId]);

            return new ProductResource($product);
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal Server Error"], 500);
        }
    }

    public function show(Request $request, Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    public function update(ProductUpdateRequest $request, Product $product): JsonResponse|ProductResource
    {
        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            if ($product->user_id == $userId) {
                $product->update($request->validated());
            } else {
                return new JsonResponse(["message" => "Not authorized"], 401);
            }

            return new ProductResource($product);
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal Server Error"], 500);
        }
    }

    public function destroy(Request $request, Product $product): JsonResponse|Response
    {
        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            if ($product->user_id == $userId) {
                $product->delete();
                return response()->noContent();
            } else {
                return response()->json(["message" => "Not Authorized"], 401);
            }
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal Server Error"], 500);
        }
    }
}
