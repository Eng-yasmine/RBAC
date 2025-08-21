<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\LoginRequest;
use App\Http\Requests\API\V1\Auth\LogoutRequest;
use App\Http\Requests\API\V1\Auth\RegisterRequest;

class AuthenticationController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        if ($user) {
            return response()->json([
                'message' => 'User registered successfully',
                'user' => UserResource::make($user),
            ], 201);
        }
    }
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => UserResource::make($user),
            ], 200);
        }
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => UserResource::make($request->user),
        ], 200);
    }
}
