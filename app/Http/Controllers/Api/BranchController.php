<?php

namespace App\Http\Controllers\Api;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController
{
    public function index(Request $request)
    {
        $query = Branch::with('stores', 'users');

        if ($request->user()->role->name !== 'admin') {
            $query->where('id', $request->user()->branch_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'location' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'manager_name' => 'nullable|string',
        ]);

        $branch = Branch::create($validated);

        return response()->json($branch->load('stores', 'users'), 201);
    }

    public function update(Request $request, string $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:branches,name,' . $branch->id,
            'location' => 'sometimes|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'manager_name' => 'nullable|string',
        ]);

        $branch->update($validated);

        return response()->json($branch->load('stores', 'users'));
    }

    public function destroy(string $id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return response()->json(null, 204);
    }
}
