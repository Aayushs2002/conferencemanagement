<?php

namespace App\Http\Controllers\Backend\Committee;

use App\Http\Controllers\Controller;
use App\Models\Committee\Committee;
use App\Models\Committee\CommitteeDesignation;
use App\Models\Committee\CommitteeMember;
use App\Models\User;
use App\Models\User\Society;
use Exception;
use Illuminate\Http\Request;

class CommitteeMemberController extends Controller
{
    public function index($society, $conference, $slug)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }

        $committee = Committee::where(['slug' => $slug])->first();
        $committee_members = CommitteeMember::where(['conference_id' => $conference->id, 'committee_id' => $committee->id, 'status' => 1])->get();
        return view('backend.committee.committee-member.index', compact('committee_members', 'committee', 'society', 'conference'));
    }

    public function create($society, $conference, $slug)
    {
        // $conferenceDetail = conference_detail();
        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $committee = Committee::whereSlug($slug)->first();
        $committee_designations = CommitteeDesignation::where(['society_id' => $conference->society_id, 'status' => 1])->get();

        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        return view('backend.committee.committee-member.create', compact('committee', 'committee_designations', 'users', 'society', 'conference'));
    }

    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'committee_id' => 'required',
                'user_id' => 'required|array',
                'designation_id' => 'required',
                'message' => 'nullable'
            ], [
                'user_id.required' => 'Atleast one user is required.',
                'designation_id.required' => 'Designation is required'
            ]);

            $designation = CommitteeDesignation::whereId($request->designation_id)->first();

            if ($designation->designation != 'Member' && count($validated['user_id']) > 1) {
                return redirect()->back()->withInput()->with('delete', 'Multiple members cannot be added beside Member designation.');
            }

            $validated['conference_id'] = $conference->id;
            $validated['slug'] = slugify($designation->designation);
            $committee = Committee::whereId($validated['committee_id'])->first();

            foreach ($validated['user_id'] as $value) {
                $checkDataExist = CommitteeMember::where(['conference_id' => $conference->id, 'committee_id' => $validated['committee_id'], 'user_id' => $value, 'status' => 1])->first();
                if (!empty($checkDataExist)) {
                    throw new Exception("User is already added as committee member.", 1);
                }
            }

            foreach ($validated['user_id'] as $value) {
                $data['conference_id'] = $conference->id;
                $data['committee_id'] = $validated['committee_id'];
                $data['user_id'] = $value;
                $data['designation_id'] = $validated['designation_id'];
                $data['message'] = $validated['message'];
                $data['slug'] = slugify($designation->designation);
                $data['created_at'] = now();
                $data['updated_at'] = now();
                $insertArray[] = $data;
            }

            CommitteeMember::insert($insertArray);

            return redirect()->route('committeeMember.index', [$society, $conference, $committee->slug])->with('status', 'Committee Member Added Successfully');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('delete', $e->getMessage());
        }
    }

    public function edit($society, $conference, CommitteeMember $committee_member)
    {
        // $conferenceDetail = conference_detail();
        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $committee = Committee::whereId($committee_member->committee_id)->first();
        $committee_designations = CommitteeDesignation::where(['society_id' => $conference->society_id, 'status' => 1])->get();

        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        // dd($users);
        return view('backend.committee.committee-member.create', compact('committee', 'committee_designations', 'users', 'committee_member', 'society', 'conference'));
    }

    public function update($society, $conference, Request $request, CommitteeMember $committee_member)
    {
        try {
            $validated = $request->validate([
                'committee_id' => 'required',
                'user_id' => 'required',
                'designation_id' => 'required',
                'message' => 'nullable'
            ]);

            $designation = CommitteeDesignation::whereId($request->designation_id)->first();
            $validated['slug'] = slugify($designation->designation);
            $committee = Committee::whereId($validated['committee_id'])->first();
            $validated['user_id'] = $request->user_id[0];

            $committee_member->update($validated);

            return redirect()->route('committeeMember.index', [$society, $conference, $committee->slug])->with('status', 'Committee Member Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('delete', $e->getMessage());
        }
    }

    public function destroy($society, $conference, CommitteeMember $committee_member)
    {
        $committee_member->update(['status' => 0]);
        $committee = Committee::whereId($committee_member->committee_id)->first();

        return redirect()->route('committeeMember.index', [$society, $conference, $committee->slug])->with('delete', 'Committee Member Deleted Successfully');
    }

    public function changeFeatured($society, $conference, CommitteeMember $committee_member)
    {
        if ($committee_member->is_featured == 1) {
            $isFeatured = 0;
        } else {
            $isFeatured = 1;
        }

        $committee_member->update(['is_featured' => $isFeatured]);

        return redirect()->back()->with('status', 'Committee Member featured status changed successfully.');
    }
}
