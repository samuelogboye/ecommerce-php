<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

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
        $banner = Banner::create($request->all());

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
