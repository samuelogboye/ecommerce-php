<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::all();

        return response()->json([
            'message' => 'All banners retrieved',
            'data' => $banners,
            'count' => count($banners),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
            'location' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $banner = Banner::create($validator->validated());

        return response()->json($banner, 201);
    }

    public function show($id)
    {
        try {
            return Banner::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Banner not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update($request->all());

        return response()->json($banner, 200);
    }

    public function destroy($id)
    {
        Banner::destroy($id);

        return response()->json(null, 204);
    }
}
