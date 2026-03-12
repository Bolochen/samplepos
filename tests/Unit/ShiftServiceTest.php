<?php

use App\Models\Shift;
use App\Repositories\Contracts\ShiftRepositoryInterface;
use App\Services\ShiftService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class);

it('retrieve all shifts', function() {
    $shifts = new LengthAwarePaginator([
        [
            'id' => 1, 
            'user_id' => 1, 
            'start_time' => '2026-03-12 09:27:10',
            'end_time' => '2026-03-12 11:27:10',
            'opening_cash' => 350000,
            'closing_cash' => 350000,
            'expected_cash' => 350000,
            'difference' => 0,
            'status' => 'closed'
        ],
        [
            'id' => 2, 
            'user_id' => 1, 
            'start_time' => '2026-03-13 09:27:10',
            'end_time' => NULL,
            'opening_cash' => 350000,
            'closing_cash' => NULL,
            'expected_cash' => NULL,
            'difference' => NULL,
            'status' => 'open'
        ],
    ], 20, 1);

    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    $repo->shouldReceive('getAllShifts')
        ->once()
        ->andReturn($shifts);

    $service = new ShiftService($repo);
    $result = $service->getAllShifts();

    expect($result)->toEqual($shifts);
});

it('retrieve filtered shifts', function(){
    $shifts = new LengthAwarePaginator([
        [
            'id' => 1, 
            'user_id' => 1, 
            'start_time' => '2026-03-12 09:27:10',
            'end_time' => '2026-03-12 11:27:10',
            'opening_cash' => 350000,
            'closing_cash' => 350000,
            'expected_cash' => 350000,
            'difference' => 0,
            'status' => 'closed'
        ],
    ], 20, 1);

    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    $repo->shouldReceive('getAllShifts')
        ->once()
        ->with(['status' => 'closed'])
        ->andReturn($shifts);

    $service = new ShiftService($repo);
    $result = $service->getAllShifts(['status' => 'closed']);

    expect($result)->toEqual($shifts);
});

it('retrieve shift by id', function() {
    $shift = new Shift([
            'id' => 1, 
            'user_id' => 1, 
            'start_time' => '2026-03-12 09:27:10',
            'end_time' => '2026-03-12 11:27:10',
            'opening_cash' => 350000,
            'closing_cash' => 350000,
            'expected_cash' => 350000,
            'difference' => 0,
            'status' => 'closed']
    );

    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    $repo->shouldReceive('getShiftById')
        ->once()
        ->with(1)
        ->andReturn($shift);

    $service = new ShiftService($repo);
    $result = $service->getShiftById(1);

    expect($result)->toEqual($shift);
});

it('retrieve shift by id but not found', function() {
    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    $repo->shouldReceive('getShiftById')
        ->once()
        ->with(1)
        ->andThrow(new ModelNotFoundException());

    $service = new ShiftService($repo);

    $this->expectException(ModelNotFoundException::class);
    
    $service->getShiftById(1);
});

it('updates a shift', function () {
    $data = [
        'end_time' => '2026-03-12 11:27:10',
        'opening_cash' => 350000,
        'closing_cash' => 350000,
        'expected_cash' => 350000,
        'difference' => 0,
        'status' => 'closed'
    ];

    $shift = new Shift([
        'id' => 1,
        ...$data
    ]);

    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    
    $repo->shouldReceive('updateShift')
        ->once()
        ->with(1, $data)
        ->andReturn($shift);

    $service = new ShiftService($repo);

    expect($service->updateShift(1, $data))
        ->toEqual($shift);
});

it('update a shift but not exist shift', function() {
    $data = [
        'end_time' => '2026-03-12 11:27:10',
        'opening_cash' => 350000,
        'closing_cash' => 350000,
        'expected_cash' => 350000,
        'difference' => 0,
        'status' => 'closed'
    ];

    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    $repo->shouldReceive('updateShift')
        ->once()
        ->with(1, $data)
        ->andThrow(new ModelNotFoundException());

    $service = new ShiftService($repo);
    $this->expectException(ModelNotFoundException::class);

    $service->updateShift(1, $data);
});

it('delete a shift', function() {
    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    $repo->shouldReceive('deleteShift')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $service = new ShiftService($repo);

    $result = $service->deleteShift(1);

    expect($result)->toBeTrue();
});

it('delete a shift but not found', function() {
    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    $repo->shouldReceive('deleteShift')
        ->once()
        ->with(1)
        ->andThrow(new ModelNotFoundException());

    $service = new ShiftService($repo);

    expect(fn () => $service->deleteShift(1))
        ->toThrow(ModelNotFoundException::class);
});

it('retrieve open shifts by user', function() {
    $shift = new Collection([
            'id' => 1, 
            'user_id' => 1, 
            'start_time' => '2026-03-12 09:27:10',
            'end_time' => '2026-03-12 11:27:10',
            'opening_cash' => 350000,
            'closing_cash' => 350000,
            'expected_cash' => 350000,
            'difference' => 0,
            'status' => 'closed']
    );

    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    $repo->shouldReceive('getOpenShiftByUser')
        ->once()
        ->with(1)
        ->andReturn($shift);

    $service = new ShiftService($repo);
    $result = $service->getOpenShiftByUser(1);

    expect($result)->toEqual($shift);
});

it('close shift', function() {
    Carbon::setTestNow('2026-03-12 12:00:00');
    $shift = new Shift([
        'id' => 1,
        'user_id' => 1,
        'start_time' => '2026-03-12 09:27:10',
        'opening_cash' => 35000,
        'status' => 'open'
    ]);

    $data = [
        'end_time' => Carbon::parse('2026-03-12 12:00:00'),
        'closing_cash' => 35000,
        'expected_cash' => 35000,
        'difference' => 0,
        'status' => 'closed'
    ];

    $repo = \Mockery::mock(ShiftRepositoryInterface::class);
    $repo->shouldReceive('getShiftById')
        ->once()
        ->with(1)
        ->andReturn($shift);

    $repo->shouldReceive('closeShift')
        ->once()
        ->with(1, $data)
        ->andReturn($shift);;

    $service = new ShiftService($repo);
    $result = $service->closeShift(1, 35000);

    expect($result)->toEqual($shift);
});
