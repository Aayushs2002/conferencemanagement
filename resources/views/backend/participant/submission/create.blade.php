@extends('backend.layouts.conference.main')
@section('title')
    Presentation Submission
@endsection
@section('content')
    @include('backend.layouts.conference-navigation')
    @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach

    @if ($setting?->abstract_guidelines)
        <div class="modal fade" id="openAbstractGuidelineModal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitleDuideline" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h4 class="text-center mb-4">Abstract Submission Guidelines</h4>
                        {!! $setting->abstract_guidelines !!}
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary m-3" data-bs-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a
                    href="{{ route('my-society.conference.submission.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($submission) ? 'Edit' : 'Add' }}
                Presentation Submission</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($submission) ? route('my-society.conference.submission.update', [$society, $conference, $submission]) : route('my-society.conference.submission.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($submission)
                        @method('patch')
                    @endisset
                    <div class="row">
                        <div class="mb-6 col-md-12">
                            {{-- <label class="form-label">Are you a student? <code>*</code></label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_student" id="is_student_yes"
                                        value="1"
                                        @if (isset($submission)) {{ $submission->is_student == true ? 'checked' : '' }} @else @checked(old('is_student') == '1') @endif
                                        required>
                                    <label class="form-check-label" for="is_student_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_student" id="is_student_no"
                                        value="0"
                                        @if (isset($submission)) {{ $submission->is_student == false ? 'checked' : '' }} @else @selected(old('is_student') == '0') @endif
                                        required>
                                    <label class="form-check-label" for="is_student_no">No</label>
                                </div>
                            </div> --}}
                            {{-- <div class="invalid-feedback d-block">Please select if you are the main presenter.</div>
                            --}}
                            @error('is_student')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="society-name">Title of Abstract<code>*</code></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="society-name" placeholder="Enter Title of Abstract" name="title"
                                value="{{ old('title') ?? @$submission?->title }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter Title of Abstract.</div>
                            @error('title')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label for="article_type_id" class="form-label">Presentation Category <code>*</code></label>
                            <select class="form-select" name="article_type_id" id="article_type_id" required>
                                <option value="" hidden>-- Select Presentation Category --</option>
                                @foreach ($articleTypes as $articleType)
                                    <option value="{{ $articleType->id }}" @selected(old('article_type_id', @$submission->article_type_id) == $articleType->id)>
                                        {{ $articleType->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please select Presentation Category.</div>
                            @error('article_type_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label for="submission_category_major_track_id" class="form-label">Theme and Sub-theme
                                <code>*</code></label>
                            <select class="form-select" name="submission_category_major_track_id"
                                id="submission_category_major_track_id" required>
                                <option value="" hidden>-- Select Theme/Sub-theme --</option>
                                @foreach ($submissionTracks as $submissionTrack)
                                    <option value="{{ $submissionTrack->id }}"
                                        data-content="{{ $submissionTrack->major_areas }}" @selected(old('submission_category_major_track_id', @$submission->submission_category_major_track_id) == $submissionTrack->id)>
                                        {{ $submissionTrack->title }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please select Theme/Sub-theme.</div>
                            @error('submission_category_major_track_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <p id="majorAreas" class="text-muted"
                                style=" font-weight: semi-bold; font-size: 0.8rem; padding-top: 0.5rem;">loading</p>
                        </div>

                        <div class="mb-6 col-md-6">
                            <label for="presentation_type" class="form-label">Presentation Type <code>*</code></label>
                            <select class="form-select" name="presentation_type" id="presentation_type" required>
                                <option value="" hidden>-- Select Presentation Type --</option>
                                <option value="1"
                                    @if (isset($submission)) {{ $submission->presentation_type == '1' ? 'selected' : '' }} @else @selected(old('presentation_type') == '1') @endif>
                                    Poster</option>
                                <option value="2"
                                    @if (isset($submission)) {{ $submission->presentation_type == '2' ? 'selected' : '' }} @else @selected(old('presentation_type') == '2') @endif>
                                    Oral</option>
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please select Presentation Type.</div>
                            @error('presentation_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        @if ($setting->competition_enabled)
                            <div class="mb-6 col-md-6">
                                <label for="competition_type" class="form-label">Competition <code>*</code></label>
                                <select class="form-select @error('competition_type') is-invalid @enderror"
                                    name="competition_type" id="competition_type" required>
                                    <option value="" hidden>-- Select Competition Type --</option>
                                    <option value="1"
                                        @if (isset($submission)) {{ $submission->competition_type == '1' ? 'selected' : '' }} @else @selected(old('competition_type') == '1') @endif>
                                        Competition</option>
                                    <option value="2"
                                        @if (isset($submission)) {{ $submission->competition_type == '2' ? 'selected' : '' }} @else @selected(old('competition_type') == '2') @endif>
                                        Non-Competition</option>
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please select Competition Type.</div>
                                @error('competition_type')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-6 col-md-9">
                            <label for="keyWord" class="form-label">Keywords <code>*(NOTE: Total number of Keywords
                                    limitation is
                                    {{ @$setting->key_word_limit ? @$setting->key_word_limit : 'infinity' }})</code>
                                <span class="text-muted"
                                    style=" font-weight: semi-bold; font-size: 0.7rem; padding-top: 0.5rem;">(Press enter
                                    after typing complete word/words to represent
                                    it
                                    as a keyword.)</span></label>

                            @php
                                $keywordsJson =
                                    old('keywords') ?:
                                    collect(explode(',', @$submission->keywords))
                                        ->map(fn($kw) => ['value' => $kw])
                                        ->toJson();
                            @endphp

                            <input id="keyWord" class="form-control" name="keywords" required
                                placeholder="Enter Keywords" value='{{ $keywordsJson }}' />

                            @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dynamic Content Sections Container -->
                        <div id="contentSectionsContainer">
                            <!-- Default Abstract Content (shown when no article type selected or no settings) -->
                            <div class="col-md-12 form-group mb-3" id="defaultAbstractContent" style="display: none;">
                                <label for="abstract_content" class="form-label">Abstract Content
                                    <code><span id="abstractRequired">*
                                        </span><span>(NOTE: Total number of Abstract Words limitation is
                                            {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'infinity' }})</span></code></label>
                                <textarea class="form-control" name="abstract_content" id="description2" cols="30" rows="5">{{ !empty(old('abstract_content')) ? old('abstract_content') : @$submission->abstract_content }}</textarea>
                                @error('abstract_content')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Dynamic Attachment Field Container -->
                        <div id="attachmentContainer">
                            @if ($setting->attachment_name)
                                <div class="mb-6 col-md-6" id="defaultAttachment" style="display: none;">
                                    <label class="form-label" for="image">
                                        {{ $setting->attachment_name }}
                                        <small class="text-muted d-block">
                                            {{ '(JPG, JPEG, PNG, PDF - Max size: 250 KB' . ($setting->attachment_required ? '*' : ' (optional)') . ')' }}
                                        </small>
                                    </label>

                                    <input type="file" class="form-control" name="image" id="image"
                                        value="{{ !empty(old('image')) ? old('image') : @$submission->image }}" />
                                    <div class="row" id="imgPreview">
                                        @if (isset($submission))
                                            <div class="col-3 mt-2">
                                                <a href="{{ asset('storage/participant/submission/image/' . $submission->image) }}"
                                                    target="_blank"><img
                                                        src="{{ asset('storage/participant/submission/image/' . $submission->image) }}"
                                                        class="img-fluid" alt="image"></a>
                                            </div>
                                        @endif
                                    </div>
                                    @error('image')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <!-- Conflict of Interest Field Container -->
                        <div id="conflictOfInterestContainer"></div>

                        <!-- Source of Funding Field Container -->
                        <div id="sourceOfFundingContainer"></div>

                        <!-- Co-Authors Section -->
                        <div class="col-md-12 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Authors</h5>
                                <button type="button" class="btn btn-primary btn-sm" id="addAuthorBtn">
                                    <i class="ti tabler-plus"></i> Add Co-Author
                                </button>
                            </div>
                            <div id="authorsContainer"></div>
                        </div>

                        @if (!isset($submission))
                            <div class="mb-6 col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="main_author"
                                                id="main_author_checkbox" value="1"
                                                {{ old('main_author') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="main_author_checkbox">
                                                <strong>I am the Main Author</strong>
                                                <i class="ti tabler-info-circle text-primary ms-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Only one author can be designated as the main author. If you uncheck this, you can select one of the co-authors as the main author."></i>
                                            </label>
                                        </div>
                                        @error('main_author')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="main_presenter"
                                                id="main_presenter_checkbox" value="1"
                                                {{ old('main_presenter') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="main_presenter_checkbox">
                                                <strong>I am the Main Presenter</strong>
                                                <i class="ti tabler-info-circle text-primary ms-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Only one author can be designated as the main presenter. If you uncheck this, you can select one of the co-authors as the main presenter."></i>
                                            </label>
                                        </div>
                                        @error('main_presenter')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="ti tabler-alert-circle"></i>
                                    You can be the main author, main presenter, or both. If not, designate co-authors
                                    accordingly.
                                </small>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-12 text-end">
                                @if (!isset($submission))
                                    <button type="button" id="previewBtn" class="btn btn-primary">
                                        <i class="ti tabler-eye me-1"></i>Preview Submission
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti tabler-device-floppy me-1"></i>Update
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">
                        <i class="ti tabler-eye me-2"></i>Preview Submission
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="previewCloseBtn">
                        <i class="ti tabler-x me-1"></i>Close
                    </button>
                    <button type="button" id="confirmSubmitBtn" class="btn btn-primary">
                        <span class="btn-text">
                            <i class="ti tabler-send me-1"></i>Confirm & Submit
                        </span>
                        <span class="btn-spinner d-none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Submitting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // Fixed JavaScript code for the presentation submission form

        let articleTypeSettings = {};
        let ckeditorInstances = {};
        let authorIndex = 0;
        const contributions = @json($contributions ?? []);
        const contributionEnabled = @json($contributionEnabled ?? false);

        $(document).ready(function() {
            // Check if user has seen the abstract guidelines before
            const abstractGuidelinesKey =
                'abstract_guidelines_seen_{{ $conference->id }}_{{ current_user()->id }}';

            const hasSeenGuidelines = localStorage.getItem(abstractGuidelinesKey);

            @if ($setting?->abstract_guidelines)
                if (!hasSeenGuidelines) {
                    $('#openAbstractGuidelineModal').modal('show');
                }

                // Mark as seen when modal is closed
                $('#openAbstractGuidelineModal').on('hidden.bs.modal', function() {
                    localStorage.setItem(abstractGuidelinesKey, 'true');
                });
            @endif

            // Major Areas display
            $('#submission_category_major_track_id').on('change', function() {
                var selectedContent = $(this).find('option:selected').data('content');
                if ($(this).val() !== '') {
                    $('#majorAreas').text('(' + selectedContent + ')');
                } else {
                    $('#majorAreas').text('');
                }
            });
            $('#submission_category_major_track_id').trigger('change');

            // Load article type settings when article type changes
            $('#article_type_id').on('change', function() {
                const articleTypeId = $(this).val();
                if (!articleTypeId) {
                    showDefaultFields();
                    return;
                }
                loadArticleTypeSettings(articleTypeId);
            });

            // Trigger on page load if editing or if validation failed
            @if (isset($submission) && $submission->article_type_id)
                $('#article_type_id').trigger('change');
            @elseif (old('article_type_id'))
                $('#article_type_id').trigger('change');
            @endif

            // Co-Author Management
            $('#addAuthorBtn').on('click', function() {
                addAuthorField();
            });

            $(document).on('click', '.remove-author-btn', function() {
                const index = $(this).data('index');
                $(`#author_row_${index}`).remove();
                updateExistingAuthorDropdowns();
            });

            // Populate existing authors if any
            @if (old('authors'))
                const oldAuthors = @json(old('authors'));
                Object.values(oldAuthors).forEach(author => {
                    addAuthorField(author);
                });
            @elseif (isset($submission) && $submission->authors)
                const existingAuthors = @json($submission->authors);
                existingAuthors.forEach(author => {
                    // Add all co-authors (submitter is already shown in the form)
                    if (author.email !== '{{ $submission->user->email ?? '' }}') {
                        addAuthorField(author);
                    }
                });
            @endif

            // Handle initial state for main author and presenter visibility on page load
            setTimeout(function() {
                // Handle Main Author visibility
                if ($('#main_author_checkbox').is(':checked')) {
                    $('.co-author-main-author').each(function() {
                        $(this).closest('.co-author-main-author-container').hide();
                    });
                } else if ($('.co-author-main-author:checked').length > 0) {
                    $('#main_author_checkbox').closest('.col-md-6').hide();
                    $('.co-author-main-author').each(function() {
                        if (!$(this).is(':checked')) {
                            $(this).closest('.co-author-main-author-container').hide();
                        }
                    });
                }

                // Handle Main Presenter visibility
                if ($('#main_presenter_checkbox').is(':checked')) {
                    $('.co-author-main-presenter').each(function() {
                        $(this).closest('.co-author-main-presenter-container').hide();
                    });
                } else if ($('.co-author-main-presenter:checked').length > 0) {
                    $('#main_presenter_checkbox').closest('.col-md-6').hide();
                    $('.co-author-main-presenter').each(function() {
                        if (!$(this).is(':checked')) {
                            $(this).closest('.co-author-main-presenter-container').hide();
                        }
                    });
                }
            }, 100);

            // Initialize Tagify for keywords
            initializeTagify();

            // Main author checkbox logic for co-authors
            $(document).on('change', '.co-author-main-author', function() {
                if ($(this).is(':checked')) {
                    // Uncheck submitter's main author checkbox
                    $('#main_author_checkbox').prop('checked', false);
                    // Hide submitter's main author checkbox container
                    $('#main_author_checkbox').closest('.col-md-6').hide();

                    // Uncheck and hide all other co-author main author checkboxes
                    $('.co-author-main-author').not(this).each(function() {
                        $(this).prop('checked', false);
                        $(this).closest('.co-author-main-author-container').hide();
                    });

                    // Keep current checkbox visible
                    $(this).closest('.co-author-main-author-container').show();
                } else {
                    // If unchecked, show submitter's main author checkbox again
                    $('#main_author_checkbox').closest('.col-md-6').show();
                    // Show all co-author main author checkboxes again
                    $('.co-author-main-author').each(function() {
                        $(this).closest('.co-author-main-author-container').show();
                    });
                }
            });

            // Submitter main author checkbox logic
            $(document).on('change', '#main_author_checkbox', function() {
                if ($(this).is(':checked')) {
                    // Uncheck and hide all co-author main author checkboxes
                    $('.co-author-main-author').each(function() {
                        $(this).prop('checked', false);
                        $(this).closest('.co-author-main-author-container').hide();
                    });
                    // Keep submitter checkbox visible
                    $(this).closest('.col-md-6').show();
                } else {
                    // If unchecked, show all co-author main author checkboxes again
                    $('.co-author-main-author').each(function() {
                        $(this).closest('.co-author-main-author-container').show();
                    });
                }
            });

            // Main presenter checkbox logic for co-authors
            $(document).on('change', '.co-author-main-presenter', function() {
                if ($(this).is(':checked')) {
                    // Uncheck submitter's main presenter checkbox
                    $('#main_presenter_checkbox').prop('checked', false);
                    // Hide submitter's main presenter checkbox container
                    $('#main_presenter_checkbox').closest('.col-md-6').hide();

                    // Uncheck and hide all other co-author main presenter checkboxes
                    $('.co-author-main-presenter').not(this).each(function() {
                        $(this).prop('checked', false);
                        $(this).closest('.co-author-main-presenter-container').hide();
                    });

                    // Keep current checkbox visible
                    $(this).closest('.co-author-main-presenter-container').show();
                } else {
                    // If unchecked, show submitter's main presenter checkbox again
                    $('#main_presenter_checkbox').closest('.col-md-6').show();
                    // Show all co-author main presenter checkboxes again
                    $('.co-author-main-presenter').each(function() {
                        $(this).closest('.co-author-main-presenter-container').show();
                    });
                }
            });

            // Submitter main presenter checkbox logic
            $(document).on('change', '#main_presenter_checkbox', function() {
                if ($(this).is(':checked')) {
                    // Uncheck and hide all co-author main presenter checkboxes
                    $('.co-author-main-presenter').each(function() {
                        $(this).prop('checked', false);
                        $(this).closest('.co-author-main-presenter-container').hide();
                    });
                    // Keep submitter checkbox visible
                    $(this).closest('.col-md-6').show();
                } else {
                    // If unchecked, show all co-author main presenter checkboxes again
                    $('.co-author-main-presenter').each(function() {
                        $(this).closest('.co-author-main-presenter-container').show();
                    });
                }
            });

            // Clear validation errors on input
            $(document).on('input change', 'input[name^="authors"]', function() {
                $(this).removeClass('is-invalid');
                const errorId = $(this).attr('id') + '_error';
                $('#' + errorId).text('');
            });

            // Preview button click handler
            $('#previewBtn').on('click', function(e) {
                e.preventDefault();
                showPreview();
            });

            // Confirm submit button in preview modal
            $('#confirmSubmitBtn').on('click', function() {
                const btn = $(this);
                const closeBtn = $('#previewCloseBtn');
                const form = $('form.needs-validation')[0];

                // Show loading state
                btn.find('.btn-text').addClass('d-none');
                btn.find('.btn-spinner').removeClass('d-none');
                btn.prop('disabled', true);
                closeBtn.prop('disabled', true);

                // Prevent modal close on backdrop click or escape
                const modal = bootstrap.Modal.getInstance($('#previewModal')[0]);
                if (modal) {
                    modal._config.backdrop = 'static';
                    modal._config.keyboard = false;
                }

                // Submit the form using native submit (bypasses jQuery to avoid event conflicts)
                setTimeout(function() {
                    form.submit();
                }, 100);
            });
        });

        function showPreview() {
            let previewHtml = '<div class="container-fluid">';

            // Basic Information
            previewHtml +=
                '<h5 class="border-bottom pb-2 mb-3"><i class="ti tabler-info-circle me-2"></i>Basic Information</h5>';
            previewHtml += '<div class="row mb-4">';

            const title = $('input[name="title"]').val() || 'Not provided';
            previewHtml += `<div class="col-md-12 mb-3"><strong>Title:</strong><br>${escapeHtml(title)}</div>`;

            const articleType = $('#article_type_id option:selected').text() || 'Not selected';
            previewHtml += `<div class="col-md-6 mb-3"><strong>Article Type:</strong><br>${escapeHtml(articleType)}</div>`;

            const presentationType = $('#presentation_type option:selected').text() || 'Not selected';
            previewHtml +=
                `<div class="col-md-6 mb-3"><strong>Presentation Type:</strong><br>${escapeHtml(presentationType)}</div>`;

            const track = $('#submission_category_major_track_id option:selected').text() || 'Not selected';
            previewHtml += `<div class="col-md-6 mb-3"><strong>Submission Track:</strong><br>${escapeHtml(track)}</div>`;

            // Parse keywords from Tagify JSON format
            let keywordsDisplay = 'Not provided';
            const keywordsRaw = $('#keyWord').val();
            if (keywordsRaw) {
                try {
                    const keywordsArray = JSON.parse(keywordsRaw);
                    if (Array.isArray(keywordsArray) && keywordsArray.length > 0) {
                        keywordsDisplay = keywordsArray.map(k => k.value).join(', ');
                    }
                } catch (e) {
                    // If not JSON, use as is
                    keywordsDisplay = keywordsRaw;
                }
            }
            previewHtml += `<div class="col-md-6 mb-3"><strong>Keywords:</strong><br>${escapeHtml(keywordsDisplay)}</div>`;

            previewHtml += '</div>';

            // Abstract/Content
            previewHtml += '<h5 class="border-bottom pb-2 mb-3"><i class="ti tabler-file-text me-2"></i>Content</h5>';

            // Check if using sections or abstract
            if ($('#contentSectionsContainer .section-content').length > 0) {
                $('#contentSectionsContainer .section-content').each(function() {
                    const sectionName = $(this).closest('.form-group').find('label').text().replace('*', '').trim();
                    const editorId = $(this).attr('id');
                    let content = 'Not provided';
                    if (ckeditorInstances[editorId]) {
                        content = ckeditorInstances[editorId].getData() || 'Not provided';
                    }
                    previewHtml +=
                        `<div class="mb-3"><strong>${escapeHtml(sectionName)}:</strong><div class="border p-3 mt-2">${content}</div></div>`;
                });
            } else if (ckeditorInstances['description2']) {
                const abstractContent = ckeditorInstances['description2'].getData() || 'Not provided';
                previewHtml +=
                    `<div class="mb-3"><strong>Abstract Content:</strong><div class="border p-3 mt-2">${abstractContent}</div></div>`;
            }

            // Attachment
            const attachmentInput = $('#image')[0];
            if (attachmentInput && attachmentInput.files && attachmentInput.files.length > 0) {
                const fileName = attachmentInput.files[0].name;
                const fileSize = (attachmentInput.files[0].size / 1024).toFixed(2); // Convert to KB
                previewHtml += `<div class="mb-3"><strong>Attachment:</strong><br>
                    <span class="badge bg-info">${escapeHtml(fileName)}</span> 
                    <small class="text-muted">(${fileSize} KB)</small>
                </div>`;
            }

            // Authors
            previewHtml += '<h5 class="border-bottom pb-2 mb-3 mt-4"><i class="ti tabler-users me-2"></i>Authors</h5>';

            // Submitter
            const submitterName = '{{ current_user()->fullName(current_user()) }}';
            const submitterEmail = '{{ current_user()->email }}';
            const isMainAuthor = $('#main_author_checkbox').is(':checked');
            const isMainPresenter = $('#main_presenter_checkbox').is(':checked');
            let submitterBadges = '';
            if (isMainAuthor) submitterBadges += '<span class=\"badge bg-success ms-2\">Main Author</span>';
            if (isMainPresenter) submitterBadges += '<span class=\"badge bg-primary ms-2\">Main Presenter</span>';

            previewHtml += `<div class="card mb-2">
                <div class="card-body">
                    <strong>${escapeHtml(submitterName)}</strong> ${submitterBadges}
                    <br><small class="text-muted">${escapeHtml(submitterEmail)}</small>
                </div>
            </div>`;

            // Co-authors
            $('.author-item').each(function() {
                const authorIndex = $(this).attr('id').replace('author_row_', '');
                const name = $(`#author_name_${authorIndex}`).val() || 'Not provided';
                const email = $(`#author_email_${authorIndex}`).val() || 'Not provided';
                const phone = $(`#author_phone_${authorIndex}`).val() || 'Not provided';
                const designation = $(`#author_designation_${authorIndex}`).val() || 'Not provided';
                const institution = $(`#author_institution_${authorIndex}`).val() || 'Not provided';
                const address = $(`#author_institution_address_${authorIndex}`).val() || 'Not provided';
                const isCoMainAuthor = $(`#co_author_main_author_${authorIndex}`).is(':checked');
                const isCoMainPresenter = $(`#co_author_main_presenter_${authorIndex}`).is(':checked');

                let badges = '';
                if (isCoMainAuthor) badges += '<span class=\"badge bg-success ms-2\">Main Author</span>';
                if (isCoMainPresenter) badges += '<span class=\"badge bg-primary ms-2\">Main Presenter</span>';

                // Get contributions
                let contributionsHtml = '';
                const selectedContributions = [];
                $(`input[name="authors[${authorIndex}][contributions][]"]:checked`).each(function() {
                    const contributionLabel = $(this).parent().find('label').text().trim();
                    selectedContributions.push(contributionLabel);
                });

                // Check for "Other" contribution
                const otherContribution = $(`#contribution_other_text_${authorIndex}`).val();
                if (otherContribution) {
                    selectedContributions.push('Other: ' + otherContribution);
                }

                if (selectedContributions.length > 0) {
                    contributionsHtml = '<br><small class="text-muted"><strong>Contributions:</strong> ' +
                        escapeHtml(selectedContributions.join(', ')) + '</small>';
                }

                previewHtml += `<div class="card mb-2">
                    <div class="card-body">
                        <strong>${escapeHtml(name)}</strong> ${badges}
                        <br><small class="text-muted">Email: ${escapeHtml(email)}</small>
                        <br><small class="text-muted">Phone: ${escapeHtml(phone)}</small>
                        <br><small class="text-muted">Designation: ${escapeHtml(designation)}</small>
                        <br><small class="text-muted">Institution: ${escapeHtml(institution)}</small>
                        <br><small class="text-muted">Address: ${escapeHtml(address)}</small>
                        ${contributionsHtml}
                    </div>
                </div>`;
            });

            // Additional Information
            if ($('#has_conflict_of_interest_yes').is(':checked') || $('#has_source_of_funding_yes').is(':checked')) {
                previewHtml +=
                    '<h5 class="border-bottom pb-2 mb-3 mt-4"><i class="ti tabler-file-info me-2\"></i>Additional Information</h5>';

                if ($('#has_conflict_of_interest_yes').is(':checked')) {
                    const conflict = $('#conflict_of_interest').val() || 'Not provided';
                    previewHtml +=
                        `<div class="mb-3"><strong>Conflict of Interest:</strong><br>${escapeHtml(conflict)}</div>`;
                }

                if ($('#has_source_of_funding_yes').is(':checked')) {
                    const funding = $('#source_of_funding').val() || 'Not provided';
                    previewHtml += `<div class="mb-3"><strong>Source of Funding:</strong><br>${escapeHtml(funding)}</div>`;
                }
            }

            previewHtml += '</div>';

            $('#previewContent').html(previewHtml);
            $('#previewModal').modal('show');
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        function initializeTagify() {
            const keywordInput = document.querySelector('#keyWord');
            if (keywordInput) {
                new Tagify(keywordInput, {
                    maxTags: {{ @$setting->key_word_limit ?? 9999 }}
                });
            }
        }

        function addAuthorField(data = null) {
            const index = authorIndex++;
            const authorId = data && data.id ? data.id : ''; // Capture the author ID
            const name = data ? data.name : '';
            const email = data ? data.email : '';
            const phone = data ? data.phone : '';
            const designation = data ? data.designation : '';
            const institution = data ? data.institution : '';
            const address = data ? data.institution_address : '';
            const isMainAuthor = data && data.main_author == 1 ? 'checked' : '';
            const isMainPresenter = data && data.main_presenter == 1 ? 'checked' : '';

            // Build dropdown options for existing authors - only include authors added before this one
            let existingAuthorsHtml = '<option value="">-- Select Existing Author Data --</option>';

            // Add submission user data
            existingAuthorsHtml +=
                `<option value="submitter" data-designation="{{ current_user()->userDetail?->designation?->designation ?? '' }}" data-institution="{{ current_user()->userDetail?->institution?->name ?? '' }}" data-address="{{ current_user()->userDetail->institute_address ?? '' }}">Submitter ({{ current_user()->fullName(current_user()) }})</option>`;

            // Add existing authors from database (for edit mode)
            @if (isset($submission) && $submission->authors)
                const existingAuthors = @json($submission->authors);
                existingAuthors.forEach(author => {
                    const safeDesignation = (author.designation || '').replace(/"/g, '&quot;');
                    const safeInstitution = (author.institution || '').replace(/"/g, '&quot;');
                    const safeAddress = (author.institution_address || '').replace(/"/g, '&quot;');
                    existingAuthorsHtml +=
                        `<option value="db_${author.id}" data-designation="${safeDesignation}" data-institution="${safeInstitution}" data-address="${safeAddress}">${author.name} (Existing)</option>`;
                });
            @endif

            // Add previously added co-authors (only those with lower index)
            $('.author-item').each(function() {
                const authorCard = $(this);
                const authorIndex = parseInt(authorCard.attr('id').replace('author_row_', ''));

                // Only include authors with lower index than current
                if (authorIndex < index) {
                    const name = authorCard.find(`#author_name_${authorIndex}`).val();
                    const designation = authorCard.find(`#author_designation_${authorIndex}`).val();
                    const institution = authorCard.find(`#author_institution_${authorIndex}`).val();
                    const address = authorCard.find(`#author_institution_address_${authorIndex}`).val();

                    if (name) {
                        const safeDesignation = (designation || '').replace(/"/g, '&quot;');
                        const safeInstitution = (institution || '').replace(/"/g, '&quot;');
                        const safeAddress = (address || '').replace(/"/g, '&quot;');
                        const authorNumber = $('.author-item').index(authorCard) + 1;
                        existingAuthorsHtml +=
                            `<option value="author_${authorIndex}" data-designation="${safeDesignation}" data-institution="${safeInstitution}" data-address="${safeAddress}">Co-Author ${authorNumber} (${name})</option>`;
                    }
                }
            });

            let contributionsHtml = '';
            if (contributionEnabled && contributions.length > 0) {
                const authorContributions = data && data.contributions ? (Array.isArray(data.contributions) ? data
                    .contributions : []) : [];
                const authorContributionIds = authorContributions.map(c => typeof c === 'object' ? c.id : parseInt(c));

                let checkboxesHtml = '';
                contributions.forEach(contribution => {
                    const isChecked = authorContributionIds.includes(contribution.id) ? 'checked' : '';
                    const description = contribution.description ? contribution.description.replace(/"/g,
                        '&quot;') : '';
                    checkboxesHtml += `
                <div class="col-md-6 col-lg-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input contribution-checkbox"
                            name="authors[${index}][contributions][]" 
                            id="contribution_${index}_${contribution.id}"
                            value="${contribution.id}" ${isChecked}>
                        <label class="form-check-label" for="contribution_${index}_${contribution.id}">
                            ${contribution.name}
                            ${description ? `<i class="ti ti-info-circle" data-bs-toggle="tooltip" title="${description}"></i>` : ''}
                        </label>
                    </div>
                </div>
            `;
                });

                const otherChecked = data && data.contribution_other ? 'checked' : '';
                const otherText = data && data.contribution_other ? data.contribution_other : '';
                const otherDisplay = otherChecked ? 'block' : 'none';

                contributionsHtml = `
            <div class="col-md-12 form-group mb-3">
                <label>Contribution <code>*</code> <small class="text-muted">(Select at least one contribution or specify other)</small></label>
                <div class="row">
                    ${checkboxesHtml}
                    <div class="col-md-6 col-lg-4 mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input contribution-other-checkbox"
                                name="authors[${index}][contribution_other_checkbox]" 
                                id="contribution_other_${index}" 
                                value="1" ${otherChecked}
                                onchange="toggleOtherContribution(${index})">
                            <label class="form-check-label" for="contribution_other_${index}">
                                Other (Please Specify)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row mt-2" id="otherContributionField_${index}" style="display: ${otherDisplay};">
                    <div class="col-md-12">
                        <input type="text" class="form-control @error('authors.${index}.contribution_other_text') is-invalid @enderror" 
                            name="authors[${index}][contribution_other_text]"
                            id="contribution_other_text_${index}" 
                            placeholder="Please specify other contribution"
                            value="${otherText}">
                        <div class="invalid-feedback" id="contribution_other_text_error_${index}"></div>
                    </div>
                </div>
                <div class="invalid-feedback d-block" id="contributions_error_${index}"></div>
            </div>
        `;
            }

            // Add hidden input for author ID to track existing authors
            const authorIdField = authorId ? `<input type="hidden" name="authors[${index}][id]" value="${authorId}">` : '';

            const html = `
        <div class="card mb-3 border author-item" id="author_row_${index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">Co-Author ${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-author-btn" data-index="${index}">
                        <i class="ti tabler-trash"></i>
                    </button>
                </div>
                <div class="row g-3">
                    ${authorIdField}
                    <div class="col-md-12">
                        <label class="form-label">Is Designation/Institution/Institution Address same as any of the following Author?</label>
                        <select class="form-select" id="existing_author_${index}" onchange="populateAuthorData(${index})">
                            ${existingAuthorsHtml}
                        </select>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6 co-author-main-author-container" style="display: ${$('#main_author_checkbox').is(':checked') || $('.co-author-main-author:checked').length > 0 ? 'none' : 'block'};">
                            <div class="form-check">
                                <input class="form-check-input co-author-main-author" type="checkbox" 
                                    name="authors[${index}][main_author]" id="co_author_main_author_${index}"
                                    value="1" data-index="${index}" ${isMainAuthor}>
                                <label class="form-check-label" for="co_author_main_author_${index}">
                                    <strong>Set as Main Author</strong>
                                    <i class="ti tabler-info-circle text-primary ms-1" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Only one author can be the main author. Selecting this will uncheck the submitter and other co-authors."></i>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 co-author-main-presenter-container" style="display: ${$('#main_presenter_checkbox').is(':checked') || $('.co-author-main-presenter:checked').length > 0 ? 'none' : 'block'};">
                            <div class="form-check">
                                <input class="form-check-input co-author-main-presenter" type="checkbox" 
                                    name="authors[${index}][main_presenter]" id="co_author_main_presenter_${index}"
                                    value="1" data-index="${index}" ${isMainPresenter}>
                                <label class="form-check-label" for="co_author_main_presenter_${index}">
                                    <strong>Set as Main Presenter</strong>
                                    <i class="ti tabler-info-circle text-primary ms-1" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Only one author can be the main presenter. Selecting this will uncheck the submitter and other co-authors."></i>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Name <code>*</code></label>
                        <input type="text" class="form-control @error('authors.${index}.name') is-invalid @enderror" 
                            name="authors[${index}][name]" id="author_name_${index}" value="${name}" required>
                        <div class="invalid-feedback" id="author_name_error_${index}"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <code>*</code></label>
                        <input type="email" class="form-control @error('authors.${index}.email') is-invalid @enderror" 
                            name="authors[${index}][email]" id="author_email_${index}" value="${email}" required>
                        <div class="invalid-feedback" id="author_email_error_${index}"></div>
                    </div>
                    <div class="col-md-6"> 
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control @error('authors.${index}.phone') is-invalid @enderror" 
                            name="authors[${index}][phone]" id="author_phone_${index}" value="${phone}">
                        <div class="invalid-feedback" id="author_phone_error_${index}"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation <code>(optional)</code></label>
                        <input type="text" class="form-control @error('authors.${index}.designation') is-invalid @enderror" 
                            name="authors[${index}][designation]" id="author_designation_${index}" value="${designation}">
                        <div class="invalid-feedback" id="author_designation_error_${index}"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Institution <code>*</code></label>
                        <input type="text" class="form-control @error('authors.${index}.institution') is-invalid @enderror" 
                            name="authors[${index}][institution]" id="author_institution_${index}" value="${institution}" required>
                        <div class="invalid-feedback" id="author_institution_error_${index}"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Institution Address <code>*</code></label>
                        <input type="text" class="form-control @error('authors.${index}.institution_address') is-invalid @enderror" 
                            name="authors[${index}][institution_address]" id="author_institution_address_${index}" value="${address}" required>
                        <div class="invalid-feedback" id="author_institution_address_error_${index}"></div>
                    </div>
                    ${contributionsHtml}
                </div>
            </div>
        </div>
    `;
            $('#authorsContainer').append(html);

            // Re-initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Update dropdowns when author data changes
            $(document).on('input',
                `#author_name_${index}, #author_designation_${index}, #author_institution_${index}, #author_institution_address_${index}`,
                function() {
                    updateExistingAuthorDropdowns();
                });

            // Populate validation errors if they exist
            @if ($errors->any())
                const errors = @json($errors->messages());

                // Check for errors for this specific author index
                if (errors[`authors.${index}.name`]) {
                    $(`#author_name_${index}`).addClass('is-invalid');
                    $(`#author_name_error_${index}`).text(errors[`authors.${index}.name`][0]);
                }
                if (errors[`authors.${index}.email`]) {
                    $(`#author_email_${index}`).addClass('is-invalid');
                    $(`#author_email_error_${index}`).text(errors[`authors.${index}.email`][0]);
                }
                if (errors[`authors.${index}.phone`]) {
                    $(`#author_phone_${index}`).addClass('is-invalid');
                    $(`#author_phone_error_${index}`).text(errors[`authors.${index}.phone`][0]);
                }
                if (errors[`authors.${index}.designation`]) {
                    $(`#author_designation_${index}`).addClass('is-invalid');
                    $(`#author_designation_error_${index}`).text(errors[`authors.${index}.designation`][0]);
                }
                if (errors[`authors.${index}.institution`]) {
                    $(`#author_institution_${index}`).addClass('is-invalid');
                    $(`#author_institution_error_${index}`).text(errors[`authors.${index}.institution`][0]);
                }
                if (errors[`authors.${index}.institution_address`]) {
                    $(`#author_institution_address_${index}`).addClass('is-invalid');
                    $(`#author_institution_address_error_${index}`).text(errors[`authors.${index}.institution_address`][0]);
                }
                if (errors[`authors.${index}.contributions`]) {
                    $(`#contributions_error_${index}`).text(errors[`authors.${index}.contributions`][0]);
                }
                if (errors[`authors.${index}.contribution_other_text`]) {
                    $(`#contribution_other_text_${index}`).addClass('is-invalid');
                    $(`#contribution_other_text_error_${index}`).text(errors[`authors.${index}.contribution_other_text`][
                        0
                    ]);
                }
            @endif
        }

        function populateAuthorData(index) {
            const selectElement = $(`#existing_author_${index}`);
            const selectedOption = selectElement.find('option:selected');

            if (selectedOption.val()) {
                const designation = selectedOption.data('designation') || '';
                const institution = selectedOption.data('institution') || '';
                const address = selectedOption.data('address') || '';

                $(`#author_designation_${index}`).val(designation);
                $(`#author_institution_${index}`).val(institution);
                $(`#author_institution_address_${index}`).val(address);
            }
        }

        function updateExistingAuthorDropdowns() {
            // Update each dropdown individually to include only authors added before it
            $('.author-item').each(function() {
                const currentAuthorCard = $(this);
                const currentAuthorIndex = currentAuthorCard.attr('id').replace('author_row_', '');
                const currentDropdown = currentAuthorCard.find(`#existing_author_${currentAuthorIndex}`);

                if (currentDropdown.length === 0) return;

                // Build options HTML for this specific dropdown
                let optionsHtml = '<option value="">-- Select Existing Author Data --</option>';

                // Add submission user data
                optionsHtml +=
                    `<option value="submitter" data-designation="{{ current_user()->userDetail?->designation?->designation ?? '' }}" data-institution="{{ current_user()->userDetail?->institution?->name ?? '' }}" data-address="{{ current_user()->userDetail->institute_address ?? '' }}">Submitter ({{ current_user()->fullName(current_user()) }})</option>`;

                // Add existing authors from database (for edit mode)
                @if (isset($submission) && $submission->authors)
                    const existingAuthors = @json($submission->authors);
                    existingAuthors.forEach(author => {
                        const safeDesignation = (author.designation || '').replace(/"/g, '&quot;');
                        const safeInstitution = (author.institution || '').replace(/"/g, '&quot;');
                        const safeAddress = (author.institution_address || '').replace(/"/g, '&quot;');
                        optionsHtml +=
                            `<option value="db_${author.id}" data-designation="${safeDesignation}" data-institution="${safeInstitution}" data-address="${safeAddress}">${author.name} (Existing)</option>`;
                    });
                @endif

                // Add only co-authors that were added BEFORE the current one
                $('.author-item').each(function() {
                    const authorCard = $(this);
                    const authorIndex = authorCard.attr('id').replace('author_row_', '');

                    // Only include authors with lower index (added before current author)
                    if (parseInt(authorIndex) < parseInt(currentAuthorIndex)) {
                        const name = authorCard.find(`#author_name_${authorIndex}`).val();
                        const designation = authorCard.find(`#author_designation_${authorIndex}`).val();
                        const institution = authorCard.find(`#author_institution_${authorIndex}`).val();
                        const address = authorCard.find(`#author_institution_address_${authorIndex}`).val();

                        if (name) {
                            const safeDesignation = (designation || '').replace(/"/g, '&quot;');
                            const safeInstitution = (institution || '').replace(/"/g, '&quot;');
                            const safeAddress = (address || '').replace(/"/g, '&quot;');
                            const authorNumber = $('.author-item').index(authorCard) + 1;
                            optionsHtml +=
                                `<option value="author_${authorIndex}" data-designation="${safeDesignation}" data-institution="${safeInstitution}" data-address="${safeAddress}">Co-Author ${authorNumber} (${name})</option>`;
                        }
                    }
                });

                // Update this specific dropdown
                const currentValue = currentDropdown.val();
                currentDropdown.html(optionsHtml);
                // Restore the previously selected value if it still exists
                if (currentValue && currentDropdown.find(`option[value="${currentValue}"]`).length) {
                    currentDropdown.val(currentValue);
                }
            });
        }

        function toggleOtherContribution(index) {
            const checkbox = $(`#contribution_other_${index}`);
            const field = $(`#otherContributionField_${index}`);
            const input = $(`#contribution_other_text_${index}`);

            if (checkbox.is(':checked')) {
                field.slideDown();
                input.attr('required', true);
            } else {
                field.slideUp();
                input.attr('required', false);
                input.val('');
            }
        }

        function loadArticleTypeSettings(articleTypeId) {
            $.ajax({
                url: '{{ route('my-society.conference.submission.get-article-type-setting', [$society, $conference]) }}',
                type: 'GET',
                data: {
                    article_type_id: articleTypeId
                },
                success: function(response) {
                    if (response.has_setting) {
                        articleTypeSettings = response.setting;
                        showDynamicFields(response.setting);
                    } else {
                        showDefaultFields();
                    }
                },
                error: function() {
                    showDefaultFields();
                }
            });
        }

        function showDynamicFields(setting) {
            // Destroy existing CKEditor instances
            for (let key in ckeditorInstances) {
                if (ckeditorInstances[key]) {
                    ckeditorInstances[key].destroy();
                    delete ckeditorInstances[key];
                }
            }

            // Clear containers
            $('#contentSectionsContainer').empty();
            $('#attachmentContainer').empty();
            $('#conflictOfInterestContainer').empty();
            $('#sourceOfFundingContainer').empty();

            // Handle content sections
            if (setting.number_of_sections > 0 && setting.sections) {
                setting.sections.forEach((section, index) => {
                    const sectionName = section.name || 'Section ' + (index + 1);
                    const wordLimit = section.word_limit || '';
                    const instruction = section.instruction || '';

                    let oldSectionContent = '';
                    @if (old('sections'))
                        const oldSections = @json(old('sections'));
                        oldSectionContent = oldSections[index]?.content || '';
                    @elseif (isset($submission) && $submission->sections)
                        const submissionSections = @json($submission->sections);
                        oldSectionContent = submissionSections[index]?.content || '';
                    @endif

                    let sectionError = '';
                    @if ($errors->any())
                        const sectionErrors = @json($errors->messages());
                        if (sectionErrors[`sections.${index}.content`]) {
                            sectionError =
                                `<p class="text-danger">${sectionErrors[`sections.${index}.content`][0]}</p>`;
                        }
                    @endif

                    const sectionHtml = `
                        <div class="col-md-12 form-group mb-3">
                            <label for="section_${index}" class="form-label">${sectionName} <code>*${wordLimit ? ` (Word Limit: ${wordLimit})` : ''}</code></label>
                            ${instruction ? `<p class="text-muted small mb-2"><i class="ti tabler-info-circle"></i> ${instruction}</p>` : ''}
                            <textarea class="form-control section-content ${sectionError ? 'is-invalid' : ''}" name="sections[${index}][content]" id="section_${index}" cols="30" rows="5"></textarea>
                            <input type="hidden" name="sections[${index}][name]" value="${sectionName}">
                            <input type="hidden" name="sections[${index}][word_limit]" value="${wordLimit}">
                            ${sectionError}
                        </div>
                    `;
                    $('#contentSectionsContainer').append(sectionHtml);

                    // Initialize CKEditor for this section
                    initializeCKEditor(`section_${index}`, wordLimit, oldSectionContent);
                });
            } else {
                // Show default abstract content
                showDefaultAbstract();
            }

            // Handle attachment - use article type setting if available, otherwise fall back to submission setting
            if (setting.attachment_name) {
                handleAttachmentField(setting);
            } else {
                // Fall back to submission setting
                @if ($setting->attachment_name)
                    const defaultSetting = {
                        attachment_name: '{!! $setting->attachment_name !!}',
                        is_attachment_required: {{ $setting->attachment_required ? 'true' : 'false' }}
                    };
                    handleAttachmentField(defaultSetting);
                @else
                    $('#attachmentContainer').empty();
                @endif
            }

            // Handle Conflict of Interest
            handleConflictOfInterest(setting);

            // Handle Source of Funding
            handleSourceOfFunding(setting);
        }

        function initializeCKEditor(elementId, wordLimit, initialContent = '') {
            const maxWords = wordLimit || Infinity;
            const copyPasteAllowed = {{ !empty($setting->copy_paste_allowed) ? 'true' : 'false' }};

            const editorConfig = {
                filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                filebrowserUploadMethod: "form",
                extraPlugins: 'wordcount',
                wordcount: {
                    showWordCount: true,
                    maxWordCount: maxWords
                }
            };

            // Add paste event blocker if copy-paste is disabled
            if (!copyPasteAllowed) {
                editorConfig.on = {
                    paste: function(evt) {
                        evt.cancel();
                        alert('Copy-paste is disabled for this submission.');
                    }
                };
            }

            ckeditorInstances[elementId] = CKEDITOR.replace(elementId, editorConfig);

            if (initialContent) {
                ckeditorInstances[elementId].on('instanceReady', function() {
                    this.setData(initialContent);
                });
            }
        }

        function showDefaultAbstract() {
            const abstractError =
                '@error('abstract_content')<p class="text-danger">{{ $message }}</p>@enderror';
            const abstractContent = {!! json_encode(!empty(old('abstract_content')) ? old('abstract_content') : @$submission->abstract_content) !!};

            const abstractHtml = `
                <div class="col-md-12 form-group mb-3">
                    <label for="abstract_content" class="form-label">Abstract Content <code>*
                        (Word Limit: {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'infinity' }})</code></label>
                    <textarea class="form-control @error('abstract_content') is-invalid @enderror" name="abstract_content" id="description2" cols="30" rows="5"></textarea>
                    ${abstractError}
                </div>
            `;
            $('#contentSectionsContainer').html(abstractHtml);

            const wordLimit = {{ @$setting->abstract_word_limit ?? 'Infinity' }};
            initializeCKEditor('description2', wordLimit, abstractContent || '');
        }

        function handleAttachmentField(setting) {
            if (setting.attachment_name) {
                const isRequired = setting.is_attachment_required;
                const submissionImage = '{{ @$submission->image }}';

                let imagePreview = '';
                @if (isset($submission) && $submission->image)
                    imagePreview = `
                                <div class="row mt-2" id="imgPreview">
                                    <div class="col-3">
                                        <a href="{{ asset('storage/participant/submission/image/' . $submission->image) }}" target="_blank">
                                            <img src="{{ asset('storage/participant/submission/image/' . $submission->image) }}" class="img-fluid" alt="image">
                                        </a>
                                    </div>
                                </div>
                            `;
                @endif

                const imageError =
                    '@error('image')<p class="text-danger">{{ $message }}</p>@enderror';
                const requiredAttr = isRequired && !submissionImage ? 'required' : '';

                const attachmentHtml = `
                    <div class="mb-6 col-md-6">
                        <label class="form-label" for="image">${setting.attachment_name} <code> (JPG, JPEG, PNG, PDF - Max size: 250 KB ${isRequired ? '*)' : '(optional))'}<\/code></label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image" ${requiredAttr} />
                        ${imagePreview}
                        ${imageError}
                    </div>
                `;
                $('#attachmentContainer').html(attachmentHtml);
            }
        }

        function handleConflictOfInterest(setting) {
            if (setting.is_conflict_of_interest_required) {
                const oldConflict = `{{ old('conflict_of_interest', @$submission->conflict_of_interest) }}`;
                const oldConflictOption =
                    `{{ old('has_conflict_of_interest', @$submission->conflict_of_interest ? 'yes' : '') }}`;
                const conflictError =
                    '@error('conflict_of_interest')<p class="text-danger">{{ $message }}</p>@enderror';
                const conflictOptionError =
                    '@error('has_conflict_of_interest')<p class="text-danger">{{ $message }}</p>@enderror';

                const conflictHtml = `
                    <div class="col-md-12 form-group mb-3">
                        <label class="form-label">Do you have any Conflict of Interest? <code>*</code></label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="has_conflict_of_interest" id="conflict_yes" value="yes" ${oldConflictOption === 'yes' ? 'checked' : ''} required>
                            <label class="form-check-label" for="conflict_yes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="has_conflict_of_interest" id="conflict_no" value="no" ${oldConflictOption === 'no' ? 'checked' : ''} required>
                            <label class="form-check-label" for="conflict_no">No</label>
                        </div>
                        ${conflictOptionError}
                    </div>
                    <div class="col-md-12 form-group mb-3" id="conflictDetailsWrapper" style="display: none;">
                        <label for="conflict_of_interest" class="form-label">Conflict of Interest Details <code>*</code></label>
                        <textarea class="form-control @error('conflict_of_interest') is-invalid @enderror" name="conflict_of_interest" id="conflict_of_interest" rows="3">${oldConflict}</textarea>
                        ${conflictError}
                    </div>
                `;
                $('#conflictOfInterestContainer').html(conflictHtml);

                $('input[name="has_conflict_of_interest"]').on('change', function() {
                    if ($(this).val() === 'yes') {
                        $('#conflictDetailsWrapper').show();
                        $('#conflict_of_interest').attr('required', true);
                    } else {
                        $('#conflictDetailsWrapper').hide();
                        $('#conflict_of_interest').attr('required', false);
                        $('#conflict_of_interest').val('');
                    }
                });

                if (oldConflictOption === 'yes') {
                    $('#conflictDetailsWrapper').show();
                }
            }
        }

        function handleSourceOfFunding(setting) {
            if (setting.is_source_of_funding_required) {
                const oldFunding = `{{ old('source_of_funding', @$submission->source_of_funding) }}`;
                const oldFundingOption =
                    `{{ old('has_source_of_funding', @$submission->source_of_funding ? 'yes' : '') }}`;
                const fundingError =
                    '@error('source_of_funding')<p class="text-danger">{{ $message }}</p>@enderror';
                const fundingOptionError =
                    '@error('has_source_of_funding')<p class="text-danger">{{ $message }}</p>@enderror';

                const fundingHtml = `
                    <div class="col-md-12 form-group mb-3">
                        <label class="form-label">Do you have any Source of Funding? <code>*</code></label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="has_source_of_funding" id="funding_yes" value="yes" ${oldFundingOption === 'yes' ? 'checked' : ''} required>
                            <label class="form-check-label" for="funding_yes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="has_source_of_funding" id="funding_no" value="no" ${oldFundingOption === 'no' ? 'checked' : ''} required>
                            <label class="form-check-label" for="funding_no">No</label>
                        </div>
                        ${fundingOptionError}
                    </div>
                    <div class="col-md-12 form-group mb-3" id="fundingDetailsWrapper" style="display: none;">
                        <label for="source_of_funding" class="form-label">Source of Funding Details <code>*</code></label>
                        <textarea class="form-control @error('source_of_funding') is-invalid @enderror" name="source_of_funding" id="source_of_funding" rows="3">${oldFunding}</textarea>
                        ${fundingError}
                    </div>
                `;
                $('#sourceOfFundingContainer').html(fundingHtml);

                $('input[name="has_source_of_funding"]').on('change', function() {
                    if ($(this).val() === 'yes') {
                        $('#fundingDetailsWrapper').show();
                        $('#source_of_funding').attr('required', true);
                    } else {
                        $('#fundingDetailsWrapper').hide();
                        $('#source_of_funding').attr('required', false);
                        $('#source_of_funding').val('');
                    }
                });

                if (oldFundingOption === 'yes') {
                    $('#fundingDetailsWrapper').show();
                }
            }
        }

        function showDefaultFields() {
            // Destroy existing CKEditor instances
            for (let key in ckeditorInstances) {
                if (ckeditorInstances[key]) {
                    ckeditorInstances[key].destroy();
                    delete ckeditorInstances[key];
                }
            }

            // Clear all containers
            $('#contentSectionsContainer').empty();
            $('#attachmentContainer').empty();
            $('#conflictOfInterestContainer').empty();
            $('#sourceOfFundingContainer').empty();

            // Recreate default abstract content
            showDefaultAbstract();

            // Recreate default attachment field
            @if ($setting->attachment_name)
                const defaultSetting = {
                    attachment_name: '{!! $setting->attachment_name !!}',
                    is_attachment_required: {{ $setting->attachment_required ? 'true' : 'false' }}
                };
                handleAttachmentField(defaultSetting);
            @endif
        }
    </script>
@endsection
