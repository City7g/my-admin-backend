<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login(): void
    {
        $user = User::factory(['password' => '123123123'])->create();

        $response = $this->postJson('/api/auth/login',
            [
                'email' => $user->email,
                'password' => '123123123'
            ]
        );

        $response->assertStatus(200);
        $this->isString($response['tokens']['access_token']);
        $this->isString($response['tokens']['refresh_token']);
        $response->assertJsonFragment(['name' => $user->name]);
        $response->assertJsonFragment(['email' => $user->email]);
    }

    public function test_login_validation_error(): void
    {
        $response = $this->postJson('/api/auth/login');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_user_not_found(): void
    {
        $fakeUser = ['email' => 'fake@example.com', 'password' => '123123123'];

        $response = $this->postJson('/api/auth/login', $fakeUser);

        $response->assertStatus(404);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_register(): void
    {
        $data = [
            'name' => 'Name',
            'email' => 'test@example.com',
            'password' => '123123123'
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(200);
        $this->isString($response['tokens']['access_token']);
        $this->isString($response['tokens']['refresh_token']);
        $response->assertJsonFragment(['name' => $data['name']]);
        $response->assertJsonFragment(['email' => $data['email']]);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    public function test_register_validation_error(): void
    {
        $response = $this->postJson('/api/auth/register');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
