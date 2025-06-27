<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\User;
use App\Models\User\UserSociety;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MySocietyController extends Controller
{
    public function index()
    {
        
        $joinedSocities = current_user()->societies;
        
        return view('backend.participant.my-society.index', compact('joinedSocities'));
    }

    public function detail($id)
    {
        return view('backend.participant.my-society.detail');
    }

    public function conference($society)
    {
        $conferences = Conference::where(['society_id' => $society->id, 'status' => 1])->get();
        // dd($conferences);
        // $checkRegistration = 
        return view('backend.participant.conference.index', compact('conferences', 'society'));
    }



    public function joinSocietySubmit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'society_id' => 'required',
                'member_type_id' => 'required',
            ], [
                'society_id.required' => 'Please select society.',
                'member_type_id.required' => 'Please select Member Type.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // UserSociety::create([
            //     'user_id' => current_user()->id,
            //     'society_id' => $request->society_id,
            //     'member_type_id' => $request->member_type_id
            // ]);
            $user = User::whereId(current_user()->id)->first();
            $user->societies()->attach($request->society_id, [
                'member_type_id' => $request->member_type_id,
            ]);


            return response()->json([
                'type' => 'success',
                'message' => 'You have successfully joined the society.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'error',
                'message' => 'An unexpected error occurred.',
                'debug' => $th->getMessage()
            ], 500);
        }
    }
}
