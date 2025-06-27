<?php

namespace App\Http\Requests\conference;

use Illuminate\Foundation\Http\FormRequest;

class WorkshopRequest extends FormRequest
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
            'workshop_title' => 'required|string|max:255',
            'workshop_type' => 'required|integer|in:1,2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'registration_deadline' => 'required|date|before_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'contact_person_email' => 'required|email|max:255',
            'no_of_participants' => 'required|integer|min:1',
            'workshop_description' => 'required|string',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:500',
            'google_map_link' => 'required|url',
            'chairperson_id' => 'required|integer|exists:users,id',
            'photo' => 'nullable|mimes:png,jpg,jpeg|max:250',
            'short_cv' => 'required|string'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $startTime = $this->input('start_time');
            $endTime = $this->input('end_time');
            $registrationDeadline = $this->input('registration_deadline');
            $startDate = $this->input('start_date');

            if ($startTime && $endTime && strtotime($endTime) <= strtotime($startTime)) {
                $validator->errors()->add('end_time', 'End time must be after start time.');
            }

            if ($registrationDeadline && $startDate && strtotime($registrationDeadline) > strtotime($startDate)) {
                $validator->errors()->add('registration_deadline', 'Registration deadline must be on or before the start date.');
            }

            if ($registrationDeadline && strtotime($registrationDeadline) < strtotime(date('Y-m-d'))) {
                $validator->errors()->add('registration_deadline', 'Registration deadline cannot be in the past.');
            }
        });
    }
}
