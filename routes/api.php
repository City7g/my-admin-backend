<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json('Hello');
});

Route::apiResource('users', UserController::class)->only('index');
