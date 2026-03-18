<?php

namespace App\Repositories;

use App\Models\TransactionItem;
use App\Repositories\Contracts\TransactionItemsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionItemRepository implements TransactionItemsRepositoryInterface
{
    public function getTransactionItems(array $filters = []): LengthAwarePaginator
    {
        return TransactionItem::
            when($filters['transaction_id'] ?? NULL, 
                fn($q, $v) => $q->where('transaction_id', $v))
            ->when($filters['menu_item_id'] ?? NULL,
                fn($q, $v) => $q->where('menu_item_id', $v))
            ->when($filters['kitchen_status'] ?? NULL,
                fn($q, $v) => $q->where('kitchen_status', $v))
            ->when($filters['serving_status'] ?? NULL,
                fn($q, $v) => $q->where('serving_status', $v))
            ->when($filters['type'] ?? NULL,
                fn($q, $v) => $q->where('type', $v))
            ->when($filters['split'] ?? NULL, function ($q) use ($filters) {
                $q->when($filters['split'] === 'split',
                    fn($q) => $q->whereNotNull('bill_id')
                                ->where('kitchen_status', 'served'))
                ->when($filters['split'] === 'nonsplit',
                    fn($q) => $q->whereNull('bill_id')
                                ->where('kitchen_status', 'served'));
            })
            ->paginate($filters['perPage'] ?? 20);
    }

    public function getTransactionItemById(int $id): ?TransactionItem
    {
        return TransactionItem::findOrFail($id);
    }

    public function getTransactionItemsByTransactionId(int $transaction_id): Collection
    {
        return TransactionItem::where('transaction_id', $transaction_id)->get();
    }

    public function checkPreparedTransactionItems(int $transaction_id): bool
    {
        return TransactionItem::where('transaction_id', $transaction_id)
            ->where('kitchen_status', '!=', 'pending')
            ->orWhere('sering_status', '!=', 'pending')
            ->exists();
    }

    public function createTransactionItem(array $data): TransactionItem
    {
        return TransactionItem::create($data);
    }

    public function bulkInsert(array $data): bool
    {
        return TransactionItem::insert($data);
    }

    public function updateTransactionItem(int $id, array $data): TransactionItem
    {
        $transaction = TransactionItem::findOrFail($id);
        $transaction->update($data);
        return $transaction;
    }

    public function updateSplitQuantities(int $transaction_id): bool
    {
        return TransactionItem::where('transaction_id', $transaction_id)
                ->where('quantity', '>', 1)->update(['quantity' => 1]);;
    }

    public function deleteTransactionItem(int $id): bool
    {
        $transaction = TransactionItem::findOrFail($id);

        return $transaction->delete();
    }
} 