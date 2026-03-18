<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getAllTransactions(array $filters = []): LengthAwarePaginator
    {
        return Transaction::
            when($filters['notransaction'] ?? null, fn ($q, $v) =>
                $q->where('notransaction', 'like', "%{$v}%")
            )
            ->when($filters['status'] ?? null, fn ($q, $v) =>
                $q->where('status', $v)
            )
            ->when($filters['table_id'] ?? null, fn ($q, $v) =>
                $q->where('table_id', $v)
            )
            ->when($filters['shift_id'] ?? null, fn ($q, $v) =>
                $q->where('shift_id', $v)
            )
            ->when($filters['user_id'] ?? null, fn ($q, $v) =>
                $q->where('user_id', $v)
            )
            ->when($filters['start_date'] ?? null && $filters['end_date'] ?? null,
                fn ($q) => $q->whereBetween('date', [
                    $filters['start_date'],
                    $filters['end_date']
                ])
            )
            ->paginate($filters['perPage'] ?? 20);
    }

    public function getTransactionById(int $id): ?Transaction
    {
        return Transaction::with('transactionItems')->findOrFail($id);
    }

    public function createTransaction(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function updateTransaction(int $id, array $data): Transaction
    {
        $transaction = Transaction::with('transactionItems')->findOrFail($id);
        $transaction->update($data);

        return $transaction;
    }

    public function deleteTransaction(int $id): bool
    {
        $transaction = Transaction::with('transactionItems')->findOrFail($id);
        return $transaction->delete();
    }

    public function cancelTransaction(int $id): bool
    {
         return Transaction::where('id', $id)->update(['status' => 'cancelled']);
    }
}