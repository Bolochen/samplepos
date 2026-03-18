<?php

namespace App\Repositories\Contracts;

use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

interface TransactionRepositoryInterface
{
    public function getAllTransactions(array $filters = []): LengthAwarePaginator;
    public function getTransactionById(int $id): ?Transaction;
    public function createTransaction(array $data): Transaction;
    public function updateTransaction(int $id, array $data): Transaction;
    public function deleteTransaction(int $id): bool;
    public function cancelTransaction(int $id): bool;
}