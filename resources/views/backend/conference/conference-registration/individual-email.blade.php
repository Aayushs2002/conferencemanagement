@extends('backend.layouts.conference.main')

@section('title')
    Send Email to {{ $registrant->user->fullName($registrant->user) }}
@endsection

@section('content')
    <div class="card border my-4 container">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Send Email to Registrant</h5>
            <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}" 
               class="btn btn-secondary btn-sm">
                <i class="ti tabler-arrow-left me-1"></i> Back to Registrants
            </a>
        </div>
        <div class="card-body">
            <!-- Registrant Info -->
            <div class="alert alert-secondary">
                <h6 class="alert-heading mb-2"><i class="ti tabler-user me-2"></i>Registrant Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Name:</strong> {{ $registrant->user->userDetail->namePrefix->prefix ?? '' }} {{ $registrant->user->fullName($registrant->user) }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $registrant->user->email }}</p>
                        <p class="mb-0"><strong>Registration ID:</strong> {{ $registrant->registration_id ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Registrant Type:</strong> 
                            @if($registrant->registrant_type == 1) Attendee
                            @elseif($registrant->registrant_type == 2) Speaker
                            @elseif($registrant->registrant_type == 3) Session Chair
                            @elseif($registrant->registrant_type == 4) Special Guest
                            @elseif($registrant->registrant_type == 5) Organizer
                            @endif
                        </p>
                        <p class="mb-1"><strong>Status:</strong> 
                            @if($registrant->verified_status == 1)
                                <span class="badge bg-success">Verified</span>
                            @elseif($registrant->verified_status == 2)
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </p>
                        <p class="mb-0"><strong>Invited:</strong> 
                            @if($registrant->is_invited)
                                <span class="badge bg-info">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Placeholder Information -->
            <div class="alert alert-info">
                <h6 class="alert-heading"><i class="ti tabler-info-circle me-2"></i>Available Placeholders</h6>
                <p class="mb-2">You can use the following placeholders in your message:</p>
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-unstyled small">
                            <li><code>{name}</code> - Full Name</li>
                            <li><code>{first_name}</code> - First Name</li>
                            <li><code>{last_name}</code> - Last Name</li>
                            <li><code>{prefix}</code> - Name Prefix</li>
                            <li><code>{email}</code> - Email Address</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-unstyled small">
                            <li><code>{registrant_type}</code> - Registrant Type</li>
                            <li><code>{registration_id}</code> - Registration ID</li>
                            <li><code>{conference_name}</code> - Conference Name</li>
                            <li><code>{conference_theme}</code> - Conference Theme</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-unstyled small">
                            <li><code>{conference_start_date}</code> - Start Date</li>
                            <li><code>{conference_end_date}</code> - End Date</li>
                            <li><code>{venue}</code> - Venue Name</li>
                            <li><code>{certificate_link}</code> - Certificate Link</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('conference.conference-registration.sendIndividualEmail', [$society, $conference, $registrant->id]) }}">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <label for="subject" class="form-label">Subject <code>*</code></label>
                        <input type="text" 
                               class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" 
                               name="subject" 
                               value="{{ old('subject') }}"
                               placeholder="Enter email subject"
                               required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="message" class="form-label">Message <code>*</code></label>
                        <div class="mb-2">
                            <small class="text-muted d-block mb-2"><i class="ti tabler-info-circle me-1"></i>Click buttons below to insert placeholders:</small>
                            <div class="btn-group flex-wrap" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{name}">Name</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{first_name}">First Name</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{last_name}">Last Name</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{prefix}">Prefix</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{email}">Email</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{registrant_type}">Type</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{registration_id}">Reg ID</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{conference_name}">Conference</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{conference_theme}">Theme</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{conference_start_date}">Start Date</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{conference_end_date}">End Date</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{venue}">Venue</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{certificate_link}">Certificate Link</button>
                            </div>
                        </div>
                        <textarea class="form-control ckeditor @error('message') is-invalid @enderror" 
                                  id="message" 
                                  name="message" 
                                  rows="12"
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12 text-end">
                        <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}" 
                           class="btn btn-secondary">
                            <i class="ti tabler-x me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="sendEmailBtn">
                            <i class="ti tabler-send me-1"></i> Send Email
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let editorInstance;
        
        $(document).ready(function() {
            // Initialize CKEditor
            CKEDITOR.replace('message', {
                filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                filebrowserUploadMethod: 'form',
                height: 400,
                toolbar: [
                    { name: 'document', items: ['Source'] },
                    { name: 'clipboard', items: ['Undo', 'Redo'] },
                    { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                    { name: 'colors', items: ['TextColor', 'BGColor'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                    { name: 'links', items: ['Link', 'Unlink'] },
                    { name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
                    { name: 'tools', items: ['Maximize'] }
                ],
                allowedContent: true,
                protectedSource: [/<span[^>]*contenteditable="false"[^>]*>.*?<\/span>/gi]
            });

            editorInstance = CKEDITOR.instances['message'];

            // Insert placeholder into CKEditor (same as email template)
            $('.insert-placeholder').on('click', function() {
                const placeholder = $(this).data('placeholder');
                if (editorInstance) {
                    const placeholderHTML = '<span style="font-weight: bold; color: #000;" contenteditable="false">' + placeholder + '</span> ';
                    editorInstance.insertHtml(placeholderHTML);
                    editorInstance.focus();
                }
            });

            // Confirm before sending
            $('#sendEmailBtn').on('click', function(e) {
                e.preventDefault();
                
                // Update the textarea with CKEditor content
                if (editorInstance) {
                    $('#message').val(editorInstance.getData());
                }
                
                if (confirm('Are you sure you want to send this email to {{ $registrant->user->email }}?')) {
                    $(this).prop('disabled', true);
                    $(this).html('<i class="ti tabler-loader me-1"></i> Sending...');
                    $(this).closest('form').submit();
                }
            });
        });
    </script>
@endsection
