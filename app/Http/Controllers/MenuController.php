<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected MenuService $menuservice;

    public function __construct(MenuService $menuservice)
    {
        $this->menuservice = $menuservice;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['category_id', 'name', 'min_price', 'max_price']);

        return response()->json($this->menuservice->getAllMenus($filters));
    }

    public function show($id)
    {
        return response()->json($this->menuservice->getMenuById($id));
    }

    public function store(ProductRequest $request)
    {
        return response()->json($this->menuservice->createMenu($request->validated()), 201);
    }

    public function update(ProductRequest $request, $id)
    {
        return response()->json($this->menuservice->updateMenu($id, $request->validated()));
    }

    public function destroy($id)
    {
        $this->menuservice->deleteMenu($id);

        return response()->json(null, 204);
    }
}
