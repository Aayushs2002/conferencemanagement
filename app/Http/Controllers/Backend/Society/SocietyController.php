<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Http\Requests\Society\AddSocietyRequest;
use App\Models\Conference\Conference;
use App\Models\User;
use App\Models\User\MemberType;
use App\Models\User\Society;
use App\Models\User\UserSociety;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SocietyController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected FileService $file_service) {}
    public function index()
    {
        if (is_super_admin()) {
            $societies  = Society::whereStatus(1)->get();
        } else {
            $societies = current_user()->societies;
        }
        // dd($societies);
        return view('backend.users.societies.index', compact('societies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.users.societies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddSocietyRequest $request)
    {
        try {
            $req = $request->all();
            $req['password'] = hash_password('password');
            $req['type'] = 2;
            $req['slug'] = slugify($req['society_name']);
            $req['f_name'] = $req['society_name'];
            $req['token'] = random_word(60);
            unset($req['society_name']);
            DB::beginTransaction();
            if (!empty($req['logo'])) {
                $req['logo'] = $this->file_service->fileUpload($req['logo'], 'societies', 'society/logo');
            }
            // insert into user detail table
            $society = Society::create($req);

            // insert into user table
            $user = User::create($req);

            //insert into pivot table user society
            // UserSociety::create([
            //     'user_id' => $user->id,
            //     'society_id' => $society->id
            // ]);
            $user->societies()->attach($society->id);

            //create role society admin role for this society
            $role = Role::where('name', 'society admin')->first();
            if (!$role) {
                $role = Role::create([
                    'name' => 'society admin',
                    'guard_name' => 'web',
                ]);
                $role->givePermissionTo(Permission::all());
                $user->assignRole($role);
            }
            DB::commit();
            return redirect()->route('society.index')->with('status', 'Society Added Successfully');
        } catch (Exception $e) {
            dd($e);

            DB::rollBack();
            return redirect()->back()->with('delete', 'Internal Server Error');
            // throw $e;
        }
    }


    public function view(Request $request)
    {
        $society = Society::whereId($request->id)->first();
        return view('backend.users.societies.view', compact('society'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Society $society)
    {
        return view('backend.users.societies.create', compact('society'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddSocietyRequest $request, Society $society)
    {
        // dd($request);
        try {
            $req = $request->all();
            $req['slug'] = slugify($req['society_name']);
            $req['f_name'] = $req['society_name'];
            unset($req['society_name']);
            DB::beginTransaction();
            // update society table
            if (!empty($req['logo'])) {
                $this->file_service->deleteFile($society->logo, 'society/logo');
                $req['logo'] = $this->file_service->fileUpload($req['logo'], 'societies', 'society/logo');
            }
            $society->update($req);

            // update user table
            $user = $society->users->where('type', 2)->first();
            $user->update($req);
            DB::commit();
            return redirect()->route('society.index')->with('status', 'Society Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Society $society)
    {
        try {
            $user = User::whereSocietyId($society->id)->first();

            $user->update(['status' => 0]);
            $society->update(['status' => 0]);

            return redirect()->back()->with('delete', 'Society Deleted Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function dashboard($society)
    {
        $conferenceCount = Conference::where([
            'society_id' => $society->id,
            'status' => 1
        ])->count();
        $typeCount = MemberType::where([
            'society_id' => $society->id,
            'status' => 1
        ])->count();
        $conferences = Conference::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.users.societies.dashboard', compact('conferenceCount', 'typeCount', 'conferences', 'society'));
    }


    public function viewDetailByAdmin($slug)
    {
        $society = Society::whereSlug($slug)->first();
        session(['societyDetail' => $society]);
        $societyDetail = society_detail();

        $conferenceCount = Conference::where([
            'society_id' => $societyDetail->id,
            'status' => 1
        ])->count();
        $typeCount = MemberType::where([
            'society_id' => $societyDetail->id,
            'status' => 1
        ])->count();
        $conferences = Conference::where([
            'society_id' => $societyDetail->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.users.societies.dashboard', compact('conferenceCount', 'typeCount', 'conferences'));
    }
}
