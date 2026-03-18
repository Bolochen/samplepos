<?php

namespace App\Services;

use App\Repositories\Contracts\TransactionItemsRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Exception;

class TransactionItemService
{
    protected $transactionItemRepository, $transactionRepository;

    public function __construct(
        TransactionItemsRepositoryInterface $transactionItemRepository,
        TransactionRepositoryInterface $transactionRepository
    )
    {
        $this->transactionItemRepository = $transactionItemRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function getAllTransactionItems(array $data = [])
    {
        return $this->transactionItemRepository->getTransactionItems($data);
    }

    public function getTransactionItemById(int $id)
    {
        return $this->transactionItemRepository->getTransactionItemById($id);
    }

    public function getTransactionItemsByTransactionId(int $transaction_id)
    {
        return $this->transactionItemRepository->getTransactionItemsByTransactionId($transaction_id);
    }

    public function createTransactionItem(array $data)
    {
        return $this->transactionItemRepository->createTransactionItem($data);
    }

    public function updateTransactionItem(int $id, array $data)
    {
        return $this->transactionItemRepository->updateTransactionItem($id, $data);
    }

    public function deleteTransactionItem($id, $role)
    {
        $item = $this->getTransactionItemById($id);

        $transactionHead = $this->transactionRepository
            ->getTransactionById($item->transaction_id);

        if($transactionHead->status === 'paid' && $role != 'admin'){
            throw new Exception('This transaction already been paid');
        }

        if($item->serving_status == 'served' && $role != 'admin'){
            throw new Exception('This item already been served');
        }

        if($item->kitchen_status == 'ready' && $role != 'admin'){
            throw new Exception('This item already ready to serve');
        }

        return $this->transactionItemRepository->deleteTransactionItem($id);
    }
}