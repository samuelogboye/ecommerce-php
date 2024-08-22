<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function index()
    {
        $orderitems = OrderItem::with('order', 'product')->get();

        return response()->json([
            'message' => 'All orderitems retrieved',
            'data' => $orderitems,
            'count' => count($orderitems),
        ], 200);
    }

    public function store(Request $request)
    {
        $orderItem = OrderItem::create($request->all());

        return response()->json($orderItem, 201);
    }

    public function show($id)
    {
        try {
            return OrderItem::with('order', 'product')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'OrderItem not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $orderItem->update($request->all());

        return response()->json($orderItem, 200);
    }

    public function destroy($id)
    {
        OrderItem::destroy($id);

        return response()->json(null, 204);
    }
}
