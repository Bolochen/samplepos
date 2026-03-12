<?php

namespace App\Services;

use App\Repositories\Contracts\ShiftRepositoryInterface;

class ShiftService
{
    protected $shiftRepository;

    public function __construct(ShiftRepositoryInterface $shiftRepository)
    {
        $this->shiftRepository = $shiftRepository;
    }

    public function getAllShifts(array $filters = [])
    {
        return $this->shiftRepository->getAllShifts($filters);
    }

    public function getShiftById(int $id)
    {
        return $this->shiftRepository->getShiftById($id);
    }

    public function createShift(array $data)
    {
        return $this->shiftRepository->createShift($data);
    }

    public function updateShift(int $id, array $data)
    {
        return $this->shiftRepository->updateShift($id, $data);
    }

    public function deleteShift(int $id)
    {
        return $this->shiftRepository->deleteShift($id);
    }

    public function getOpenShiftByUser(int $userId)
    {
        return $this->shiftRepository->getOpenShiftByUser($userId);
    }

    public function closeShift(int $id, float $closingCash)
    {
        $shift = $this->getShiftById($id);

        if ($shift->status === 'closed') {
            throw new \Exception('Shift already closed');
        }

        $cashIn = 0; // nanti dari PaymentRepository
        
        $expectedCash = $shift->opening_cash + $cashIn;
        $difference = $closingCash - $expectedCash;

        $data = [
            'end_time' => now(),
            'closing_cash' => $closingCash,
            'expected_cash' => $expectedCash,
            'difference' => $difference,
            'status' => 'closed'
        ];

        return $this->shiftRepository->closeShift($id, $data);
    }
}