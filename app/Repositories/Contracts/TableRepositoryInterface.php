<?php

namespace App\Repositories\Contracts;

use App\Models\Table;
use Illuminate\Database\Eloquent\Collection;

interface TableRepositoryInterface
{
    public function getAllTables(array $filters=[]): Collection;

    public function getTableById(int $id): ?Table;

    public function createTable(array $data): Table;

    public function updateTable(int $id, array $data): Table;

    public function deleteTable(int $id): bool;
}