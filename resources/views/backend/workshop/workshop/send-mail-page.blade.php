@extends('backend.layouts.conference.main')

@section('title')
    Send Mail to Workshop Participants
@endsection

@section('content')
    <div class="card border my-4 container">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Send Mail to Workshop Participants</h5>
            <a href="{{ route('workshop.index', [$society, $conference]) }}" class="btn btn-secondary btn-sm">
                <i class="ti tabler-arrow-left me-1"></i> Back to Workshops
            </a>
        </div>
        <div class="card-body">
            <form id="mailForm">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="workshop_id" class="mb-2">Select Workshop <code>*</code></label>
                        <select name="workshop_id" id="workshop_id"
                            class="form-control @error('workshop_id') is-invalid @enderror">
                            <option value="">-- Select Workshop --</option>
                            @foreach ($workshops as $workshop)
                                <option value="{{ $workshop->id }}">
                                    {{ $workshop->workshop_title }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-danger workshop_id"></p> 
                    </div> 
                    <div class="col-md-6 form-group mb-3">
                        <label for="recipient_type" class="mb-2">Recipient Type <code>*</code></label>
                        <select name="recipient_type" id="recipient_type"
                            class="form-control @error('recipient_type') is-invalid @enderror">
                            <option value="">-- Select Recipient Type --</option>
                            <option value="1">Registrants</option>
                            <option value="2">Trainers</option>
                            <option value="3">Both (Registrants & Trainers)</option>
                        </select>
                        <p class="text-danger recipient_type"></p>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label for="User" class="form-label">Recipients List<code>*</code></label>
                        <input id="User" name="User" class="form-control" />
                        <p class="text-danger User"></p>
                    </div>
                    <div class="mb-6 col-md-12 mt-3">
                        <label class="form-label" for="subject">Subject <code>*</code></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject"
                            placeholder="Enter a Subject" name="subject" />
                        <p class="text-danger subject"></p>
                        @error('subject')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="mail_content">Mail Content <code>*</code></label>
                        <div class="mb-2">
                            <small class="text-muted d-block mb-2"><i class="ti tabler-info-circle me-1"></i>Click buttons below to insert placeholders:</small>
                            <div class="btn-group flex-wrap" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{name}">Name</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{first_name}">First Name</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{middle_name}">Middle Name</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{last_name}">Last Name</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{prefix}">Prefix</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{email}">Email</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{registrant_type}">Type</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{registration_id}">Reg ID</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{workshop_title}">Workshop Title</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{workshop_slogan}">Workshop Slogan</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{workshop_start_date}">Workshop Start</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{workshop_end_date}">Workshop End</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{workshop_start_time}">Start Time</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{workshop_end_time}">End Time</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{venue}">Venue</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{venue_address}">Address</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{conference_name}">Conference</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{conference_theme}">Theme</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{conference_start_date}">Conf Start</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{conference_end_date}">Conf End</button>
                                <button type="button" class="btn btn-sm btn-outline-primary insert-placeholder" data-placeholder="{certificate_link}">Certificate Link</button>
                            </div>
                        </div>
                        <textarea class="form-control ckeditor" id="mail_content" name="mail_content" rows="5" cols="30"></textarea>
                        <p class="text-danger mail_content"></p>
                        @error('mail_content')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-12 text-end">
                        <a href="{{ route('workshop.index', [$society, $conference]) }}" class="btn btn-secondary">
                            <i class="ti tabler-x me-1"></i> Cancel
                        </a>
                        <button type="submit" id="sendMail" class="btn btn-primary">
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
            CKEDITOR.replace('mail_content', {
                filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                filebrowserUploadMethod: 'form',
                extraPlugins: 'wordcount',
                wordcount: {
                    showWordCount: true,
                },
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

            editorInstance = CKEDITOR.instances['mail_content'];

            // Insert placeholder into CKEditor
            $('.insert-placeholder').on('click', function() {
                const placeholder = $(this).data('placeholder');
                if (editorInstance) {
                    const placeholderHTML = '<span style="font-weight: bold; color: #000;" contenteditable="false">' + placeholder + '</span>&nbsp;';
                    editorInstance.insertHtml(placeholderHTML);
                    editorInstance.focus();
                }
            });

            // Initialize other components
            const getUsersUrl = @json(route('workshop.get.users', [$society, $conference]));
            const UserEl = document.querySelector('#User');
            let User;

            function tagTemplate(tagData) {
                return `
        <tag title="${tagData.title || tagData.email}"
          contenteditable='false'
          spellcheck='false'
          tabIndex="-1"
          class="${this.settings.classNames.tag} ${tagData.class || ''}"
          ${this.getAttributes(tagData)}
        >
          <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
          <div>
            <div class='tagify__tag__avatar-wrap'>
              <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
            </div>
            <span class='tagify__tag-text'>${tagData.name}</span>
          </div>
        </tag>
      `;
            }

            function suggestionItemTemplate(tagData) {
                return `
        <div ${this.getAttributes(tagData)}
          class='tagify__dropdown__item align-items-center ${tagData.class || ''}'
          tabindex="0"
          role="option"
        >
          ${
            tagData.avatar
              ? `<div class='tagify__dropdown__item__avatar-wrap'>
                  <img onerror="this.style.visibility='hidden'" src="${tagData.avatar}">
                </div>`
              : ''
          }
          <div class="fw-medium">${tagData.name}</div>
          <span>${tagData.email}</span>
        </div>
      `;
            }

            function dropdownHeaderTemplate(suggestions) {
                return `
        <div class="${this.settings.classNames.dropdownItem} ${this.settings.classNames.dropdownItem}__addAll">
            <strong>${this.value.length ? 'Add remaining' : 'Add All'}</strong>
            <span>${suggestions.length} recipients</span>
        </div>
      `;
            }

            if (UserEl) {
                User = new Tagify(UserEl, {
                    tagTextProp: 'name',
                    enforceWhitelist: true,
                    skipInvalid: true,
                    dropdown: {
                        closeOnSelect: false,
                        enabled: 0,
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

                User.on('dropdown:select', onSelectSuggestion)
                    .on('edit:start', onEditStart);

                function onSelectSuggestion(e) {
                    if (e.detail.elm.classList.contains(`${User.settings.classNames.dropdownItem}__addAll`)) {
                        User.dropdown.selectAll();
                    }
                }

                function onEditStart({
                    detail: {
                        tag,
                        data
                    }
                }) {
                    User.setTagTextNode(tag, `${data.name} <${data.email}>`);
                }
            }

            function fetchAndUpdateUsers(workshopId, recipientType) {
                if (!workshopId || !recipientType || !User) return;

                User.loading(true);
                fetch(`${getUsersUrl}?workshop_id=${workshopId}&recipient_type=${recipientType}`)
                    .then(res => {
                        if (!res.ok) {
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Fetched users:', data);
                        User.settings.whitelist = data;
                        User.loading(false);
                        User.removeAllTags();
                        
                        if (data.length > 0) {
                            User.dropdown.show.call(User);
                            notyf.success(`${data.length} recipient(s) loaded`);
                        } else {
                            notyf.error('No recipients found for the selected criteria');
                        }
                    })
                    .catch(err => {
                        console.error('User fetch error:', err);
                        User.loading(false);
                        notyf.error('Failed to load recipients. Please try again.');
                    });
            }

            document.querySelector('#workshop_id')?.addEventListener('change', function() {
                fetchAndUpdateUsers(this.value, document.querySelector('#recipient_type').value);
            });

            document.querySelector('#recipient_type')?.addEventListener('change', function() {
                fetchAndUpdateUsers(document.querySelector('#workshop_id').value, this.value);
            });

            $("#sendMail").on('click', function(e) {
                e.preventDefault();
                var data = new FormData($('#mailForm')[0]);
                data.append('mail_content', CKEDITOR.instances['mail_content'].getData());
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: '{{ route('workshop.sendMailSubmit', [$society, $conference]) }}',
                    data: data,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#sendMail').attr('disabled', true);
                        $('#sendMail').html('<i class="ti tabler-loader me-1"></i> Sending...');
                    },
                    success: function(response) {
                        $('#sendMail').attr('disabled', false);
                        $('#sendMail').html('<i class="ti tabler-send me-1"></i> Send Email');
                        if (response.type == 'success') {
                            notyf.success(response.message);
                            setTimeout(function() {
                                window.location.href = '{{ route('workshop.index', [$society, $conference]) }}';
                            }, 1000);
                        } else if (response.type == 'error') {
                            notyf.error(response.message);
                        }
                    },
                    error: function(response) {
                        var errors = response.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            $('.' + key).html('');
                            $('.' + key).append(val);
                            $('#' + key).addClass('border-danger');
                            $('#' + key).on('input change', function() {
                                $('.' + key).html('');
                                $('#' + key).removeClass('border-danger');
                            });
                        });

                        $('#sendMail').attr('disabled', false);
                        $('#sendMail').html('<i class="ti tabler-send me-1"></i> Send Email');
                    }
                });
            });
        });
    </script>
@endsection
