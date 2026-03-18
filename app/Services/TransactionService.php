<?php

namespace App\Services;

use App\Repositories\Contracts\TransactionItemsRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    protected $transactionRepository, $transactionItemRepository;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        TransactionItemsRepositoryInterface $transactionItemRepository    
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->transactionItemRepository = $transactionItemRepository;
    }

    public function getAllTransactions(array $filters = [])
    {
        return $this->transactionRepository->getAllTransactions($filters);
    }

    public function getTransactionById(int $id)
    {
        return $this->transactionRepository->getTransactionById($id);
    }

    public function createTransaction(array $data)
    {
        return $this->transactionRepository->createTransaction($data);
    }

    public function updateTransaction(int $id, array $data)
    {
        return $this->transactionRepository->updateTransaction($id, $data);
    }

    private function ensureNoPreparedItems(int $transactionId): void
    {
        $hasPreparedItems = $this->transactionItemRepository
            ->checkPreparedTransactionItems($transactionId);

        if ($hasPreparedItems) {
            throw new Exception("Prepared items can't be cancelled or deleted");
        }
    }

    public function deleteTransaction(int $id)
    {
        $this->ensureNoPreparedItems($id);

        return $this->transactionRepository->deleteTransaction($id);
    }

    public function cancelTransaction(int $id, string $role)
    {
        $transaction = $this->getTransactionById($id);

        if ($transaction->status === 'cancelled' && $role != 'admin') {
            throw new Exception("Transaction already cancelled");
        }

        if ($transaction->status === 'paid' && $role != 'admin') {
            throw new Exception("Cannot cancel paid transaction");
        }

        $this->ensureNoPreparedItems($id);

        return $this->transactionRepository->cancelTransaction($id);
    }

    public function paidPayment(int $id, string $role)
    {
        return DB::transaction(function () use ($id, $role) {
            $transaction = $this->getTransactionById($id);

            if($transaction->status !== 'unpaid' && $role != 'admin'){
                throw new Exception("Not an unpaid transaction");
            }

            // later payment repository
            $totalpayment = 0;

            if($transaction->total <= $totalpayment){
                return $this->updateTransaction($id, ['status' => 'paid']);
            }

            return $transaction;
        });
    }

    public function split(int $id, string $role): bool
    {
        return DB::transaction(function () use ($id, $role) {
            $transaction = $this->getTransactionById($id);

            if($transaction->status !== 'unpaid' && $role != 'admin'){
                throw new Exception("Not an unpaid transaction");
            }

            $items = $transaction->transactionItems;

            $splits = [];

            foreach ($items as $item) {
                if ($item->quantity > 1) {
                    for($i = 1; $i < $item->quantity;$i++){
                        $splits[] = [
                            'transaction_id' => $item->transaction_id,
                            'menu_item_id' => $item->menu_item_id,
                            'quantity' => 1,
                            'price' => $item->price,
                            'kitchen_status' => $item->kitchen_status,
                            'serving_status' => $item->serving_status,
                            'type' => $item->type,
                            'notes' => $item->notes,
                        ];
                    }
                }
            }

            if(!empty($splits)) {
                $this->transactionItemRepository->bulkInsert($splits);
                $this->transactionItemRepository->updateSplitQuantities($id);
            }

            return true;
        });
    }
}