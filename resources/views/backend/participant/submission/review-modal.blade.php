<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="rounded-top">
        <h4 class="text-center mb-4">Review for <span class="text-danger">(Topic:
                {{ $submission->title }})</h4>

    </div>
    <hr class="py-2">
    @if ($submission->expert_id == current_user()->id)
        <div class="closeModal">
            <label class="card-text mb-2">Do you want to accept the request?</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" value="1" name="request" id="yes">
                <label class="form-check-label" for="yes">Yes</label>
            </div>
            <div class="form-check form-check-inline pt-1">
                <input class="form-check-input" type="radio" value="2" name="request" id="no">
                <label class="form-check-label" for="no">No</label>
            </div>
        </div>


        <form id="decisionForm">
            @csrf
            <div class="row">
                <input type="hidden" id="requestType" name="requestType">
                <input type="hidden" id="submissionId" name="id" value="{{ $submission->id }}">
                <div class="col-md-12 form-group mb-3">

                    <input type="hidden" name="type" value="{{ $submission->presentation_type == 1 ? 2 : 1 }}">

                    @if (!empty($submission->sections))
                        {{-- Display sections if they exist --}}
                        @foreach ($submission->sections as $index => $section)
                            <div class="col-md-12 form-group mb-3 decisionForm" style="display: none;">
                                <label
                                    for="section_{{ $index }}">{{ $section['name'] ?? 'Section ' . ($index + 1) }}
                                    <code>*</code></label>
                                <textarea class="form-control section-content" name="sections[{{ $index }}][content]"
                                    id="section_{{ $index }}" cols="30" rows="5" readonly>{{ old('sections.' . $index . '.content', $section['content'] ?? '') }}</textarea>
                                <input type="hidden" name="sections[{{ $index }}][name]"
                                    value="{{ $section['name'] ?? '' }}">
                                <input type="hidden" name="sections[{{ $index }}][word_limit]"
                                    value="{{ $section['word_limit'] ?? '' }}">
                                <p class="text-danger section-error-{{ $index }}"></p>
                            </div>
                        @endforeach
                    @else
                        {{-- Display abstract content if no sections --}}
                        <div class="col-md-12 form-group mb-3 decisionForm" id="abstractContent" style="display: none;">
                            <label for="abstract_content">Abstract Content <code>* <span>(NOTE: Total number of
                                        Abstract
                                        Words limitation is
                                        {{ !empty(@$setting->abstract_word_limit) ? $setting->abstract_word_limit : 'infinity' }})</span></code></label>
                            <textarea class="form-control" name="abstract_content" id="abstract_content" cols="30" rows="5" readonly>{{ !empty(old('abstract_content')) ? old('abstract_content') : $submission->abstract_content }}</textarea>
                            @error('abstract_content')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <p class="text-danger abstract_content"></p>
                        </div>
                    @endif
                    <div class="col-md-12 form-group mb-3 decisionForm" style="display: none;" id="remarksDiv">
                        <label for="remarks">Review Remarks <code>*</code></label>
                        <textarea class="form-control" name="remarks" id="remarks" cols="30" rows="5">{{ isset($submission) ? $submission->remarks : old('remarks') }}</textarea>
                        <p class="text-danger remarks"></p>
                    </div>
                    {{-- @endif --}}
                    @if ($setting->scoring_allowed == 1)
                        {{-- Section-based ratings if article type has sections --}}
                        @if (!empty($articleTypeSections) && is_array($articleTypeSections))
                            <div class="row pl-3 decisionForm" style="display: none;">
                                <div class="col-md-12 mb-4">
                                    <div class="alert border-0 mb-0" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning bg-opacity-25 rounded-circle p-2 me-3">
                                                <i class="ti tabler-award text-warning fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark">Score Based On Sections</h6>
                                                <small class="text-muted">Rate each section based on quality and content. Maximum marks are shown for each section.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @foreach ($articleTypeSections as $index => $section)
                                    @php
                                        $maxMarks = $section['max_marks'] ?? 2;
                                        // Generate score options based on max_marks
                                        $scoreOptions = [];
                                        if ($maxMarks >= 1) {
                                            $step = ($maxMarks <= 2) ? 1 : 0.5;
                                            for ($i = 0; $i <= $maxMarks; $i += $step) {
                                                $scoreOptions[] = $i;
                                            }
                                        } else {
                                            $scoreOptions = [0];
                                        }
                                    @endphp
                                    <div class="col-md-6 form-group mb-3 sectionRatingField">
                                        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                                            <div class="card-body p-3">
                                                <label for="section_rating_{{ $index }}" class="form-label fw-semibold mb-2">
                                                    <i class="ti tabler-file-text text-primary me-1"></i>
                                                    {{ $section['name'] ?? 'Section ' . ($index + 1) }}
                                                </label>
                                                
                                                @if(!empty($section['reviewer_instruction']))
                                                    <div class="alert border-0 py-2 px-3 mb-3" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);">
                                                        <div class="d-flex align-items-start">
                                                            <i class="ti tabler-checklist text-success me-2 mt-1 flex-shrink-0"></i>
                                                            <small class="text-dark mb-0" style="line-height: 1.5;">
                                                                <strong>Review Criteria:</strong> {{ $section['reviewer_instruction'] }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                        <i class="ti tabler-star me-1"></i>Max: {{ $maxMarks }}
                                                    </span>
                                                    <code class="bg-danger bg-opacity-10 text-danger px-2 py-1 rounded">*</code>
                                                </div>
                                                <select name="section_ratings[{{ $index }}][rating]"
                                                    id="section_rating_{{ $index }}"
                                                    class="form-control form-select section-rating-select" 
                                                    data-max-marks="{{ $maxMarks }}">
                                                    <option value="" hidden>-- Select Score --</option>
                                                    @foreach($scoreOptions as $score)
                                                        <option value="{{ $score }}">{{ $score }} {{ $score == $maxMarks ? '(Max)' : '' }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="section_ratings[{{ $index }}][name]"
                                                    value="{{ $section['name'] ?? 'Section ' . ($index + 1) }}">
                                                <input type="hidden" name="section_ratings[{{ $index }}][max_marks]"
                                                    value="{{ $maxMarks }}">
                                                <p class="text-danger section-rating-error-{{ $index }} mb-0 mt-2 small"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Grammar/Language rating for section-based --}}
                                {{-- <div class="col-md-6 form-group mb-3 sectionRatingField">
                                    <label for="grammar">Grammar/Languages <code>*</code></label>
                                    <select name="grammar" id="grammar" class="form-control section-rating-select">
                                        <option value="" hidden>-- Select Score --</option>
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select>
                                    <p class="text-danger grammar"></p>
                                </div> --}}
                            </div>

                            {{-- Overall rating field for section-based (shown when total < 10) --}}
                            {{-- <div class="row pl-3 decisionForm" id="sectionOverallRatingDiv" style="display: none;"> --}}
                            {{-- <div class="col-md-12 mb-3">
                                    <div class="alert alert-warning">
                                        <i class="ti ti-info-circle"></i> 
                                        <strong>Additional Score Required:</strong> 
                                        <span id="remainingScoreText"></span>
                                    </div>
                                </div> --}}
                            <div class="col-md-12 form-group mb-3 decisionForm" id="sectionOverallRatingDiv"
                                style="display: none;">
                                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);">
                                    <div class="card-body p-3">
                                        <label for="section_overall_rating" class="form-label fw-semibold mb-2">
                                            <i class="ti tabler-certificate text-success me-1"></i>
                                            Overall Rating (Consistency, Grammar, Language, etc.)
                                        </label>
                                        <select name="overall_rating" id="section_overall_rating" class="form-control form-select">
                                            <option value="" hidden>-- Select Additional Score --</option>
                                        </select>
                                        <p class="text-danger overall_rating mb-0 mt-2 small"></p>
                                    </div>
                                </div>
                            </div>
                            {{-- </div> --}}
                        @else
                            {{-- Default ratings (Introduction, Method, Result, Conclusion, Grammar) --}}
                            <div class="row pl-3 decisionForm" style="display: none;">
                                <div class="col-md-12 mb-3">
                                    <label class="text-danger" for="defaultCheck1">Score Base On below Topic
                                        <code>(Check the box if the structure not applicable <input
                                                class="form-check-input mt-1" type="checkbox" value="1"
                                                name="structure" id="defaultCheck1" />) </code></label>
                                </div>
                                <div class="col-md-4 form-group mb-3 ifAcceptContents">
                                    <label for="introduction">Introduction/Background <code>*</code></label>
                                    <select name="introduction" id="introduction"
                                        class="form-control @error('introduction') is-invalid @enderror">
                                        <option value="" hidden>-- Select Score --</option>
                                        <option value="0" @selected(old('introduction') === 0)>0</option>
                                        <option value="1" @selected(old('introduction') == 1)>1</option>
                                        <option value="2" @selected(old('introduction') == 2)>2</option>
                                    </select>
                                    <p class="text-danger introduction"></p>
                                </div>
                                <div class="col-md-4 form-group mb-3 ifAcceptContents">
                                    <label for="method">Methods <code>*</code></label>
                                    <select name="method" id="method"
                                        class="form-control @error('method') is-invalid @enderror">
                                        <option value="" hidden>-- Select Score --</option>
                                        <option value="0" @selected(old('method') === 0)>0</option>
                                        <option value="1" @selected(old('method') == 1)>1</option>
                                        <option value="2" @selected(old('method') == 2)>2</option>
                                    </select>
                                    <p class="text-danger method"></p>
                                </div>
                                <div class="col-md-4 form-group mb-3 ifAcceptContents">
                                    <label for="result">Results/Findings <code>*</code></label>
                                    <select name="result" id="result"
                                        class="form-control @error('result') is-invalid @enderror">
                                        <option value="" hidden>-- Select Score --</option>
                                        <option value="0" @selected(old('result') === 0)>0</option>
                                        <option value="1" @selected(old('result') == 1)>1</option>
                                        <option value="2" @selected(old('result') == 2)>2</option>
                                    </select>
                                    <p class="text-danger result"></p>
                                </div>
                                <div class="col-md-5 form-group mb-3 ifAcceptContents">
                                    <label for="conclusion">Conclusion <code>*</code></label>
                                    <select name="conclusion" id="conclusion"
                                        class="form-control @error('conclusion') is-invalid @enderror">
                                        <option value="" hidden>-- Select Score --</option>
                                        <option value="0" @selected(old('conclusion') === 0)>0</option>
                                        <option value="1" @selected(old('conclusion') == 1)>1</option>
                                        <option value="2" @selected(old('conclusion') == 2)>2</option>
                                    </select>
                                    <p class="text-danger conclusion"></p>
                                </div>
                                <div class="col-md-5 form-group mb-3 ifAcceptContents">
                                    <label for="grammar">Grammar/Languages <code>*</code></label>
                                    <select name="grammar" id="grammar"
                                        class="form-control @error('grammar') is-invalid @enderror">
                                        <option value="" hidden>-- Select Score --</option>
                                        <option value="0" @selected(old('grammar') === 0)>0</option>
                                        <option value="1" @selected(old('grammar') == 1)>1</option>
                                        <option value="2" @selected(old('grammar') == 2)>2</option>
                                    </select>
                                    <p class="text-danger grammar"></p>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-5 overall_ratings mb-3" style="display: none;">
                            <label for="overall_rating">Overall Rating <code>*</code></label>
                            <input type='number' class="form-control" name="overall_rating" id="overall_rating">
                            <p class="text-danger overall_rating"></p>
                        </div>
                    @endif



                    <div class="col-md-12 form-group mb-3 decisionRejectRemark" style="display: none;">
                        <label for="reject_remarks">Remarks <code>*</code></label>
                        <textarea class="form-control" name="reject_remarks" id="reject_remarks" cols="30" rows="5">{{ isset($submission) ? $submission->reject_remarks : old('reject_remarks') }}</textarea>
                        <p class="text-danger reject_remarks"></p>
                    </div>



                    <div class="col-md-12 text-end formbutton" style="display: none;">
                        <button type="submit" id="decideRequest" class="btn btn-primary">Send</button>
                    </div>
                </div>
        </form>
    @endif
