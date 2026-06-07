<?php

namespace App\Http\Controllers\Backend\Template;

use App\Http\Controllers\Controller;
use App\Models\Conference\ArticleType;
use App\Models\Template\EmailTemplate;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
{
    use AuthorizesRequests;

    private function getPartnerLogos($conference)
    {
        return collect($conference->partner_logos ?? [])
            ->filter(fn($p) => is_array($p) && !empty($p['abbreviation']))
            ->values();
    }

    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        $email_templates = EmailTemplate::where('conference_id', $conference->id)->get();
        return view('backend.template.email-template.index', compact('society', 'conference', 'email_templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        $partnerLogos = $this->getPartnerLogos($conference);
        $articleTypes = ArticleType::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.template.email-template.create', compact('society', 'conference', 'partnerLogos', 'articleTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        $validated = $request->validate([
            'key'                        => 'required',
            'subject'                    => 'required',
            'body'                       => 'required',
            'partner_filter'             => 'nullable|array',
            'partner_filter.*'           => 'nullable|string',
            'article_type_filter'        => 'nullable|array',
            'article_type_filter.*'      => 'nullable|integer',
            'presentation_type_filter'   => 'nullable|array',
            'presentation_type_filter.*' => 'nullable|string',
        ]);
        try {

            $validated['conference_id'] = $conference->id;
            if (empty($validated['partner_filter']))           $validated['partner_filter'] = null;
            if (empty($validated['article_type_filter']))      $validated['article_type_filter'] = null;
            if (empty($validated['presentation_type_filter'])) $validated['presentation_type_filter'] = null;

            EmailTemplate::create($validated);

            return redirect()
                ->route('email-template.index', [$society, $conference])
                ->with('status', 'Email Template created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
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
    public function edit($society, $conference, EmailTemplate $email_template)
    {
        // $this->authorize('edit', $email_template);
        $partnerLogos = $this->getPartnerLogos($conference);
        $articleTypes = ArticleType::where(['conference_id' => $conference->id, 'status' => 1])->get();
        return view('backend.template.email-template.create', compact('society', 'conference', 'email_template', 'partnerLogos', 'articleTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, EmailTemplate $email_template)
    {
        $validated = $request->validate([
            'key'                        => 'required',
            'subject'                    => 'required',
            'body'                       => 'required',
            'partner_filter'             => 'nullable|array',
            'partner_filter.*'           => 'nullable|string',
            'article_type_filter'        => 'nullable|array',
            'article_type_filter.*'      => 'nullable|integer',
            'presentation_type_filter'   => 'nullable|array',
            'presentation_type_filter.*' => 'nullable|string',
        ]);

        $validated['conference_id'] = $conference->id;
        if (empty($validated['partner_filter']))           $validated['partner_filter'] = null;
        if (empty($validated['article_type_filter']))      $validated['article_type_filter'] = null;
        if (empty($validated['presentation_type_filter'])) $validated['presentation_type_filter'] = null;
        try {
            $email_template->update($validated);

            return redirect()
                ->route('email-template.index', [$society, $conference])
                ->with('status', 'Email Template updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, EmailTemplate $email_template)
    {
        try {
            $email_template->delete();
            return redirect()
                ->route('email-template.index', [$society, $conference])
                ->with('status', 'Email Template deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
