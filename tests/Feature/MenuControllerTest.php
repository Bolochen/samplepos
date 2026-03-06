<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can get all menus', function () {

    $cashier = User::factory()->create(['role' => 'cashier']);
    $token = $cashier->createToken('test')->plainTextToken;

    Menu::factory()->count(3)->create();

    $response = $this->withHeader('Authorization', "Bearer $token")
                     ->getJson('/api/menus');

    $response->assertStatus(200)
             ->assertJsonCount(3);
});

it('can filter menus by name', function () {

    $cashier = User::factory()->create(['role' => 'cashier']);
    $token = $cashier->createToken('test')->plainTextToken;

    Menu::factory()->create(['name' => 'Coffee']);
    Menu::factory()->create(['name' => 'Burger']);

    $response = $this->withHeader('Authorization', "Bearer $token")
                     ->getJson('/api/menus?name=Coffee');

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'Coffee'])
             ->assertJsonMissing(['name' => 'Burger']);
});

it('can create menu', function () {

    $cashier = User::factory()->create(['role' => 'cashier']);
    $token = $cashier->createToken('test')->plainTextToken;

    $category = Category::factory()->create();

    $data = [
        'name' => 'Latte',
        'price' => 20000,
        'category_id' => $category->id
    ];

    $response = $this->withHeader('Authorization', "Bearer $token")
                     ->postJson('/api/menus', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('menus', [
        'name' => 'Latte'
    ]);
});

it('can show menu', function () {

    $cashier = User::factory()->create(['role' => 'cashier']);
    $token = $cashier->createToken('test')->plainTextToken;

    $menu = Menu::factory()->create();

    $response = $this->withHeader('Authorization', "Bearer $token")
                     ->getJson("/api/menus/{$menu->id}");

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'id' => $menu->id
             ]);
});

it('can update menu', function () {

    $cashier = User::factory()->create(['role' => 'cashier']);
    $token = $cashier->createToken('test')->plainTextToken;

    $menu = Menu::factory()->create();

    $data = [
        'category_id' => $menu->category_id,
        'name' => 'Updated Menu',
        'price' => 50000
    ];

    $response = $this->withHeader('Authorization', "Bearer $token")
                     ->putJson("/api/menus/{$menu->id}", $data);

    $response->assertStatus(200);

    $this->assertDatabaseHas('menus', [
        'name' => 'Updated Menu'
    ]);
});

it('can delete menu', function () {

    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('test')->plainTextToken;

    $menu = Menu::factory()->create();

    $response = $this->withHeader('Authorization', "Bearer $token")
                     ->deleteJson("/api/menus/{$menu->id}");

    $response->assertNoContent();

    $this->assertSoftDeleted('menus', [
        'id' => $menu->id
    ]);
});