</div>


<script>
    var ckeditorInstances = {};
    var guidelineContent = '';
    var guidelineTitle = '';

    // Store guideline content
    @if ($submission->presentation_type == 1 && !empty($setting->poster_reviewer_guide))
        guidelineContent = {!! json_encode($setting->poster_reviewer_guide) !!};
        guidelineTitle = 'Poster Reviewer Guidelines';
    @elseif ($submission->presentation_type == 2 && !empty($setting->oral_reviewer_guide))
        guidelineContent = {!! json_encode($setting->oral_reviewer_guide) !!};
        guidelineTitle = 'Oral Reviewer Guidelines';
    @endif

    @if (!empty($submission->sections))
        // Initialize CKEditor for each section
        @foreach ($submission->sections as $index => $section)
            ckeditorInstances['section_{{ $index }}'] = CKEDITOR.replace('section_{{ $index }}', {
                filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                filebrowserUploadMethod: "form",
                extraPlugins: 'wordcount',
                readOnly: true,
                wordcount: {
                    showWordCount: true,
                    maxWordCount: {{ $section['word_limit'] ?? 'Infinity' }},
                }
            });
        @endforeach
    @else
        // Initialize CKEditor for abstract content
        ckeditorInstances['abstract_content'] = CKEDITOR.replace('abstract_content', {
            filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
            filebrowserUploadMethod: "form",
            extraPlugins: 'wordcount',
            readOnly: true,
            wordcount: {
                showWordCount: true,
                maxWordCount: {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'Infinity' }},
            }
        });
    @endif

    $('#yes').on('change', function() {
        if ($(this).is(':checked')) {
            var data = $(this).val();
            $('#requestType').val(data);

            // Check if guideline exists and has content
            if (guidelineContent && guidelineContent.trim() !== '') {
                // Show loading spinner in the review modal
                var loadingHtml = `
                    <div class="text-center py-5" id="guidelineLoading">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading Guidelines...</p>
                    </div>
                `;

                // Hide radio buttons and show loader
                $('.closeModal').hide();
                $('#decisionForm').hide();
                $('#pricingModal .modal-body').append(loadingHtml);

                // Create guideline modal dynamically
                var guidelineModalHtml = `
                    <div class="modal fade" id="guidelineModal" tabindex="-1" aria-labelledby="guidelineModalLabel" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="guidelineModalLabel">
                                        <i class="ti tabler-info-circle"></i> ${guidelineTitle}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ${guidelineContent}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand, Continue Review</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing guideline modal if any
                $('#guidelineModal').remove();

                // Append to body
                $('body').append(guidelineModalHtml);

                // Hide review modal with fade effect
                setTimeout(function() {
                    $('#pricingModal').modal('hide');

                    // Show guideline modal after review modal is hidden
                    $('#pricingModal').one('hidden.bs.modal', function() {
                        var guideModal = new bootstrap.Modal(document.getElementById(
                            'guidelineModal'));
                        guideModal.show();

                        // Remove loading spinner
                        $('#guidelineLoading').remove();
                        $('.closeModal').show();
                        $('#decisionForm').show();
                    });
                }, 500);

                // When guideline modal closes, reopen review modal with loader
                $('#guidelineModal').on('hidden.bs.modal', function() {
                    // Show loading in between
                    var reopenLoadingHtml = `
                        <div class="modal fade show" id="reopenLoader" style="display: flex !important; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
                            <div class="text-center text-white">
                                <div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3">Preparing Review Form...</p>
                            </div>
                        </div>
                    `;
                    $('body').append(reopenLoadingHtml);

                    $('#guidelineModal').remove();

                    setTimeout(function() {
                        $('#reopenLoader').remove();
                        $('#pricingModal').modal('show');
                        $('.decisionRejectRemark').hide();
                        $('.decisionForm').show();
                        $('.formbutton').show();

                        // Check if overall rating is needed
                        if ($('.section-rating-select').length > 0) {
                            checkSectionOverallRatingRequired();
                        }
                    }, 400);
                });
            } else {
                // No guideline, show review form directly
                $('.decisionRejectRemark').hide();
                $('.decisionForm').show();
                $('.formbutton').show();

                // Check if overall rating is needed
                if ($('.section-rating-select').length > 0) {
                    checkSectionOverallRatingRequired();
                }
            }
        }
    });


    $('#no').on('change', function() {
        if ($(this).is(':checked')) {

            var data = $(this).val();
            $('#requestType').val(data);
            $('.decisionForm').hide();
            $('.formbutton').show();
            $('.decisionRejectRemark').show();
        }
    });

    $('#defaultCheck1').on('change', function() {
        if ($(this).is(':checked')) {
            $('.ifAcceptContents').hide();
            $('.overall_ratings').show();
        } else {
            $('.ifAcceptContents').show();
            $('.overall_ratings').hide();
        }
    });

    // Check if overall rating field should be shown for section-based ratings
    function checkSectionOverallRatingRequired() {
        // Only proceed if section rating fields exist
        if ($('.section-rating-select').length === 0) {
            return;
        }

        // Get total marks from article type setting (default 10 if not set)
        @php
            $totalMarks = 10; // Default
            if (!empty($articleTypeSections) && isset($submission->articleType->setting->total_marks)) {
                $totalMarks = $submission->articleType->setting->total_marks;
            }
        @endphp
        const totalMarks = {{ $totalMarks }};

        // Calculate maximum possible score based on each section's max_marks
        let maxPossibleScore = 0;
        $('.section-rating-select').each(function() {
            const sectionMaxMarks = parseFloat($(this).data('max-marks')) || 2;
            maxPossibleScore += sectionMaxMarks;
        });

        if (maxPossibleScore < totalMarks) {
            // Show overall rating field if maximum possible score is less than total marks
            const remaining = totalMarks - maxPossibleScore;

            // Populate options based on remaining marks (support decimals)
            const $select = $('#section_overall_rating');
            $select.empty();
            $select.append('<option value="" hidden>-- Select Additional Score --</option>');
            
            // Generate options with 0.5 step if remaining > 2, otherwise 1 step
            const step = remaining > 2 ? 0.5 : 1;
            for (let i = step; i <= remaining; i += step) {
                $select.append(`<option value="${i}">${i}${i === remaining ? ' (Max)' : ''}</option>`);
            }

            $('#sectionOverallRatingDiv').show();
            $('#section_overall_rating').attr('required', true);
            
            // Update label with badge showing remaining marks
            $('#sectionOverallRatingDiv .form-label').html(
                `<i class="ti tabler-certificate text-success me-1"></i>Overall Rating (Consistency, Grammar, Language, etc.) <span class="badge rounded-pill ms-2" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);"><i class="ti tabler-star me-1"></i>Remaining: ${remaining}</span><code class="ms-1">*</code>`
            );
        } else {
            // Hide overall rating field if max possible score is already at or above total marks
            $('#sectionOverallRatingDiv').hide();
            $('#section_overall_rating').attr('required', false);
            $('#section_overall_rating').val('');
        }
    }

    // Check on page load when "Yes" is selected and form is shown
    $('#yes').on('change', function() {
        if ($(this).is(':checked')) {
            // Wait for the form to be displayed, then check
            setTimeout(function() {
                if ($('.section-rating-select').length > 0) {
                    checkSectionOverallRatingRequired();
                }
            }, 100);
        }
    });


    $("#decideRequest").on('click', function(e) {
        e.preventDefault();
        var data = new FormData($('#decisionForm')[0]);

        // Explicitly add overall_rating if it exists and has a value
        var overallRatingValue = $('#section_overall_rating').val();
        if (overallRatingValue && overallRatingValue !== '' && overallRatingValue !== null) {
            data.set('overall_rating', overallRatingValue);
            console.log('Adding overall_rating to form data:', overallRatingValue);
        } else {
            console.log('overall_rating field value:', overallRatingValue);
        }

        @if (!empty($submission->sections))
            // Get data from section editors
            @foreach ($submission->sections as $index => $section)
                data.set('sections[{{ $index }}][content]', ckeditorInstances[
                    'section_{{ $index }}'].getData());
            @endforeach
        @else
            // Get data from abstract content editor
            data.append('abstract_content', ckeditorInstances['abstract_content'].getData());
        @endif
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route('my-society.conference.submission.reviewSubmit', [$society, $conference]) }}',
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#decideRequest').attr('disabled', true);
                $('#decideRequest').append(
                    ' <div class="spinner-border spinner-border-sm text-secondary" role="status"><span class="visually-hidden">Loading...</span> </div>'
                );
            },
            success: function(response) {
                $(".modal").modal("hide");
                notyf.success(response.message);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function(response) {
                var errors = response.responseJSON.errors;
                $.each(errors, function(key, val) {
                    // Handle nested keys like section_ratings.0.rating
                    var sanitizedKey = key.replace(/\./g, '-');
                    var fieldId = '#' + key.replace(/\./g, '_');

                    $('.' + key).html('');
                    $('.' + key).append(val);
                    $('.section-rating-error-' + key.split('.')[1]).html(val);

                    $(fieldId).addClass('border-danger');
                    $('#' + key).addClass('border-danger');

                    $(fieldId).on('input change', function() {
                        $('.' + key).html('');
                        $('.section-rating-error-' + key.split('.')[1]).html('');
                        $(this).removeClass('border-danger');
                    });

                    $('#' + key).on('input change', function() {
                        $('.' + key).html('');
                        $(this).removeClass('border-danger');
                    });
                });
                // }
                $('#decideRequest').attr('disabled', false);
                $('#decideRequest').text('Send');
            }
        });
    });
</script>
