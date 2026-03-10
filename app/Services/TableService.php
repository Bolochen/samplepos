<?php

namespace App\Services;

use App\Repositories\Contracts\TableRepositoryInterface;

class TableService
{
    protected $tableRepository;

    public function __construct(TableRepositoryInterface $tableRepository)
    {
        $this->tableRepository = $tableRepository;
    }

    public function getAllTables(array $filters = [])
    {
        return $this->tableRepository->getAllTables($filters);
    }

    public function getTableById(int $id)
    {
        return $this->tableRepository->getTableById($id);
    }

    public function createTable(array $data)
    {
        return $this->tableRepository->createTable($data);
    }

    public function updateTable(int $id, array $data)
    {
        return $this->tableRepository->updateTable($id, $data);
    }

    public function deleteTable(int $id)
    {
        return $this->tableRepository->deleteTable($id);
    }
}