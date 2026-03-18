<?php

namespace App\Repositories\Contracts;

use App\Models\TransactionItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TransactionItemsRepositoryInterface
{
    public function getTransactionItems(array $filters = []): LengthAwarePaginator;
    public function getTransactionItemById(int $id): ?TransactionItem;
    public function getTransactionItemsByTransactionId(int $transaction_id): Collection;
    public function checkPreparedTransactionItems(int $transaction_id): bool;
    public function createTransactionItem(array $data): TransactionItem;
    public function bulkInsert(array $data): bool;
    public function updateTransactionItem(int $id, array $data): TransactionItem;
    public function updateSplitQuantities(int $transaction_id): bool;
    public function deleteTransactionItem(int $id): bool;
}