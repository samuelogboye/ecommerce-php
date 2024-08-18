<?php

namespace App\Http\Controllers;

use App\Models\View;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        return View::with('product')->get();
    }

    public function store(Request $request)
    {
        $view = View::create($request->all());
        return response()->json($view, 201);
    }

    public function show($id)
    {
        return View::with('product')->findOrFail($id);
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
