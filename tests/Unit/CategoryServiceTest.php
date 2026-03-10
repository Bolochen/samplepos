<?php

use App\Models\User;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\CategoryService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Tests\TestCase;

uses(TestCase::class);

it('retrieves all categories', function(){
    $categories = [
        ['name' => 'Drink'],
        ['name' => 'Food'],
    ];

    $repo = Mockery::mock(CategoryRepositoryInterface::class);
    $repo->shouldReceive('getAllCategories')
        ->once()
        ->andReturn($categories);

    $service = new CategoryService($repo);

    $result = $service->getAllCategories();

    expect($result)->toEqual($categories);
});

it('retrieves filtered category', function(){
    $categories = [
        ['name' => 'Drink']
    ];

    $repo = Mockery::mock(CategoryRepositoryInterface::class);
    $repo->shouldReceive('getAllCategories')
        ->once()
        ->with(['name' => 'drink'])
        ->andReturn($categories);

    $service = new CategoryService($repo);

    $filters = ['name' => 'drink'];
    $result = $service->getAllCategories($filters);

    expect($result)->toEqual($categories);
});

it('retrieve a category by id', function() {
    $category = ['id' => 1, 'name' => 'Drink'];

    $repo = Mockery::mock(CategoryRepositoryInterface::class);
    $repo->shouldReceive('getCategoryById')
        ->once()
        ->with(1)
        ->andReturn($category);

    $service = new CategoryService($repo);

    $result = $service->getCategoryById(1);

    expect($result)->toEqual($category);
});

it('retrieve a category by id and throw exception if not found', function() {
    $repo = Mockery::mock(CategoryRepositoryInterface::class);
    $repo->shouldReceive('getCategoryById')
        ->once()
        ->with(5)
        ->andThrow(new ModelNotFoundException());

    $service = new CategoryService($repo);

    $this->expectException(ModelNotFoundException::class);

    $service->getCategoryById(5);
});

it('create a new category', function() {
    $category = ['name' => 'Drink'];
    $createdCategory = ['id' => 1, 'name' => 'Drink'];

    $repo = Mockery::mock(CategoryRepositoryInterface::class);

    $repo->shouldReceive('checkUniqueCategory')
        ->once()
        ->with('Drink', null)
        ->andReturn(false);

    $repo->shouldReceive('createCategory')
        ->once()
        ->with($category)
        ->andReturn($createdCategory);

    $service = new CategoryService($repo);
    $result = $service->createCategory($category);

    expect($result)->toEqual($createdCategory);
});

it('create a new category but throw duplicate exception', function() {
    $category = ['name' => 'Drink'];

    $repo = Mockery::mock(CategoryRepositoryInterface::class);
    $repo->shouldReceive('checkUniqueCategory')
        ->once()
        ->with('Drink', NULL)
        ->andReturn(true);

    $service = new CategoryService($repo);

    $this->expectException(DomainException::class);

    $service->createCategory($category);
});

it('update a category', function() {
    $category = ['name' => 'Soft Drink'];
    $updatedCategory = ['id' => 1, 'name' => 'Soft Drink'];

    $repo = Mockery::mock(CategoryRepositoryInterface::class);
    $repo->shouldReceive('checkUniqueCategory')
        ->once()
        ->with('Soft Drink', 1)
        ->andReturn(false);

    $repo->shouldReceive('updateCategory')
        ->once()
        ->with(1, $category)
        ->andReturn($updatedCategory);

    $service = new CategoryService($repo);
    $result = $service->updateCategory(1, $category);

    expect($result)->toEqual($updatedCategory);
});

it('update a category but new name has been taken', function() {
    $repo = Mockery::mock(CategoryRepositoryInterface::class);
    $repo->shouldReceive('checkUniqueCategory')
        ->once()
        ->with('Soft Drink', 1)
        ->andReturn(true);

    $service = new CategoryService($repo);

    $this->expectException(DomainException::class);

    $service->updateCategory(1, ['name' => 'Soft Drink']);
});

it('delete a category and user is an admin', function() {
    $user = User::factory()->make(['role' => 'admin']);

    $repo = Mockery::mock(CategoryRepositoryInterface::class);
    $repo->shouldReceive('deleteCategory')
        ->once()
        ->with(1)
        ->andReturn(true);

    $service = new CategoryService($repo);
    $result = $service->deleteCategory(1, $user);

    expect($result)->toBeTrue();
});

it('delete a category and user is not an admin', function() {
    $user = User::factory()->make(['role' => 'cashier']);

    $repo = Mockery::mock(CategoryRepositoryInterface::class);
    $repo->shouldNotReceive('deleteCategory')
        ->with(1)
        ->andThrow(new AuthorizationException());

    $service = new CategoryService($repo);
    
    $this->expectException(AuthorizationException::class);

    $service->deleteCategory(1, $user);
});