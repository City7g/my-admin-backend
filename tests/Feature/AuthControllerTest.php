<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login(): void
    {
        $user = User::factory(["password" => "password"])->create();

        $response = $this->postJson("/api/auth/login", [
            "email" => $user->email,
            "password" => "password",
        ]);

        $response->assertStatus(200);
        $this->isString($response["token"]);
        $this->assertEquals($user->name, $response["user"]["name"]);
        $this->assertEquals($user->email, $response["user"]["email"]);
        $this->assertArrayNotHasKey("password", $response["user"]);
    }

    public function test_login_validation_error(): void
    {
        $response = $this->postJson("/api/auth/login");

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["email", "password"]);
    }

    public function test_login_user_not_found(): void
    {
        $fakeUser = ["email" => "fake@example.com", "password" => "123123123"];

        $response = $this->postJson("/api/auth/login", $fakeUser);

        $response->assertStatus(404);
        $response->assertJsonValidationErrors(["email"]);
    }

    public function test_register(): void
    {
        $data = [
            "name" => "Name",
            "email" => "test@example.com",
            "password" => "123123123",
        ];

        $response = $this->postJson("/api/auth/register", $data);

        $response->assertStatus(200);
        $this->isString($response["token"]);
        $this->assertEquals($data["name"], $response["user"]["name"]);
        $this->assertEquals($data["email"], $response["user"]["email"]);
        $this->assertArrayNotHasKey("password", $response["user"]);

        $this->assertDatabaseHas("users", [
            "name" => $data["name"],
            "email" => $data["email"],
        ]);
    }

    public function test_register_validation_error(): void
    {
        $response = $this->postJson("/api/auth/register");

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["name", "email", "password"]);
    }

    public function test_me(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ["*"]);

        $response = $this->getJson("/api/auth/me");

        $response->assertStatus(200);
        $this->assertEquals($user->name, $response["name"]);
        $this->assertEquals($user->email, $response["email"]);
        $this->assertArrayNotHasKey("password", $response);
    }

    public function test_me_without_token(): void
    {
        $response = $this->getJson("/api/auth/me");

        $response->assertStatus(401);
    }

    public function test_me_invalid_token(): void
    {
        $response = $this->getJson(
            "/api/auth/me",
            headers: ["Authorization" => "invalid text"],
        );

        $response->assertStatus(401);
    }

    public function test_me_token_expired(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken("test")->plainTextToken;

        Carbon::setTestNow(Carbon::now()->addYear());

        $response = $this->getJson(
            "/api/auth/me",
            headers: ["Authorization" => "Bearer " . $token],
        );

        $response->assertStatus(401);
    }

    public function test_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken("test")->plainTextToken;

        $this->postJson(
            "/api/auth/logout",
            headers: [
                "Authorization" => "Bearer " . $token,
            ],
        );

        $this->assertDatabaseMissing("personal_access_tokens", [
            "tokenable_id" => $user->id,
        ]);
    }
}
