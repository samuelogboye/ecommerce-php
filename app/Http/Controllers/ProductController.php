<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'subCategory', 'tags', 'views')->get();

        return response()->json([
            'message' => 'All products retrieved',
            'data' => $products,
            'count' => count($products),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255', // As description is nullable
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id', // Ensure the category exists
            'subcategory_id' => 'nullable|integer|exists:sub_categories,id', // Check if subcategory exists if provided
            'featured_image' => 'nullable|string', // Assuming a URL or a path
            'rank' => 'integer',
            'status' => 'string|in:available,out of stock,discontinued',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create($validator->validated());

        return response()->json($product, 201);
    }

    public function show($id)
    {
        try {
            return Product::with('category', 'subCategory', 'tags', 'views')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255', // As description is nullable
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id', // Ensure the category exists
            'subcategory_id' => 'nullable|integer|exists:sub_categories,id', // Check if subcategory exists if provided
            'featured_image' => 'nullable|string', // Assuming a URL or a path
            'rank' => 'integer',
            'status' => 'string|in:available,out of stock,discontinued',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (! $product || $product->user_id !== Auth::id()) {
            return response()->json(['message' => "Product not found"], 404);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}
