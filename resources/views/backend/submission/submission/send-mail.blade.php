<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Send Mail</span></h5>
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
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label for="presentation_type" class="mb-2">Presentation Type <code>*</code></label>
                    <select name="presentation_type" id="presentation_types"
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
                </div>
                <div class="col-md-12">
                    <label for="User" class="form-label">Users List<code>*</code></label>
                    <input id="User" name="User" class="form-control" />
                </div>
                <div class="mb-6 col-md-12 mt-3">
                    <label class="form-label" for="subject">Subject <code>*</code></label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject"
                        placeholder="Enter a Subject" name="subject" />

                    @error('subject')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="form-label" for="mail_content">Mail Content <code>*</code></label>
                    <textarea class="form-control ckeditor" id="mail_content" name="mail_content" rows="5" cols="30"></textarea>
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
    $(document).ready(function() {

        CKEDITOR.replace('mail_content', {
            filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
            filebrowserUploadMethod: "form",
            extraPlugins: 'wordcount',
            wordcount: {
                showWordCount: true,
            }
        });

        const getUsersUrl = @json(route('submission.get.users', [$society, $conference]));
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
        <span>${suggestions.length} members</span>
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

        function fetchAndUpdateUsers(userType, presentationType) {
            if (!userType || !presentationType || !User) return;

            User.loading(true);
            fetch(`${getUsersUrl}?user_type=${userType}&presentation_type=${presentationType}`)
                .then(res => res.json())
                .then(data => {
                    User.settings.whitelist = data;
                    User.loading(false);
                    User.removeAllTags();
                    User.dropdown.show.call(User);
                })
                .catch(err => {
                    console.error('User fetch error', err);
                    User.loading(false);
                });
        }

        document.querySelector('#user_type')?.addEventListener('change', function() {
            fetchAndUpdateUsers(this.value, document.querySelector('#presentation_types').value);
        });

        document.querySelector('#presentation_types')?.addEventListener('change', function() {
            fetchAndUpdateUsers(document.querySelector('#user_type').value, this.value);
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
                url: '{{ route('submission.sendMailSubmit', [$society, $conference]) }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#sendMail').attr('disabled', true);
                    $('#sendMail').append(
                        '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                    );
                },
                success: function(response) {
                    $('#sendMail').attr('disabled', false);
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
                    var errors = response.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        $('.' + key).html('');
                        $('.' + key).append(val);
                        $('#' + key).addClass('border-danger');
                        $('#' + key).on('input', function() {
                            $('.' + key).html('');
                            $('#' + key).removeClass('border-danger');
                        });
                    });

                    $('#sendMail').attr('disabled', false);
                    $('#sendMail').text('Update');
                }
            });
        });
    });
</script>
