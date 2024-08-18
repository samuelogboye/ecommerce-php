<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return Tag::with('products')->get();
    }

    public function store(Request $request)
    {
        $tag = Tag::create($request->all());
        return response()->json($tag, 201);
    }

    public function show($id)
    {
        return Tag::with('products')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update($request->all());
        return response()->json($tag, 200);
    }

    public function destroy($id)
    {
        Tag::destroy($id);
        return response()->json(null, 204);
    }
}
