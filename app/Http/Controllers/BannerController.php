<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        return Banner::all();
    }

    public function store(Request $request)
    {
        $banner = Banner::create($request->all());
        return response()->json($banner, 201);
    }

    public function show($id)
    {
        return Banner::findOrFail($id);
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
