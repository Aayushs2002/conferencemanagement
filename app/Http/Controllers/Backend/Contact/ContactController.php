<?php

namespace App\Http\Controllers\Backend\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->get();
        return view('backend.contact.index', compact('contacts'));
    }

    public function show(Contact $contact)
    {
        return view('backend.contact.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        try {
            $contact->delete();
            return redirect()->back()->with('status', 'Contact deleted successfully');
        } catch (\Exception) {
            return redirect()->back()->with('delete', 'Failed to delete contact');
        }
    }
}
