<?php

namespace App\Http\Controllers;

use App\Models\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        $views =  View::with('product')->get();
        return response()->json([
            'message'=> 'All views retrieved',
            'data'=>$views,
            'count'=>count($views)
        ], 200);
    }

    public function store(Request $request)
    {
        $view = View::create($request->all());
        return response()->json($view, 201);
    }

    public function show($id)
    {
        try {
            return View::with('product')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'View not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $view = View::findOrFail($id);
        $view->update($request->all());
        return response()->json($view, 200);
    }

    public function destroy($id)
    {
        View::destroy($id);
        return response()->json(null, 204);
    }
}
