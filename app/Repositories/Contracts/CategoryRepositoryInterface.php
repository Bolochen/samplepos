<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface
{
    public function getAllCategories(array $filters=[]);

    public function getCategoryByid(int $id);

    public function checkUniqueCategory(string $name, ?int $id);

    public function createCategory(array $data);

    public function updateCategory(int $id, array $data);

    public function deleteCategory(int $id);
}