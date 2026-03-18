<?php

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('retrieve all transaction items', function() {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    TransactionItem::factory()->count(5)->create();

    $response = $this->getJson('/api/transactionItems');

    $response->assertOk()->assertJsonCount(5,'data');
});

it('retrieve transaction item by id', function() {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $transaction_item = TransactionItem::factory()->create();

    $response = $this->getJson('/api/transactionItems/'.$transaction_item->transaction_id);

    $response->assertOk();

    $this->assertDatabaseHas('transaction_items', [
        'transaction_id' => $transaction_item->transaction_id,
        'id' => $transaction_item->id
    ]);
});

it('failed retrieve not found transaction item', function() {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/transactionItems/999');

    $response->assertNotFound();
});