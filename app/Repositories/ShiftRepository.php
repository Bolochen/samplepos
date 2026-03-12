<?php

namespace App\Repositories;

use App\Models\Shift;
use App\Repositories\Contracts\ShiftRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ShiftRepository implements ShiftRepositoryInterface
{
    public function getAllShifts(array $filters = []): LengthAwarePaginator
    {
        return Shift::with('user')
            ->when($filters['status'] ?? null, fn($q, $v) =>
                $q->where('status', $v)
            )
            ->when(
                !empty($filters['start_date']) && !empty($filters['end_date']),
                fn($q) => $q->whereBetween('start_time', [
                    $filters['start_date'],
                    $filters['end_date']
                ])
            )
            ->when(isset($filters['difference']) && $filters['difference'] !== '',
                fn($q) => $q->where('difference', '!=', 0)
            )
            ->paginate($filters['perPage'] ?? 20);
    }

    public function getShiftById(int $id): ?Shift
    {
        return Shift::findOrFail($id);
    }

    public function createShift(array $data): Shift
    {
        return Shift::create($data);
    }

    public function updateShift(int $id, array $data): Shift
    {
        $shift = Shift::findOrFail($id);
        $shift->update($data);

        return $shift;
    }

    public function deleteShift($id): bool
    {
        $shift = Shift::findOrFail($id);

        return $shift->delete();
    }

    public function getOpenShiftByUser(int $userId): Collection
    {
        return Shift::where('user_id', $userId)
            ->where('status', 'open')->get();
    }

    public function closeShift(int $id, array $data): Shift
    {
        $shift = Shift::findOrFail($id);
        $shift->update($data);

        return $shift;
    }
}