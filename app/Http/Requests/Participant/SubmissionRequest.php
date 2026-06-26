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
        $submission = $this->route('submission');
        $isUpdating = $this->isMethod('patch') || $this->isMethod('put');
        $isDraft = $this->boolean('is_draft');

        if ($isDraft) {
            return [
                'title' => 'nullable|string|max:255',
                'article_type_id' => 'nullable',
                'submission_category_major_track_id' => 'nullable',
                'presentation_type' => 'nullable',
                'competition_type' => 'nullable',
                'keywords' => 'nullable',
                'image' => 'nullable|mimes:jpg,jpeg,png,pdf|max:250',
                'sections' => 'nullable|array',
                'sections.*.content' => 'nullable|string',
                'sections.*.name' => 'nullable|string',
                'sections.*.word_limit' => 'nullable|integer',
                'abstract_content' => 'nullable|string',
                'video_link' => 'nullable|url|max:500',
                'conflict_of_interest' => 'nullable|string',
                'source_of_funding' => 'nullable|string',
                'has_conflict_of_interest' => 'nullable|in:yes,no',
                'has_source_of_funding' => 'nullable|in:yes,no',
                'main_author' => 'nullable|in:0,1',
                'main_presenter' => 'nullable|in:0,1',
                'authors' => 'nullable|array',
                'authors.*.name' => 'nullable|string',
                'authors.*.email' => 'nullable|email',
                'authors.*.phone' => 'nullable|string',
                'authors.*.designation' => 'nullable|string',
                'authors.*.institution' => 'nullable|string',
                'authors.*.institution_address' => 'nullable|string',
                'authors.*.main_author' => 'nullable|in:0,1',
                'authors.*.main_presenter' => 'nullable|in:0,1',
                'authors.*.contributions' => 'nullable|array',
                'authors.*.contribution_other_checkbox' => 'nullable',
                'authors.*.contribution_other_text' => 'nullable',
                'is_draft' => 'nullable|boolean',
                'collaborative_partner' => 'nullable|string',
            ];
        }

        $setting = \App\Models\SubmissionSetting::where('conference_id', $conferenceId)
            ->select('attachment_name', 'attachment_required', 'competition_enabled', 'contribution_enabled')
            ->first();

        $imageRule = 'nullable|mimes:jpg,jpeg,png,pdf|max:250';

        if ($setting && $setting->attachment_name && $setting->attachment_required == 1) {
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
            'video_link' => 'nullable|url|max:500',
            'conflict_of_interest' => 'nullable|string',
            'source_of_funding' => 'nullable|string',
            'has_conflict_of_interest' => 'nullable|in:yes,no',
            'has_source_of_funding' => 'nullable|in:yes,no',
            // 'is_student' => 'required|boolean',
            'main_author' => 'nullable|in:0,1',
            'authors' => 'nullable|array',
            'authors.*.name' => 'required|string',
            'authors.*.email' => 'required|email',
            'authors.*.phone' => 'nullable|string',
            'authors.*.designation' => 'nullable|string',
            'authors.*.institution' => 'required|string',
            'authors.*.institution_address' => 'required|string',
            'authors.*.main_author' => 'nullable|in:0,1',
            'authors.*.contributions' => 'nullable|array',
            'authors.*.contribution_other_checkbox' => 'nullable',
            'authors.*.contribution_other_text' => 'nullable|required_if:authors.*.contribution_other_checkbox,1',
        ];

        // Validate unique emails and phones across all co-authors for this submission
        if ($this->has('authors') && is_array($this->authors)) {
            $emails = [];
            $phones = [];

            foreach ($this->authors as $index => $author) {
                // Check email uniqueness
                if (!empty($author['email'])) {
                    if (in_array(strtolower($author['email']), array_map('strtolower', $emails))) {
                        $rules["authors.{$index}.email"] = 'required|email|distinct';
                    }
                    $emails[] = $author['email'];
                }

                // Check phone uniqueness
                if (!empty($author['phone'])) {
                    if (in_array($author['phone'], $phones)) {
                        $rules["authors.{$index}.phone"] = 'nullable|string|distinct';
                    }
                    $phones[] = $author['phone'];
                }

                // Validate contributions if enabled - require at least one contribution OR other text
                if ($setting && $setting->contribution_enabled) {
                    $hasContributions = !empty($author['contributions']) && count($author['contributions']) > 0;
                    $hasOtherText = !empty($author['contribution_other_checkbox']) && !empty($author['contribution_other_text']);

                    // If neither contributions nor other text is provided, make contributions required
                    if (!$hasContributions && !$hasOtherText) {
                        $rules["authors.{$index}.contributions"] = 'required|array|min:1';
                    } else {
                        // At least one form of contribution exists
                        $rules["authors.{$index}.contributions"] = 'nullable|array';
                    }
                }
            }
        }
        // dd($this->article_type_id);
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

        if ($this->has('authors') && is_array($this->authors)) {
            foreach ($this->authors as $index => $author) {
                $attributes["authors.{$index}.name"] = 'Co-Author ' . ($index + 1) . ' Name';
                $attributes["authors.{$index}.email"] = 'Co-Author ' . ($index + 1) . ' Email';
                $attributes["authors.{$index}.phone"] = 'Co-Author ' . ($index + 1) . ' Phone';
                $attributes["authors.{$index}.designation"] = 'Co-Author ' . ($index + 1) . ' Designation';
                $attributes["authors.{$index}.institution"] = 'Co-Author ' . ($index + 1) . ' Institution';
                $attributes["authors.{$index}.institution_address"] = 'Co-Author ' . ($index + 1) . ' Institution Address';
                $attributes["authors.{$index}.contribution_other_text"] = 'Co-Author ' . ($index + 1) . ' Other Contribution';
            }
        }

        return $attributes;
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'authors.*.email.distinct' => 'Each co-author must have a unique email address.',
            'authors.*.phone.distinct' => 'Each co-author must have a unique phone number.',
            'authors.*.designation.required' => 'Designation is required for all co-authors.',
            'authors.*.institution.required' => 'Institution is required for all co-authors.',
            'authors.*.institution_address.required' => 'Institution address is required for all co-authors.',
            'authors.*.contributions.required' => 'Please select at least one contribution or specify other contribution.',
            'authors.*.contributions.min' => 'Please select at least one contribution or specify other contribution.',
        ];
    }
}
