<?php

use App\Models\Table;
use App\Repositories\Contracts\TableRepositoryInterface;
use App\Services\TableService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

uses(TestCase::class);

it('retrieves all tables', function() {
    $tables = new Collection([
       ['id' => 1, "name" => "table1"],
       ['id' => 2, "name" => "table2"],
    ]);

    $repo = \Mockery::mock(TableRepositoryInterface::class);
    $repo->shouldReceive('getAllTables')
        ->once()
        ->andReturn($tables);

    $service = new TableService($repo);

    $result = $service->getAllTables();

    expect($result)->toEqual($tables);
});

it('retrieves filtered tables', function() {
    $tables = new Collection([
        ['id' => 1, "name" => "table1"],
        ['id' => 2, "name" => "table2"],
    ]);

    $repo = \Mockery::mock(TableRepositoryInterface::class);
    $repo->shouldReceive('getAllTables')
        ->once()
        ->with(['name' => 'table'])
        ->andReturn($tables);

    $service = new TableService($repo);

    $result = $service->getAllTables(['name' => 'table']);

    expect($result)->toEqual($tables);
});

it('retrieve a table by id', function() {
    $table = new Table(['id' => 1, "name" => "table1"]);

    $repo = \Mockery::mock(TableRepositoryInterface::class);
    $repo->shouldReceive('getTableById')
        ->once()
        ->with(1)
        ->andReturn($table);

    $service = new TableService($repo);
    $result = $service->getTableById(1);

    expect($result)->toEqual($table);
});

it('retrieve a table by id but not exists', function() {
    $repo = \Mockery::mock(TableRepositoryInterface::class);
    $repo->shouldReceive('getTableById')
        ->once()
        ->with(1)
        ->andThrow(new ModelNotFoundException());

    $service = new TableService($repo);

    $this->expectException(ModelNotFoundException::class);

    $service->getTableById(1);
});

it('update a table', function() {
    $table = new Table(
        ['id' => 1, 'name' => 'table update', 'status' => 'reserved']
    );
    
    $repo = \Mockery::mock(TableRepositoryInterface::class);
    $repo->shouldReceive('updateTable')
        ->once()
        ->with(1,['name' => 'table update', 'status' => 'reserved'])
        ->andReturn($table);

    $service = new TableService($repo);
    $result = $service->updateTable(1,['name' => 'table update', 'status' => 'reserved']);

    expect($result)->toEqual($table);
});

it('update a table but table not exists', function() {    
    $repo = \Mockery::mock(TableRepositoryInterface::class);
    $repo->shouldReceive('updateTable')
        ->once()
        ->with(1,['name' => 'table update', 'status' => 'reserved'])
        ->andThrow(new ModelNotFoundException());

    $service = new TableService($repo);

    $this->expectException(ModelNotFoundException::class);

    $service->updateTable(1,['name' => 'table update', 'status' => 'reserved']);
});

it('delete a table', function() {
    $repo = \Mockery::mock(TableRepositoryInterface::class);
    $repo->shouldReceive('deleteTable')
        ->once()
        ->with(1)
        ->andReturnTrue();

    $service = new TableService($repo);
    $result = $service->deleteTable(1);

    expect($result)->toBeTrue();
});