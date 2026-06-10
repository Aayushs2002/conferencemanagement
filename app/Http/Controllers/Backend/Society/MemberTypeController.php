<?php

namespace App\Http\Controllers\Backend\Society;

use App\Http\Controllers\Controller;
use App\Http\Requests\Society\MemberTypeRequest;
use App\Models\Society\SocietySetting;
use App\Models\User\MemberType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MemberTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society)
    {
        $types = MemberType::where([
            'society_id' => $society->id,
            'status' => 1
        ])->get();
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
            $req['display_order'] = (int) (MemberType::where('society_id', $society->id)->max('display_order') ?? 0) + 1;
            MemberType::create($req);

            return redirect()->route('memberType.index', $society)->with('status', 'Member Type Added Successfully');
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

            return redirect()->route('memberType.index', $society)->with('status', 'Member Type Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function destroy($society, MemberType $memberType)
    {
        try {
            $memberType->update(['status' => 0]);
            return redirect()->route('memberType.index', $society)->with('status', 'Member Type Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }

    public function fetchExternalMemberTypes($society)
    {
        try {
            $memberTypeApi = SocietySetting::where('society_id', $society->id)->pluck('member_type_api')->first();
            $response = Http::get($memberTypeApi);

            if ($response->failed()) {
                return response()->json(['error' => 'Failed to fetch member types'], 500);
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateOrder(Request $request, $society)
    {
        try {
            $validated = $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:member_types,id',
                'orders.*.position' => 'required|integer|min:1',
            ]);

            foreach ($validated['orders'] as $order) {
                MemberType::where('society_id', $society->id)
                    ->where('id', $order['id'])
                    ->update(['display_order' => $order['position']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
