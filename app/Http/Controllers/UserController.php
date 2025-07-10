<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    public function store(UserStoreRequest $request)
    {   

        $data = ["name" => $request->name, "email" => $request->email, "password" => Hash::make($request->password)];
        $user = User::create($data);
        $token = $user->createToken('auth-token',['*'], now()->addWeek())->plainTextToken;

        return response()->json(["user" => $user, "token" => $token], 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json(["message" => "User not found"], 404);
        }

        $isCorrect = Hash::check($request->password, $user->password);


        if(!$isCorrect){
            return response()->json(["message" => "Incorrect credentials"], 401);
        }

        $token = $user->createToken('auth-token',['*'], now()->addWeek())->plainTextToken;

        return response()->json(["token" => $token], 200);
    }

}
