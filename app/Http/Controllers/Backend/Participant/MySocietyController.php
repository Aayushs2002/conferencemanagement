<?php

namespace App\Http\Controllers\Backend\Participant;

use App\Http\Controllers\Controller;
use App\Models\Conference\Conference;
use App\Models\User;
use App\Models\User\MemberType;
use App\Models\User\UserSociety;
use App\Services\File\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MySocietyController extends Controller
{
    public function __construct(protected FileService $file_service)
    {
    }

    public function index()
    {

        $joinedSocities = current_user()->societies()->with('pivot.memberType')->get();

        return view('backend.participant.my-society.index', compact('joinedSocities'));
    }

    public function detail($id)
    {
        return view('backend.participant.my-society.detail');
    }

    public function conference($society)
    {
        $activeConferences = Conference::where([
            'society_id' => $society->id,
            'status' => 1,
            'is_archived' => 0
        ])->orderBy('start_date', 'desc')->get();

        $archivedConferences = Conference::where([
            'society_id' => $society->id,
            'status' => 1,
            'is_archived' => 1
        ])->orderBy('archived_at', 'desc')->get();

        // Get user's society membership with member type
        $userSociety = UserSociety::where([
            'user_id' => current_user()->id,
            'society_id' => $society->id
        ])->with('memberType')->first();

        return view('backend.participant.conference.index', compact('activeConferences', 'archivedConferences', 'society', 'userSociety'));
    }



    public function joinSocietySubmit(Request $request)
    {
        try {
            $memberType = MemberType::find($request->member_type_id);
            $requiresVerification = (bool) ($memberType?->requires_student_verification ?? false);

            $validator = Validator::make($request->all(), [
                'society_id' => 'required',
                'member_type_id' => 'required',
                'id_card_document' => [$requiresVerification ? 'required' : 'nullable', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
                'official_letter_document' => [$requiresVerification ? 'required' : 'nullable', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            ], [
                'society_id.required' => 'Please select society.',
                'member_type_id.required' => 'Please select Member Type.',
                'id_card_document.required' => 'ID Card is required for this member type.',
                'id_card_document.mimes' => 'ID card must be JPG, PNG, or PDF.',
                'id_card_document.max' => 'ID card file size must not exceed 5MB.',
                'official_letter_document.required' => 'Official Letter is required for this member type.',
                'official_letter_document.mimes' => 'Official letter must be JPG, PNG, or PDF.',
                'official_letter_document.max' => 'Official letter file size must not exceed 5MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $attachData = ['member_type_id' => $request->member_type_id];

            // Handle ID card upload
            if ($request->hasFile('id_card_document')) {
                $attachData['id_card_document'] = $this->file_service->fileUpload(
                    $request->file('id_card_document'),
                    'id-card',
                    'society/student-verification'
                );
            }

            // Handle official letter upload
            if ($request->hasFile('official_letter_document')) {
                $attachData['official_letter_document'] = $this->file_service->fileUpload(
                    $request->file('official_letter_document'),
                    'official-letter',
                    'society/student-verification'
                );
            }

            // Set upload timestamp if any document uploaded
            if (isset($attachData['id_card_document']) || isset($attachData['official_letter_document'])) {
                $attachData['documents_uploaded_at'] = now();
            }

            $user = User::whereId(current_user()->id)->first();
            $user->societies()->attach($request->society_id, $attachData);

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

    public function updateDocuments(Request $request, $society_id)
    {
        $validator = Validator::make($request->all(), [
            'id_card_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'official_letter_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'id_card_document.mimes' => 'ID card must be JPG, PNG, or PDF.',
            'id_card_document.max' => 'ID card file size must not exceed 5MB.',
            'official_letter_document.mimes' => 'Official letter must be JPG, PNG, or PDF.',
            'official_letter_document.max' => 'Official letter file size must not exceed 5MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userSociety = UserSociety::where([
            'user_id' => current_user()->id,
            'society_id' => $society_id
        ])->first();

        if (!$userSociety) {
            return redirect()->back()->with('delete', 'Society membership not found.');
        }

        $updateData = [];

        // Handle ID card upload
        if ($request->hasFile('id_card_document')) {
            // Delete old file if exists
            if ($userSociety->id_card_document) {
                $this->file_service->deleteFile(
                    $userSociety->id_card_document,
                    'society/student-verification'
                );
            }

            $updateData['id_card_document'] = $this->file_service->fileUpload(
                $request->file('id_card_document'),
                'id-card',
                'society/student-verification'
            );
        }

        // Handle official letter upload
        if ($request->hasFile('official_letter_document')) {
            // Delete old file if exists
            if ($userSociety->official_letter_document) {
                $this->file_service->deleteFile(
                    $userSociety->official_letter_document,
                    'society/student-verification'
                );
            }

            $updateData['official_letter_document'] = $this->file_service->fileUpload(
                $request->file('official_letter_document'),
                'official-letter',
                'society/student-verification'
            );
        }

        // Update timestamp
        if (!empty($updateData)) {
            $updateData['documents_uploaded_at'] = now();
            $userSociety->update($updateData);
        }

        return redirect()->back()->with('status', 'Documents uploaded successfully!');
    }
}
