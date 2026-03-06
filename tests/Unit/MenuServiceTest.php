<?php

use App\Models\User;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Services\MenuService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;

uses(Tests\TestCase::class);

it('retrieves all menus', function() {
    $menus = [
        (object) ['id' => 1, 'name' => 'Menu 1'],
        (object) ['id' => 2, 'name' => 'Menu 2'],
    ];

    $repo = Mockery::mock(MenuRepositoryInterface::class);
    $repo->shouldReceive('getAllMenus')
        ->once()
        ->andReturn($menus);

    $service = new MenuService($repo);

    $result = $service->getAllMenus();

    expect($result)->toEqual($menus);
});

it('retrieve a menu by id', function() {
    $menu = (object) ['id' => 1, 'name' => 'Menu 1'];

    $repo = Mockery::mock(MenuRepositoryInterface::class);
    $repo->shouldReceive('getMenuById')
        ->once()
        ->with(1)
        ->andReturn($menu);

    $service = new MenuService($repo);

    $result = $service->getMenuById(1);

    expect($result)->toEqual($menu);
});

it('retrieve a menu by id and throws exception if not found', function() {
    $repo = Mockery::mock(MenuRepositoryInterface::class);
    $repo->shouldReceive('getMenuById')
        ->once()
        ->with(999)
        ->andThrow(new ModelNotFoundException());

    $service = new MenuService($repo);

    $this->expectException(ModelNotFoundException::class);

    $service->getMenuById(999);
});

it('create a new menu', function() {
    $menuData = ['name' => 'New Menu'];
    $createdMenu = (object) ['id' => 1, 'name' => 'New Menu'];

    $repo = Mockery::mock(MenuRepositoryInterface::class);
    $repo->shouldReceive('createMenu')
        ->once()
        ->with($menuData)
        ->andReturn($createdMenu);

    $service = new MenuService($repo);

    $result = $service->createMenu($menuData);

    expect($result)->toEqual($createdMenu);
});

it('update a menu', function() {
    $createdMenu = (object) ['id' => 1, 'name' => 'New Menu'];
    $updatedMenu = (object) ['id' => 1, 'name' => 'Updated Menu'];

    $repo = Mockery::mock(MenuRepositoryInterface::class);
    $repo->shouldReceive('updateMenu')
        ->once()
        ->with(1, ['name' => 'Updated Menu'])
        ->andReturn($updatedMenu);

    $service = new MenuService($repo);

    $result = $service->updateMenu(1, ['name' => 'Updated Menu']);

    expect($result)->toEqual($updatedMenu);
});

it('allow admin users to delete a menu', function () {
    $admin = User::factory()->make(['role' => 'admin']);

    $this->actingAs($admin);

    $menuId = 10;

    $repo = Mockery::mock(MenuRepositoryInterface::class);
    $repo->shouldReceive('deleteMenu')
        ->once()
        ->with($menuId)
        ->andReturnTrue();

    $service = new MenuService($repo);

    $result = $service->deleteMenu($menuId, $admin);

    expect($result)->toBeTrue();
});

it('prevents non admin to delete a menu', function () {
    $cashier = User::factory()->make(['role' => 'cashier']);

    $this->actingAs($cashier);

    $menuId = 1;

    $repo = Mockery::mock(MenuRepositoryInterface::class);

    $service = new MenuService($repo);

    $this->expectException(AuthorizationException::class);

    $service->deleteMenu($menuId, $cashier);
});