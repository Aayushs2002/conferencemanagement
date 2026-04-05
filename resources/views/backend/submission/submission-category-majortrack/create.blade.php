@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($submissionCategoryMajortrack) ? 'Edit' : 'Add' }} Submission Theme/Sub-theme
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('submission.category-majortrack.index',[$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($submissionCategoryMajortrack) ? 'Edit' : 'Add' }} Submission Theme/Sub-theme</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($submissionCategoryMajortrack) ? route('submission.category-majortrack.update', [$society, $conference, $submissionCategoryMajortrack->id]) : route('submission.category-majortrack.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($submissionCategoryMajortrack)
                        @method('patch')
                    @endisset 
                    <div class="row">

                        <div class="mb-6 col-md-12">
                            <label class="form-label" for="title">Theme/Sub-theme Title <code>*</code></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                placeholder="Enter Title " name="title"
                                value="{{ !empty(old('title')) ? old('title') : @$submissionCategoryMajortrack->title }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Title.</div>
                            @error('title')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label for="major_areas">Theme/Sub-theme Major Areas <code>*</code></label>
                            <textarea name="major_areas" class="form-control @error('major_areas') is-invalid @enderror" id="majorArea"
                                cols="30" rows="5" required>{{ isset($submissionCategoryMajortrack) ? $submissionCategoryMajortrack->major_areas : old('major_areas') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Major Track.</div>
                            @error('major_areas')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="manager_user_ids">Assignable Submission Managers</label>
                            <input id="manager_user_ids_tagify" class="form-control @error('manager_user_ids') is-invalid @enderror @error('manager_user_ids.*') is-invalid @enderror" />
                            <div id="manager_user_ids_container"></div>
                            <small class="text-muted">Selected users can manage submissions under this theme/sub-theme.</small>
                            @error('manager_user_ids')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            @error('manager_user_ids.*')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($submissionCategoryMajortrack) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @php
        $managerUsers = $assignableUsers
            ->map(function ($assignableUser) {
                return [
                    'value' => $assignableUser->id,
                    'name' => trim($assignableUser->fullName($assignableUser)),
                    'email' => $assignableUser->email,
                    'avatar' => 'https://i.pravatar.cc/80?u=' . urlencode($assignableUser->email),
                ];
            })
            ->values();
    @endphp
    <script>
        (function() {
            const managerUsers = @json($managerUsers);

            const selectedManagerIds = @json(old('manager_user_ids', isset($submissionCategoryMajortrack) ? $submissionCategoryMajortrack->managers->pluck('id')->toArray() : []));
            const managerInput = document.getElementById('manager_user_ids_tagify');
            const container = document.getElementById('manager_user_ids_container');

            function syncHiddenInputs(tagifyInstance) {
                container.innerHTML = '';
                (tagifyInstance.value || []).forEach(function(item) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'manager_user_ids[]';
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

            const preselectedUsers = managerUsers.filter(function(user) {
                return selectedManagerIds.map(Number).includes(Number(user.value));
            });

            if (preselectedUsers.length > 0) {
                tagify.addTags(preselectedUsers);
            }

            syncHiddenInputs(tagify);
            tagify.on('add', function() {
                syncHiddenInputs(tagify);
            });
            tagify.on('remove', function() {
                syncHiddenInputs(tagify);
            });
        })();
    </script>
@endsection
