<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository; 
    }

    public function getAllCategories(array $filters = [])
    {
        return $this->categoryRepository->getAllCategories($filters);
    }

    public function getCategoryById(int $id)
    {
        return $this->categoryRepository->getCategoryById($id);
    }

    public function createCategory(array $data)
    {
        $exists = $this->categoryRepository->checkUniqueCategory($data['name'], null);

        if($exists){
            throw new DomainException("Category name must be unique");
        }

        return $this->categoryRepository->createCategory($data);
    }

    public function updateCategory(int $id, array $data)
    {
        $exists = $this->categoryRepository->checkUniqueCategory($data['name'], $id);

        if($exists){
            throw new DomainException("New category name has been taken");
        }

        return $this->categoryRepository->updateCategory($id, $data);
    }

    public function deleteCategory(int $id, User $user)
    {
        if (! $user || $user->role !== 'admin') {
            throw new AuthorizationException('Only admin users can delete categories.');
        }

        return $this->categoryRepository->deleteCategory($id);
    }
}