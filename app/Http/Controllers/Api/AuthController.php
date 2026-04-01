<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;

class AuthController extends Controller
{
  public function register(RegisterRequest $request)
  {
    try {
      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
      ]);

      return response()->json(['success' => true, 'user' => $user, "message" => "User registered successfully"], 201);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => 'An error occurred while checking email uniqueness'], 500);
    }
  }

  public function login(LoginRequest $request)
  {
    try {
      $user = User::where('email', $request->email)->first();

      if (!$user || !\Hash::check($request->password, $user->password)) {
        return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
      }
      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json(['success' => true, 'access_token' => $token, 'token_type' => 'Bearer', 'user' => $user, "message" => "Login successful"], 200);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => 'An error occurred while checking email uniqueness'], 500);
    }
  }
}
