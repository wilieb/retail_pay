<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController
{
    public function index(Request $request, $type = 'sales')
    {
        $query = Transaction::with('product', 'fromStore', 'fromStore.branch', 'user')
            ->where('transaction_type', $type);

        if ($request->user()->role->name === 'branch_manager') {
            $query->whereHas('fromStore', function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id);
            });
        } elseif ($request->user()->role->name === 'store_manager') {
            $query->where('from_store_id', $request->user()->store_id);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        return response()->json($transactions->map(function ($transaction) {
            return [
                'transaction_id' => $transaction->transaction_id,
                'product_name' => $transaction->product->name ?? 'N/A',
                'quantity' => $transaction->quantity,
                'amount' => $transaction->amount,
                'transaction_date' => $transaction->transaction_date ?? $transaction->created_at,
                'user_name' => $transaction->user->name ?? 'N/A',
                'branch_name' => $transaction->fromStore->branch->name ?? null,
                'store_name' => $transaction->fromStore->name ?? 'N/A',
            ];
        }));
    }

    public function createSale(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
        ]);

        $transaction = Transaction::create([
            ...$validated,
            'transaction_type' => 'sales',
            'from_store_id' => $request->user()->store_id,
            'user_id' => $request->user()->id,
            'transaction_date' => now(),
        ]);

        $product = $transaction->product;
        $product->decrement('quantity', $validated['quantity']);

        return response()->json($transaction->load('product', 'fromStore.branch', 'user'), 201);
    }
}
