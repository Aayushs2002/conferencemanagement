    {{-- <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <h5 class="modal-title" id="exampleModalCenterTitle">{{ isset($author) ? 'Edit' : 'Add' }} Author for topic:
            {{ $submission->topic }} <small class="text-danger">(NOTE: Total number of Authors Limitation is
                {{ !empty(@$authorLimit->authors_limit) ? @$authorLimit->authors_limit : 'infinity' }})</small></h5>
        <hr class="py-3">
        <div class="rounded-top">
            <form
                action="{{ isset($author) ? route('my-society.conference.submission.author.update', [$society, $conference, $author]) : route('my-society.conference.submission.author.store', [$society, $conference]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @isset($author)
                    @method('patch')
                @endisset
                <div class="row">
                    <input type="hidden" name="submission_id" value="{{ $submission->id }}"> 

                    @if (!in_array(1, $checkMainAuthor) || (isset($author) ? $author->main_author == 1 : ''))
                        <div class="col-md-12 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="main_author" id="main_author"
                                value="1"
                                @isset($author) @if ($author->main_author == 1) checked @endif @endisset />
                            <label for="main_author" class="form-check-label">Is Main Author ? </label>
                        </div>
                    @endif
                    <div class="@if (!in_array(1, $checkMainAuthor) || (isset($author) ? $author->main_author == 1 : '')) col-md-6 @else col-md-6 @endif form-group mb-3">
                        <label for="name">Full Name <code>*</code></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            id="name" value="{{ isset($author) ? $author->name : old('name') }}"
                            placeholder="Enter author name" required />
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="@if (!in_array(1, $checkMainAuthor) || (isset($author) ? $author->main_author == 1 : '')) col-md-6 @else col-md-6 @endif form-group mb-3">
                        <label for="email">Email <code>*</code></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                            id="email" value="{{ isset($author) ? $author->email : old('email') }}"
                            placeholder="Enter author email" required />
                        @error('email')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    @if ($author == null)
                        <div class="col-md-12 form-group my-3">
                            <label for="old_author">Is Designation/Institution/Institution Address same as any of the
                                following Author?</label>
                            <select name="old_author" id="oldAuthor" class="form-control">
                                <option value="0">-- Select Author --</option>
                                @foreach ($authors as $auth)
                                    <option value="{{ $auth->id }}">{{ $auth->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-6 form-group mb-3">
                        <label for="designation">Designation <code>*</code></label>
                        <input type="text" class="form-control @error('designation') is-invalid @enderror"
                            name="designation" id="designation"
                            value="{{ isset($author) ? $author->designation : old('designation') }}"
                            placeholder="Enter author designation" required />
                        @error('designation')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="institution">Institution <code>*</code></label>
                        <input type="text" class="form-control @error('institution') is-invalid @enderror"
                            name="institution" id="institution"
                            value="{{ isset($author) ? $author->institution : old('institution') }}"
                            placeholder="Enter author institution" required />
                        @error('institution')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="institution_address">Institution Address <code>*</code></label>
                        <input type="text" class="form-control @error('institution_address') is-invalid @enderror"
                            name="institution_address" id="institutionAddress"
                            value="{{ isset($author) ? $author->institution_address : old('institution_address') }}"
                            placeholder="Enter author institution address" required />
                        @error('institution_address')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label for="phone">Phone <code id="phoneCondition">(Optional)</code></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                            id="phone" value="{{ isset($author) ? $author->phone : old('phone') }}"
                            placeholder="Enter author phone number" />
                        @error('phone')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($contributionEnabled && count($contributions) > 0)
                        <div class="col-md-12 form-group mb-3">
                            <label>Contribution <small class="text-muted">(Select applicable contributions)</small></label>
                            <div class="row">
                                @php
                                    $authorContributions = isset($author) ? $author->contributions->pluck('id')->toArray() : [];
                                @endphp
                                @foreach ($contributions as $contribution)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                class="form-check-input" 
                                                name="contributions[]" 
                                                id="contribution_{{ $contribution->id }}"
                                                value="{{ $contribution->id }}"
                                                {{ in_array($contribution->id, $authorContributions) ? 'checked' : '' }} />
                                            <label class="form-check-label" for="contribution_{{ $contribution->id }}">
                                                {{ $contribution->name }}
                                                @if ($contribution->description)
                                                    <i class="ti ti-info-circle" 
                                                    data-bs-toggle="tooltip" 
                                                    title="{{ $contribution->description }}"></i>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('contributions')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">{{ isset($author) ? 'Update' : 'Submit' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script>
        // Initialize tooltips
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        $("#main_author").change(function() {
            if ($(this).is(":checked")) {
                $("#phoneCondition").text('*')
                $("#phone").attr('required', true)
            } else {
                $("#phoneCondition").text('(Optional)')
                $("#phone").attr('required', false)
            }
        });

        $("#main_author").trigger("change");

        $("#oldAuthor").change(function(e) {
            e.preventDefault();
            var oldAuthor = $(this).val();
            if (oldAuthor == 0) {
                $("#designation").val('');
                $("#institution").val('');
                $("#institutionAddress").val('');
            } else {
                var url =
                    '{{ route('my-society.conference.submission.author.oldAuthor', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var data = {
                    _token: _token,
                    oldAuthor: oldAuthor
                };
                $.post(url, data, function(response) {
                    $("#designation").val(response.designation);
                    $("#institution").val(response.institution);
                    $("#institutionAddress").val(response.institution_address);
                });
            }
        });
    </script> --}}


    <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <h5 class="modal-title" id="exampleModalCenterTitle">{{ isset($author) ? 'Edit' : 'Add' }} Author for topic:
            {{ $submission->topic }} <small class="text-danger">(NOTE: Total number of Authors Limitation is
                {{ !empty(@$authorLimit->authors_limit) ? @$authorLimit->authors_limit : 'infinity' }})</small></h5>
        <hr class="py-3">

        <!-- Alert container for AJAX messages -->
        <div id="alert-container"></div>

        <div class="rounded-top">
            <form id="authorForm" enctype="multipart/form-data">
                @csrf
                @isset($author)
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="author_id" value="{{ $author->id }}">
                @endisset
                <div class="row">
                    <input type="hidden" name="submission_id" value="{{ $submission->id }}">

                    @if (!in_array(1, $checkMainAuthor) || (isset($author) ? $author->main_author == 1 : ''))
                        <div class="col-md-6 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="main_author" id="main_author"
                                value="1"
                                @isset($author) @if ($author->main_author == 1) checked @endif @endisset />
                            <label for="main_author" class="form-check-label">Is Main Author?</label>
                        </div>
                    @endif
                    
                    @if (!in_array(1, $checkMainPresenter) || (isset($author) ? $author->main_presenter == 1 : ''))
                        <div class="col-md-6 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="main_presenter" id="main_presenter"
                                value="1"
                                @isset($author) @if ($author->main_presenter == 1) checked @endif @endisset />
                            <label for="main_presenter" class="form-check-label">Is Main Presenter?</label>
                        </div>
                    @endif
                    <div class="@if (!in_array(1, $checkMainAuthor) || (isset($author) ? $author->main_author == 1 : '')) col-md-6 @else col-md-6 @endif form-group mb-3">
                        <label for="name">Full Name <code>*</code></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            id="name" value="{{ isset($author) ? $author->name : old('name') }}"
                            placeholder="Enter author name" required />
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="name">Full Name <code>*</code></label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="{{ isset($author) ? $author->name : old('name') }}" placeholder="Enter author name"
                            required />
                        <span class="invalid-feedback" id="name-error"></span>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="email">Email <code>*</code></label>
                        <input type="email" class="form-control" name="email" id="email"
                            value="{{ isset($author) ? $author->email : old('email') }}"
                            placeholder="Enter author email" required />
                        <span class="invalid-feedback" id="email-error"></span>
                    </div>

                    @if ($author == null)
                        <div class="col-md-12 form-group my-3">
                            <label for="old_author">Is Designation/Institution/Institution Address same as any of the
                                following Author?</label>
                            <select name="old_author" id="oldAuthor" class="form-control">
                                <option value="0">-- Select Author --</option>
                                @foreach ($authors as $auth)
                                    <option value="{{ $auth->id }}">{{ $auth->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-6 form-group mb-3">
                        <label for="designation">Designation <code>*</code></label>
                        <input type="text" class="form-control" name="designation" id="designation"
                            value="{{ isset($author) ? $author->designation : old('designation') }}"
                            placeholder="Enter author designation" required />
                        <span class="invalid-feedback" id="designation-error"></span>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="institution">Institution <code>*</code></label>
                        <input type="text" class="form-control" name="institution" id="institution"
                            value="{{ isset($author) ? $author->institution : old('institution') }}"
                            placeholder="Enter author institution" required />
                        <span class="invalid-feedback" id="institution-error"></span>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="institution_address">Institution Address <code>*</code></label>
                        <input type="text" class="form-control" name="institution_address" id="institutionAddress"
                            value="{{ isset($author) ? $author->institution_address : old('institution_address') }}"
                            placeholder="Enter author institution address" required />
                        <span class="invalid-feedback" id="institution_address-error"></span>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="phone">Phone <code id="phoneCondition">(Optional)</code></label>
                        <input type="text" class="form-control" name="phone" id="phone"
                            value="{{ isset($author) ? $author->phone : old('phone') }}"
                            placeholder="Enter author phone number" />
                        <span class="invalid-feedback" id="phone-error"></span>
                    </div>

                    @if ($contributionEnabled && count($contributions) > 0)
                        <div class="col-md-12 form-group mb-3">
                            <label>Contribution <code>*</code> <small class="text-muted">(Select applicable
                                    contributions or specify other)</small></label>
                            <div class="row">
                                @php
                                    $authorContributions = isset($author)
                                        ? $author->contributions->pluck('id')->toArray()
                                        : [];
                                @endphp
                                @foreach ($contributions as $contribution)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input contribution-checkbox"
                                                name="contributions[]" id="contribution_{{ $contribution->id }}"
                                                value="{{ $contribution->id }}"
                                                {{ in_array($contribution->id, $authorContributions) ? 'checked' : '' }} />
                                            <label class="form-check-label"
                                                for="contribution_{{ $contribution->id }}">
                                                {{ $contribution->name }}
                                                @if ($contribution->description)
                                                    <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                        title="{{ $contribution->description }}"></i>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            name="contribution_other_checkbox" id="contribution_other" value="1"
                                            {{ isset($author) && !empty($author->contribution_other) ? 'checked' : '' }} />
                                        <label class="form-check-label" for="contribution_other">
                                            Other (Please Specify)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2" id="otherContributionField"
                                style="{{ isset($author) && !empty($author->contribution_other) ? 'display: block;' : 'display: none;' }}">
                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="contribution_other_text"
                                        id="contribution_other_text" placeholder="Please specify other contribution"
                                        value="{{ isset($author) ? $author->contribution_other : old('contribution_other_text') }}" />
                                    <span class="invalid-feedback" id="contribution_other_text-error"></span>
                                </div>
                            </div>
                            <span class="invalid-feedback d-block" id="contributions-error"></span>
                        </div>
                    @endif

                    <div class="col-md-12 text-end">
                        <button type="button" id="submitBtn" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm d-none" id="submitSpinner"></span>
                            <span id="submitBtnText">{{ isset($author) ? 'Update' : 'Submit' }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Main author checkbox change
            $("#main_author").change(function() {
                if ($(this).is(":checked")) {
                    $("#phoneCondition").text('*')
                    $("#phone").attr('required', true)

                    // Show confirmation if another main author exists
                    @if (in_array(1, $checkMainAuthor) && (!isset($author) || $author->main_author != 1))
                        if (!confirm(
                                'A main author already exists for this submission. Setting this author as the main author will remove the main author status from the current main author. Do you want to continue?'
                                )) {
                            $(this).prop('checked', false);
                            $("#phoneCondition").text('(Optional)')
                            $("#phone").attr('required', false)
                            return false;
                        }
                    @endif
                } else {
                    $("#phoneCondition").text('(Optional)')
                    $("#phone").attr('required', false)
                }
            });

            $("#main_author").trigger("change");

            // Old author selection
            $("#oldAuthor").change(function(e) {
                e.preventDefault();
                var oldAuthor = $(this).val();
                if (oldAuthor == 0) {
                    $("#designation").val('');
                    $("#institution").val('');
                    $("#institutionAddress").val('');
                } else {
                    var url =
                        '{{ route('my-society.conference.submission.author.oldAuthor', [$society, $conference]) }}';
                    var _token = '{{ csrf_token() }}';
                    var data = {
                        _token: _token,
                        oldAuthor: oldAuthor
                    };
                    $.post(url, data, function(response) {
                        $("#designation").val(response.designation);
                        $("#institution").val(response.institution);
                        $("#institutionAddress").val(response.institution_address);
                    });
                }
            });

            // Handle "Other" contribution checkbox
            // Handle "Other" contribution checkbox
            $("#contribution_other").change(function() {
                if ($(this).is(":checked")) {
                    $("#otherContributionField").slideDown();
                    $("#contribution_other_text").attr('required', true);
                    // Remove required from regular contributions when other is selected
                    $(".contribution-checkbox").attr('required', false);
                } else {
                    $("#otherContributionField").slideUp();
                    $("#contribution_other_text").attr('required', false);
                    $("#contribution_other_text").val('');
                }
            });

            // Handle regular contribution checkboxes
            $(".contribution-checkbox").change(function() {
                var anyChecked = $(".contribution-checkbox:checked").length > 0;
                if (anyChecked) {
                    // If any regular contribution is checked, make other optional
                    $("#contribution_other").attr('required', false);
                    $("#contribution_other_text").attr('required', false);
                }
            });

            // Trigger change on page load to set correct state
            $("#contribution_other").trigger("change");

            // Check on page load if other was selected (for edit mode)
            @isset($author)
                @if (!empty($author->contribution_other))
                    $("#contribution_other").prop('checked', true);
                    $("#otherContributionField").show();
                    $("#contribution_other_text").attr('required', true);
                @endif
            @endisset

            // Form submission via AJAX
            $("#submitBtn").click(function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#alert-container').html('');

                // Show loading state
                $('#submitSpinner').removeClass('d-none');
                $('#submitBtn').prop('disabled', true);

                // Prepare form data
                var formData = new FormData($('#authorForm')[0]);

                // Determine URL based on whether it's create or update
                @isset($author)
                    var url =
                        '{{ route('my-society.conference.submission.author.update', [$society, $conference, $author]) }}';
                @else
                    var url =
                        '{{ route('my-society.conference.submission.author.store', [$society, $conference]) }}';
                @endisset

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Hide loading state
                        $('#submitSpinner').addClass('d-none');
                        $('#submitBtn').prop('disabled', false);

                        if (response.success) {
                            // Show success message
                            showAlert('success', response.message);
                            notyf.success(response.message);

                            // Close modal immediately
                            $('#pricingModal{{ $submission->id }}').modal('hide');

                            // Reload page after short delay
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        // Hide loading state
                        $('#submitSpinner').addClass('d-none');
                        $('#submitBtn').prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors;

                            $.each(errors, function(field, messages) {
                                // Handle contributions field specially since it's an array
                                var fieldId = field;
                                if (field === 'institution_address') {
                                    fieldId = 'institutionAddress';
                                }
                                if (field === 'contributions') {
                                    // For contributions array, the error span is already 'contributions-error'
                                    $('#contributions-error').text(messages[0]);
                                    $('#contributions-error').parent().find(
                                        '.form-check-input').first().addClass(
                                        'is-invalid');
                                } else {
                                    // Add error class to field
                                    $('#' + fieldId).addClass('is-invalid');
                                    // Show error message
                                    $('#' + fieldId + '-error').text(messages[0]);
                                }
                            });

                            showAlert('danger', 'Please correct the errors and try again.');
                        } else if (xhr.status === 400) {
                            // Business logic error (like author limit reached)
                            showAlert('danger', xhr.responseJSON.message);
                        } else {
                            // General error
                            showAlert('danger', 'An error occurred. Please try again.');
                        }
                    }
                });
            });

            // Helper function to show alerts
            function showAlert(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                var icon = type === 'success' ? 'check-circle' : 'exclamation-circle';

                var alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="ti ti-${icon}"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

                $('#alert-container').html(alertHtml);

                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 5000);
            }

            // Clear validation errors on input
            $('.form-control, .form-check-input').on('input change', function() {
                $(this).removeClass('is-invalid');
                var fieldId = $(this).attr('id');
                if (fieldId) {
                    $('#' + fieldId + '-error').text('');
                }
            });
        });
    </script>
