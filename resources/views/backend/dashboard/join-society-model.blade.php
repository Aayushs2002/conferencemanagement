<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <div class="text-center mb-6">
            <h4 class="mb-2">Join Society</h4>
            <p>Choose Your Society And Member Type</p>
        </div>
        <form class="needs-validation">
            <div class="row
            g-6">
                <div class="mb-6 col-md-6">
                    <label for="society_id" class="form-label">Society <code>*</code></label>
                    <select class="form-select" name="society_id" id="society_id" required>
                        <option value="" hidden>-- Select Society --</option>
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
                        '<option value=""  hidden>-- Select Member Type --</option>');

                    if (response.type === 'success' && response.data.length > 0) {
                        $.each(response.data, function(index, item) {
                            $('#member_type_id').append('<option value="' + item
                                .id + '">' + item.type + '</option>');
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

        $('form.needs-validation').on('submit', function(e) {
            e.preventDefault();
            $('.text-danger').remove();
            $('input, select').removeClass('is-invalid');
            let $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true).text('Submitting...');

            let formData = {
                society_id: $('#society_id').val(),
                member_type_id: $('#member_type_id').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                type: 'POST',
                url: '{{ route('joinSocietySubmit') }}',
                data: formData,
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
        });

    });
</script>
