<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


use Throwable;


class CategoryController extends Controller
{
    public function index(Request $request): CategoryCollection|JsonResponse
    {
        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            $categories = Category::where("user_id", $userId)->get();

            return new CategoryCollection($categories);
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal Server Error"], 500);
        }
    }

    public function store(CategoryStoreRequest $request): CategoryResource|JsonResponse
    {
        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            $category = Category::create($request->validated() + ["user_id" => $userId]);

            return new CategoryResource($category);
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal Server Error"], 500);
        }
    }

    public function show(Request $request, Category $category)
    {
        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            if ($category->user_id != $userId) {
                return response()->json(["message" => "Incorrect details provided"], 401);
            }
            return new CategoryResource($category);
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal Server Error"], 500);
        }
    }

    public function update(CategoryUpdateRequest $request, Category $category): CategoryResource|JsonResponse
    {
        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            if ($category->user_id == $userId) {
                $category->update($request->validated());
                return new CategoryResource($category);
            } else {
                return response()->json(["message" => "Not Authorized"], 401);
            }
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal Server Error"], 500);
        }
    }

    public function destroy(Request $request, Category $category): CategoryResource|JsonResponse|Response
    {
        try {
            $userId = $request->query("user_id");

            $user = User::find($userId);

            if (!$user) {
                return response()->json(["message" => "Invalid user"], 400);
            }

            if ($category->user_id == $userId) {
                $category->delete();
                return response()->noContent();
            } else {
                return response()->json(["message" => "Not Authorized"], 401);
            }
        } catch (Throwable $e) {
            return response()->json(["message" => "Internal Server Error"], 500);
        }
    }
}
