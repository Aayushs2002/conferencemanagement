@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($email_template) ? 'Edit' : 'Add' }} Email Template
@endsection

@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header">
                <a href="{{ route('email-template.index', [$society, $conference]) }}">
                    <i class="ti tabler-arrow-narrow-left"></i>
                </a>
                {{ isset($email_template) ? 'Edit' : 'Add' }} Email Template
            </h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($email_template) ? route('email-template.update', [$society, $conference, $email_template->id]) : route('email-template.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @isset($email_template)
                        @method('patch')
                    @endisset

                    <div class="alert alert-info p-3 rounded">
                        <strong>Insert Placeholders:</strong>
                        <div id="placeholder-buttons" class="mt-2">
                            {{-- Placeholder buttons will load here dynamically --}}
                        </div>
                        <small class="text-warning d-block mt-2">
                            ⚠ Click the buttons above to insert placeholders into your email. They will be replaced with
                            real values when sending.
                        </small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Template Type <span class="text-danger">*</span></label>
                            <select id="template-type" class="form-select" name="key">
                                <option value="">-- Select Template Type --</option>
                                <option value="1"
                                    {{ old('template_type', isset($email_template) ? $email_template->key : '') == 1 ? 'selected' : '' }}>
                                    User Submission</option>
                                <option value="2"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 2 ? 'selected' : '' }}>
                                    Submission Accepted</option>
                                <option value="3"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 3 ? 'selected' : '' }}>
                                    Submission Correction</option>
                                <option value="4"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 4 ? 'selected' : '' }}>
                                    Submission Rejected</option>
                                <option value="5"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 5 ? 'selected' : '' }}>
                                    Expert Assigned</option>
                                <option value="9"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 9 ? 'selected' : '' }}>
                                    Convert Oral to Poster</option>
                            </select>
                            @error('key')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="subject" id="subject"
                                value="{{ old('subject', isset($email_template) ? $email_template->subject : '') }}"
                                placeholder="Enter Subject">
                            @error('subject')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                                    {{-- Partner / Category / Presentation Type filters --}}
                    @php
                        $savedFilter     = old('partner_filter', isset($email_template) ? ($email_template->partner_filter ?? []) : []);
                        $filterType      = (isset($email_template) && !empty($email_template->partner_filter)) ? 'selected' : 'all';
                        $savedArticleTypes   = old('article_type_filter', isset($email_template) ? ($email_template->article_type_filter ?? []) : []);
                        $savedPresTypes      = old('presentation_type_filter', isset($email_template) ? ($email_template->presentation_type_filter ?? []) : []);
                    @endphp
                    <div class="row mb-3" id="partner_filter_section">
                        <div class="col-md-4">
                            <label class="form-label">Send Email To</label>
                            <select id="partner_filter_type" class="form-select">
                                <option value="all" {{ $filterType === 'all' ? 'selected' : '' }}>All Submitters</option>
                                <option value="selected" {{ $filterType === 'selected' ? 'selected' : '' }}>Submitters by Partner</option>
                            </select>
                        </div>
                        <div class="col-md-8" id="partner_filter_select_wrapper"
                            style="{{ $filterType === 'selected' ? '' : 'display:none' }}">
                            <label class="form-label">Select Partners</label>
                            <select name="partner_filter[]" id="partner_filter" class="form-select select2" multiple>
                                @foreach ($partnerLogos as $partner)
                                    <option value="{{ $partner['abbreviation'] }}"
                                        @if (is_array($savedFilter) && in_array($partner['abbreviation'], $savedFilter)) selected @endif>
                                        {{ $partner['abbreviation'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('partner_filter')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Presentation Category filter --}}
                        <div class="col-md-6 mt-3">
                            <label class="form-label">Presentation Category <small class="text-muted">(leave empty = all categories)</small></label>
                            <select name="article_type_filter[]" id="article_type_filter" class="form-select select2" multiple>
                                @foreach ($articleTypes as $at)
                                    <option value="{{ $at->id }}"
                                        @if (is_array($savedArticleTypes) && in_array($at->id, $savedArticleTypes)) selected @endif>
                                        {{ $at->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('article_type_filter')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Presentation Type filter --}}
                        <div class="col-md-6 mt-3">
                            <label class="form-label">Presentation Type <small class="text-muted">(leave empty = all types)</small></label>
                            <select name="presentation_type_filter[]" id="presentation_type_filter" class="form-select select2" multiple>
                                <option value="1" @if (is_array($savedPresTypes) && in_array('1', $savedPresTypes)) selected @endif>Poster</option>
                                <option value="2" @if (is_array($savedPresTypes) && in_array('2', $savedPresTypes)) selected @endif>Oral</option>
                                <option value="3" @if (is_array($savedPresTypes) && in_array('3', $savedPresTypes)) selected @endif>Video</option>
                            </select>
                            @error('presentation_type_filter')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Body <span class="text-danger">*</span></label>
                        <textarea id="bodyEditor" name="body">
                            {!! old('body', isset($email_template) ? $email_template->body : '') !!}
                        </textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($email_template) ? 'Update Template' : 'Create Template' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const placeholdersMap = {
            1: [{
                    tag: "{submission_topic}",
                    label: "Submission Topic"
                },
                {
                    tag: "{conference_name}",
                    label: "Conference Name"
                },
                {
                    tag: "{conference_theme}",
                    label: "Conference Theme"
                },
                {
                    tag: "{conference_date}",
                    label: "Conference Date"
                },
                {
                    tag: "{society_email}",
                    label: "Society Email"
                },
            ],
            2: [{
                tag: "{submission_topic}",
                label: "Submission Topic"
            }, ],
            3: [{
                tag: "{submission_topic}",
                label: "Submission Topic"
            }, ],
            4: [{
                    tag: "{submission_topic}",
                    label: "Submission Topic"
                },
                {
                    tag: "{reject_remark}",
                    label: "Reject Remark"
                },
            ],
            5: [{
                tag: "{submission_topic}",
                label: "Submission Topic"
            }, ],
            9: [{
                tag: "{submission_topic}",
                label: "Submission Topic"
            },
            {
                tag: "{response_link}",
                label: "Response Link"
            },
            {
                tag: "{current_format}",
                label: "Current Format (Oral/Poster)"
            },
            {
                tag: "{requested_format}",
                label: "Requested Format (Oral/Poster)"
            }]
        };

        const placeholderButtonsContainer = document.getElementById('placeholder-buttons');
        const templateTypeSelect = document.getElementById('template-type');

        function loadPlaceholders(type) {
            placeholderButtonsContainer.innerHTML = "";
            if (placeholdersMap[type]) {
                placeholdersMap[type].forEach(ph => {
                    const btn = document.createElement('button');
                    btn.type = "button";
                    btn.className = "btn btn-outline-primary btn-sm me-2 mb-2";
                    btn.innerText = ph.label;
                    btn.onclick = () => {
                        insertPlaceholder(ph.tag);
                    };
                    placeholderButtonsContainer.appendChild(btn);
                });
            }
        }

        function insertPlaceholder(tag) {
            const placeholderHTML =
                `<span style="font-weight: bold;"  contenteditable="false">${tag}</span>`;
            CKEDITOR.instances.bodyEditor.insertHtml(placeholderHTML);
        }

        document.addEventListener("DOMContentLoaded", () => {
            loadPlaceholders(templateTypeSelect.value);
        });

        templateTypeSelect.addEventListener('change', (e) => {
            loadPlaceholders(e.target.value);
        });

        // Partner filter type toggle
        document.getElementById('partner_filter_type').addEventListener('change', function() {
            const wrapper = document.getElementById('partner_filter_select_wrapper');
            if (this.value === 'selected') {
                wrapper.style.display = '';
            } else {
                wrapper.style.display = 'none';
                // Clear selections when switching to "all"
                $('#partner_filter').val(null).trigger('change');
            }
        });

        CKEDITOR.replace('bodyEditor', {
            height: 300,
            allowedContent: true
        });

        // Init select2 for partner filter
        $(document).ready(function() {
            $('#partner_filter').select2({ placeholder: 'Select partners', width: '100%' });
            $('#article_type_filter').select2({ placeholder: 'Select categories (all if empty)', width: '100%' });
            $('#presentation_type_filter').select2({ placeholder: 'Select types (all if empty)', width: '100%' });
        });
    </script>
@endsection
