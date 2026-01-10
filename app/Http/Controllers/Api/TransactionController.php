<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use App\Services\TransactionNumberGenerator;
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
        $userRole = $request->user()->role->name ?? 'user';
        $isAdminOrBranchManager = in_array($userRole, ['admin', 'branch_manager']);
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'from_store_id' => $isAdminOrBranchManager ? 'required|exists:stores,id' : 'nullable|exists:stores,id',
        ]);

        if ($isAdminOrBranchManager) {
            $fromStoreId = $validated['from_store_id'];
        } else {
            $fromStoreId = $request->user()->store_id;
        }

        $transaction = Transaction::create([
            'transaction_id' => TransactionNumberGenerator::generate('sales'),
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'amount' => $validated['amount'],
            'transaction_type' => 'sales',
            'from_store_id' => $fromStoreId,
            'to_store_id' => $fromStoreId,
            'user_id' => $request->user()->id,
            'transaction_date' => now(),
        ]);

        $product = $transaction->product;
        $product->decrement('quantity', $validated['quantity']);

        return response()->json($transaction->load('product', 'fromStore.branch', 'user'), 201);
    }
}
