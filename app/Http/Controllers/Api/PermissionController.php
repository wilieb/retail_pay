<?php

namespace App\Http\Controllers\Api;

use App\Models\Permision;

class PermissionController
{
    public function index()
    {
        return response()->json(Permision::with('roles')->get());
    }
}
