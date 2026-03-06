<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase; // This resets the database after the test finishes!

    public function test_user_can_login_with_correct_credentials(): void
    {
        // 1. Preparation: Create a dummy user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Action: Send a POST request to the login endpoint
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // 3. Assertion: Check if the response is successful and contains the token
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'user' => [
                         'id',
                         'name',
                         'email',
                     ]
                 ]);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        // 1. Preparation
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Action
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // 3. Assertion: Check if it returns a 422 Validation Error
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}
