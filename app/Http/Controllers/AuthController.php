<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

// use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (Auth::attempt($validated)) {
            $token = $request->user()->createToken('test');
            return ['token' => $token->plainTextToken];;
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create($validated);

        Auth::login($user);

        $token = $request->user()->createToken('test');
        return ['token' => $token->plainTextToken];;
    }
}
