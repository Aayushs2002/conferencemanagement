<?php

namespace App\Http\Controllers\Backend\Template;

use App\Http\Controllers\Controller;
use App\Models\Template\EmailTemplate;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
{
    use AuthorizesRequests;
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
        return view('backend.template.email-template.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        $validated = $request->validate([
            'key' => [
                'required',
                Rule::unique('email_templates')
                    ->where('conference_id', $conference->id)
            ],
            'subject' => 'required',
            'body' => 'required'
        ]);
        try {

            $validated['conference_id'] = $conference->id;

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
        return view('backend.template.email-template.create', compact('society', 'conference', 'email_template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, EmailTemplate $email_template)
    {
        $validated = $request->validate([
            'key' => [
                'required',
                Rule::unique('email_templates')
                    ->where('conference_id', $conference->id)
                    ->ignore($email_template->id)
            ],
            'subject' => 'required',
            'body' => 'required'
        ]);

        $validated['conference_id'] = $conference->id;
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
    public function destroy(string $id)
    {
        //
    }
}
