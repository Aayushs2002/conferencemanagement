<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title">Merge Institution: <span class="text-primary">{{ $institution->name }}</span></h5>
        <p class="text-muted mb-0">All users from the second institution will be transferred to this institution</p>
        <hr class="py-4">

        <form id="mergeInstitutionForm">
            @csrf
            <div class="row">
                <input type="hidden" name="institution_id" value="{{ $institution->id }}">
                
                <div class="col-md-12 form-group mb-3">
                    <label for="second_institution_id">Select Institution to Merge <code>*</code></label>
                    <select name="second_institution_id" id="second_institution_id" 
                        class="form-control @error('second_institution_id') is-invalid @enderror">
                        <option value="" hidden>-- Select Institution --</option>
                        @foreach ($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-danger second_institution_id"></p>
                </div>

                <div class="col-md-12">
                    <div class="alert alert-warning" role="alert">
                        <i class="icon-base ti tabler-alert-triangle me-2"></i>
                        <strong>Warning:</strong> This action will:
                        <ul class="mb-0 mt-2">
                            <li>Transfer all users from the second institution to <strong>{{ $institution->name }}</strong></li>
                            <li>Delete the second institution permanently</li>
                            <li>This action cannot be undone</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="mergeInstitution" class="btn btn-primary">Merge Institution</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#mergeInstitution").on('click', function(e) {
            e.preventDefault();
            
            // Confirm before merging
            Swal.fire({
                title: 'Are you sure?',
                text: "This will merge the selected institution and cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, merge it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var data = new FormData($('#mergeInstitutionForm')[0]);
                    
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        type: "POST",
                        url: '{{ route('institution.mergeSubmit') }}',
                        data: data,
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#mergeInstitution').attr('disabled', true);
                            $('#mergeInstitution').html(
                                '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Merging...'
                            );
                        },
                        success: function(response) {
                            $("#pricingModal").modal("hide");
                            notyf.success(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        },
                        error: function(response) {
                            if (response.status === 422) {
                                var errors = response.responseJSON.errors;
                                $.each(errors, function(key, val) {
                                    $('.' + key).html('');
                                    $('.' + key).append(val[0]);
                                    $('#' + key).addClass('border-danger');
                                    $('#' + key).on('change', function() {
                                        $('.' + key).html('');
                                        $('#' + key).removeClass('border-danger');
                                    });
                                });
                            } else {
                                notyf.error(response.responseJSON.message || 'An error occurred');
                            }
                            
                            $('#mergeInstitution').attr('disabled', false);
                            $('#mergeInstitution').text('Merge Institution');
                        }
                    });
                }
            });
        });
    });
</script>
