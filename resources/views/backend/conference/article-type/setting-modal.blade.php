<div class="modal-header">
    <h5 class="modal-title" id="settingModalLabel">Article Type Settings - {{ $articleType->name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="settingForm" action="{{ route('articleType.settingSubmit', [$society, $conference]) }}" method="POST">
    @csrf
    <input type="hidden" name="article_type_id" value="{{ $articleType->id }}">

    <div class="modal-body">
        <div class="row">
            <!-- Scoring Configuration -->
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-circle p-2 me-2">
                                    <i class="ti tabler-stars fs-5"></i>
                                </div>
                                <h6 class="mb-0 text-white fw-semibold">Scoring Configuration</h6>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="scoring_allowed" 
                                       name="scoring_allowed" value="1" style="width: 3rem; height: 1.5rem;"
                                       {{ old('scoring_allowed', $setting->scoring_allowed ?? 1) ? 'checked' : '' }}>
                                <label class="form-check-label text-white fw-semibold ms-2" for="scoring_allowed">
                                    Scoring Enabled
                                </label> 
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4" id="scoringConfigBody">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <label for="total_marks" class="form-label fw-semibold mb-2">
                                    Total Marks <span class="text-danger scoring-required">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="ti tabler-award text-primary"></i>
                                    </span>
                                    <input type="number" class="form-control border-start-0" id="total_marks" 
                                           name="total_marks" 
                                           value="{{ old('total_marks', $setting->total_marks ?? 10) }}"
                                           placeholder="e.g., 10" min="1" max="100" required>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti tabler-info-circle me-1"></i>Maximum marks for this article type
                                </small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="alert alert-info border-0 mb-0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                                    <div class="d-flex align-items-start">
                                        <i class="ti tabler-bulb text-primary fs-4 me-2 mt-1"></i>
                                        <div>
                                            <strong class="d-block mb-1">Tip:</strong>
                                            <small>If sections don't cover total marks, reviewers will see an "Overall Rating" field for the remaining marks.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Title Scoring Configuration -->
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-circle p-2 me-2">
                                    <i class="ti tabler-heading fs-5"></i>
                                </div>
                                <h6 class="mb-0 text-white fw-semibold">Title Scoring</h6>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="title_scoring_enabled" 
                                       name="title_scoring_enabled" value="1" style="width: 3rem; height: 1.5rem;"
                                       {{ old('title_scoring_enabled', $setting->title_scoring_enabled ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label text-white fw-semibold ms-2" for="title_scoring_enabled">
                                    Enable Title Scoring
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4" id="titleScoringBody" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title_max_marks" class="form-label fw-semibold mb-2">
                                    Title Maximum Marks <span class="text-danger title-scoring-required">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="ti tabler-star text-warning"></i>
                                    </span>
                                    <input type="number" class="form-control border-start-0" id="title_max_marks" 
                                           name="title_max_marks" 
                                           value="{{ old('title_max_marks', $setting->title_max_marks ?? 0) }}"
                                           placeholder="e.g., 2" min="0" step="0.5">
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti tabler-info-circle me-1"></i>Maximum marks for the article title
                                </small>
                                <div id="titleMarksFeedback" class="mt-1" style="display: none;"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="alert alert-info border-0 mb-0" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);">
                                    <div class="d-flex align-items-start">
                                        <i class="ti tabler-info-circle text-warning fs-4 me-2 mt-1"></i>
                                        <div>
                                            <strong class="d-block mb-1">Note:</strong>
                                            <small>Title marks will be included in the total marks calculation along with section marks.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-0">
                                <label for="title_reviewer_instruction" class="form-label fw-semibold mb-2">
                                    <i class="ti tabler-checklist text-success me-1"></i>Reviewer Instructions for Title
                                </label>
                                <textarea class="form-control" id="title_reviewer_instruction" name="title_reviewer_instruction" rows="3" 
                                          placeholder="Criteria for reviewers when rating the title (e.g., Assess clarity, relevance, accuracy, and engagement of the title)">{{ old('title_reviewer_instruction', $setting->title_reviewer_instruction ?? '') }}</textarea>
                                <small class="text-muted d-block mt-1">
                                    <i class="ti tabler-info-circle me-1"></i>These instructions will be shown to reviewers during title evaluation
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Number of Sections -->
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm"> 
                    <div class="card-header bg-primary border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-circle p-2 me-2">
                                    <i class="ti tabler-list fs-5"></i>
                                </div>
                                <h6 class="mb-0 text-white fw-semibold">Article Sections</h6>
                            </div>
                            <button type="button" class="btn btn-light btn-sm rounded-pill px-3" id="addSectionBtn">
                                <i class="ti tabler-plus me-1"></i>Add Section
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <input type="hidden" id="number_of_sections" name="number_of_sections" value="{{ old('number_of_sections', $setting->number_of_sections ?? 0) }}">

                        <!-- Dynamic Sections Container -->
                        <div id="sectionsContainer" class="mt-3">
                            @if(isset($setting->sections) && is_array($setting->sections))
                                @foreach($setting->sections as $index => $section)
                                    <div class="section-group mb-3 p-4 border rounded-3 position-relative" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px solid #dee2e6 !important;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute rounded-circle remove-section-btn" style="top: 12px; right: 12px; width: 32px; height: 32px; padding: 0;" title="Remove Section">
                                            <i class="ti tabler-x"></i>
                                        </button>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="ti tabler-file-text text-primary"></i>
                                            </div>
                                            <h6 class="text-primary fw-semibold mb-0">Section {{ $index + 1 }}</h6>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label fw-semibold mb-2">Section Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="section_name[]" 
                                                       value="{{ $section['name'] ?? '' }}" 
                                                       placeholder="e.g., Introduction, Methods, Results" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold mb-2">Word Limit <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="ti tabler-file-text text-muted"></i>
                                                    </span>
                                                    <input type="number" class="form-control" name="section_word_limit[]" 
                                                           value="{{ $section['word_limit'] ?? '' }}" 
                                                           placeholder="e.g., 500" min="1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3 section-scoring-field">
                                                <label class="form-label fw-semibold mb-2">Maximum Marks <span class="text-danger scoring-required">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="ti tabler-star text-warning"></i> 
                                                    </span>
                                                    <input type="number" class="form-control section-max-marks" name="section_max_marks[]" 
                                                           value="{{ $section['max_marks'] ?? 2 }}" 
                                                           placeholder="2" min="0" step="0.5" required>
                                                </div>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="ti tabler-info-circle me-1"></i>Max score reviewers can award
                                                </small>
                                                <div class="section-marks-feedback mt-1" style="display: none;"></div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label fw-semibold mb-2">
                                                    <i class="ti tabler-user-edit text-info me-1"></i>Author Instructions
                                                </label>
                                                <textarea class="form-control" name="section_instruction[]" rows="2" 
                                                          placeholder="Instructions for authors when writing this section">{{ $section['instruction'] ?? '' }}</textarea>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="ti tabler-info-circle me-1"></i>Shown to authors during submission
                                                </small>
                                            </div>
                                            <div class="col-md-12 mb-0 section-scoring-field">
                                                <label class="form-label fw-semibold mb-2">
                                                    <i class="ti tabler-checklist text-success me-1"></i>Reviewer Instructions
                                                </label>
                                                <textarea class="form-control" name="section_reviewer_instruction[]" rows="2" 
                                                          placeholder="Criteria for reviewers when rating this section">{{ $section['reviewer_instruction'] ?? '' }}</textarea>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="ti tabler-info-circle me-1"></i>Shown to reviewers during evaluation
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif 
                        </div>
                        
                        <div class="alert alert-light border mb-0 mt-2" style="background-color: #f8f9fa;">
                            <i class="ti tabler-info-circle text-primary me-2"></i>
                            <small class="text-muted">Click <strong>"Add Section"</strong> to create new sections. Remove sections using the <i class="ti tabler-x"></i> button.</small>
                        </div>
                        
                        <!-- Overall Rating Instructions (appears when section marks < total marks) -->
                        <div id="overallInstructionField" class="mt-3" style="display: none;">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-warning bg-opacity-25 rounded-circle p-2 me-2">
                                            <i class="ti tabler-alert-triangle text-warning fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark">Overall Rating Instructions Required</h6>
                                            <small class="text-muted" id="remainingMarksText">Section marks don't add up to total marks.</small>
                                        </div>
                                    </div>
                                    <label for="overall_instruction" class="form-label fw-semibold mb-2">
                                        <i class="ti tabler-checklist text-warning me-1"></i>Instructions for Reviewers
                                    </label>
                                    <textarea class="form-control" id="overall_instruction" name="overall_instruction" rows="3"
                                        placeholder="Instructions for reviewers when rating the overall score (e.g., Consider grammar, language quality, consistency, formatting, etc.)">{{ old('overall_instruction', $setting->overall_instruction ?? '') }}</textarea>
                                    <small class="text-muted d-block mt-1">
                                        <i class="ti tabler-info-circle me-1"></i>These instructions will be shown to reviewers along with the overall rating field
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachment Settings -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-info ">
                        <h6 class="mb-0 text-white">Attachment Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3 mt-3">
                            <input class="form-check-input" type="checkbox" id="is_attachment_required" 
                                   name="is_attachment_required" value="1"
                                   {{ old('is_attachment_required', $setting->is_attachment_required ?? 0) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_attachment_required">
                                Attachment Required
                            </label>
                        </div>
                        
                        <div id="attachmentNameField" style="display: {{ old('is_attachment_required', $setting->is_attachment_required ?? 0) ? 'block' : 'none' }}">
                            <label for="attachment_name" class="form-label">Attachment Field Name</label>
                            <input type="text" class="form-control" id="attachment_name" 
                                   name="attachment_name" 
                                   value="{{ old('attachment_name', $setting->attachment_name ?? '') }}"
                                   placeholder="e.g., Research Paper PDF, Full Article Document">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Type Access Control -->
            @php
                $allowedIds = old('allowed_member_type_ids') ?? $setting->allowed_member_type_ids ?? [];
                $restrictEnabled = !empty($allowedIds);
            @endphp
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-circle p-2 me-2">
                                    <i class="ti tabler-shield-lock fs-5 "></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-white fw-semibold">Submission Access Control</h6>
                                    <small class="text-white opacity-75">Restrict which member types can submit this article type</small>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="restrict_by_member_type"
                                       style="width: 3rem; height: 1.5rem;"
                                       {{ $restrictEnabled ? 'checked' : '' }}>
                                <label class="form-check-label text-white fw-semibold ms-2" for="restrict_by_member_type">
                                    Restrict Access
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4" id="memberTypeBody" style="{{ $restrictEnabled ? '' : 'display: none;' }}">
                        @if(isset($memberTypes) && $memberTypes->count() > 0)
                            <div class="d-flex align-items-center mb-3 p-3 rounded-3" style="background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%); border: 1px solid #ffd54f;">
                                <i class="ti tabler-info-circle text-warning me-2 fs-5"></i>
                                <small class="text-muted">Select the member types that are allowed to see and submit this article type. If <strong>no types</strong> are selected while restriction is enabled, all users can still submit.</small>
                            </div>
                            <div class="row g-3">
                                @foreach($memberTypes as $memberType)
                                    @php $isChecked = in_array($memberType->id, (array) $allowedIds); @endphp
                                    <div class="col-md-4 col-sm-6">
                                        <div class="member-type-card card h-100 cursor-pointer {{ $isChecked ? 'border-warning shadow-sm' : 'border' }}"
                                             style="{{ $isChecked ? 'background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%); border-color: #ffc107 !important;' : '' }}"
                                             onclick="document.getElementById('member_type_{{ $memberType->id }}').click()">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start">
                                                    <input class="form-check-input member-type-checkbox flex-shrink-0 mt-1 me-3"
                                                           type="checkbox"
                                                           id="member_type_{{ $memberType->id }}"
                                                           name="allowed_member_type_ids[]"
                                                           value="{{ $memberType->id }}"
                                                           onclick="event.stopPropagation()"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <i class="ti tabler-id-badge-2 text-warning me-1 fs-5"></i>
                                                            <span class="fw-semibold text-dark">{{ $memberType->type }}</span>
                                                        </div>
                                                        {{-- @if($memberType->is_society_member)
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                                <i class="ti tabler-check me-1"></i>Society Member
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                                <i class="ti tabler-user me-1"></i>General
                                                            </span>
                                                        @endif --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="alert alert-warning border-0 mb-0 mt-3" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);">
                                <div class="d-flex align-items-center">
                                    <i class="ti tabler-alert-triangle text-warning me-2 fs-5"></i>
                                    <small><strong>Important:</strong> Users whose member type is not selected will not see this article type in the submission form.</small>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info border-0 mb-0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                                <div class="d-flex align-items-center">
                                    <i class="ti tabler-info-circle text-info me-2 fs-5"></i>
                                    <div>
                                        <strong class="d-block mb-1">No Member Types Found</strong>
                                        <small class="text-muted">No active member types are configured for this society. Add member types in the society settings to enable access control.</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Other Settings -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0 text-white">Additional Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3 mt-3">
                                <label for="author_limit" class="form-label">Author Limit</label>
                                <input type="number" class="form-control" id="author_limit" 
                                       name="author_limit" 
                                       value="{{ old('author_limit', $setting->author_limit ?? '') }}"
                                       placeholder="e.g., 5" min="1">
                                <small class="text-muted">Maximum number of authors allowed</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_conflict_of_interest_required" 
                                           name="is_conflict_of_interest_required" value="1"
                                           {{ old('is_conflict_of_interest_required', $setting->is_conflict_of_interest_required ?? 0) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_conflict_of_interest_required">
                                        Conflict of Interest Required
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_source_of_funding_required" 
                                           name="is_source_of_funding_required" value="1"
                                           {{ old('is_source_of_funding_required', $setting->is_source_of_funding_required ?? 0) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_source_of_funding_required">
                                        Source of Funding Required
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Toggle scoring fields based on scoring_allowed checkbox
        function toggleScoringFields() {
            const scoringEnabled = $('#scoring_allowed').is(':checked');
            
            if (scoringEnabled) {
                $('#scoringConfigBody').slideDown();
                $('.section-scoring-field').slideDown();
                $('.scoring-required').show();
                $('#total_marks').prop('required', true);
                $('.section-max-marks').prop('required', true);
                // Also check title scoring state
                toggleTitleScoringFields();
                checkOverallInstructionRequired();
            } else {
                $('#scoringConfigBody').slideUp();
                $('.section-scoring-field').slideUp();
                $('.scoring-required').hide();
                $('#total_marks').prop('required', false);
                $('.section-max-marks').prop('required', false);
                $('#titleScoringBody').slideUp();
                $('#title_scoring_enabled').prop('disabled', true);
                $('#overallInstructionField').slideUp();
            }
        }

        // Toggle title scoring fields
        function toggleTitleScoringFields() {
            const scoringEnabled = $('#scoring_allowed').is(':checked');
            const titleScoringEnabled = $('#title_scoring_enabled').is(':checked');
            
            if (!scoringEnabled) {
                $('#titleScoringBody').slideUp();
                $('#title_scoring_enabled').prop('disabled', true);
                $('#title_max_marks').prop('required', false);
                return;
            }
            
            $('#title_scoring_enabled').prop('disabled', false);
            
            if (titleScoringEnabled) {
                $('#titleScoringBody').slideDown();
                $('.title-scoring-required').show();
                $('#title_max_marks').prop('required', true);
            } else {
                $('#titleScoringBody').slideUp();
                $('.title-scoring-required').hide();
                $('#title_max_marks').prop('required', false);
            }
            
            checkOverallInstructionRequired();
        }

        // Check if overall instruction is required
        function checkOverallInstructionRequired() {
            if (!$('#scoring_allowed').is(':checked')) {
                $('#overallInstructionField').hide();
                $('.section-marks-feedback').hide().html('');
                $('#titleMarksFeedback').hide().html('');
                return;
            }

            const totalMarks = parseFloat($('#total_marks').val()) || 10;
            let sectionMarksSum = 0;
            let titleMarks = 0;
            
            // Add title marks if title scoring is enabled
            if ($('#title_scoring_enabled').is(':checked')) {
                titleMarks = parseFloat($('#title_max_marks').val()) || 0;
            }
            
            // Calculate the total sum of all section marks
            $('.section-max-marks').each(function() {
                const value = parseFloat($(this).val()) || 0;
                sectionMarksSum += value;
            });
            
            // Calculate combined marks (title + sections)
            const combinedMarks = titleMarks + sectionMarksSum;
            
            // Show validation for title marks
            if ($('#title_scoring_enabled').is(':checked')) {
                const titleFeedback = $('#titleMarksFeedback');
                if (titleMarks < 0) {
                    titleFeedback.html(`<small class="text-danger"><i class="ti tabler-alert-circle me-1"></i>Marks cannot be negative</small>`).show();
                } else if (combinedMarks > totalMarks) {
                    const excess = combinedMarks - totalMarks;
                    titleFeedback.html(`<small class="text-danger"><i class="ti tabler-alert-circle me-1"></i>Combined marks (${combinedMarks}) exceed total marks (${totalMarks}) by ${excess}</small>`).show();
                } else {
                    titleFeedback.hide().html('');
                }
            }
            
            // Show validation messages for section marks based on the total sum
            $('.section-max-marks').each(function() {
                const value = parseFloat($(this).val()) || 0;
                const feedbackDiv = $(this).closest('.section-scoring-field').find('.section-marks-feedback');
                
                if (value < 0) {
                    feedbackDiv.html(`<small class="text-danger"><i class="ti tabler-alert-circle me-1"></i>Marks cannot be negative</small>`).show();
                } else if (combinedMarks > totalMarks) {
                    const excess = combinedMarks - totalMarks;
                    feedbackDiv.html(`<small class="text-danger"><i class="ti tabler-alert-circle me-1"></i>Combined marks (${combinedMarks}) exceed total marks (${totalMarks}) by ${excess}</small>`).show();
                } else {
                    feedbackDiv.hide().html('');
                }
            });
            
            // Show overall instruction field based on combined sum
            if (combinedMarks < totalMarks && combinedMarks >= 0) {
                const remaining = totalMarks - combinedMarks;
                let breakdownText = '';
                if (titleMarks > 0) {
                    breakdownText = `(${totalMarks} - ${titleMarks} title - ${sectionMarksSum} sections)`;
                } else {
                    breakdownText = `(${totalMarks} - ${sectionMarksSum})`;
                }
                $('#remainingMarksText').text(
                    `Remaining ${remaining} marks ${breakdownText} will be for overall rating. Provide instructions for reviewers.`
                );
                $('#overallInstructionField').slideDown();
            } else if (combinedMarks > totalMarks) {
                const excess = combinedMarks - totalMarks;
                let breakdownText = '';
                if (titleMarks > 0) {
                    breakdownText = `(${titleMarks} title + ${sectionMarksSum} sections = ${combinedMarks})`;
                } else {
                    breakdownText = `(${sectionMarksSum})`;
                }
                $('#remainingMarksText').text(
                    `Combined marks ${breakdownText} exceed total marks (${totalMarks}) by ${excess}. Please adjust.`
                );
                $('#overallInstructionField').slideDown();
            } else {
                $('#overallInstructionField').slideUp();
            }
        }

        // Initialize on page load
        toggleScoringFields();

        // Listen to scoring toggle
        $('#scoring_allowed').on('change', function() {
            toggleScoringFields();
        });

        // Listen to title scoring toggle
        $('#title_scoring_enabled').on('change', function() {
            toggleTitleScoringFields();
        });

        // Listen to total marks, title marks, and section marks changes
        $(document).on('input', '#total_marks, #title_max_marks, .section-max-marks', function() {
            if ($('#scoring_allowed').is(':checked')) {
                checkOverallInstructionRequired();
            }
        });

        // Update section count and renumber sections
        function updateSectionCount() {
            var sectionCount = $('#sectionsContainer .section-group').length;
            $('#number_of_sections').val(sectionCount);
            
            // Renumber all sections
            $('#sectionsContainer .section-group').each(function(index) {
                $(this).find('h6').text('Section ' + (index + 1));
            });

            // Check overall instruction after section count changes
            if ($('#scoring_allowed').is(':checked')) {
                checkOverallInstructionRequired();
            }
        }

        // Add new section
        $('#addSectionBtn').on('click', function() {
            var currentCount = $('#sectionsContainer .section-group').length;
            var newSectionIndex = currentCount + 1;
            var scoringSectionClass = $('#scoring_allowed').is(':checked') ? '' : 'style="display:none;"';
            
            var sectionHtml = `
                <div class="section-group mb-3 p-4 border rounded-3 position-relative" style="background: linear-gradient(135deg, #e8f4f8 0%, #d4e9f2 100%); border: 2px solid #90caf9 !important;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute rounded-circle remove-section-btn" style="top: 12px; right: 12px; width: 32px; height: 32px; padding: 0;" title="Remove Section">
                        <i class="ti tabler-x"></i>
                    </button>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                            <i class="ti tabler-file-text text-primary"></i>
                        </div>
                        <h6 class="text-primary fw-semibold mb-0">Section ${newSectionIndex}</h6>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold mb-2">Section Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="section_name[]" 
                                   placeholder="e.g., Introduction, Methods, Results" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold mb-2">Word Limit <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti tabler-file-text text-muted"></i>
                                </span>
                                <input type="number" class="form-control" name="section_word_limit[]" 
                                       placeholder="e.g., 500" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 section-scoring-field" ${scoringSectionClass}>
                            <label class="form-label fw-semibold mb-2">Maximum Marks <span class="text-danger scoring-required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti tabler-star text-warning"></i>
                                </span>
                                <input type="number" class="form-control section-max-marks" name="section_max_marks[]" 
                                       placeholder="2" min="0" step="0.5" value="2" required>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="ti tabler-info-circle me-1"></i>Max score reviewers can award
                            </small>
                            <div class="section-marks-feedback mt-1" style="display: none;"></div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold mb-2">
                                <i class="ti tabler-user-edit text-info me-1"></i>Author Instructions
                            </label>
                            <textarea class="form-control" name="section_instruction[]" rows="2" 
                                      placeholder="Instructions for authors when writing this section"></textarea>
                            <small class="text-muted d-block mt-1">
                                <i class="ti tabler-info-circle me-1"></i>Shown to authors during submission
                            </small>
                        </div>
                        <div class="col-md-12 mb-0 section-scoring-field" ${scoringSectionClass}>
                            <label class="form-label fw-semibold mb-2">
                                <i class="ti tabler-checklist text-success me-1"></i>Reviewer Instructions
                            </label>
                            <textarea class="form-control" name="section_reviewer_instruction[]" rows="2" 
                                      placeholder="Criteria for reviewers when rating this section"></textarea>
                            <small class="text-muted d-block mt-1">
                                <i class="ti tabler-info-circle me-1"></i>Shown to reviewers during evaluation
                            </small>
                        </div>
                    </div>
                </div>
            `;
            
            $('#sectionsContainer').append(sectionHtml);
            updateSectionCount();
        });

        // Remove section
        $(document).on('click', '.remove-section-btn', function() {
            $(this).closest('.section-group').remove();
            updateSectionCount();
        });

        // Initialize section count on page load
        updateSectionCount();

        // Toggle attachment name field
        $('#is_attachment_required').on('change', function() {
            if ($(this).is(':checked')) {
                $('#attachmentNameField').slideDown();
            } else {
                $('#attachmentNameField').slideUp();
            }
        });

        // Toggle member type restriction panel
        $('#restrict_by_member_type').on('change', function() {
            if ($(this).is(':checked')) {
                $('#memberTypeBody').slideDown();
            } else {
                $('#memberTypeBody').slideUp();
                // Uncheck all member type checkboxes and reset card styles
                $('.member-type-checkbox').prop('checked', false);
                $('.member-type-card').css('background', '').css('border-color', '').removeClass('shadow-sm');
            }
        });

        // Member type card click visual feedback
        $(document).on('change', '.member-type-checkbox', function() {
            const card = $(this).closest('.member-type-card');
            if ($(this).is(':checked')) {
                card.css({
                    'background': 'linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%)',
                    'border-color': '#ffc107'
                }).addClass('shadow-sm');
            } else {
                card.css({
                    'background': '',
                    'border-color': ''
                }).removeClass('shadow-sm');
            }
        });

        // Clear validation error when user starts typing
        $(document).on('input change', '.is-invalid', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });

        // Validate marks don't exceed total marks
        function validateSectionMarks() {
            if (!$('#scoring_allowed').is(':checked')) {
                return true; // Skip validation if scoring is disabled
            }

            var totalMarks = parseFloat($('#total_marks').val()) || 10;
            var sectionMarksSum = 0;
            var titleMarks = 0;
            
            // Add title marks if enabled
            if ($('#title_scoring_enabled').is(':checked')) {
                titleMarks = parseFloat($('#title_max_marks').val()) || 0;
            }
            
            $('.section-max-marks').each(function() {
                sectionMarksSum += parseFloat($(this).val()) || 0;
            });
            
            var combinedMarks = titleMarks + sectionMarksSum;
            
            if (combinedMarks > totalMarks) {
                var breakdown = titleMarks > 0 ? `(${titleMarks} title + ${sectionMarksSum} sections = ${combinedMarks})` : `(${sectionMarksSum})`;
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Configuration',
                    text: `Combined marks ${breakdown} exceed total marks (${totalMarks}). Please adjust the values.`,
                });
                return false;
            }
            return true;
        }

        // Handle form submission
        $('#settingForm').on('submit', function(e) {
            e.preventDefault();

            // Validate section marks
            if (!validateSectionMarks()) {
                return false;
            }

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = $(this).serialize();
            var url = $(this).attr('action');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#settingModal').modal('hide');
                    }
                },
                error: function(xhr) {
                    console.log('Error status:', xhr.status);
                    console.log('Response:', xhr.responseJSON);
                    
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        // Validation errors
                        var errors = xhr.responseJSON.errors; 
                        
                        $.each(errors, function(key, messages) {
                            var inputField;
                            
                            // Handle array fields (section_name.0, section_word_limit.1, etc.)
                            if (key.includes('.')) {
                                var parts = key.split('.');
                                var fieldName = parts[0];
                                var index = parseInt(parts[1]);
                                
                                // Find the input by name and index
                                inputField = $(`input[name="${fieldName}[]"]`).eq(index);
                                if (inputField.length === 0) {
                                    inputField = $(`textarea[name="${fieldName}[]"]`).eq(index);
                                }
                            } else {
                                // Regular fields
                                inputField = $(`input[name="${key}"]`);
                                if (inputField.length === 0) {
                                    inputField = $(`textarea[name="${key}"]`);
                                }
                                if (inputField.length === 0) {
                                    inputField = $(`select[name="${key}"]`);
                                }
                            }
                            
                            if (inputField.length > 0) {
                                inputField.addClass('is-invalid');
                                inputField.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                            }
                        });

                        // Scroll to first error
                        var firstError = $('.is-invalid').first();
                        if (firstError.length) {
                            firstError[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } else {
                        // Other errors - show in SweetAlert
                        var errorMessage = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    }
                }
            });
        });
    });
</script>
