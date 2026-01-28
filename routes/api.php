<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return response()->json("Hello");
});

Route::post("/auth/login", [AuthController::class, "login"])->name("login");
Route::post("/auth/register", [AuthController::class, "register"])->name(
    "register",
);
Route::get("/auth/me", [AuthController::class, "me"])
    ->name("me")
    ->middleware("auth:sanctum");
Route::post("/auth/logout", [AuthController::class, "logout"])
    ->name("logout")
    ->middleware("auth:sanctum");

Route::apiResource("users", UserController::class);
// Route::apiResource('users', UserController::class)->middleware('auth:sanctum');
