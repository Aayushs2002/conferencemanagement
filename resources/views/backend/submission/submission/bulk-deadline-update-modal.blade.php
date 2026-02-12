<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Bulk Update Review Deadline</h5>
        <hr class="py-2">
        
        <div class="alert alert-info">
            <i class="ti tabler-info-circle me-2"></i>
            You are about to update the review deadline for <strong>{{ count($submissionIds) }} submission(s)</strong>.
        </div>

        <form id="bulkDeadlineForm">
            @csrf
            <input type="hidden" name="ids" id="bulkSubmissionIds" value="{{ json_encode($submissionIds) }}">
            
            <div class="row">
                <div class="col-md-12 form-group mb-3">
                    <label for="bulk_deadline_review_deadline">
                        <i class="ti tabler-calendar me-1"></i> New Review Deadline <code>*</code>
                    </label>
                    <input type="datetime-local" name="review_deadline" id="bulk_deadline_review_deadline" 
                           class="form-control @error('review_deadline') is-invalid @enderror" 
                           required>
                    <small class="text-muted">Set the new deadline for all selected submissions</small>
                    <p class="text-danger review_deadline"></p>
                </div>

                <div class="col-md-12 form-group mb-3">
                    <label class="form-label">
                        <i class="ti tabler-list me-1"></i> Submissions to be Updated:
                    </label>
                    <div class="card">
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            <ol class="ps-3">
                                @foreach ($submissions as $submission)
                                    <li class="mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <strong>{{ $submission->title }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    Speaker: {{ $submission->presenter?->fullName($submission->presenter) }} | 
                                                    Type: {{ $submission->presentation_type == 1 ? 'Poster' : 'Oral' }}
                                                </small>
                                                @if($submission->review_deadline)
                                                    <br>
                                                    <small class="text-primary">
                                                        <i class="ti tabler-clock me-1"></i>
                                                        Current Deadline: {{ \Carbon\Carbon::parse($submission->review_deadline)->format('M d, Y h:i A') }}
                                                    </small>
                                                @else
                                                    <br>
                                                    <small class="text-warning">
                                                        <i class="ti tabler-alert-circle me-1"></i>
                                                        No deadline set
                                                    </small>
                                                @endif
                                                @if($submission->expert_id)
                                                    <br>
                                                    <small class="text-success">
                                                        <i class="ti tabler-user-check me-1"></i>
                                                        Assigned to: {{ $submission->expert?->fullName($submission->expert) ?? 'Expert' }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 form-group mb-3">
                    <div class="alert alert-warning">
                        <i class="ti tabler-alert-triangle me-2"></i>
                        <strong>Important:</strong> This will update the review deadline for all selected submissions. 
                        If any of these submissions are assigned to experts, the experts will have the new deadline to complete their reviews.
                    </div>
                </div>

                <div class="col-md-12 form-group mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirm_deadline_update" required>
                        <label class="form-check-label" for="confirm_deadline_update">
                            I confirm that I want to update the review deadline for these submissions
                        </label>
                    </div>
                </div>

                <div class="col-md-12 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="bulkDeadlineUpdateRequest" class="btn btn-warning">
                        <i class="ti tabler-calendar-check me-1"></i> Update Deadline
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#bulkDeadlineUpdateRequest").on('click', function(e) {
            e.preventDefault();
            
            if (!$('#confirm_deadline_update').is(':checked')) {
                notyf.error('Please confirm the deadline update by checking the checkbox');
                return;
            }
            
            var data = new FormData($('#bulkDeadlineForm')[0]);
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                type: "POST",
                url: '{{ route('submission.bulkUpdateDeadline', [$society, $conference]) }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#bulkDeadlineUpdateRequest').attr('disabled', true);
                    $('#bulkDeadlineUpdateRequest').html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...'
                    );
                },
                success: function(response) {
                    $('#bulkDeadlineUpdateRequest').attr('disabled', false);
                    $('#bulkDeadlineUpdateRequest').html('<i class="ti tabler-calendar-check me-1"></i> Update Deadline');
                    
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
                    $('#bulkDeadlineUpdateRequest').attr('disabled', false);
                    $('#bulkDeadlineUpdateRequest').html('<i class="ti tabler-calendar-check me-1"></i> Update Deadline');
                    
                    if (errors) {
                        $.each(errors, function(key, value) {
                            $('.' + key).text(value);
                            notyf.error(value[0]);
                        });
                    } else if(response.responseJSON.message) {
                        notyf.error(response.responseJSON.message);
                    } else {
                        notyf.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>
