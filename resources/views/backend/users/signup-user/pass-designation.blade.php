<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h5 class=" mb-4 " style="background: white;">Pass Designation <span class="text-danger">(User
                Name:
                {{ $user->fullName($user) }})</span></h5>
        <form id="verifyForm">
            @csrf
            <div class="row">
                <div class="col-md-12 form-group mb-3">
                    <input type="hidden" id="userId" name="user_id" value="{{ $user->id }}">

                    <label for="pass_designation">Pass Designation <code>*</code></label>
                    <input type="text" class="form-control @error('pass_designation') is-invalid @enderror"
                        name="pass_designation" id="pass_designation" value="{{ $passDesignation?->pass_designation }}"
                        placeholder="Enter pass designation" />
                    @error('pass_designation')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror

                    @error('registrant_type')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-md-12 text-end">
                    <button type="submit" id="submitData" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $("#submitData").on('click', function(e) {
        e.preventDefault();
        var data = new FormData($('#verifyForm')[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route('signup-user.passDesginationSubmit', [$society, $conference]) }}',
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#submitData').attr('disabled', true);
                $('#submitData').append(
                    '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                );
            },
            success: function(response) {
                $('#submitData').attr('disabled', false);
                $('#submitData').text('Submit');
                if (response.type == 'success') {
                    $(".modal").modal("hide");
                    notyf.success(response.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
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
                $('#submitData').attr('disabled', false);
                $('#submitData').text('Submit');
            }
        });
    });
</script>
