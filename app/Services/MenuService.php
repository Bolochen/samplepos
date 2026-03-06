<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\MenuRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class MenuService
{
    protected MenuRepositoryInterface $menuRepository;

    public function __construct(MenuRepositoryInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function getAllMenus(array $filters = [])
    {
        return $this->menuRepository->getAllMenus($filters);
    }

    public function getMenuById($id)
    {
        return $this->menuRepository->getMenuById($id);
    }

    public function createMenu(array $data)
    {
        return $this->menuRepository->createMenu($data);
    }

    public function updateMenu($id, array $data)
    {
        return $this->menuRepository->updateMenu($id, $data);
    }

    public function deleteMenu($id, ?User $user = null)
    {
        $user ??= auth()->user();

        if (! $user || $user->role !== 'admin') {
            throw new AuthorizationException('Only admin users can delete menus.');
        }

        return $this->menuRepository->deleteMenu($id);
    }
}

