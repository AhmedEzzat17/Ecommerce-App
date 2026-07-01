<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function format(product $product)
    {
        return [
            'id'          => $product->id,
            'title'       => $product->title,
            'description' => $product->description,
            'price'       => $product->price,
            'Budget_Range'    => $product->Budget_Range,
            'note'        => $product->note,
            'date'        => $product->date,
            'images'      => collect($product->images ?? [])->map(fn($img) => asset('storage/' . $img))->values(),
            'category'    => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name] : null,
        ];
    }

    private function saveImages(Request $request, product $product, $isUpdate = false)
    {
        if (!$request->hasFile('images')) return;

        $images = [];
        if ($isUpdate) {
            if (!empty($product->images)) {
                foreach ($product->images as $oldImg) {
                    Storage::disk('public')->delete($oldImg);
                }
            }
        } else {
            $images = $product->images ?? [];
        }

        foreach ($request->file('images') as $img) {
            $images[] = $img->store('products', 'public');
        }

        $product->update(['images' => $images]);
    }

    public function index(Request $request)
    {
        $query = Product::with('category');
        if ($request->search) $query->where('title', 'like', '%' . $request->search . '%');
        match($request->sort) {
            'price' => $query->orderBy('price'),
            'date'  => $query->orderBy('date'),
            default => $query->latest(),
        };
        return response()->json($query->paginate(10)->through(fn($p) => $this->format($p)));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'Budget_Range'    => 'required|in:Low,Medium,High',
            'note'        => 'nullable|string',
            'date'        => 'nullable|date',
            'images'      => 'nullable|array',
            'images.*'    => 'image|max:2048',
        ]);

        $product = Product::create(
            $request->only(['category_id', 'title', 'description', 'price', 'Budget_Range', 'note', 'date'])
            + ['user_id' => $request->user()->id, 'images' => []]
        );

        $this->saveImages($request, $product);

        return response()->json(['message' => 'Product created', 'product' => $this->format($product->load('category'))], 201);
    }

    public function show(Request $request, product $id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($this->format($product));
    }

    public function update(Request $request, product $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'Budget_Range'    => 'sometimes|in:Low,Medium,High',
            'note'        => 'nullable|string',
            'date'        => 'nullable|date',
            'images'      => 'nullable|array',
            'images.*'    => 'image|max:2048',
        ]);
        $product->update($request->only(['category_id', 'title', 'description', 'price', 'Budget_Range', 'note', 'date']));
        $this->saveImages($request, $product, true);
        return response()->json(['message' => 'Product updated', 'product' => $this->format($product->load('category'))]);
    }



    public function destroy(Request $request, product $id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function restore(Request $request, product $id)
    {
        Product::withTrashed()->findOrFail($id)->restore();
        return response()->json(['message' => 'Product restored']);
    }



    public function deleted(Request $request)
    {
        $products = Product::with('category')->onlyTrashed()->latest()->get();
        return response()->json($products->map(fn($p) => $this->format($p))->values());
    }

    public function dashboard(Request $request)
    {
        $total = Product::withTrashed()->count();
        $active = Product::count();
        return response()->json([
            'total_products'   => $total,
            'active_products'  => $active,
            'deleted_products' => Product::onlyTrashed()->count(),
            'percentage'       => $total > 0 ? round($active / $total * 100, 2) : 0,
        ]);
    }
}
