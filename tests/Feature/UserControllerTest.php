<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends Tests\TestCase
{
    use RefreshDatabase;

    public function test_return_list_of_users()
    {
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_create_user()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'superSecretPassword',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_return_user_by_id()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    public function test_return_user_by_id_not_found()
    {
        $response = $this->getJson('/api/users/1');

        $response->assertStatus(404);
    }

    public function test_update_user()
    {
        $user = User::factory()->create();
        $payload = ['name' => 'New name'];

        $response = $this->putJson("/api/users/{$user->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New name',
        ]);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
