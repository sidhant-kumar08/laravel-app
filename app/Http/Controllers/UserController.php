<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    public function store(Request $request)
    {   
        if(!$request->email || !$request->name || !$request->password){
            return response()->json(["message" => "Please check all fields"]);
        }

        $data = ["name" => $request->name, "email" => $request->email, "password" => Hash::make($request->password)];

        $user = User::create($data);
        $token = $user->createToken('auth-token',['*'], now()->addWeek())->plainTextToken;

        return response()->json(["user" => $user, "token" => $token]);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json(["message" => "Invalid credentials"]);
        }

        $isCorrect = Hash::check($request->password, $user->password);


        if(!$isCorrect){
            return response()->json(["message" => "Incorrect credentials"]);
        }

        $token = $user->createToken('auth-token',['*'], now()->addWeek())->plainTextToken;

        return response()->json(["token" => $token]);
    }

}
