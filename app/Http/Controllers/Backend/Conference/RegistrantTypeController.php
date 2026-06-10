<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\RegistrantType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrantTypeController extends Controller
{
    public function index($society, $conference)
    {
        $globalTypes  = RegistrantType::whereNull('conference_id')->where('status', 1)->orderBy('id')->get();
        $hiddenIds    = RegistrantType::hiddenIdsForConference($conference->id);
        $conferenceTypes = RegistrantType::where('conference_id', $conference->id)->where('status', 1)->orderBy('id')->get();

        return view('backend.conference.registrant-type.index', compact(
            'society',
            'conference',
            'globalTypes',
            'hiddenIds',
            'conferenceTypes'
        ));
    }

    public function store(Request $request, $society, $conference)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        RegistrantType::create([
            'name'          => $request->name,
            'conference_id' => $conference->id,
            'status'        => 1,
        ]);

        return redirect()->back()->with('status', 'Registration type added successfully.');
    }

    public function update(Request $request, $society, $conference, RegistrantType $registrantType)
    {
        if ($registrantType->conference_id !== $conference->id) {
            return redirect()->back()->with('delete', 'Global registration types cannot be edited here.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $registrantType->update(['name' => $request->name]);

        return redirect()->back()->with('status', 'Registration type updated successfully.');
    }

    public function destroy($society, $conference, RegistrantType $registrantType)
    {
        if ($registrantType->conference_id !== $conference->id) {
            return redirect()->back()->with('delete', 'Global registration types cannot be deleted here.');
        }

        $registrantType->update(['status' => 0]);

        return redirect()->back()->with('status', 'Registration type removed successfully.');
    }

    /**
     * Toggle visibility of a global type for this conference.
     */
    public function toggleGlobal($society, $conference, RegistrantType $registrantType)
    {
        if (!is_null($registrantType->conference_id)) {
            return redirect()->back()->with('delete', 'Only global types can be toggled.');
        }

        $exists = DB::table('conference_registrant_type_hidden')
            ->where('conference_id', $conference->id)
            ->where('registrant_type_id', $registrantType->id)
            ->exists();

        if ($exists) {
            DB::table('conference_registrant_type_hidden')
                ->where('conference_id', $conference->id)
                ->where('registrant_type_id', $registrantType->id)
                ->delete();
            return redirect()->back()->with('status', "\"{$registrantType->name}\" is now visible for this conference.");
        } else {
            DB::table('conference_registrant_type_hidden')->insert([
                'conference_id'      => $conference->id,
                'registrant_type_id' => $registrantType->id,
            ]);
            return redirect()->back()->with('status', "\"{$registrantType->name}\" has been hidden for this conference.");
        }
    }

    // -----------------------------------------------------------------------
    // Super admin: manage global registration types
    // -----------------------------------------------------------------------

    public function globalIndex()
    {
        $globalTypes = RegistrantType::whereNull('conference_id')->orderBy('id')->get();
        return view('backend.registrant-type.global-index', compact('globalTypes'));
    }

    public function globalStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);

        RegistrantType::create([
            'name'          => $request->name,
            'conference_id' => null,
            'status'        => 1,
        ]);

        return redirect()->back()->with('status', 'Global registration type added successfully.');
    }

    public function globalUpdate(Request $request, RegistrantType $registrantType)
    {
        if (!is_null($registrantType->conference_id)) {
            return redirect()->back()->with('delete', 'Not a global type.');
        }

        $request->validate(['name' => 'required|string|max:100']);
        $registrantType->update(['name' => $request->name]);

        return redirect()->back()->with('status', 'Global registration type updated successfully.');
    }

    public function globalDestroy(RegistrantType $registrantType)
    {
        if (!is_null($registrantType->conference_id)) {
            return redirect()->back()->with('delete', 'Not a global type.');
        }

        $registrantType->update(['status' => 0]);

        return redirect()->back()->with('status', 'Global registration type disabled successfully.');
    }
}
