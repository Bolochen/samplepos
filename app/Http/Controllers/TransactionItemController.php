<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionItemRequest;
use App\Services\TransactionItemService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionItemController extends Controller
{
    protected $transactionService, $transactionItemService;

    public function __construct(
        TransactionService $transactionService,
        TransactionItemService $transactionItemService
    )
    {
        $this->transactionService = $transactionService;
        $this->transactionItemService = $transactionItemService;
    }

    public function index(Request $request)
    {
        $filter = $request->validate([
            'transaction_id' => 'nullable|exists:transaction_items,id',
            'menu_item_id' => 'nullable|exists:menus,id',
            'kitchen_status' => 'nullable|in:pending,preparing,ready',
            'serving_status' => 'nullable|in:pending,served',
            'split' => 'nullable|in:split,nonsplit',
            'type' => 'nullable|type:order,preorder',
            'perPage' => 'nullable|numeric|max:50'
        ]);

        return response()->json($this->transactionItemService
            ->getAllTransactionItems($filter));
    }

    public function show(int $id)
    {
        return response()->json($this->transactionItemService
            ->getTransactionItemById($id));
    }

    public function store(TransactionItemRequest $request)
    {
        return response()->json($this->transactionItemService
            ->createTransactionItem($request->validated()));
    }

    public function update(int $id, TransactionItemRequest $request)
    {
        return response()->json($this->transactionItemService
            ->updateTransactionItem($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $role = auth()->user()->role;
        return response()->json($this->transactionItemService
            ->deleteTransaction($id, $role));
    }
}