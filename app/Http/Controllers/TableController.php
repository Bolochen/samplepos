<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableRequest;
use App\Services\TableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    protected $tableService;

    public function __construct(TableService $tableService)
    {
        $this->tableService = $tableService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'name' => 'nullable|string',
            'status' => 'nullable|in:empty,reserved'
        ]);

        return response()->json($this->tableService->getAllTables($filters));
    }

    public function show($id): JsonResponse
    {
        return response()->json($this->tableService->getTableById($id));
    }

    public function store(TableRequest $request)
    {
        return response()->json(
            $this->tableService->createTable($request->validated()),201);
    }

    public function update(int $id, TableRequest $request)
    {
        return response()->json($this->tableService->updateTable(
            $id, $request->validated()));
    }

    public function destroy(int $id)
    {
        return response()->json(
            $this->tableService->deleteTable($id), 204);
    }
}