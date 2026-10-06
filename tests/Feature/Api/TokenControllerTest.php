<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_token_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password123')]);

        $response = $this->postJson('/api/tokens/create', [
            'email' => $user->email,
            'password' => 'password123',
            'device_name' => 'test-device',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['token']);
    }

    public function test_create_token_with_wrong_password_returns_422(): void
    {
        $user = User::factory()->create(['password' => Hash::make('correct-password')]);

        $this->postJson('/api/tokens/create', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'test-device',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_create_token_with_missing_fields_returns_422(): void
    {
        $this->postJson('/api/tokens/create', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password', 'device_name']);
    }

    public function test_revoke_token_returns_200(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->deleteJson('/api/tokens/revoke')
            ->assertOk()
            ->assertJsonPath('message', 'トークンを失効しました');
    }

    public function test_revoke_token_without_auth_returns_401(): void
    {
        $this->deleteJson('/api/tokens/revoke')
            ->assertUnauthorized();
    }
}
