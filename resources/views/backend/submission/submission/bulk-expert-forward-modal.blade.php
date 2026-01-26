<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Bulk Assign Submissions to Expert</h5>
        <hr class="py-2">
        
        <div class="alert alert-info">
            <i class="ti tabler-info-circle me-2"></i>
            You are about to assign <strong>{{ count($submissionIds) }} submission(s)</strong> to an expert for review.
        </div>

        <form id="bulkDataForm">
            @csrf
            <input type="hidden" name="ids" id="bulkSubmissionIds" value="{{ json_encode($submissionIds) }}">
            
            <div class="row">
                <div class="col-md-12 form-group mb-3">
                    <label for="expert_id">Select Expert <code>*</code></label>
                    <select name="expert_id" id="bulk_expert_id" class="form-control @error('expert_id') is-invalid @enderror">
                        <option value="" hidden>-- Select Expert --</option>
                        @foreach ($experts as $expert)
                            <option value="{{ $expert->user_id }}">
                                {{ $expert->expert->fullName($expert->expert) }} 
                                ({{ $expert->expert->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-danger expert_id"></p>
                </div>

                <div class="col-md-12 form-group mb-3">
                    <label class="form-label">
                        <i class="ti tabler-key me-1"></i> Password Options <code>*</code>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="password_option" id="generate_new_password" value="generate" checked>
                        <label class="form-check-label" for="generate_new_password">
                            Generate and send new password (Recommended)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="password_option" id="keep_existing_password" value="keep">
                        <label class="form-check-label" for="keep_existing_password">
                            Keep existing password (Don't change)
                        </label>
                    </div>
                    <p class="text-danger password_option"></p>
                </div>

                <div class="col-md-12 form-group mb-3">
                    <label class="form-label">
                        <i class="ti tabler-list me-1"></i> Submissions to be Assigned:
                    </label>
                    <div class="card">
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            <ol class="ps-3">
                                @foreach ($submissions as $submission)
                                    <li class="mb-2">
                                        <strong>{{ $submission->title }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            Speaker: {{ $submission->presenter?->fullName($submission->presenter) }} | 
                                            Type: {{ $submission->presentation_type == 1 ? 'Poster' : 'Oral' }}
                                        </small>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 form-group mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirm_assignment" required>
                        <label class="form-check-label" for="confirm_assignment">
                            I confirm that I want to assign these submissions to the selected expert
                        </label>
                    </div>
                </div>

                <div class="col-md-12 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="bulkForwardRequest" class="btn btn-primary">
                        <i class="ti tabler-send me-1"></i> Assign to Expert
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#bulkForwardRequest").on('click', function(e) {
            e.preventDefault();
            
            if (!$('#confirm_assignment').is(':checked')) {
                notyf.error('Please confirm the assignment by checking the checkbox');
                return;
            }
            
            var data = new FormData($('#bulkDataForm')[0]);
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                type: "POST",
                url: '{{ route('submission.bulkExpertForward', [$society, $conference]) }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#bulkForwardRequest').attr('disabled', true);
                    $('#bulkForwardRequest').html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing...'
                    );
                },
                success: function(response) {
                    $('#bulkForwardRequest').attr('disabled', false);
                    $('#bulkForwardRequest').html('<i class="ti tabler-send me-1"></i> Assign to Expert');
                    
                    if (response.type == 'success') {
                        $("#pricingModal").modal("hide");
                        notyf.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else if (response.type == 'error') {
                        notyf.error(response.message);
                    }
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function(key, val) {
                        $('.' + key).html('');
                        $('.' + key).append(val);
                        $('#bulk_' + key).addClass('border-danger');
                        $('#bulk_' + key).on('change', function() {
                            $('.' + key).html('');
                            $('#bulk_' + key).removeClass('border-danger');
                        });
                    });
                    $('#bulkForwardRequest').attr('disabled', false);
                    $('#bulkForwardRequest').html('<i class="ti tabler-send me-1"></i> Assign to Expert');
                    notyf.error('Please fix the validation errors');
                }
            });
        });
    });
</script>
