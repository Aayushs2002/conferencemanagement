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
        $submission = $this->route('submission'); // Get submission if editing
        $isUpdating = $this->isMethod('patch') || $this->isMethod('put'); // Check if it's an update request
        
        $setting = \App\Models\SubmissionSetting::where('conference_id', $conferenceId)
            ->select('attachment_name', 'attachment_required', 'competition_enabled')
            ->first();

        // Default image rule
        $imageRule = 'nullable|mimes:jpg,jpeg,png,pdf|max:250';
        
        // Check submission setting first (fallback)
        if ($setting && $setting->attachment_name && $setting->attachment_required == 1) {
            // If updating and image already exists, make it optional
            if ($isUpdating && $submission && $submission->image) {
                $imageRule = 'nullable|mimes:jpg,jpeg,png,pdf|max:250';
            } else {
                $imageRule = 'required|mimes:jpg,jpeg,png,pdf|max:250';
            }
        }

        $rules = [
            'title' => 'required',
            'article_type_id' => 'required',
            'submission_category_major_track_id' => 'required',
            'presentation_type' => 'required',
            'competition_type' => $setting && $setting->competition_enabled ? 'required|in:1,2' : 'nullable|in:1,2',
            'keywords' => 'required',
            'image' => $imageRule,
            'sections' => 'nullable|array',
            'sections.*.content' => 'nullable|string',
            'sections.*.name' => 'nullable|string',
            'sections.*.word_limit' => 'nullable|integer',
            'abstract_content' => 'nullable|string',
            'conflict_of_interest' => 'nullable|string',
            'source_of_funding' => 'nullable|string',
            'has_conflict_of_interest' => 'nullable|in:yes,no',
            'has_source_of_funding' => 'nullable|in:yes,no',
            'is_student' => 'required|boolean',
        ];

        // Get article type setting to check if fields are required (takes priority)
        if ($this->article_type_id) {
            $articleTypeSetting = \App\Models\Conference\ArticleTypeSetting::where('article_type_id', $this->article_type_id)->first();
            
            // Only apply article type settings if they exist
            if ($articleTypeSetting) {
                // If sections exist, make them required
                if ($articleTypeSetting->number_of_sections > 0) {
                    $rules['sections'] = 'required|array';
                    $rules['sections.*.content'] = 'required|string';
                    // When using sections, abstract_content should be null (not submitted by user)
                    $rules['abstract_content'] = 'nullable|string';
                } else {
                    // When not using sections, abstract_content is required
                    $rules['abstract_content'] = 'required|string';
                    $rules['sections'] = 'nullable|array';
                }
                
                // Make conflict of interest required if enabled
                if ($articleTypeSetting->is_conflict_of_interest_required) {
                    $rules['has_conflict_of_interest'] = 'required|in:yes,no';
                    // Only require details if user selected "yes"
                    if ($this->has_conflict_of_interest === 'yes') {
                        $rules['conflict_of_interest'] = 'required|string';
                    } else {
                        $rules['conflict_of_interest'] = 'nullable|string';
                    }
                }
                
                // Make source of funding required if enabled
                if ($articleTypeSetting->is_source_of_funding_required) {
                    $rules['has_source_of_funding'] = 'required|in:yes,no';
                    // Only require details if user selected "yes"
                    if ($this->has_source_of_funding === 'yes') {
                        $rules['source_of_funding'] = 'required|string';
                    } else {
                        $rules['source_of_funding'] = 'nullable|string';
                    }
                }
                
                // Handle attachment based on article type setting (overrides submission setting)
                if ($articleTypeSetting->attachment_name) {
                    if ($articleTypeSetting->is_attachment_required) {
                        // If updating and image already exists, make it optional
                        if ($isUpdating && $submission && $submission->image) {
                            $rules['image'] = 'nullable|mimes:jpg,jpeg,png,pdf|max:250';
                        } else {
                            $rules['image'] = 'required|mimes:jpg,jpeg,png,pdf|max:250';
                        }
                    } else {
                        // Optional attachment
                        $rules['image'] = 'nullable|mimes:jpg,jpeg,png,pdf|max:250';
                    }
                }
            } else {
                // No article type setting exists - fall back to default submission settings
                // Abstract content is required by default when no sections
                $rules['abstract_content'] = 'required|string';
            }
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [];
        
        // Check if sections are submitted
        if ($this->has('sections') && is_array($this->sections)) {
            foreach ($this->sections as $index => $section) {
                // Use the actual section name from the input
                $sectionName = $section['name'] ?? 'Section ' . ($index + 1);
                $attributes["sections.{$index}.content"] = $sectionName;
            }
        }
        
        return $attributes;
    }
}
