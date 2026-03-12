<?php

namespace App\Repositories\Contracts;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ShiftRepositoryInterface
{
    public function getAllShifts(array $filters = []): LengthAwarePaginator;
    public function getShiftById(int $id): ?Shift;
    public function createShift(array $data): Shift;
    public function updateShift(int $id, array $adata): Shift;
    public function deleteShift($id): bool;
    public function getOpenShiftByUser(int $userId): Collection;
    public function closeShift(int $id, array $data): Shift; 
}