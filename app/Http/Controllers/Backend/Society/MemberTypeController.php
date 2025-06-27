<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Http\Requests\Society\MemberTypeRequest;
use App\Models\User\MemberType;
use Exception;

class MemberTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society)
    {

        // if (!empty(session()->get('conferenceDetail'))) {
        //     session()->forget('conferenceDetail');
        // }
        // $societyDetail = society_detail();

        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }
        // if (is_society_admin()) {
        //     $types = MemberType::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        //     $types = MemberType::where([
        //         'society_id' => $societyDetail->id,
        //         'status' => 1
        //     ])->latest()->get();
        // } else {
        //     return redirect()->route('dashboard');
        // }
        $types = MemberType::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.users.member-type.index', compact('types', 'society'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society)
    {
        return view('backend.users.member-type.create', compact('society'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MemberTypeRequest $request, $society)
    {
        try {
            $req = $request->all();
            $req['society_id'] = $society->id;
            MemberType::create($req);

            return redirect()->route('member-type.index', 'society')->with('status', 'Member Type Added Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, MemberType $memberType)
    {
        return view('backend.users.member-type.create', compact('memberType', 'society'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MemberTypeRequest $request, $society, MemberType $memberType)
    {
        try {
            $req = $request->all();

            $memberType->update($req);

            return redirect()->route('member-type.index', 'society')->with('status', 'Member Type Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }
}
