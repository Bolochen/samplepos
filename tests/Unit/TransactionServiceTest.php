<?php

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Repositories\Contracts\TransactionItemsRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

uses(TestCase::class);

it('retrieves all transactions', function() {
    $transactions = new LengthAwarePaginator([
        ['id' => 1, 'notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1],
        ['id' => 2, 'notransaction' => '20260313002', 'date'=>'2026-03-13','user_id'=>1],
    ],20,1);

    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $repo->shouldReceive('getAllTransactions')
        ->once()
        ->andReturn($transactions);

    $service = new TransactionService($repo, $repo2);
    $result = $service->getAllTransactions();

    expect($result)->toEqual($transactions);
});

it('retrieves filtered transactions', function() {
    $transactions = new LengthAwarePaginator([
        ['id' => 1, 'notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1],
        ['id' => 2, 'notransaction' => '20260313002', 'date'=>'2026-03-13','user_id'=>1],
    ],20,1);

    $filters = ['user_id' => 1];

    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $repo->shouldReceive('getAllTransactions')
        ->once()
        ->with($filters)
        ->andReturn($transactions);

    $service = new TransactionService($repo, $repo2);
    $result = $service->getAllTransactions($filters);

    expect($result)->toEqual($transactions);
});

it('retrieve a transaction by id', function() {
    $transaction = new Transaction(
        ['id' => 1, 'notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1]);

    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $repo->shouldReceive('getTransactionById')
        ->once()
        ->with(1)
        ->andReturn($transaction);

    $service = new TransactionService($repo, $repo2);
    $result = $service->getTransactionById(1);

    expect($result)->toEqual($transaction);
});

it('failed to retrieve a transaction by id', function() {
    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $repo->shouldReceive('getTransactionById')
        ->once()
        ->with(1)
        ->andThrow(new ModelNotFoundException());

    $service = new TransactionService($repo, $repo2);

    expect(fn() => $service->getTransactionById(1))->toThrow(ModelNotFoundException::class);
});

it('create a transaction', function() {
    $data = ['notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1];
    $transaction = new Transaction(
        ['id' => 1, ...$data]);

    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $repo->shouldReceive('createTransaction')
        ->once()
        ->with($data)
        ->andReturn($transaction);

    $service = new TransactionService($repo, $repo2);
    $result = $service->createTransaction($data);

    expect($result)->toEqual($transaction);
});

it('update a transaction', function() {
    $data = ['notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1];
    $transaction = new Transaction(
        ['id' => 1, ...$data]);

    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $repo->shouldReceive('updateTransaction')
        ->once()
        ->with(1,$data)
        ->andReturn($transaction);

    $service = new TransactionService($repo, $repo2);
    $result = $service->updateTransaction(1,$data);

    expect($result)->toEqual($transaction);
});

it('delete a transaction', function() {
    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    
    $repo2->shouldReceive('checkPreparedTransactionItems')
        ->once()
        ->with(1)
        ->andReturnFalse();
    
    $repo->shouldReceive('deleteTransaction')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $service = new TransactionService($repo, $repo2);
    $result = $service->deleteTransaction(1);

    expect($result)->toBeTrue();
});

it('cancel payment', function(){
    $data =  ['id' => 1, 'notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1,'status' => 'unpaid'];

    $transaction = new Transaction(
        [$data, 'status' => 'unpaid']);

    $cancelled = new Transaction(
        [$data, 'status' => 'cancelled']);

    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    
    $repo2->shouldReceive('checkPreparedTransactionItems')
        ->once()
        ->with(1)
        ->andReturnFalse();
    
    $repo->shouldReceive('getTransactionById')
        ->once()
        ->with(1)
        ->andReturn($transaction);

    $repo->shouldReceive('cancelTransaction')
        ->once()
        ->with(1)
        ->andReturn($cancelled);

    $service = new TransactionService($repo, $repo2);
    $result = $service->cancelTransaction(1,'cashier');

    expect($result)->toEqual($cancelled);
});

it('failed to cancel not an unpaid payment', function(){
    $data =  ['id' => 1, 'notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1];

    $transaction = new Transaction(
        [$data, 'status' => 'cancelled']);

    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);
    $repo->shouldReceive('getTransactionById')
        ->once()
        ->with(1)
        ->andReturn($transaction);

    $repo->shouldNotReceive('cancelTransaction')
        ->with(1)
        ->andThrow(new Exception());

    $service = new TransactionService($repo, $repo2);

    expect(fn() => $service->cancelTransaction(1,'cashier'))->toThrow(Exception::class);
});

it('split transaction items', function(){
    $items = new Collection([
        new TransactionItem(['transaction_id' => 1, 
            'menu_item_id' => 1, 'quantity' => 3]),
        new TransactionItem(['transaction_id' => 1, 
            'menu_item_id' => 3, 'quantity' => 1]),
    ]);

    $transaction = new Transaction(['id' => 1, 'notransaction' => '20260313001', 'date'=>'2026-03-13','user_id'=>1, 'status' => 'unpaid']);

    $transaction->setRelation('transactionItems', $items);

    $splittedItems = [
        [
            'transaction_id' => 1,
            'menu_item_id' => 1,
            'quantity' => 1,
            'price' => null,
            'kitchen_status' => null,
            'serving_status' => null,
            'type' => null,
            'notes' => null,
        ],
        [
            'transaction_id' => 1,
            'menu_item_id' => 1,
            'quantity' => 1,
            'price' => null,
            'kitchen_status' => null,
            'serving_status' => null,
            'type' => null,
            'notes' => null,
        ],
    ];

    $repo = \Mockery::mock(TransactionRepositoryInterface::class);
    $repo->shouldReceive('getTransactionById')
        ->once()
        ->with(1)
        ->andReturn($transaction);

    $repo2 = \Mockery::mock(TransactionItemsRepositoryInterface::class);

    $repo2->shouldReceive('bulkInsert')
        ->once()
        ->with($splittedItems)
        ->andReturnTrue();

    $repo2->shouldReceive('updateSplitQuantities')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $service = new TransactionService($repo, $repo2);
    $result = $service->split(1, 'cashier');

    expect($result)->toBeTrue();
});


