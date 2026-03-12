<?php

use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('get all shifts', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    Shift::factory()->count(3)->create([
        'user_id' => $user->id
    ]);

    $response = $this->getJson('/api/shifts');

    $response->assertOk()->assertJsonCount(3, 'data');
});

it('get filtered shifts', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    Shift::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => 'closed'
    ]);

    $response = $this->getJson('/api/shifts?status=closed');

    $response->assertOk()->assertJsonCount(3, 'data');
});

it('get filter by id', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $shift = Shift::factory()->create([
        'user_id' => $user->id,
        'status' => 'closed'
    ]);

    $response = $this->getJson('/api/shifts/'.$shift->id);
    $response->assertOk();

    $this->assertDatabaseHas('shifts', [
        'user_id' => $user->id,
        'status' => 'closed'
    ]);
});

it('failed to get not exist shift', function(){
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/shifts/999');
    $response->assertStatus(404);
});

it('can create a shift', function(){
    Carbon::setTestNow("2026-03-12 12:00:00");
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user); 

    $data = [
        'user_id' => $user->id,
        'start_time' => now(),
        'opening_cash' => 500000
    ];

    $response = $this->postJson('/api/shifts', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('shifts',$data);
});

it('update a shift', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $shift = Shift::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->putJson('/api/shifts/'. $shift->id, [
        'user_id' => $shift->user_id,
        'start_time' => $shift->start_time,
        'opening_cash' => 500000,
    ]);

    $response->assertOK();

    $this->assertDatabaseHas('shifts', [
        'user_id' => $user->id,
        'opening_cash' => 500000
    ]);
});

it('failed to update non exist shift', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $response = $this->putJson('/api/shifts/999', [
        'user_id' => $user->id,
        'start_time' => now(),
        'opening_cash' => 500000,
    ]);

    $response->assertNotFound();
});

it('delete a shift', function() {
    $user = User::factory()->create(['role' => 'admin']);
    Sanctum::actingAs($user);

    $shift = Shift::factory()->create([
        'user_id' => $user->id,
        'opening_cash' => 500000,
        'start_time' => now()
    ]);

    $response = $this->deleteJson('/api/shifts/'.$shift->id);

    $response->assertNoContent();

    $this->assertDatabaseMissing('shifts',[
        'id' => $shift->id
    ]);
});

it('get all open shifts by user', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    Shift::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => 'open'
    ]);

    $response = $this->getJson('/api/shifts/open?user_id='.$user->id);
    $response->assertOk()->assertJsonCount(3);
});

it('close a shift', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $shift = Shift::factory()->create([
        'user_id' => $user->id,
        'status' => 'open'
    ]);

    $response = $this->patchJson("/api/shifts/$shift->id/close", [
                'closing_cash' => 650000]);

    $response->assertOk();

    $this->assertDatabaseHas('shifts', [
        'opening_cash' => $shift->opening_cash,
        'closing_cash' => 650000,
    ]);
});