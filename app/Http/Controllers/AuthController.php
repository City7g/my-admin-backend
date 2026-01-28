<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            return [
                "token" => $this->generateToken($user),
                "user" => $user,
            ];
        }

        throw ValidationException::withMessages([
            "email" => ["The provided credentials do not match our records."],
        ])->status(404);
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create($validated);

        Auth::login($user);

        return [
            "token" => $this->generateToken($user),
            "user" => $user,
        ];
    }

    public function me(Request $request)
    {
        // dd(now());
        return $request->user();
    }

    public function generateToken(User $user): string
    {
        $expireTime = now()->addMinutes(config("sanctum.expiration"));

        return $user->createToken(
            "token",
            ["token"],
            $expireTime,
        )->plainTextToken;
    }

    public function generateTokens(User $user): array
    {
        $atExpireTime = now()->addMinutes(config("sanctum.expiration"));
        $rtExpireTime = now()->addMinutes(config("sanctum.rt_expiration"));

        $accessToken = $user->createToken(
            "access_token",
            ["access-api"],
            $atExpireTime,
        );
        $refreshToken = $user->createToken(
            "refresh_token",
            ["issue-access-token"],
            $rtExpireTime,
        );

        return [
            "access_token" => $accessToken->plainTextToken,
            "refresh_token" => $refreshToken->plainTextToken,
        ];
    }
}
