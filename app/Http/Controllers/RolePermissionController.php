<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage users']);
    }

    /**
     * Display the permission matrix
     */
    public function index()
    {
        // Get all roles ordered by name
        $roles = Role::orderBy('name')->get();
        
        // Get all permissions ordered by name
        $permissions = Permission::orderBy('name')->get();
        
        // Build permission matrix
        $matrix = [];
        foreach ($permissions as $permission) {
            $matrix[$permission->name] = [];
            foreach ($roles as $role) {
                $matrix[$permission->name][$role->name] = $role->hasPermissionTo($permission->name);
            }
        }
        
        return view('role-permissions.index', compact('roles', 'permissions', 'matrix'));
    }

    /**
     * Update role permissions
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('manage users');
        
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);
        
        // Sync permissions for the role
        $role->syncPermissions($validated['permissions'] ?? []);
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully for ' . $role->name
        ]);
    }

    /**
     * Toggle a single permission for a role
     */
    public function toggle(Request $request)
    {
        $this->authorize('manage users');
        
        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
            'permission' => 'required|exists:permissions,name'
        ]);
        
        $role = Role::findByName($validated['role']);
        $permission = Permission::findByName($validated['permission']);
        
        // Toggle the permission
        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
            $hasPermission = false;
        } else {
            $role->givePermissionTo($permission);
            $hasPermission = true;
        }
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties([
                'permission' => $validated['permission'],
                'action' => $hasPermission ? 'granted' : 'revoked'
            ])
            ->log($hasPermission ? 'Permission granted' : 'Permission revoked');
        
        return response()->json([
            'success' => true,
            'hasPermission' => $hasPermission,
            'message' => $hasPermission ? 'Permission granted' : 'Permission revoked'
        ]);
    }
}
