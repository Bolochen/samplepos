<?php

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Repositories\Contracts\TransactionItemsRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\TransactionItemService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

uses(TestCase::class);

it('retrieves all transaction items', function() {
    $items = new LengthAwarePaginator([
        new TransactionItem([
            'transaction_id' => 1,
            'menu_item_id' => 1,
            'quantity' => 3,
            'price' => 3,
            'kitchen_status' => 'ready',
            'serving_status' => 'served',
            'type' => 'order',
            'notes' => NULL,
            'bill_id' => NULL
        ]),
    ],20,1);

    $itemRepo = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $headRepo = \Mockery::mock(TransactionRepositoryInterface::class);
    
    $itemRepo->shouldReceive('getTransactionItems')
            ->once()
            ->andReturn($items);

    $service = new TransactionItemService($itemRepo, $headRepo);
    $result = $service->getAllTransactionItems();

    expect($result)->toEqual($items);
});

it('retrieve transaction item by id', function() {
    $item = new TransactionItem([
        'id' => 1,
        'transaction_id' => 1,
        'menu_item_id' => 1,
        'quantity' => 3,
        'price' => 3,
        'kitchen_status' => 'ready',
        'serving_status' => 'served',
        'type' => 'order',
        'notes' => NULL,
        'bill_id' => NULL
    ]);

    $itemRepo = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $headRepo = \Mockery::mock(TransactionRepositoryInterface::class);
    
    $itemRepo->shouldReceive('getTransactionItemById')
            ->once()
            ->with(1)
            ->andReturn($item);

    $service = new TransactionItemService($itemRepo, $headRepo);

    $result = $service->getTransactionItemById(1);

    expect($result)->toEqual($item);
});

it('retrieve transaction items by transaction id', function() {
    $items = new Collection([
        new TransactionItem([
            'id' => 1,
            'transaction_id' => 1,
            'menu_item_id' => 1,
            'quantity' => 3,
            'price' => 3,
            'kitchen_status' => 'ready',
            'serving_status' => 'served',
            'type' => 'order',
            'notes' => NULL,
            'bill_id' => NULL
        ]),
        new TransactionItem([
            'id' => 2,
            'transaction_id' => 1,
            'menu_item_id' => 1,
            'quantity' => 3,
            'price' => 3,
            'kitchen_status' => 'ready',
            'serving_status' => 'served',
            'type' => 'order',
            'notes' => NULL,
            'bill_id' => NULL
        ]),
    ]);

    $itemRepo = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $headRepo = \Mockery::mock(TransactionRepositoryInterface::class);
    
    $itemRepo->shouldReceive('getTransactionItemsByTransactionId')
            ->once()
            ->with(1)
            ->andReturn($items);

    $service = new TransactionItemService($itemRepo, $headRepo);

    $result = $service->getTransactionItemsByTransactionId(1);

    expect($result)->toEqual($items);
});

it('update a transaction item', function() {
    $data = [
        'transaction_id' => 1,
        'menu_item_id' => 1,
        'quantity' => 3,
        'price' => 3,
        'kitchen_status' => 'preparing',
        'type' => 'order',
        'notes' => NULL,
        'bill_id' => NULL
    ];

    $updatedTransaction = new TransactionItem([
            'id' => 1, $data, 
            'serving_status' => 'preparing',
    ]);

    $itemRepo = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $headRepo = \Mockery::mock(TransactionRepositoryInterface::class);

    $itemRepo->shouldReceive('updateTransactionItem')
            ->once()
            ->with(1, $data)
            ->andReturn($updatedTransaction);

    $service = new TransactionItemService($itemRepo, $headRepo);

    $result = $service->updateTransactionItem(1, $data);

    expect($result)->toEqual($updatedTransaction);
});

it('delete a transaction item', function() {
    $item = new TransactionItem([
        'id' => 1,
        'transaction_id' => 1,
        'menu_item_id' => 1,
        'quantity' => 3,
        'price' => 3,
        'kitchen_status' => 'preparing',
        'serving_status' => 'pending',
        'type' => 'order',
        'notes' => NULL,
        'bill_id' => NULL
    ]);

    $transaction = new Transaction(
        ['id' => 1, 'notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1,'status' => 'unpaid']);

    $itemRepo = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $headRepo = \Mockery::mock(TransactionRepositoryInterface::class);

    $itemRepo->shouldReceive('getTransactionItemById')
            ->once()
            ->with(1)
            ->andReturn($item);

    $headRepo->shouldReceive('getTransactionById')
            ->once()
            ->with(1)
            ->andReturn($transaction);

    $itemRepo->shouldReceive('deleteTransactionItem')
            ->once()
            ->with(1)
            ->andReturnTrue();

    $service = new TransactionItemService($itemRepo, $headRepo);

    $result = $service->deleteTransactionItem(1, 'cashier');

    expect($result)->toBeTrue();
});

it('failed to delete a prepared transaction item', function() {
    $item = new TransactionItem([
        'id' => 1,
        'transaction_id' => 1,
        'menu_item_id' => 1,
        'quantity' => 3,
        'price' => 3,
        'kitchen_status' => 'ready',
        'serving_status' => 'pending',
        'type' => 'order',
        'notes' => NULL,
        'bill_id' => NULL
    ]);

    $transaction = new Transaction(
        ['id' => 1, 'notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1,'status' => 'unpaid']);

    $itemRepo = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $headRepo = \Mockery::mock(TransactionRepositoryInterface::class);

    $itemRepo->shouldReceive('getTransactionItemById')
            ->once()
            ->with(1)
            ->andReturn($item);

    $headRepo->shouldReceive('getTransactionById')
            ->once()
            ->with(1)
            ->andReturn($transaction);

    $itemRepo->shouldNotReceive('deleteTransactionItem');

    $service = new TransactionItemService($itemRepo, $headRepo);

    expect(fn() => $service->deleteTransactionItem(1, 'cashier'))
        ->toThrow(Exception::class);
});