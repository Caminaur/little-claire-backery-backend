<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(
            Category::query()->orderBy('position')->paginate(20)
        );
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent(204, []);
    }
}
