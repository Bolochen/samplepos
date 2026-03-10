<?php

use App\Models\Category;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('can get all tables', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    Table::factory()->count(3)->create();

    $response = $this->getJson('/api/tables');

    $response->assertOk()->assertJsonCount(3);
});

it('can get filtered tables', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    Table::factory()->create(['status' => 'empty']);
    Table::factory()->count(2)->create(['status' => 'reserved']);

    $response = $this->getJson('/api/tables?status=reserved');

    $response->assertOk()->assertJsonCount(2);
});

it('can get table by id', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $table =  Table::factory()->create(['name' => 'meja satu']);

    $response = $this->getJson('/api/tables/'.$table->id);

    $response->assertOk()
        ->assertJson([
            'id' => $table->id,
            'name' => 'meja satu'
        ]);
});

it('can get table by id but table not exist', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/tables/999');

    $response->assertNotFound();
});

it('can create table', function () {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $table = ['name' => 'meja satu'];
    $createdTable = ['name' => 'meja satu', 'status' => 'empty'];

    $response = $this->postJson('/api/tables', $table);

    $response->assertStatus(201);

    $this->assertDatabaseHas('tables',$table);
});

it('validation fail when creating table', function () {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $table = ['status' => 'reserved'];

    $response = $this->postJson('/api/tables', $table);

    $response->assertStatus(422);
});

it('can update a table', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $table = Table::factory()->create(['name' => 'meja satu']);
    $updatedTable = ['name' => 'meja baru', 'status' => 'reserved'];

    $response = $this->putJson('/api/tables/'.$table->id, $updatedTable);

    $response->assertOk();

    $this->assertDatabaseHas('tables', $updatedTable);
});

it('can delete a table', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $table = Table::factory()->create(['name' => 'meja satu']);
    $updatedTable = ['name' => 'meja baru', 'status' => 'reserved'];

    $response = $this->deleteJson('/api/tables/'.$table->id);

    $response->assertNoContent();

    $this->assertDatabaseMissing('tables', $updatedTable);
});