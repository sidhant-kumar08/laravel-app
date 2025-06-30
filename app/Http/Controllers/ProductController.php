<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index(Request $request): ProductCollection
    {
        $products = Product::all();

        return new ProductCollection($products);
    }

    public function store(ProductStoreRequest $request): ProductResource
    {
        $product = Product::create($request->validated());

        return new ProductResource($product);
    }

    public function show(Request $request, Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    public function update(ProductUpdateRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());

        return new ProductResource($product);
    }

    public function destroy(Request $request, Product $product): Response
    {
        $product->delete();

        return response()->noContent();
    }

    public function getCategory(Request $request, Product $product){
        return $product->category;
    }

    public function getRelatedUser(Request $request, Product $product)
    {
        return $product->user;
    }
}
