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
 
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3"><i class="ti tabler-filter me-2"></i>Filter Recipients</h6>
                        <p class="text-muted small mb-0">Filter the recipient list, then load users and add all or only the ones you want to email.</p>
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
                        <label for="attendance_status" class="form-label">Attendance Status</label>
                        <select name="attendance_status" id="attendance_status" class="form-control">
                            <option value="">-- All --</option>
                            <option value="1" {{ old('attendance_status') == 1 ? 'selected' : '' }}>Attended</option>
                            <option value="0" {{ old('attendance_status') === '0' ? 'selected' : '' }}>Not Attended</option>
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
                    <div class="col-12 mt-1">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <label for="User" class="form-label mb-0">Recipients List</label>
                            <div class="btn-group" role="group" aria-label="Recipient actions">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="loadRecipients">Load Users</button>
                                <button type="button" class="btn btn-sm btn-outline-success" id="addAllRecipients">Add All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearRecipients">Clear</button>
                            </div>
                        </div>
                        <input id="User" name="User" class="form-control" />
                        <small id="recipientSummary" class="text-muted d-block mt-1">Loaded: 0 | Selected: 0</small>
                        <p class="text-danger User"></p>
                        <!-- Hidden field to store selected user IDs -->
                        <input type="hidden" id="selectedUserIds" name="selectedUserIds" />
                    </div>
                </div>

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
        let recipientTagify;
        const bulkRecipientsUrl = @json(route('conference.conference-registration.getBulkEmailUsers', [$society, $conference]));
        
        $(document).ready(function() {
            function notifyError(message) {
                if (typeof notyf !== 'undefined') {
                    notyf.error(message);
                } else {
                    alert(message);
                }
            }

            function notifySuccess(message) {
                if (typeof notyf !== 'undefined') {
                    notyf.success(message);
                }
            }

            function updateRecipientSummary() {
                const loadedCount = recipientTagify ? (recipientTagify.settings.whitelist || []).length : 0;
                const selectedCount = recipientTagify ? (recipientTagify.value || []).length : 0;
                $('#recipientSummary').text('Loaded: ' + loadedCount + ' | Selected: ' + selectedCount);
            }

            function updateSelectedUserIds() {
                // Extract selected user IDs from Tagify and store in hidden field
                const selectedUsers = recipientTagify.value || [];
                const userIds = selectedUsers.map(u => u.id).join(',');
                $('#selectedUserIds').val(userIds);
            }

            function initRecipientTagify() {
                if (typeof Tagify === 'undefined') {
                    notifyError('Recipient selector could not be initialized. Please refresh and try again.');
                    return;
                }

                const recipientInput = document.getElementById('User');
                if (!recipientInput) {
                    return;
                }

                if (recipientInput._tagify) {
                    recipientInput._tagify.destroy();
                }

                function tagTemplate(tagData) {
                    return `
<tag title="${tagData.title || tagData.email}" contenteditable='false' spellcheck='false' tabIndex="-1" class="${this.settings.classNames.tag} ${tagData.class || ''}" ${this.getAttributes(tagData)}>
  <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
  <div>
    <div class='tagify__tag__avatar-wrap'>
      <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
    </div>
    <span class='tagify__tag-text'>${tagData.name}</span>
  </div>
</tag>`;
                }

                function suggestionItemTemplate(tagData) {
                    return `
<div ${this.getAttributes(tagData)} class='tagify__dropdown__item align-items-center ${tagData.class || ''}' tabindex="0" role="option">
  ${tagData.avatar ? `<div class='tagify__dropdown__item__avatar-wrap'><img onerror="this.style.visibility='hidden'" src="${tagData.avatar}"></div>` : ''}
  <div class="fw-medium">${tagData.name || 'Unknown'}</div>
  <span>${tagData.email || ''}</span>
</div>`;
                }

                function dropdownHeaderTemplate(suggestions) {
                    return `
<div class="${this.settings.classNames.dropdownItem} ${this.settings.classNames.dropdownItem}__addAll">
  <strong>${this.value.length ? 'Add remaining' : 'Add All'}</strong>
  <span>${suggestions.length} members</span>
</div>`;
                }

                recipientTagify = new Tagify(recipientInput, {
                    tagTextProp: 'name',
                    enforceWhitelist: true,
                    skipInvalid: true,
                    dropdown: {
                        closeOnSelect: false,
                        enabled: 1,
                        maxItems: 10000,
                        classname: 'users-list',
                        searchKeys: ['name', 'email']
                    },
                    templates: {
                        tag: tagTemplate,
                        dropdownItem: suggestionItemTemplate,
                        dropdownHeader: dropdownHeaderTemplate
                    },
                    whitelist: []
                });

                recipientTagify.on('dropdown:select', function(e) {
                    if (e.detail && e.detail.elm && e.detail.elm.classList.contains(recipientTagify.settings.classNames.dropdownItem + '__addAll')) {
                        recipientTagify.dropdown.selectAll();
                        updateRecipientSummary();
                    }
                });

                recipientTagify.on('add', function(e) {
                    updateRecipientSummary();
                    updateSelectedUserIds();
                });
                
                recipientTagify.on('remove', function(e) {
                    updateRecipientSummary();
                    updateSelectedUserIds();
                });
                
                // Add click handler to show dropdown with suggestions
                recipientTagify.DOM.input.addEventListener('click', function() {
                    if (recipientTagify.settings.whitelist && recipientTagify.settings.whitelist.length > 0) {
                        recipientTagify.DOM.input.value = '';
                        const event = new Event('input', { bubbles: true });
                        recipientTagify.DOM.input.dispatchEvent(event);
                    }
                });
                
                updateRecipientSummary();
            }

            function fetchAndUpdateRecipients(showMessage) {
                if (!recipientTagify) {
                    notifyError('Recipient selector is not ready');
                    return;
                }

                const filters = {
                    registrant_type: $('#registrant_type').val(),
                    is_invited: $('#is_invited').val(),
                    verified_status: $('#verified_status').val(),
                    payment_type: $('#payment_type').val(),
                    prefix: $('#prefix').val(),
                    country_id: $('#country_id').val(),
                    attendance_status: $('#attendance_status').val(),
                    from: $('#from').val(),
                    to: $('#to').val(),
                    _t: Date.now()
                };

                recipientTagify.loading(true);
                $.ajax({
                    url: bulkRecipientsUrl,
                    type: 'GET',
                    data: filters,
                    dataType: 'json',
                    success: function(data) {
                        try {
                            const users = Array.isArray(data) ? data : [];
                            
                            if (users.length === 0) {
                                recipientTagify.loading(false);
                                updateRecipientSummary();
                                if (showMessage) {
                                    notifyError('No users found for selected filters');
                                }
                                return;
                            }
                            
                            // Validate and clean user data - ensure all strings are properly typed
                            const validUsers = users.filter(u => {
                                return u && 
                                       (u.id || u.id === 0) && 
                                       String(u.name).trim() && 
                                       String(u.email).trim();
                            }).map(u => ({
                                id: u.id,
                                name: String(u.name).trim(),
                                email: String(u.email).trim(),
                                avatar: u.avatar || '',
                                title: u.title || '',
                                class: u.class || ''
                            }));
                            
                            if (validUsers.length === 0) {
                                recipientTagify.loading(false);
                                updateRecipientSummary();
                                if (showMessage) {
                                    notifyError('No valid users found');
                                }
                                return;
                            }
                            
                            // Update whitelist with validated data
                            recipientTagify.whitelist = validUsers;
                            recipientTagify.settings.whitelist = validUsers;
                            recipientTagify.loading(false);
                            recipientTagify.removeAllTags();
                            
                            updateRecipientSummary();
                            
                            // Focus on input 
                            recipientTagify.DOM.input.focus();
                            recipientTagify.DOM.input.value = '';
                            
                            // Manually trigger the dropdown by simulating user input
                            setTimeout(() => {
                                // Type a character to trigger suggestions
                                recipientTagify.DOM.input.value = 'a';
                                const inputEvent = new Event('input', { bubbles: true });
                                recipientTagify.DOM.input.dispatchEvent(inputEvent);
                                
                                // After a brief delay, clear and show all
                                setTimeout(() => {
                                    recipientTagify.DOM.input.value = '';
                                    const clearEvent = new Event('input', { bubbles: true });
                                    recipientTagify.DOM.input.dispatchEvent(clearEvent);
                                }, 150);
                            }, 100);

                            if (showMessage) {
                                notifySuccess(validUsers.length + ' user(s) loaded. Start typing or click in the field to view suggestions.');
                            }
                        } catch (err) {
                            recipientTagify.loading(false);
                            console.error('Error processing users:', err);
                            notifyError('Error loading users. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        recipientTagify.loading(false);
                        console.error('Recipient fetch error', xhr);
                        notifyError('Failed to load users. Please try again.');
                    }
                });
            }

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

            initRecipientTagify();

            $(document)
                .off('change.bulkEmail', '#registrant_type, #is_invited, #verified_status, #payment_type, #prefix, #country_id, #attendance_status, #from, #to')
                .on('change.bulkEmail', '#registrant_type, #is_invited, #verified_status, #payment_type, #prefix, #country_id, #attendance_status, #from, #to', function() {
                    fetchAndUpdateRecipients(false);
                });

            $(document)
                .off('click.bulkEmail', '#loadRecipients')
                .on('click.bulkEmail', '#loadRecipients', function(e) {
                    e.preventDefault();
                    fetchAndUpdateRecipients(true);
                });

            $(document)
                .off('click.bulkEmail', '#addAllRecipients')
                .on('click.bulkEmail', '#addAllRecipients', function(e) {
                    e.preventDefault();
                    if (!recipientTagify || !recipientTagify.settings.whitelist || recipientTagify.settings.whitelist.length === 0) {
                        notifyError('Load users first');
                        return;
                    }
                    recipientTagify.removeAllTags();
                    recipientTagify.addTags(recipientTagify.settings.whitelist);
                    updateRecipientSummary();
                    notifySuccess('All filtered users added');
                });

            $(document)
                .off('click.bulkEmail', '#clearRecipients')
                .on('click.bulkEmail', '#clearRecipients', function(e) {
                    e.preventDefault();
                    if (!recipientTagify) {
                        return;
                    }
                    recipientTagify.removeAllTags();
                    updateRecipientSummary();
                });

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
                
                // Check if users are selected
                const selectedUsers = recipientTagify.value || [];
                if (selectedUsers.length === 0) {
                    notifyError('Please select at least one recipient');
                    return;
                }
                
                // Update the hidden field with selected user IDs
                updateSelectedUserIds();
                
                // Update the textarea with CKEditor content
                if (editorInstance) {
                    $('#message').val(editorInstance.getData());
                }
                
                if (confirm('Are you sure you want to send this email to ' + selectedUsers.length + ' selected registrant(s)? This action cannot be undone.')) {
                    $(this).prop('disabled', true);
                    $(this).html('<i class="ti tabler-loader me-1"></i> Sending...');
                    $(this).closest('form').submit();
                }
            });
        });
    </script>
@endsection
