<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * 📦 Merr të gjithë produktet me kategoritë e tyre
     */
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return response()->json($products);
    }

    /**
     * ➕ Shton një produkt të ri
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,avif|max:4096',
        ]);

        // 🖼️ Ruajtja e imazhit në storage/public/products
        $image = $request->file('image');
        $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('products', $imageName, 'public');

        // 📁 Ruaj path-in publik për React
        $validated['image'] = 'storage/' . $imagePath;

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Produkti u shtua me sukses!',
            'product' => $product->load('category'),
        ], 201);
    }

    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /**
     * ✏️ Përditëson një produkt ekzistues
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:4096',
        ]);

        // 🖼️ Nëse vjen imazh i ri, fshij të vjetrin dhe ruaj të riun
        if ($request->hasFile('image')) {
            if ($product->image && str_starts_with($product->image, 'storage/')) {
                $oldPath = str_replace('storage/', '', $product->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Produkti u përditësua me sukses!',
            'product' => $product->load('category'),
        ]);
    }

    /**
     * 🗑️ Fshin një produkt
     */
    public function destroy(Product $product)
    {
        // 🧹 Fshij imazhin nga storage nëse ekziston
        if ($product->image && str_starts_with($product->image, 'storage/')) {
            $oldPath = str_replace('storage/', '', $product->image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $product->delete();

        return response()->json([
            'message' => 'Produkti u fshi me sukses!',
        ]);
    }
}
