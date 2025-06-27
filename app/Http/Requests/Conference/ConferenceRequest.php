<?php

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;

class ConferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'conference_name' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'conference_theme' => 'nullable',
            'conference_logo' => 'nullable|mimes:png,jpg',
            'conference_description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'early_bird_registration_deadline' => 'required|date',
            'regular_registration_deadline' => 'required|date|after_or_equal:early_bird_registration_deadline',
            'primary_color' => 'required',
            'secendary_color' => 'required',
            'venue_name' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'venue_address' => 'required',
            'venue_contact_person_name' => 'required',
            'venue_phone_number' => 'required|regex:/^\d{10}$/',
            'venue_email' => 'required|email',
            'google_map_link' => 'required',
            'start_time' => 'required',
            'organizer_name' => 'required',
            'organizer_logo' => 'nullable|mimes:jpg,png',
            'organizer_contact_person' => 'required',
            'organizer_email' => 'required|email',
            'organizer_phone_number' => 'required|regex:/^\d{10}$/',
            'organizer_description' => 'required'
        ];
    }
}
