<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

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
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|uuid|exists:orders,id',
            'product_id' => 'required|integer|exists:products,id',
            'order_qty' => 'required|integer',
            'total_amount' => 'required|string|max:255',
            'order_date' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $orderItem = OrderItem::create($validator->validated());

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
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|uuid|exists:orders,id',
            'product_id' => 'required|integer|exists:products,id',
            'order_qty' => 'required|integer',
            'total_amount' => 'required|string|max:255',
            'order_date' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $orderItem = OrderItem::findOrFail($id);
        $orderItem->update($request->all());

        return response()->json($orderItem, 200);
    }

    public function destroy($id)
    {
        $orderItem = OrderItem::find($id);

        if (! $orderItem || $orderItem->user_id !== Auth::id()) {
            return response()->json(['message' => "Order not found"], 404);
        }

        $orderItem->delete();

        return response()->json(null, 204);
    }
}
