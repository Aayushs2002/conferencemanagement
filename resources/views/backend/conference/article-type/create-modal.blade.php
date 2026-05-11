<div class="modal-header">
    <h5 class="modal-title" id="pricingModalLabel">{{ isset($articleType) ? 'Edit' : 'Add' }} Presentation Category</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="needs-validation" id="articleTypeForm" novalidate>
        @csrf

        @isset($articleType)
            @method('patch')
            <input type="hidden" name="id" value="{{ $articleType->id }}">
        @endisset
 
        <div class="row">
            <div class="mb-6 col-md-12">
                <label class="form-label" for="name">Presentation Category Name<code>*</code></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    placeholder="Enter Presentation Category Name" name="name"
                    value="{{ old('name') ?? @$articleType?->name }}" required />
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please enter Presentation Category Name.</div>
                @error('name')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 form-group mb-3">
                <label for="manager_user_ids_at">Assignable Submission Managers</label>
                <input id="manager_user_ids_at_tagify" class="form-control @error('manager_user_ids') is-invalid @enderror" />
                <div id="manager_user_ids_at_container"></div>
                <small class="text-muted">Selected users can manage submissions under this presentation category.</small>
                @error('manager_user_ids')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                    {{ isset($articleType) ? 'Update' : 'Submit' }}
                </button>
            </div>
        </div>
    </form>
</div>

@php
    $managerUsers = isset($assignableUsers) ? $assignableUsers->map(function ($u) {
        return [
            'value' => $u->id,
            'name'  => trim($u->fullName($u)),
            'email' => $u->email,
            'avatar' => 'https://i.pravatar.cc/80?u=' . urlencode($u->email),
        ];
    })->values() : collect();
@endphp

<script>
    (function () {
        const managerUsers = @json($managerUsers);
        const selectedManagerIds = @json(old('manager_user_ids', isset($articleType) ? $articleType->managers->pluck('id')->toArray() : []));

        const managerInput = document.getElementById('manager_user_ids_at_tagify');
        const container   = document.getElementById('manager_user_ids_at_container');

        function syncHiddenInputs(tagifyInstance) {
            container.innerHTML = '';
            (tagifyInstance.value || []).forEach(function (item) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type  = 'hidden';
                hiddenInput.name  = 'manager_user_ids[]';
                hiddenInput.value = item.value;
                container.appendChild(hiddenInput);
            });
        }

        if (typeof Tagify === 'undefined' || !managerInput) {
            return;
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

        const tagify = new Tagify(managerInput, {
            tagTextProp: 'name',
            enforceWhitelist: true,
            skipInvalid: true,
            dropdown: {
                closeOnSelect: false,
                enabled: 0,
                maxItems: 1000,
                classname: 'users-list',
                searchKeys: ['name', 'email']
            },
            templates: {
                tag: tagTemplate,
                dropdownItem: suggestionItemTemplate,
            },
            whitelist: managerUsers
        });

        const preselectedUsers = managerUsers.filter(function (user) {
            return selectedManagerIds.map(Number).includes(Number(user.value));
        });

        if (preselectedUsers.length > 0) {
            tagify.addTags(preselectedUsers);
        }

        syncHiddenInputs(tagify);
        tagify.on('add',    function () { syncHiddenInputs(tagify); });
        tagify.on('remove', function () { syncHiddenInputs(tagify); });
    })();
</script>

<script>
    $(document).ready(function() {

        // Prevent duplicate event bindings
        $(document).off("submit", "#articleTypeForm");

        $(document).on("submit", "#articleTypeForm", function(e) {
            e.preventDefault();

            // HTML5 validation
            if (!this.checkValidity()) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }

            let formData = new FormData(this);

            // Ensure output is a valid JS string
            let url = {!! json_encode(
                isset($articleType)
                    ? route('articleType.update', [$society, $conference, $articleType->id])
                    : route('articleType.store', [$society, $conference]),
            ) !!};

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function(response) {
                    $('#pricingModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then(() => location.reload());
                },

                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;

                    if (errors) {
                        let errorMessage = Object.values(errors)
                            .flat()
                            .join('\n');

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: errorMessage
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred'
                        });
                    }
                }
            });
        });
    });
</script>
