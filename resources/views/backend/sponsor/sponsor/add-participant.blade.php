<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class="text-center mb-4 " style="background: white;">Add Participants <span class="text-danger">(Sponsor Name:
                {{ $sponsor->name }})</h4>
        <form id="verifyForm">
            @csrf
            <p>Actual Total Participants: {{ $sponsor->total_attendee }}</p>
            <div class="row">
                <input type="hidden" id="registrationId" name="id" value="{{ $sponsor->id }}">
                <div class="col-md-6 form-group mb-3">
                    <label for="additional_value">Total Additional Participant's Number <code>*</code></label>
                    <input type="number" name="additional_value" id="additional_value"
                        class="form-control @error('additional_value') is-invalid @enderror">
                    <p class="text-danger additional_value"></p>
                </div>
                <div class="col-md-12">
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
            url: '{{ route('sponsor.addParticipantSubmit') }}',
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
