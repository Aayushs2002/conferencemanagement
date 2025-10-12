<?php

namespace App\Http\Controllers\Frontend\MainPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactController extends Controller
{
    public function __construct(
        private readonly Contact $contact
    ) {}

    public function index(): View
    {
        return view('frontend.main-page.contact-us.index');
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        try {
            $this->contact->create($request->validated());

            return redirect()
                ->back()
                ->with('success', 'Your conference details have been submitted successfully! We will contact you soon.');
        } catch (Throwable $e) {
            Log::error('Error creating contact request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->validated()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }
}
