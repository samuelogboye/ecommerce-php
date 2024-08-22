<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::with('category', 'products')->get();

        return response()->json([
            'message' => 'All subcategories retrieved',
            'data' => $subcategories,
            'count' => count($subcategories),
        ], 200);
    }

    public function store(Request $request)
    {
        $subCategory = SubCategory::create($request->all());

        return response()->json($subCategory, 201);
    }

    public function show($id)
    {
        try {
            return SubCategory::with('category', 'products')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'SubCategory not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->update($request->all());

        return response()->json($subCategory, 200);
    }

    public function destroy($id)
    {
        SubCategory::destroy($id);

        return response()->json(null, 204);
    }
}
