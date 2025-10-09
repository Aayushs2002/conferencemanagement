<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.main-page.contact-us.index');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'conference_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'no_of_national_participant' => 'required|integer|min:0',
            'no_of_international_participant' => 'required|integer|min:0',
            'query' => 'nullable|string|max:1000'
        ], [
            'full_name.required' => 'Full name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'contact_number.required' => 'Contact number is required.',
            'conference_type.required' => 'Conference type is required.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'End date must be equal to or after the start date.',
            'no_of_national_participant.required' => 'Number of national participants is required.',
            'no_of_national_participant.integer' => 'National participants must be a number.',
            'no_of_national_participant.min' => 'National participants cannot be negative.',
            'no_of_international_participant.required' => 'Number of international participants is required.',
            'no_of_international_participant.integer' => 'International participants must be a number.',
            'no_of_international_participant.min' => 'International participants cannot be negative.',
        ]);

        try {
            Contact::create($validated);

            return redirect()->back()->with('success', 'Your conference details have been submitted successfully! We will contact you soon.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
