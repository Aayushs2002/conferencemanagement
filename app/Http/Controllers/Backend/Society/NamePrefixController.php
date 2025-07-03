<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Http\Requests\Society\NamePrefixRequest;
use App\Models\User\NamePrefix;
use Illuminate\Http\Request;

class NamePrefixController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $name_prefixs = NamePrefix::where([
            'status' => 1
        ])->latest()->get();

        return view('backend.users.name-prefix.index', compact('name_prefixs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.users.name-prefix.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NamePrefixRequest $request)
    {
        try {
            $req = $request->all();
            NamePrefix::create($req);
            return redirect()->route('name-prefix.index')->with('status', 'Name Prefix Added Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
            //throw $th;
        }
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
    public function edit(NamePrefix $name_prefix)
    {
        return view('backend.users.name-prefix.create', compact('name_prefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NamePrefixRequest $request, NamePrefix $name_prefix)
    {
        try {
            $req = $request->all();
            $name_prefix->update($req);
            return redirect()->route('name-prefix.index')->with('status', 'Name Prefix Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NamePrefix $name_prefix)
    {
        $name_prefix->update(['status' => 0]);

        return redirect()->route('name-prefix.index')->with('delete', 'Name Prefix Deleted Successfully');
    }
}
