<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_users()
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $thirdUser = User::factory()->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonFragments([
            ["name" => $firstUser->name, "email" => $firstUser->email],
            ["name" => $secondUser->name, "email" => $secondUser->email],
            ["name" => $thirdUser->name, "email" => $thirdUser->email],
        ]);

        $response->assertJsonMissing(['password' => $firstUser->password]);
        $response->assertJsonMissing(['password' => $secondUser->password]);
        $response->assertJsonMissing(['password' => $thirdUser->password]);
    }

    public function test_list_users_empty()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $this->assertEquals([], $response->json());
    }

    public function test_create_user_success()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    public function test_create_user_validation_error()
    {
        $response = $this->postJson('/api/users', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_create_user_invalid_email()
    {
        $data = [
            'name' => 'John',
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_view_user_success()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    public function test_view_user_not_found()
    {
        $response = $this->getJson('/api/users/999');

        $response->assertStatus(404);
    }

    public function test_update_user_success()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment($data);

        $this->assertDatabaseHas('users', array_merge(['id' => $user->id], $data));
    }

    public function test_update_user_not_found()
    {
        $response = $this->putJson('/api/users/999', [
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(404);
    }

    public function test_update_user_invalid_data()
    {
        $user = User::factory()->create();

        $response = $this->putJson("/api/users/{$user->id}", [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_delete_user_success()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_delete_user_not_found()
    {
        $response = $this->deleteJson('/api/users/999');

        $response->assertStatus(404);
    }
}
