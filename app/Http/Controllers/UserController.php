<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(CreateUserRequest $request)
    {
        $validated = $request->all();

        return User::create($validated);
    }

    public function show(User $user)
    {
        return $user;
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->all();

        $user->update($validated);

        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
