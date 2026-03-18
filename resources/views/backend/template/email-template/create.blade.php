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
                                    Submission Accepted - Oral</option>
                                <option value="6"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 6 ? 'selected' : '' }}>
                                    Submission Accepted - Poster</option>
                                <option value="3"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 3 ? 'selected' : '' }}>
                                    Submission Correction - Oral</option>
                                <option value="7"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 7 ? 'selected' : '' }}>
                                    Submission Correction - Poster</option>
                                <option value="4"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 4 ? 'selected' : '' }}>
                                    Submission Rejected - Oral</option>
                                <option value="8"
                                    {{ old('key', isset($email_template) ? $email_template->key : '') == 8 ? 'selected' : '' }}>
                                    Submission Rejected - Poster</option>
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
            6: [{
                tag: "{submission_topic}",
                label: "Submission Topic"
            }, ],
            7: [{
                tag: "{submission_topic}",
                label: "Submission Topic"
            }, ],
            8: [{
                    tag: "{submission_topic}",
                    label: "Submission Topic"
                },
                {
                    tag: "{reject_remark}",
                    label: "Reject Remark"
                },
            ],
            9: [{
                tag: "{submission_topic}",
                label: "Submission Topic"
            }, ]
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

        CKEDITOR.replace('bodyEditor', {
            height: 300,
            allowedContent: true
        });
    </script>
@endsection
