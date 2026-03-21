<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <div class="text-center mb-6">
            <h4 class="mb-2">Join Institution</h4>
            <p>Choose Your Institution And Member Type</p>
        </div>
        <form class="needs-validation"> 
            <div class="row
            g-6">
                <div class="mb-6 col-md-6">
                    <label for="society_id" class="form-label">Institution <code>*</code></label>
                    <select class="form-select" name="society_id" id="society_id" required>
                        <option value="" hidden>-- Select Institution --</option>
                        @foreach ($societies as $society)
                            <option value="{{ $society->id }}">
                                {{ $society->users->where('type', 2)->value('f_name') }}</option>
                        @endforeach

                    </select>

                    @error('society_id')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6 col-md-6">
                    <label for="member_type_id" class="form-label">Member Type<code>*</code></label>
                    <select class="form-select" name="member_type_id" id="member_type_id" required>
                        <option value="" hidden>-- Select Member Type --</option>

                    </select>

                    @error('member_type_id')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Student/Resident Verification Documents Section -->
            <div id="studentDocumentsSection" class="row g-6 mt-2" style="display: none;">
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="ti-tablerinfo-circle me-2"></i>
                        <div>
                            <strong>Student/Resident Verification Required</strong><br>
                            <small>Please upload your ID Card and/or Official Letter from HoD and Principal Office to verify your student/resident status.</small>
                        </div>
                    </div>
                </div>

                <div class="mb-6 col-md-6">
                    <label for="id_card_document" class="form-label">
                        <i class="ti-tablerid-badge me-1"></i> ID Card
                        <small class="text-muted">(JPG, PNG, or PDF - Max 5MB)</small>
                    </label>
                    <input type="file" class="form-control" name="id_card_document"
                           id="id_card_document" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="mt-2" id="idCardPreview"></div>
                    @error('id_card_document')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6 col-md-6">
                    <label for="official_letter_document" class="form-label">
                        <i class="ti-tablerfile-certificate me-1"></i> Official Letter from HoD/Principal
                        <small class="text-muted">(JPG, PNG, or PDF - Max 5MB)</small>
                    </label>
                    <input type="file" class="form-control" name="official_letter_document"
                           id="official_letter_document" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="mt-2" id="officialLetterPreview"></div>
                    @error('official_letter_document')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="col-12 text-center mt-4">
                <button type="submit" class="btn btn-primary me-3" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                        id="spinner"></span>
                    <span id="submitText">Submit</span>
                </button>


            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {

        // When society changes, populate member types
        $('#society_id').on('change', function() {
            var society_id = $(this).val();
            if (!society_id) return;

            $.ajax({
                type: 'GET',
                url: '{{ route('getMemberType') }}',
                data: {
                    society_id: society_id
                },
                success: function(response) {
                    $('#member_type_id').empty().append(
                        '<option value="" hidden>-- Select Member Type --</option>');
                    if (response.type === 'success' && response.data.length > 0) {
                        $.each(response.data, function(index, item) {
                            $('#member_type_id').append(
                                '<option value="' + item.id +
                                '" data-is-society-member="' + item.is_society_member +
                                '" data-requires-verification="' + item.requires_student_verification +
                                '">' + item.type + '</option>'
                            );
                        });
                    } else {
                        $('#member_type_id').append(
                            '<option disabled>No Member Types Found</option>');
                    }
                },
                error: function(xhr) {
                    console.log('AJAX Error:', xhr);
                }
            });
        });

        // Show/hide document upload section based on member type
        $('#member_type_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var requiresVerification = selectedOption.data('requires-verification');

            if (requiresVerification == 1) {
                $('#studentDocumentsSection').slideDown();
            } else {
                $('#studentDocumentsSection').slideUp();
                // Clear file inputs
                $('#id_card_document').val('');
                $('#official_letter_document').val('');
                $('#idCardPreview').html('');
                $('#officialLetterPreview').html('');
            }
        });

        // File preview functionality
        $('#id_card_document').on('change', function() {
            handleFilePreview(this, '#idCardPreview');
        });

        $('#official_letter_document').on('change', function() {
            handleFilePreview(this, '#officialLetterPreview');
        });

        function handleFilePreview(input, previewId) {
            var file = input.files[0];
            if (file) {
                var fileName = file.name;
                var fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                var fileExt = fileName.split('.').pop().toLowerCase();

                var icon = 'ti-file';
                if (fileExt === 'pdf') icon = 'ti-file-type-pdf';
                else if (['jpg', 'jpeg', 'png'].includes(fileExt)) icon = 'ti-photo';

                $(previewId).html(
                    '<div class="alert alert-success d-flex align-items-center py-2 px-3">' +
                        '<i class="ti ' + icon + ' me-2"></i>' +
                        '<small><strong>' + fileName + '</strong> (' + fileSize + ' MB)</small>' +
                    '</div>'
                );
            } else {
                $(previewId).html('');
            }
        }

        $('form.needs-validation').on('submit', function(e) {
            e.preventDefault();
            $('.text-danger').remove();
            $('input, select').removeClass('is-invalid');
            let $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true).text('Submitting...');

            let society_id = $('#society_id').val();
            let member_type_id = $('#member_type_id').val();
            let is_society_member = $('#member_type_id option:selected').data('is-society-member');

            // Create FormData to handle file uploads
            let formData = new FormData(this);

            if (is_society_member == 1) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('checkCouncilMembership') }}',
                    data: {
                        member_type_id: member_type_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.isMember) {
                            submitJoinSocietyForm(formData, $submitBtn);
                        } else {
                            $submitBtn.prop('disabled', false).text('Submit');
                            notyf.error(response.error ||
                                "You are not a member of the selected council for this member type."
                                );
                        }
                    },
                    error: function(e) {
                        $submitBtn.prop('disabled', false).text('Submit');
                        notyf.error("Could not verify membership. Please try again.");
                    }
                });
            } else {
                submitJoinSocietyForm(formData, $submitBtn);
            }
        });


        function submitJoinSocietyForm(formData, $submitBtn) {
            $.ajax({
                type: 'POST',
                url: '{{ route('joinSocietySubmit') }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.type === 'success') {
                        notyf.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        $submitBtn.prop('disabled', false).text('Submit');
                        notyf.error(response.message || "Something went wrong.");
                    }
                },
                error: function(xhr) {
                    $submitBtn.prop('disabled', false).text('Submit');
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, messages) {
                            let input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.after('<div class="text-danger">' + messages[0] +
                                '</div>');
                        });
                    } else {
                        notyf.error("An unexpected error occurred.");
                    }
                }
            });
        }

    });
</script>
