<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);

        $user = User::create($request->all());
        $token =  $user->createToken('MyApp')->plainTextToken;
        return response()->json(['data' =>['token' => $token],'message' => "User registered successfully"], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['data' =>['user' => $user,'token' => $token],'message'=> 'User login successfully.'], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
}
