<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('can get all categories', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    $token = $user->createToken('test')->plainTextToken;

    Category::factory()->count(3)->create();

    $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/categories');

    $response->assertStatus(200)->assertJsonCount(3);
});

it('can filter categories by name', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    $token = $user->createToken('test')->plainTextToken;

    Category::factory()->create(['name' => 'Soft Drink']);
    Category::factory()->create(['name' => 'Alcohol Drink']);

    $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/categories?name=drink');

    $response->assertStatus(200)->assertJsonCount(2);
});

it('can get categories by id', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    $token = $user->createToken('test')->plainTextToken;

    $category = Category::factory()->create(['name' => 'Food']);

    $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/categories/'.$category->id);

    $response->assertStatus(200);

    $this->assertDatabaseHas('categories', ['name' => 'Food']);
});

it('can get category by id but not found', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    $token = $user->createToken('test')->plainTextToken;

    Category::factory()->create(['name' => 'Food']);

    $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/categories/999');

    $response->assertNotFound();
});

it('can create category', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/categories', ['name' => 'Drink']);

    $response->assertStatus(201);

    $this->assertDatabaseHas('categories', ['name' => 'Drink']);
});

it('create category but name has been taken', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    $token = $user->createToken('test')->plainTextToken;

    $category = Category::factory()->create(['name' => 'Drink']);

    $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/categories', ['name' => 'Drink']);

    $response->assertStatus(422);

    $this->assertDatabaseCount('categories', 1);
});

it('can update menu', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $category = Category::factory()->create(['name' => 'Soft Drink']);

    $response = $this->putJson('/api/categories/'.$category->id, 
            ['name' => 'Drink']);

        $response->assertStatus(200);

    $this->assertDatabaseHas('categories', [
        'name' => 'Drink'
    ]);
});

it('cannot update menu with taken name', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $category1 = Category::factory()->create(['name' => 'Drink']);
    $category2 = Category::factory()->create(['name' => 'Soft Drink']);

    $response = $this->putJson('/api/categories/'.$category2->id, 
            ['name' => 'Drink']);

    $response->assertStatus(422);
});

it('can delete menu as an admin', function() {
    $user = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($user);

    $category = Category::factory()->create(['name' => 'Drink']);

    $response = $this->deleteJson('/api/categories/'.$category->id);

    $response->assertNoContent();

    $this->assertSoftDeleted('categories', [
        'id' => $category->id
    ]);
});

it('cannot delete menu as non admin', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $category = Category::factory()->create(['name' => 'Drink']);

    $response = $this->deleteJson('/api/categories/'.$category->id);

    $response->assertStatus(403);
});
