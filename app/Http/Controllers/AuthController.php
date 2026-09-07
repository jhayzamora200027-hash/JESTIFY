<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        return $this->success(['user' => new UserResource($user), 'token' => $user->createToken('flutter')->plainTextToken], 'Registration successful', 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->validated('email'))->first();
        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return $this->error('Invalid credentials.', ['credentials' => ['The email or password is incorrect.']], 422);
        }
        return $this->success(['user' => new UserResource($user), 'token' => $user->createToken('flutter')->plainTextToken], 'Login successful');
    }

    public function logout()
    {
        request()->user()->currentAccessToken()?->delete();
        return $this->success(null, 'Logout successful');
    }
}
