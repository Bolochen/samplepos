<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Repositories\Contracts\MenuRepositoryInterface;

class MenuRepository implements MenuRepositoryInterface
{
    public function getAllMenus(array $filters = [])
    {
        return Menu::with('category')
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['name'] ?? null, fn ($query, $name) => $query->where('name', 'like', "%{$name}%"))
            ->when($filters['min_price'] ?? null, fn ($query, $min) => $query->where('price', '>=', $min))
            ->when($filters['max_price'] ?? null, fn ($query, $max) => $query->where('price', '<=', $max))
            ->get();
    }

    public function getMenuById($id)
    {
        return Menu::with('category')->findOrFail($id);
    }

    public function createMenu(array $data)
    {
        return Menu::create($data);
    }

    public function updateMenu($id, array $data)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($data);

        return $menu;
    }

    public function deleteMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
    }
}
