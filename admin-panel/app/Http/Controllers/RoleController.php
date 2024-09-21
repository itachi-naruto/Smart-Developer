<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        return Role::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles',
        ]);

        $role = Role::create($request->only('name'));

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'create_role',
            'details' => json_encode($role)
        ]);

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }


    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);
        $role->update($request->only('name'));

        // Update permissions
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions'));
        }

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'update_role',
            'details' => json_encode(['role_id' => $role->id, 'permissions' => $request->input('permissions')])
        ]);

        return response()->json(['message' => 'Role updated successfully']);
    }
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'delete_role',
            'details' => json_encode($role)
        ]);

        return response()->json(['message' => 'Role deleted successfully']);
    }

    public function assignRoleToUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($request->input('role_id'));

        $user->assignRole($role);

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'assign_role',
            'details' => json_encode([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ])
        ]);

        return response()->json(['message' => 'Role assigned to user successfully']);
    }

    public function removeRoleFromUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($request->input('role_id'));

        $user->removeRole($role);

        Log::create([
            'user_id' => Auth::id(),
            'action' => 'remove_role',
            'details' => json_encode([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ])
        ]);

        return response()->json(['message' => 'Role removed from user successfully']);
    }
}

