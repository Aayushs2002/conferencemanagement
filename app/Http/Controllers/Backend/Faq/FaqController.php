<?php

namespace App\Http\Controllers\Backend\Faq;

use App\Http\Controllers\Controller;
use App\Models\Faq\Faq;
use App\Models\Faq\FaqCategory;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }
        $faqs = Faq::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.faq.faq.index', compact('faqs', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        // $societyDetail = society_detail();

        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }
        // if (is_society_admin()) {
        //     $faq_categories = FaqCategory::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        // } else {
        //     return redirect()->route('dashboard');
        // }
        $faq_categories = FaqCategory::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.faq.faq.create', compact('faq_categories', 'society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'faq_category_id' => 'nullable',
                'question' => 'required|unique:faqs,question',
                'answer' => 'required',
            ]);
            $validated['conference_id'] = $conference->id;

            Faq::create($validated);

            return redirect()->route('faq.index', [$society, $conference])->with('status', 'Faq Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, Faq $faq)
    {
        // $societyDetail = society_detail();

        // if (is_super_admin() && empty($societyDetail)) {
        //     return redirect()->route('dashboard');
        // }
        // if (is_society_admin()) {
        //     $faq_categories = FaqCategory::where([
        //         'society_id' => current_user()->societies->value('id'),
        //         'status' => 1
        //     ])->latest()->get();
        // } elseif (is_super_admin()) {
        //     $faq_categories = FaqCategory::where([
        //         'society_id' => $societyDetail->id,
        //         'status' => 1
        //     ])->latest()->get();
        // } else {
        //     return redirect()->route('dashboard');
        // }
        $faq_categories = FaqCategory::where([
            'society_id' => $society->id,
            'status' => 1
        ])->latest()->get();
        return view('backend.faq.faq.create', compact('faq_categories', 'faq', 'conference', 'society'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, Faq $faq)
    {
        try {
            $validated = $request->validate([
                'faq_category_id' => 'nullable',
                'question' => 'required|unique:faqs,question,' . $faq->id,
                'answer' => 'required',
            ]);

            $faq->update($validated);

            return redirect()->route('faq.index', [$society, $conference])->with('status', 'Faq Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, Faq $faq)
    {
        try {
            $faq->update(['status' => 0]);

            return redirect()->route('faq.index', [$society, $conference])->with('delete', 'Faq  Deleted Successfully');
        } catch (QueryException $e) {
            return redirect()->back()->with('delete', 'Cannot delete this faq.');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function changeStatus(Faq $faq)
    {
        if ($faq->visible_status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }

        $faq->update(['visible_status' => $status]);

        return redirect()->back()->with('status', 'Faq publish status changed successfully.');
    }
}
