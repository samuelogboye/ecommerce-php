<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'subCategory', 'tags')->get();
        return response()->json([
            'message'=> 'All products retrieved',
            'products'=>$products
        ], 200);
    }

    public function store(Request $request)
    {
        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    public function show($id)
    {
        try {
            return Product::with('category', 'subCategory', 'tags')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(null, 204);
    }
}
