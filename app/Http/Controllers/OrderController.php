<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Validator;

class OrderController extends Controller
{
    // TODO: for Admin only
    // public function index()
    // {
    //     $orders = Order::with('orderItems', 'transactions')->get();

    //     return response()->json([
    //         'message' => 'All orders retrieved',
    //         'data' => $orders,
    //         'count' => count($orders),
    //     ], 200);
    // }
    public function index()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Retrieve orders for the current user, including related orderItems and transactions
        $orders = Order::with('orderItems', 'transactions')
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
            'count' => $orders->count(),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subtotal' => 'required|string',
            'shipping_cost' => 'required|string',
            'total' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the authenticated user's ID
        $userId = Auth::id();

        // Add the user ID to the validated data
        $validatedData = $validator->validated();
        $validatedData['user_id'] = $userId;

        $order = Order::create($validatedData);

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
        $validator = Validator::make($request->all(), [
            'subtotal' => 'required|string',
            'shipping_cost' => 'required|string',
            'total' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = Order::findOrFail($id);
        $order->update($request->all());

        return response()->json($order, 200);
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (! $order || $order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(null, 204);
    }
}
