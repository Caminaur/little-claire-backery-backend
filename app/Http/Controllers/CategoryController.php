<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Requests\Category\UploadCategoryImageRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query()->orderBy('position');

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        return CategoryResource::collection($query->paginate(20));
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

    public function uploadImage(UploadCategoryImageRequest $request, Category $category)
    {
        if ($category->image_url && str_starts_with($category->image_url, 'storage/')) {
            Storage::disk('public')->delete(substr($category->image_url, strlen('storage/')));
        }

        $path = $request->file('image')->store('categories', 'public');
        $category->update(['image_url' => 'storage/' . $path]);

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
