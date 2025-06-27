<?php

namespace App\Http\Requests\Participant;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
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
            'title' => 'required',
            'article_type' => 'required',
            'submission_category_major_track_id' => 'required',
            'presentation_type' => 'required',
            'keywords' => 'required',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:250'
        ];
    }
}
