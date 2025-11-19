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
        $conferenceId = $this->route('conference')->id;
        // dd(  $this->route('conference'));
        $setting = \App\Models\SubmissionSetting::where('conference_id', $conferenceId)
            ->select('attachment_name', 'attachment_required')
            ->first();

        $imageRule = 'nullable|mimes:jpg,jpeg,png,pdf|max:250';
        
        if ($setting && $setting->attachment_name && $setting->attachment_required == 1) {
            $imageRule = 'required|mimes:jpg,jpeg,png,pdf|max:250';
        }

        return [
            'title' => 'required',
            'article_type' => 'required',
            'submission_category_major_track_id' => 'required',
            'presentation_type' => 'required',
            'keywords' => 'required',
            'image' => $imageRule
        ];
    }
}
