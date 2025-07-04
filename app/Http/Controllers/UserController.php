<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function store(UserStoreRequest $request)
    {
        $user = User::create($request->validated());
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(["user" => $user, "token" => $token]);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json(["message" => "Invalid credentials"]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(["token" => $token]);
    }

}
