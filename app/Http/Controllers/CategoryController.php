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
use Symfony\Component\HttpFoundation\JsonResponse as HttpFoundationJsonResponse;


//custom
use App\Helpers\AppHelper;

use function PHPUnit\Framework\isEmpty;

class CategoryController extends Controller
{
    public function index(Request $request) : CategoryCollection|JsonResponse
    {
        $userId = AppHelper::checkUserIdInRequest($request);

        if ($userId == -1) {
            return response()->json(["message" => "Please provide userId"], 400);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(["message" => "Invalid user"], 400);
        }

        $categories = Category::where("user_id", $userId)->get();

        return new CategoryCollection($categories);
    }

    public function store(CategoryStoreRequest $request) : CategoryResource|JsonResponse
    {
        $userId = AppHelper::checkUserIdInRequest($request);

        if ($userId == -1) {
            return response()->json(["message" => "Please provide userId"], 400);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(["message" => "Invalid user"], 400);
        }

        $category = Category::create($request->validated() + ["user_id" => $userId]);

        return new CategoryResource($category);
    }

    public function show(Request $request, Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    public function update(CategoryUpdateRequest $request, Category $category) : CategoryResource|JsonResponse
    {
        $userId = AppHelper::checkUserIdInRequest($request);

        if ($userId == -1) {
            return response()->json(["message" => "Please provide userId"], 400);
        }

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
    }

    public function destroy(Request $request, Category $category) : CategoryResource|JsonResponse|Response
    {
        $userId = AppHelper::checkUserIdInRequest($request);

        if ($userId == -1) {
            return response()->json(["message" => "Please provide userId"], 400);
        }

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
    }
}
