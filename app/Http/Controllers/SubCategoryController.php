<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        return SubCategory::with('category', 'products')->get();
    }

    public function store(Request $request)
    {
        $subCategory = SubCategory::create($request->all());
        return response()->json($subCategory, 201);
    }

    public function show($id)
    {
        return SubCategory::with('category', 'products')->findOrFail($id);
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
