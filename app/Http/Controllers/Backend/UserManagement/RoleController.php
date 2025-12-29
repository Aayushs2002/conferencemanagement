<?php

namespace App\Http\Controllers\Backend\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\ActivityLog;
use App\Models\User\Society;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    public function index($society, $conference)
    {
        $roles = Role::where('society_id', $society->id)->get();
        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        
        // Count users per role for this conference
        $roleCounts = [];
        foreach ($roles as $role) {
            $count = DB::table('conference_user_roles')
                ->where('conference_id', $conference->id)
                ->where('role_id', $role->id) 
                ->count();
            $roleCounts[$role->id] = $count;
        }
        
        return view('backend.user-management.role.index', compact('society', 'conference', 'roles', 'users', 'roleCounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        $permissions = Permission::all();
        return view('backend.user-management.role.create', compact('permissions', 'society', 'conference'));
    }

    /**
     * Store a newly created resource in storage. 
     */
    public function store(Request $request, $society, $conference)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($society) {
                    return $query->where('society_id', $society->id);
                })
            ],
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::transaction(function () use ($request, $society) {
            $existingRole = Role::where('name', $request->name)
                ->where('society_id', $society->id)
                ->first();

            if ($existingRole) {
                throw new ValidationException('Role already exists in this society');
            }

            $role = new Role([
                "name" => $request->name,
                "guard_name" => "web",
                "society_id" => $society->id
            ]);

            $role->saveQuietly();

            $permissionIds = $request->permissions;
            $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

            $role->givePermissionTo($permissions);
        });

        return response()->json(['success' => true, 'message' => 'Role created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, Role $role)
    {
        $permissions = Permission::all();

        return view('backend.user-management.role.create', compact('permissions', 'conference', 'society', 'role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($society) {
                    return $query->where('society_id', $society->id);
                })->ignore($role->id)
            ],
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            DB::transaction(function () use ($request, $role, $conference) {
                $oldPermissions = $role->permissions->pluck('id')->toArray();
                $newPermissions = $request->permissions;

                $removedPermissions = array_diff($oldPermissions, $newPermissions);
                $addedPermissions = array_diff($newPermissions, $oldPermissions);

                // Update role name
                $role->update([
                    'name' => $request->name
                ]);
                // Sync role permissions
                $permissions = Permission::whereIn('id', $newPermissions)->pluck('name')->toArray();
                $role->syncPermissions($permissions);

                // Fetch user IDs who have this role in this conference
                $userIds = DB::table('conference_user_roles')
                    ->where('conference_id', $conference->id)
                    ->where('role_id', $role->id)
                    ->pluck('user_id')
                    ->toArray();

                // Delete removed permissions from conference_user_permission
                if (!empty($removedPermissions) && !empty($userIds)) {
                    DB::table('conference_user_permission')
                        ->where('conference_id', $conference->id)
                        ->whereIn('user_id', $userIds)
                        ->whereIn('permission_id', $removedPermissions)
                        ->delete();
                }

                // Add new permissions for users to conference_user_permission
                if (!empty($addedPermissions) && !empty($userIds)) {
                    $insertData = [];

                    foreach ($userIds as $userId) {
                        foreach ($addedPermissions as $permissionId) {
                            $insertData[] = [
                                'conference_id' => $conference->id,
                                'user_id' => $userId,
                                'permission_id' => $permissionId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    // Use upsert to avoid duplicates
                    DB::table('conference_user_permission')->upsert(
                        $insertData,
                        ['conference_id', 'user_id', 'permission_id'],
                        ['updated_at']
                    );
                }
            });

            return response()->json(['success' => true, 'message' => 'Role Updated successfully']);
        } catch (\Exception $e) {
            // dd($e);
            return response()->json(['error' => true, 'message' => 'Failed to update role. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function assignRoleForm(Request $request, $society, $conference)
    {
        $user = User::where('id', $request->id)->first();
        $roles = Role::where('society_id', $society->id)->get();
        return view('backend.user-management.role.assign-role', compact('user', 'society', 'conference', 'roles'));
    }


    public function assignRoleFormSubmit(Request $request, $society, $conference)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:users,id',
                'role' => 'required|string|exists:roles,name',
            ]);

            $user = User::findOrFail($request->id);

            $role = Role::where('name', $request->role)
                ->where('society_id', $society->id)
                ->firstOrFail();

            // Check if user already has this role for this conference
            $existingRole = $user->conferenceRoles()
                ->wherePivot('role_id', $role->id)
                ->wherePivot('conference_id', $conference->id)
                ->exists();

            if ($existingRole) {
                throw new \Exception('User already has this role assigned for this conference.');
            }

            // Assign role globally (for Spatie)
            if (!$user->hasRole($role->name)) {
                $user->assignRole($role->name);
            }
            
            // Attach role for this specific conference
            $user->conferenceRoles()->attach($role->id, ['conference_id' => $conference->id]);

            // Add permissions for this conference
            foreach ($role->permissions as $permission) {
                $exists = $user->conferencePermissions()
                    ->wherePivot('permission_id', $permission->id)
                    ->wherePivot('conference_id', $conference->id)
                    ->exists();

                if (! $exists) {
                    $user->conferencePermissions()->attach($permission->id, ['conference_id' => $conference->id]);
                }
            }

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Role assigned successfully.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function getUserActivityLog(Request $request, $society, $conference)
    {
        $userId = $request->user_id;
        $user = User::findOrFail($userId);
        
        $activityLogs = ActivityLog::where('conference_id', $conference->id)
            ->where('user_id', $userId)
            ->where('action', 'Registered Conference')
            ->orWhere('action', 'Invited Conference')
            ->orderByDesc('created_at')
            ->get();
        
        return view('backend.user-management.role.activity-log', compact('user', 'activityLogs', 'society', 'conference'));
    }

    public function removeRoleForm(Request $request, $society, $conference)
    {
        $user = User::where('id', $request->id)->first();
        
        // Get all roles assigned to this user for this conference
        $userRoles = $user->conferenceRoles()
            ->wherePivot('conference_id', $conference->id)
            ->get();
            
        return view('backend.user-management.role.remove-role', compact('user', 'society', 'conference', 'userRoles'));
    }

    public function removeRoleFormSubmit(Request $request, $society, $conference)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'id' => 'required|exists:users,id',
                'role_id' => 'required|exists:roles,id',
            ]);

            $user = User::findOrFail($request->id);
            $role = Role::findOrFail($request->role_id);

            // Check if the user has this role for this conference
            $hasRole = $user->conferenceRoles()
                ->wherePivot('role_id', $role->id)
                ->wherePivot('conference_id', $conference->id)
                ->exists();

            if (!$hasRole) {
                throw new \Exception('User does not have this role for the selected conference.');
            }

            // Remove the role from conference_user_roles
            DB::table('conference_user_roles')
                ->where('user_id', $user->id)
                ->where('role_id', $role->id)
                ->where('conference_id', $conference->id)
                ->delete();

            // Get permissions that should be removed
            // Only remove permissions that are not granted by other roles
            $otherRoles = $user->conferenceRoles()
                ->wherePivot('conference_id', $conference->id)
                ->get();
            
            $permissionsToKeep = collect();
            foreach ($otherRoles as $otherRole) {
                $permissionsToKeep = $permissionsToKeep->merge($otherRole->permissions->pluck('id'));
            }
            $permissionsToKeep = $permissionsToKeep->unique();

            // Get permissions from the role being removed
            $rolePermissions = $role->permissions->pluck('id');

            // Remove permissions that are not in other roles
            $permissionsToRemove = $rolePermissions->diff($permissionsToKeep);

            if ($permissionsToRemove->isNotEmpty()) {
                DB::table('conference_user_permission')
                    ->where('user_id', $user->id)
                    ->where('conference_id', $conference->id)
                    ->whereIn('permission_id', $permissionsToRemove->toArray())
                    ->delete();
            }

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Role removed successfully.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}