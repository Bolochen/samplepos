<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('registers a new user and returns a token', function () {
    $response = $this->postJson('/api/register', [
        'username' => 'jdoe',
        'name' => 'John Doe',
        'role' => 'cashier',
        'password' => 'secret123',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'username', 'name', 'role'],
            'token',
        ]);

    $this->assertDatabaseHas('users', [
        'username' => 'jdoe',
        'name' => 'John Doe',
        'role' => 'cashier',
    ]);
});

it('logs in with correct credentials and returns a token', function () {
    $user = User::factory()->create([
        'username' => 'jdoe',
        'name' => 'John Doe',
        'role' => 'cashier',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->postJson('/api/login', [
        'username' => 'jdoe',
        'password' => 'secret123',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'username', 'name', 'role'],
            'token',
        ])
        ->assertJsonPath('user.id', $user->id);
});

it('returns 401 for invalid login credentials', function () {
    User::factory()->create([
        'username' => 'jdoe',
        'role' => 'cashier',
        'password' => Hash::make('secret123'),
    ]);

    $this->postJson('/api/login', [
        'username' => 'jdoe',
        'password' => 'wrong-password',
    ])->assertStatus(401);
});

it('logs out and deletes all tokens', function () {
    $user = User::factory()->create([
        'username' => 'jdoe',
        'role' => 'cashier',
        'password' => Hash::make('secret123'),
    ]);

    $user->createToken('auth_token');

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/logout')
        ->assertOk()
        ->assertJson(['message' => 'Logged out successfully']);

    expect($user->tokens()->count())->toBe(0);
});
