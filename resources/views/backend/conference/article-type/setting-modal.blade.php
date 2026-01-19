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
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 me-2">
                                <i class="ti tabler-stars  fs-5"></i>
                            </div>
                            <h6 class="mb-0 text-white fw-semibold">Scoring Configuration</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <label for="total_marks" class="form-label fw-semibold mb-2">
                                    Total Marks <span class="text-danger">*</span>
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
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold mb-2">Maximum Marks <span class="text-danger">*</span></label>
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
                                            <div class="col-md-12 mb-0">
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
        // Update section count and renumber sections
        function updateSectionCount() {
            var sectionCount = $('#sectionsContainer .section-group').length;
            $('#number_of_sections').val(sectionCount);
            
            // Renumber all sections
            $('#sectionsContainer .section-group').each(function(index) {
                $(this).find('h6').text('Section ' + (index + 1));
            });
        }

        // Add new section
        $('#addSectionBtn').on('click', function() {
            var currentCount = $('#sectionsContainer .section-group').length;
            var newSectionIndex = currentCount + 1;
            
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold mb-2">Maximum Marks <span class="text-danger">*</span></label>
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
                        <div class="col-md-12 mb-0">
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

        // Clear validation error when user starts typing
        $(document).on('input change', '.is-invalid', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });

        // Validate section marks don't exceed total marks
        function validateSectionMarks() {
            var totalMarks = parseFloat($('#total_marks').val()) || 10;
            var sectionMarksSum = 0;
            
            $('.section-max-marks').each(function() {
                sectionMarksSum += parseFloat($(this).val()) || 0;
            });
            
            if (sectionMarksSum > totalMarks) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Configuration',
                    text: `Sum of section marks (${sectionMarksSum}) exceeds total marks (${totalMarks}). Please adjust the values.`,
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
