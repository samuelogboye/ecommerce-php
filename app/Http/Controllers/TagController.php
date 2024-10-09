<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::with('products')->get();

        return response()->json([
            'message' => 'All tags retrieved',
            'data' => $tags,
            'count' => count($tags),
        ], 200);
    }

    public function store(Request $request)
    {
        $tag = Tag::create($request->all());

        return response()->json($tag, 201);
    }

    public function show($id)
    {
        try {
            return Tag::with('products')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Tag not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update($request->all());

        return response()->json($tag, 200);
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);

        if (! $tag) {
            return response()->json(['message' => 'Tag not found'], 404);
        }

        $tag->delete();

        return response()->json(null, 204);
    }
}
