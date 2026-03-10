<?php

namespace App\Repositories;

use App\Models\Table;
use App\Repositories\Contracts\TableRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TableRepository implements TableRepositoryInterface
{
    public function getAllTables(array $filters = []): Collection
    {
        return Table::
            when($filters['name'] ?? null, 
                fn ($q, $name) => $q->where('name', 'like', "%$name%"))
            ->when($filters['status'] ?? null, 
                fn ($q, $status) => $q->where('status', 'like', "%$status%"))   
        ->get();
    }

    public function getTableById(int $id): ?Table
    {
        return Table::findOrFail($id);
    }

    public function createTable(array $data): Table
    {
        return Table::create($data);
    }

    public function updateTable(int $id, array $data): Table
    {
        $table = Table::findOrFail($id);
        $table->update($data);

        return $table;
    }

    public function deleteTable(int $id): bool
    {
        $table = Table::findOrFail($id);
        
        return $table->delete();
    }
}