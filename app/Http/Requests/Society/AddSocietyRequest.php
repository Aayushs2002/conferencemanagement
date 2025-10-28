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
        $phoneRule = 'required|string|min:10|max:15';
        $logoRule = 'required|mimes:jpg,jpeg,png|max:2048';
        $subDomainRule = 'required|unique:societies,sub_domain_name|regex:/^[a-z0-9-]+$/';
        
        if (!$this->isMethod('post') && $this->society) {
            $userId = $this->society->users->where('type', 2)->first()?->id;
            if ($userId) {
                $emailRule .= ',' . $userId;
            }
            $logoRule = 'nullable|mimes:jpg,jpeg,png|max:2048';
            $subDomainRule = 'required|unique:societies,sub_domain_name,' . $this->society->id . '|regex:/^[a-z0-9-]+$/';
        }
        
        return [
            'society_name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|min:10|max:15',
            'contact_person_email' => 'required|email|max:255',
            'email' => $emailRule,
            'phone' => $phoneRule,
            'logo' => $logoRule,
            'description' => 'nullable|string|max:1000',
            'sub_domain_name' => $subDomainRule,
            'features' => 'required|array|min:1',
            'features.*' => 'exists:features,id',
        ];
    }


    public function messages(): array
    {
        return [
            'society_name.required' => 'Society Name field is required.',
            'society_name.max' => 'Society Name may not exceed 255 characters.',
            'abbreviation.required' => 'Abbreviation field is required.',
            'address.required' => 'Address field is required.',
            'contact_person.required' => 'Contact Person field is required.',
            'contact_person_phone.required' => 'Contact Person Phone field is required.',
            'contact_person_phone.min' => 'Contact Person Phone must be at least 10 digits.',
            'contact_person_email.required' => 'Contact Person Email field is required.',
            'contact_person_email.email' => 'Contact Person Email must be a valid email address.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Phone field is required.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'logo.required' => 'Logo is required.',
            'logo.mimes' => 'Logo must be a file of type: jpg, jpeg, png.',
            'logo.max' => 'Logo size may not exceed 2MB.',
            'sub_domain_name.required' => 'Sub Domain Name is required.',
            'sub_domain_name.unique' => 'This subdomain is already taken.',
            'sub_domain_name.regex' => 'Subdomain may only contain lowercase letters, numbers, and hyphens.',
            'features.required' => 'Please select at least one feature.',
            'features.min' => 'Please select at least one feature.',
            'features.*.exists' => 'Selected feature is invalid.',
        ];
    }
}
