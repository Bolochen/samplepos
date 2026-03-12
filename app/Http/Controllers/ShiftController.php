<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShiftRequest;
use App\Services\ShiftService;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    protected $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function index(Request $request)
    {
        $filter = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        return response()->json($this->shiftService
            ->getAllShifts($filter));
    }

    public function show(int $id)
    {
        return response()->json($this->shiftService
            ->getShiftById($id));
    }

    public function store(ShiftRequest $request)
    {
        $data = $request->validated();

        $data['start_time'] = now();
        $data['user_id'] = auth()->id();

        $shift = $this->shiftService->createShift($data);

        return response()->json($shift,201);
    }

    public function update(int $id, ShiftRequest $request)
    {
        $data = $request->validated();
        
        $shift = $this->shiftService->updateShift($id, $data);

        return response()->json($shift);
    }

    public function destroy(int $id)
    {
        return response()->json(
            $this->shiftService->deleteShift($id),204);
    }

    public function getOpenShiftByUser(Request $request)
    {
        $data = $request->validate(
            ['user_id' => 'required|exists:users,id']);

        return response()->json(
            $this->shiftService->getOpenShiftByUser($data['user_id']));
    }

    public function closeShift(int $id, Request $request)
    {
        $data = $request->validate(
            ['closing_cash' => 'required|numeric']);
            
        return response()->json(
            $this->shiftService->closeShift($id, $data['closing_cash']));
    }
}