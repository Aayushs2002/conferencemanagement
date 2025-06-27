<?php

namespace App\Http\Controllers\Backend\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Society;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::latest()->get();
        // dd($permissions);
        return view('backend.user-management.permission.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.user-management.permission.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'parent' => 'required',
        ]);
        $req = $request->all();
        $req['guard_name'] = "web";
        $permission = Permission::create($req);
        Role::whereIn('name', ['SuperAdmin', 'society admin'])->get()->each(function ($role) use ($permission) {
            $role->givePermissionTo($permission);
        });

        $SuperUser = User::where('type', 1)->firstOrFail();
        $societyAdmins = User::with('societies.conferences')->where('type', 2)->get();
        // dd($societyAdmins);
        if ($societyAdmins->isNotEmpty()) {
            foreach ($societyAdmins as $societyAdmin) {
                foreach ($societyAdmin->societies as $society) {
                    foreach ($society->conferences as $conference) {
                        $SuperUser->conferencePermissions()->attach($permission->id, ['conference_id' => $conference->id]);
                        $societyAdmin->conferencePermissions()->attach($permission->id, ['conference_id' => $conference->id]);
                    }
                }
            }
        }
 
        return redirect()->route('permission.index')->with('status', 'Permission Successfully Added');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('backend.user-management.permission.create', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|max:255',
            'parent' => 'required',
        ]);
        $req = $request->all();
        $req['guard_name'] = "web";
        $permission->update($req);

        return redirect()->route('permission.index')->with('status', 'Permission Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
