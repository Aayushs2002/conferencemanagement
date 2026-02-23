@extends('backend.layouts.conference.main')

@section('title')
    Send Bulk Email
@endsection

@section('content')
    <div class="card border my-4 container">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Send Bulk Email to Registrants</h5>
            <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}" 
               class="btn btn-secondary btn-sm">
                <i class="ti tabler-arrow-left me-1"></i> Back to Registrants
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('conference.conference-registration.sendBulkEmail', [$society, $conference]) }}">
                @csrf

                <!-- Filters --> 
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3"><i class="ti tabler-filter me-2"></i>Filter Recipients (Optional)</h6>
                        <p class="text-muted small">Leave filters empty to send to all registrants</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="registrant_type" class="form-label">Registrant Type</label>
                        <select name="registrant_type" id="registrant_type" class="form-control">
                            <option value="">-- All Types --</option>
                            <option value="1" {{ old('registrant_type') == 1 ? 'selected' : '' }}>Attendee</option>
                            <option value="2" {{ old('registrant_type') == 2 ? 'selected' : '' }}>Speaker</option>
                            <option value="3" {{ old('registrant_type') == 3 ? 'selected' : '' }}>Session Chair</option>
                            <option value="4" {{ old('registrant_type') == 4 ? 'selected' : '' }}>Special Guest</option>
                            <option value="5" {{ old('registrant_type') == 5 ? 'selected' : '' }}>Organizer</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="is_invited" class="form-label">Invited Status</label>
                        <select name="is_invited" id="is_invited" class="form-control">
                            <option value="">-- All --</option>
                            <option value="1" {{ old('is_invited') == 1 ? 'selected' : '' }}>Invited</option>
                            <option value="0" {{ old('is_invited') === '0' ? 'selected' : '' }}>Self-Registered</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="verified_status" class="form-label">Verification Status</label>
                        <select name="verified_status" id="verified_status" class="form-control">
                            <option value="">-- All --</option>
                            <option value="0" {{ old('verified_status') === '0' ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ old('verified_status') == 1 ? 'selected' : '' }}>Verified</option>
                            <option value="2" {{ old('verified_status') == 2 ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select name="payment_type" id="payment_type" class="form-control">
                            <option value="">-- All --</option>
                            <option value="1" {{ old('payment_type') == 1 ? 'selected' : '' }}>Fone Pay</option>
                            <option value="2" {{ old('payment_type') == 2 ? 'selected' : '' }}>Moco</option>
                            <option value="3" {{ old('payment_type') == 3 ? 'selected' : '' }}>Esewa</option>
                            <option value="4" {{ old('payment_type') == 4 ? 'selected' : '' }}>Khalti</option>
                            <option value="5" {{ old('payment_type') == 5 ? 'selected' : '' }}>Card Payment</option>
                            <option value="6" {{ old('payment_type') == 6 ? 'selected' : '' }}>Voucher</option>
                            <option value="7" {{ old('payment_type') == 7 ? 'selected' : '' }}>ConnectIPS</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="prefix" class="form-label">Name Prefix</label>
                        <select name="prefix" id="prefix" class="form-control">
                            <option value="">-- All --</option>
                            @foreach ($name_prefiexs as $name_prefiex)
                                <option value="{{ $name_prefiex->id }}" {{ old('prefix') == $name_prefiex->id ? 'selected' : '' }}>
                                    {{ $name_prefiex->prefix }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3"> 
                        <label for="country_id" class="form-label">Country</label>
                        <select class="form-control select2" name="country_id" id="country_id">
                            <option value="">-- All Countries --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->country_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="from" class="form-label">From Date</label>
                        <input type="date" 
                               value="{{ old('from') }}" 
                               class="form-control" 
                               id="from" 
                               name="from" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="to" class="form-label">To Date</label>
                        <input type="date" 
                               value="{{ old('to') }}" 
                               class="form-control" 
                               id="to" 
                               name="to" />
                    </div>
                </div>

                <!-- Email Content -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3"><i class="ti tabler-mail me-2"></i>Email Content</h6>
                    </div>
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
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{venue_address}">Address</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{certificate_link}">Certificate Link</button>
                            </div>
                        </div>
                        <textarea class="form-control ckeditor @error('message') is-invalid @enderror" 
                                  id="message" 
                                  name="message" 
                                  rows="10"
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

            // Initialize Select2 for country dropdown if available
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    placeholder: '-- All Countries --',
                    allowClear: true
                });
            }

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
                
                if (confirm('Are you sure you want to send this email to the selected registrants? This action cannot be undone.')) {
                    $(this).prop('disabled', true);
                    $(this).html('<i class="ti tabler-loader me-1"></i> Sending...');
                    $(this).closest('form').submit();
                }
            });
        });
    </script>
@endsection
