<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Send Mail</h5>
        <p class="text-muted mb-0">Filter submission users first, then add one or all recipients in a single click.</p>
        <hr class="py-4">

        <form id="mailForm">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group mb-3">
                    <label for="user_type" class="mb-2">Submission User Type <code>*</code></label>
                    <select name="user_type" id="user_type"
                        class="form-control @error('user_type') is-invalid @enderror">
                        <option value="">-- Submission User Type --</option>
                        <option value="1"> 
                            Presenter
                        </option>
                        <option value="2">
                            Expert
                        </option>
                    </select>
                    <p class="text-danger user_type"></p>
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label for="presentation_type" class="mb-2">Presentation Type <code>*</code></label>
                    <select name="presentation_type" id="presentation_type"
                        class="form-control @error('presentation_type') is-invalid @enderror">
                        <option value="">-- Presentation Type --</option>
                        <option value="2">
                            Oral
                        </option> 
                        <option value="1">
                            Poster
                        </option>
                        <option value="3">
                            Both
                        </option>
                    </select>
                    <p class="text-danger presentation_type"></p>
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label for="request_status" class="mb-2">Request Status</label>
                    <select name="request_status" id="request_status"
                        class="form-control @error('request_status') is-invalid @enderror">
                        <option value="">-- All Request Status --</option>
                        <option value="0">Pending</option>
                        <option value="1">Accepted</option>
                        <option value="2">Correction</option>
                        <option value="3">Rejected</option>
                    </select>
                    <p class="text-danger request_status"></p>
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label for="submission_category_major_track_id" class="mb-2">Theme/Sub-theme</label>
                    <select name="submission_category_major_track_id" id="submission_category_major_track_id"
                        class="form-control @error('submission_category_major_track_id') is-invalid @enderror">
                        <option value="">-- All Theme/Sub-theme --</option>
                        @foreach ($submissionTracks as $submissionTrack)
                            <option value="{{ $submissionTrack->id }}">{{ $submissionTrack->title }}</option>
                        @endforeach
                    </select>
                    <p class="text-danger submission_category_major_track_id"></p>
                </div>
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <label for="User" class="form-label mb-0">Users List<code>*</code></label>
                        <div class="btn-group" role="group" aria-label="Recipient actions">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="loadRecipients">Load Users</button>
                            <button type="button" class="btn btn-sm btn-outline-success" id="addAllRecipients">Add All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearRecipients">Clear</button>
                        </div>
                    </div>
                    <input id="User" name="User" class="form-control" />
                    <small id="recipientSummary" class="text-muted d-block mt-1">Loaded: 0 | Selected: 0</small>
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
                    <textarea class="form-control ckeditor" id="mail_content" name="mail_content" rows="5" cols="30"></textarea>
                    <p class="text-danger mail_content"></p>
                    @error('mail_content')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-md-12 text-end">
                    <button type="submit" id="sendMail" class="btn btn-primary">Send</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    (function() {
        var getUsersUrl = @json(route('submission.get.users', [$society, $conference]));
        var User = null;

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
            var loadedCount = User ? (User.settings.whitelist || []).length : 0;
            var selectedCount = User ? (User.value || []).length : 0;
            $('#mailForm #recipientSummary').text('Loaded: ' + loadedCount + ' | Selected: ' + selectedCount);
        }

        function clearValidationError(fieldId) {
            $('#mailForm .' + fieldId).html('');
            $('#mailForm #' + fieldId).removeClass('border-danger');
        }

        function initEditor() {
            if (typeof CKEDITOR === 'undefined') {
                return;
            }

            if (CKEDITOR.instances['mail_content']) {
                CKEDITOR.instances['mail_content'].destroy(true);
            }

            CKEDITOR.replace('mail_content', {
                filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                filebrowserUploadMethod: 'form',
                extraPlugins: 'wordcount',
                wordcount: { showWordCount: true },
                height: 320
            });
        }

        function initTagify() {
            if (typeof Tagify === 'undefined') {
                notifyError('User selector could not be initialized. Please refresh and try again.');
                return;
            }

            var userInput = document.getElementById('User');
            if (!userInput) {
                return;
            }

            if (userInput._tagify) {
                userInput._tagify.destroy();
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
  <div class="fw-medium">${tagData.name}</div>
  <span>${tagData.email}</span>
</div>`;
            }

            function dropdownHeaderTemplate(suggestions) {
                return `
<div class="${this.settings.classNames.dropdownItem} ${this.settings.classNames.dropdownItem}__addAll">
  <strong>${this.value.length ? 'Add remaining' : 'Add All'}</strong>
  <span>${suggestions.length} members</span>
</div>`;
            }

            User = new Tagify(userInput, {
                tagTextProp: 'name',
                enforceWhitelist: true,
                skipInvalid: true,
                dropdown: {
                    closeOnSelect: false,
                    enabled: 0,
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

            User.on('dropdown:select', function(e) {
                if (e.detail && e.detail.elm && e.detail.elm.classList.contains(User.settings.classNames.dropdownItem + '__addAll')) {
                    User.dropdown.selectAll();
                    updateRecipientSummary();
                }
            });

            User.on('add', updateRecipientSummary);
            User.on('remove', updateRecipientSummary);
            updateRecipientSummary();
        }

        function fetchAndUpdateUsers(showMessage) {
            if (!User) {
                notifyError('User selector is not ready');
                return;
            }

            var $mailForm = $('#mailForm');
            var userType = $mailForm.find('#user_type').val();
            var presentationType = $mailForm.find('#presentation_type').val();
            var requestStatus = $mailForm.find('#request_status').val();
            var trackId = $mailForm.find('#submission_category_major_track_id').val();

            if (!userType || !presentationType) {
                User.settings.whitelist = [];
                User.removeAllTags();
                updateRecipientSummary();
                if (showMessage) {
                    notifyError('Please select Submission User Type and Presentation Type first');
                }
                return;
            }

            var payload = {
                user_type: userType,
                presentation_type: presentationType,
                _t: Date.now()
            };

            if (requestStatus !== '') {
                payload.request_status = requestStatus;
            }
            if (trackId !== '') {
                payload.submission_category_major_track_id = trackId;
            }

            User.loading(true);
            $.ajax({
                url: getUsersUrl,
                type: 'GET',
                data: payload,
                dataType: 'json',
                success: function(data) {
                    var users = Array.isArray(data) ? data : [];
                    User.settings.whitelist = users;
                    User.loading(false);
                    User.removeAllTags();
                    User.dropdown.show.call(User);
                    updateRecipientSummary();

                    if (showMessage) {
                        if (users.length > 0) {
                            notifySuccess(users.length + ' user(s) loaded');
                        } else {
                            notifyError('No users found for selected filters');
                        }
                    }
                },
                error: function(xhr) {
                    User.loading(false);
                    console.error('User fetch error', xhr);
                    notifyError('Failed to load users. Please try again.');
                }
            });
        }

        initEditor();
        initTagify();

        $(document)
            .off('change.submissionMail', '#user_type')
            .on('change.submissionMail', '#user_type', function() {
                fetchAndUpdateUsers(false);
            });

        $(document)
            .off('change.submissionMail', '#presentation_type')
            .on('change.submissionMail', '#presentation_type', function() {
                fetchAndUpdateUsers(false);
            });

        $(document)
            .off('change.submissionMail', '#request_status')
            .on('change.submissionMail', '#request_status', function() {
                fetchAndUpdateUsers(false);
            });

        $(document)
            .off('change.submissionMail', '#submission_category_major_track_id')
            .on('change.submissionMail', '#submission_category_major_track_id', function() {
                fetchAndUpdateUsers(false);
            });

        $(document)
            .off('click.submissionMail', '#loadRecipients')
            .on('click.submissionMail', '#loadRecipients', function(e) {
                e.preventDefault();
                fetchAndUpdateUsers(true);
            });

        $(document)
            .off('click.submissionMail', '#addAllRecipients')
            .on('click.submissionMail', '#addAllRecipients', function(e) {
                e.preventDefault();
                if (!User || !User.settings.whitelist || User.settings.whitelist.length === 0) {
                    notifyError('Load users first');
                    return;
                }
                User.removeAllTags();
                User.addTags(User.settings.whitelist);
                updateRecipientSummary();
                notifySuccess('All filtered users added');
            });

        $(document)
            .off('click.submissionMail', '#clearRecipients')
            .on('click.submissionMail', '#clearRecipients', function(e) {
                e.preventDefault();
                if (!User) return;
                User.removeAllTags();
                updateRecipientSummary();
            });

        $(document)
            .off('click.submissionMail', '#sendMail')
            .on('click.submissionMail', '#sendMail', function(e) {
            e.preventDefault();
            var data = new FormData($('#mailForm')[0]);
            var mailContent = '';
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['mail_content']) {
                mailContent = CKEDITOR.instances['mail_content'].getData();
            } else {
                mailContent = $('#mailForm #mail_content').val();
            }
            data.append('mail_content', mailContent);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: '{{ route('submission.sendMailSubmit', [$society, $conference]) }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#sendMail').attr('disabled', true);
                    $('#sendMail').find('.spinner').remove();
                    $('#sendMail').append(
                        '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                    );
                },
                success: function(response) {
                    $('#sendMail').attr('disabled', false);
                    $('#sendMail').find('.spinner').remove();
                    if (response.type == 'success') {
                        $(".modal").modal("hide");
                        notyf.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else if (response.type == 'error') {
                        notyf.error(response.message);
                    }

                },
                error: function(response) {
                    var errors = (response.responseJSON && response.responseJSON.errors) ? response.responseJSON.errors : {};
                    $.each(errors, function(key, val) {
                        $('.' + key).html('');
                        $('.' + key).append(val);
                        $('#' + key).addClass('border-danger');
                        $('#' + key).on('input change', function() {
                            clearValidationError(key);
                        });
                    });

                    $('#sendMail').attr('disabled', false);
                    $('#sendMail').find('.spinner').remove();
                    $('#sendMail').text('Send');
                }
            });
        });
    })();
</script>
