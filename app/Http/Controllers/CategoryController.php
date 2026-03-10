<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $data = $request->validate(['name' => 'nullable|string']);

        return response()->json($this->categoryService->getAllCategories($data));
    }

    public function show($id)
    {
        return response()->json($this->categoryService->getCategoryById($id));
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
    
        return response()->json($this->categoryService->createCategory($data), 201);
    }

    public function update($id, CategoryRequest $request)
    {
        $data = $request->validated();

        return response()->json($this->categoryService->updateCategory($id, $data));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $this->categoryService->deleteCategory($id, $user);

        return response()->json(null, 204);
    }
}