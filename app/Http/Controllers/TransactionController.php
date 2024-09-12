<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('order')->get();

        return response()->json([
            'message' => 'All transactions retrieved',
            'data' => $transactions,
            'count' => count($transactions),
        ], 200);
    }

    public function store(Request $request)
    {
        $transaction = Transaction::create($request->all());

        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        try {
            return Transaction::with('order')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());

        return response()->json($transaction, 200);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (! $transaction || $transaction->user_id !== Auth::id()) {
            return response()->json(['message' => "Transaction not found"], 404);
        }

        $transaction->delete();

        return response()->json(null, 204);
    }
}
