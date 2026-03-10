<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories(array $filters = [])
    {
        return Category::when($filters['name'] ?? null, 
                fn($q,$name) => $q->where('name', 'like' , "%{$name}%"))
            ->get();
    }

    public function getCategoryById($id)
    {
        return Category::findOrFail($id);
    }

    public function checkUniqueCategory(string $name, ?int $id)
    {
        return Category::where('name', $name)
            ->when($id, fn($q,$id) => $q->whereNot('id',$id))->exists();
    }

    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function updateCategory($id, array $data)
    {
        $menu = Category::findOrFail($id);
        $menu->update($data);

        return $menu;
    }

    public function deleteCategory($id)
    {
        $menu = Category::findOrFail($id);
        $menu->delete();
    }
}