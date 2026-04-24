<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\UploadProductImageRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\GenerateMenuPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['variants', 'category']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        return ProductResource::collection($query->orderBy('id')->paginate(20));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $initialPrice = $data['initial_price'] ?? null;
        unset($data['initial_price']);

        $product = Product::create($data);

        if ($initialPrice !== null) {
            $product->variants()->create([
                'label' => null,
                'price' => $initialPrice,
                'position' => 1,
                'is_active' => true,
            ]);
        }

        $product->load(['variants', 'category']);

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function show(Product $product)
    {
        $product->load(['variants', 'category']);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product, GenerateMenuPdfService $pdfService)
    {
        $product->update($request->validated());

        foreach ($product->menus as $menu) {
            $pdfService->handle($menu);
        }

        $product->load(['variants', 'category']);

        return new ProductResource($product);
    }

    public function uploadImage(UploadProductImageRequest $request, Product $product)
    {
        if ($product->image_url && str_starts_with($product->image_url, 'storage/')) {
            Storage::disk('public')->delete(substr($product->image_url, strlen('storage/')));
        }

        $path = $request->file('image')->store('products', 'public');
        $product->update(['image_url' => 'storage/' . $path]);

        return new ProductResource($product->load(['variants', 'category']));
    }

    public function destroy(Product $product, GenerateMenuPdfService $pdfService)
    {
        $menus = $product->menus()->get();
        $product->delete();

        foreach ($menus as $menu) {
            $pdfService->handle($menu);
        }

        return response()->noContent();
    }
}
