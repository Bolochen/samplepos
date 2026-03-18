<?php

use App\Models\Shift;
use App\Models\Table;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('can retrieve all transactions', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    Transaction::factory()->count(5)->create();

    $response = $this->getJson('/api/transactions');

    $response->assertOk()->assertJsonCount(5,'data');
});

it('retrieve transaction by id', function() {
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $transaction = Transaction::factory()->create();

    $response = $this->getJson('/api/transactions/'.$transaction->id);

    $response->assertOk()
        ->assertJson([
            'id' => $transaction->id,
            'notransaction' => $transaction->notransaction
        ]);
});

it('create a transaction', function() {
    Carbon::setTestNow(now());
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $shift = Shift::factory()->create();
    $table = Table::factory()->create();

    $data = [
        'notransaction' => 'INV'. date('Ymd') . str_pad(rand(1,1000),4,0, STR_PAD_LEFT),
        'shift_id' => $shift->id,
        'table_id' => $table->id,
        'user_id' => $user->id,
        'date' => now()
    ];

    $response = $this->postJson('/api/transactions', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('transactions',[
        'user_id' => $user->id,
        'table_id' => $table->id,
        'shift_id' => $shift->id,
        'date' => now()->toISOString()
    ]);
});

it('failed to create a transaction', function() {
    Carbon::setTestNow(now());
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $table = Table::factory()->create();

    $data = [
        'notransaction' => 'INV'. date('Ymd') . str_pad(rand(1,1000),4,0, STR_PAD_LEFT),
        'shift_id' => 999,
        'table_id' => $table->id,
        'user_id' => $user->id,
        'date' => now()
    ];

    $response = $this->postJson('/api/transactions', $data);

    $response->assertStatus(422);

    $this->assertDatabaseMissing('transactions',[
        'user_id' => $user->id,
        'table_id' => $table->id,
        'shift_id' => 999,
        'date' => now()->toISOString()
    ]);
});

it('update a transaction', function() {
    Carbon::setTestNow(now());
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $transaction = Transaction::factory()->create();

    $data = [
        'notransaction' => $transaction->notransaction,
        'shift_id' => $transaction->shift_id,
        'table_id' => $transaction->table_id,
        'user_id' => $transaction->id,
        'date' => $transaction->date,
        'subtotal' => 50000,
        'total' => 50000
    ];

    $response = $this->putJson('/api/transactions/'.$transaction->id, $data);

    $response->assertOK()->assertJson([
        'id' => $transaction->id,
        'subtotal' => 50000,
        'total' => 50000
    ]);
});

it('delete a transaction', function() {
    Carbon::setTestNow(now());
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $transaction = Transaction::factory()->create();

    $response = $this->deleteJson('/api/transactions/'.$transaction->id);

    $response->assertNoContent();

    $this->assertDatabaseMissing('transactions',[
        'id' => $transaction->id
    ]);
});

it('failed to delete prepared transactions', function() {
    Carbon::setTestNow(now());
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $transaction = Transaction::factory()->create();
    TransactionItem::factory()->create([
        'transaction_id' => $transaction->id,
        'kitchen_status' => 'ready'
    ]);

    $response = $this->deleteJson('/api/transactions/'.$transaction->id);

    $response->assertStatus(500);
});

it('cancel a transaction', function() {
    Carbon::setTestNow(now());
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $transaction = Transaction::factory()->create();

    $response = $this->postJson('/api/transactions/'.$transaction->id.'/cancel');

    $response->assertOk();
});

it('failed to cancel a transaction', function() {
    Carbon::setTestNow(now());
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $transaction = Transaction::factory()->create(['status' => 'paid']);

    $response = $this->postJson('/api/transactions/'.$transaction->id.'/cancel');

    $response->assertStatus(500);
});

it('split transaction items', function() {
    Carbon::setTestNow(now());
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $transaction = Transaction::factory()->create(['status' => 'unpaid']);
    $item = TransactionItem::factory()
        ->create([
        'transaction_id' => $transaction->id,
        'quantity' => 5
    ]);

    $response = $this->postJson('/api/transactions/'.$transaction->id.'/split');

    $response->assertOk();

    $this->assertDatabaseHas('transaction_items',[
        'id' => $item->id,
        'quantity' => 1
    ]);
});

it('failed to split transaction items', function() {
    Carbon::setTestNow(now());
    $user = User::factory()->create(['role' => 'cashier']);
    Sanctum::actingAs($user);

    $transaction = Transaction::factory()->create(['status' => 'cancelled']);

    $response = $this->postJson('/api/transactions/'.$transaction->id.'/split');

    $response->assertStatus(500);
});