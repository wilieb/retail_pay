<?php

namespace App\Http\Controllers\Api;

use App\Models\Transfer;
use Illuminate\Http\Request;

class TransferController
{
    public function index(Request $request)
    {
        $query = Transfer::with('product', 'fromStore', 'toStore', 'requestedBy', 'approvedBy');

        $status = $request->query('status');
        if ($status) {
            $query->where('status', $status);
        }

        if ($request->user()->role->name === 'store_manager') {
            $query->where(function ($q) use ($request) {
                $q->where('from_store_id', $request->user()->store_id)
                  ->orWhere('to_store_id', $request->user()->store_id);
            });
        } elseif ($request->user()->role->name === 'branch_manager') {
            $query->whereHas('fromStore', function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id);
            })->orWhereHas('toStore', function ($q) use ($request) {
                $q->where('branch_id', $request->user()->branch_id);
            });
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
        ]);

        $transfer = Transfer::create([
            ...$validated,
            'requested_by_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return response()->json($transfer->load('product', 'fromStore', 'toStore', 'requestedBy'), 201);
    }

    public function approve(Request $request, string $id)
    {
        $transfer = Transfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return response()->json(['error' => 'Transfer is not pending'], 400);
        }

        $transfer->update([
            'status' => 'approved',
            'approved_by_id' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $product = $transfer->product;
        $product->decrement('quantity', $transfer->quantity);

        return response()->json($transfer->load('product', 'fromStore', 'toStore', 'requestedBy', 'approvedBy'));
    }

    public function reject(Request $request, string $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $transfer = Transfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return response()->json(['error' => 'Transfer is not pending'], 400);
        }

        $transfer->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by_id' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return response()->json($transfer->load('product', 'fromStore', 'toStore', 'requestedBy', 'approvedBy'));
    }
}
