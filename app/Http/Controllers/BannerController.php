<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class BannerController extends Controller
{
    private const BANNER_NOT_FOUND_ERROR = 'Banner not found';

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
            'location' => 'required|string|max:50',
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
            return response()->json(['message' => self::BANNER_NOT_FOUND_ERROR], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
            'location' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $banner = Banner::findOrFail($id);
        $banner->update($request->all());

        return response()->json($banner, 200);
    }

    public function destroy($id)
    {
        $banner = Banner::find($id);

        if (! $banner) {
            return response()->json(['message' => self::BANNER_NOT_FOUND_ERROR], 404);
        }

        $banner->delete();

        return response()->json(null, 204);
    }
}
