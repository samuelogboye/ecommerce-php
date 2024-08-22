<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems', 'transactions')->get();

        return response()->json([
            'message' => 'All orders retrieved',
            'data' => $orders,
            'count' => count($orders),
        ], 200);
    }

    public function store(Request $request)
    {
        $order = Order::create($request->all());

        return response()->json($order, 201);
    }

    public function show($id)
    {
        try {
            return Order::with('orderItems', 'transactions')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());

        return response()->json($order, 200);
    }

    public function destroy($id)
    {
        Order::destroy($id);

        return response()->json(null, 204);
    }
}
