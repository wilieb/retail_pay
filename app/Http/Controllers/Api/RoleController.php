<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController
{
    public function index(Request $request)
    {
        return response()->json(Role::with('permissions')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($validated);

        return response()->json($role->load('permissions'), 201);
    }

    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return response()->json($role->load('permissions'));
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(null, 204);
    }

    public function permissions(string $id)
    {
        $role = Role::findOrFail($id);

        return response()->json($role->permissions);
    }

    public function assignPermission(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'permission_id' => 'required|exists:permisions,id',
        ]);

        if (!$role->permissions()->where('permission_id', $validated['permission_id'])->exists()) {
            $role->permissions()->attach($validated['permission_id']);
        }

        return response()->json($role->load('permissions'));
    }

    public function removePermission(Request $request, string $id, string $permissionId)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach($permissionId);

        return response()->json($role->load('permissions'));
    }
}
