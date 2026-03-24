<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Illuminate\Http\Request;

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
        $data = $request->validated();

        if (isset($data['position'])) {
            Category::where('position', '>=', $data['position'])->increment('position');
        }

        $category = Category::create($data);

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        if (isset($data['position']) && $data['position'] !== $category->position) {
            $newPos = $data['position'];
            $oldPos = $category->position;

            if ($newPos < $oldPos) {
                Category::where('id', '!=', $category->id)
                    ->whereBetween('position', [$newPos, $oldPos - 1])
                    ->increment('position');
            } else {
                Category::where('id', '!=', $category->id)
                    ->whereBetween('position', [$oldPos + 1, $newPos])
                    ->decrement('position');
            }
        }

        $category->update($data);

        return new CategoryResource($category);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:categories,id',
        ]);

        foreach ($request->input('ids') as $position => $id) {
            Category::where('id', $id)->update(['position' => $position + 1]);
        }

        return response()->noContent();
    }

    public function destroy(Category $category)
    {
        $position = $category->position;
        $category->delete();

        Category::where('position', '>', $position)->decrement('position');

        return response()->noContent(204, []);
    }
}
