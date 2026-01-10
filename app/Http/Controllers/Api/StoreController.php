<?php

namespace App\Http\Controllers\Api;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController
{
    public function index(Request $request)
    {
        $query = Store::with('branch', 'users', 'products');

        if ($request->user()->role->name === 'branch_manager') {
            $query->where('branch_id', $request->user()->branch_id);
        } elseif ($request->user()->role->name === 'store_manager') {
            $query->where('id', $request->user()->store_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stores,name',
            'branch_id' => 'required|exists:branches,id',
            'location' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'manager_name' => 'nullable|string',
        ]);

        $store = Store::create($validated);

        return response()->json($store->load('branch', 'users', 'products'), 201);
    }

    public function update(Request $request, string $id)
    {
        $store = Store::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:stores,name,' . $store->id,
            'branch_id' => 'sometimes|exists:branches,id',
            'location' => 'sometimes|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'manager_name' => 'nullable|string',
        ]);

        $store->update($validated);

        return response()->json($store->load('branch', 'users', 'products'));
    }

    public function destroy(string $id)
    {
        $store = Store::findOrFail($id);
        $store->delete();

        return response()->json(null, 204);
    }
}
