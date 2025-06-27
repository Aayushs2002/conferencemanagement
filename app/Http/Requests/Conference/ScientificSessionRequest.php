<?php

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;

class ScientificSessionRequest extends FormRequest
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

        $req = request()->all();
        $rules = [
            'day' => 'required',
            'hall_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'scientific_session_category_id' => 'required',
            'presenter_id' => 'nullable',
            'description' => 'nullable'
        ];

        if (isset($req['scientific_session_category_id']) && $req['scientific_session_category_id'] == 5) {
            $rules['session_chair_id'] = 'nullable';
        } else {
            $rules['session_chair_id'] = 'required';
        }

        if (isset($req['is_from_submission'])&&$req['is_from_submission'] == 1) {
            $rules['submission_id'] = 'required';
            $rules['topic'] = 'nullable';
        } else {
            $rules['submission_id'] = 'nullable';
            $rules['topic'] = 'required';
        }

        return $rules;
    }
}
