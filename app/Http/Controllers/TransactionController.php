<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Services\TransactionItemService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
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
        $filters = $request->validate([
            'notransaction' => 'nullable|string',
            'status' => 'nullable|in:unpaid,paid,cancelled',
            'table_id' => 'nullable|exists:tables,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'user_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        return response()->json($this->transactionService
            ->getAllTransactions($filters)); 
    }

    public function show(int $id)
    {
        return response()->json($this->transactionService
            ->getTransactionById($id)); 
    }

    public function store(TransactionRequest $request)
    {
        return response()->json($this->transactionService
            ->createTransaction($request->validated()),201);
    }

    public function update(int $id, TransactionRequest $request)
    {
        return response()->json($this->transactionService
            ->updateTransaction($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        return response()->json($this->transactionService
            ->deleteTransaction($id),204);
    }

    public function cancel(int $id)
    {
        $role = auth()->user()->role;
        return response()->json($this->transactionService
            ->cancelTransaction($id, $role));
    }

    public function check(int $id)
    {
        // check status
        // check payment
        // 
    }

    public function split(int $id)
    {
        $role = auth()->user()->role;
        return response()->json($this->transactionService
            ->split($id, $role));
    }
}