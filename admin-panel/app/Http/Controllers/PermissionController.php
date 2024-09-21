<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index()
    {
        return Permission::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions',
        ]);

        $permission = Permission::create($request->only('name'));

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'create_permission',
            'details' => json_encode($permission)
        ]);

        return response()->json(['message' => 'Permission created successfully', 'permission' => $permission], 201);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update($request->only('name'));

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'update_permission',
            'details' => json_encode($permission)
        ]);

        return response()->json(['message' => 'Permission updated successfully']);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'delete_permission',
            'details' => json_encode($permission)
        ]);

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}

