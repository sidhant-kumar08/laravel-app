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


//custom
use App\Helpers\AppHelper;



class ProductController extends Controller
{
    public function index(Request $request)
    {
        $userId = AppHelper::checkUserIdInRequest($request);

        if ($userId == -1) {
            return response()->json(["message" => "Please provide userId"], 400);
        }

        $products = Product::where("user_id", $userId)->get();

        if($products->isEmpty()){
            return response()->json(["message" => "No data found"], 404);//user_id is not exist in that product table
        }

        return new ProductCollection($products);
    }

    public function store(ProductStoreRequest $request)
    {

        $userId = AppHelper::checkUserIdInRequest($request);

        if ($userId == -1) {
            return response()->json(["message" => "Please provide userId"], 400);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(["message" => "Invalid user"], 400);
        }


        $product = Product::create($request->validated() + ["user_id" => $userId]);

        return new ProductResource($product);
    }

    public function show(Request $request, Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $userId = AppHelper::checkUserIdInRequest($request);

        if ($userId == -1) {
            return response()->json(["message" => "Please provide userId"], 400);
        }

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
    }

    public function destroy(Request $request, Product $product)
    {
        $userId = AppHelper::checkUserIdInRequest($request);

        if ($userId == -1) {
            return response()->json(["message" => "Please provide userId"], 400);
        }

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
    }
}

