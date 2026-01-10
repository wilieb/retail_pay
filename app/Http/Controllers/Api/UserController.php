<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->user()->role->name === 'admin') {
            $users = $query->with('role', 'branch', 'store')->get();
        } elseif ($request->user()->role->name === 'branch_manager') {
            $users = $query->where('branch_id', $request->user()->branch_id)
                ->with('role', 'branch', 'store')->get();
        } elseif ($request->user()->role->name === 'store_manager') {
            $users = $query->where('store_id', $request->user()->store_id)
                ->with('role', 'branch', 'store')->get();
        } else {
            $users = collect();
        }

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)],
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'store_id' => 'nullable|exists:stores,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($user->load('role', 'branch', 'store'), 201);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role_id' => 'sometimes|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'store_id' => 'nullable|exists:stores,id',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => ['sometimes', Password::min(8)],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user->load('role', 'branch', 'store'));
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }
}
