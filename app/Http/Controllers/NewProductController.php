<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class NewProductController extends Controller
{

    public function index(Category $category, Request $request) : JsonResponse
    {
        try {

            $filter = $request->query("filter");

            if ($filter == "all") {
                $Products = $category->products;
                return response()->json(["data" => $Products], 200);
            } else if ($filter == "my") {
                $userId = $request->query("user_id");
                
                $user = User::find($userId);
                
                if (!$user) {
                    return response()->json(["message" => "Invalid user"], 400);
                }
                
                $Products = Product::where("user_id", $userId)->get();

                return response()->json(ProductResource::collection($Products), 200);
            } else {
                return response()->json(["message" => "Invalid request, check the filter"], 400);
            }


        } catch (Throwable $e) {
            return response()->json(["message" => "Internal server error"], 500);
        }
    }


    public function store(ProductStoreRequest $request, Category $category): JsonResponse|ProductResource
    {
        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            $data = $request->validated() + ["category_id" => $category->id, "user_id" => $userId];
            $product = Product::create($data);

            return new ProductResource($product);
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal server error"], 500);
        }
    }

    public function show($category, Product $product, Request $request): JsonResponse|ProductResource
    {
        try {

            if ($product->category_id != $category) {
                return response()->json(["message" => "Please check all details"], 404);
            }

            return new ProductResource($product);
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal server error"], 500);
        }
    }


    public function update(ProductUpdateRequest $request, $category, Product $product): JsonResponse|ProductResource
    {
        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            if ($product->category_id != $category) {
                return response()->json(["message" => "Please check all details"], 404);
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


    public function destroy(Request $request, $category, Product $product): JsonResponse|Response
    {

        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            if ($product->category_id != $category) {
                return response()->json(["message" => "Please check all details"], 404);
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
