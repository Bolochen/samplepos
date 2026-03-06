<?php

namespace App\Repositories\Contracts;

interface MenuRepositoryInterface
{
    public function getAllMenus(array $filters = []);

    public function getMenuById($id);

    public function createMenu(array $data);

    public function updateMenu($id, array $data);

    public function deleteMenu($id);
}
