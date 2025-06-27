<?php

namespace App\Http\Requests\Society;

use Illuminate\Foundation\Http\FormRequest;

class AddSocietyRequest extends FormRequest
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
        $emailRule = 'required|email|unique:users,email';
        $phoneRule = 'required|regex:/^\d{10}$/|unique:user_details,phone';
        $logoRule = 'required|mimes:jpg,jpeg,png|max:250';
        if (!$this->isMethod('post') && $this->society) {
            $emailRule .= ',' . $this->society->users->where('type', 2)->value('id');
            $phoneRule .= ',' . $this->society->users->where('type', 2)->value('id');
            $logoRule = 'nullable|mimes:jpg,jpeg,png|max:250';
        }
        // dd($emailRule);
        return [
            'society_name' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'abbreviation' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'address' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'contact_person' => 'required',
            'contact_person_phone' => 'required|regex:/^\d{10}$/',
            'contact_person_email' => 'required|email',
            'email' => $emailRule,
            'phone' => $phoneRule,
            'logo' => $logoRule,
            'description' => 'nullable',
        ];
    }


    public function messages(): array
    {
        return [
            'society_name.required' => 'Society Name field is required',
            'society_name.regex' => 'The name may only contain letters, numbers, and spaces.'
        ];
    }
}
