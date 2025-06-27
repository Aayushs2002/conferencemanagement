<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h5 class=" mb-4 " style="background: white;">Convert Registrant Type <span class="text-danger">(Registrant
                Name:
                {{ $registration->user->fullName($registration->user) }})</span></h5>
        <form id="verifyForm">
            @csrf
            <div class="row">
                <input type="hidden" id="registrationId" name="id" value="{{ $registration->id }}">
                <div class="col-md-12 form-group mb-3">
                    <label for="registrant_type" class="mb-2">Registration Type <code>*</code></label>
                    <select name="registrant_type" id="registrant_type"
                        class="form-control @error('registrant_type') is-invalid @enderror">
                        <option value="">-- Select Registrant Type --</option>
                        <option value="1"
                            {{ old('registrant_type', optional($registration)->registrant_type) == 1 ? 'selected' : '' }}>
                            Attendee
                        </option>
                        <option value="2"
                            {{ old('registrant_type', optional($registration)->registrant_type) == 2 ? 'selected' : '' }}>
                            Speaker
                        </option>
                        <option value="3"
                            {{ old('registrant_type', optional($registration)->registrant_type) == 3 ? 'selected' : '' }}>
                            Session Chair
                        </option>
                        <option value="4"
                            {{ old('registrant_type', optional($registration)->registrant_type) == 4 ? 'selected' : '' }}>
                            Special Guest
                        </option>
                    </select>

                    @error('registrant_type')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-md-12">
                    <div class="row" id="accompanyPersonsDetail">

                    </div>
                </div>
                <div class="col-md-12 text-end">
                    <button type="submit" id="verifyRegistrant" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@if (old('person_name'))
    <script>
        var personsValue = @json(old('person_name', []));
        var errorMessages = @json($errors->get('person_name.*'));
    </script>
@else
    <script>
        var personsValue = @json([]);
        var errorMessages = @json([]);
    </script>
@endif
<script>
    $("#verifyRegistrant").on('click', function(e) {
        e.preventDefault();
        var data = new FormData($('#verifyForm')[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route('conference.conference-registration.convertRegistrantTypesubmit',[$society, $conference]) }}',
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#verifyRegistrant').attr('disabled', true);
                $('#verifyRegistrant').append(
                    '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                );
            },
            success: function(response) {
                $('#verifyRegistrant').attr('disabled', false);
                $('#verifyRegistrant').text('Submit');
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
                $('#verifyRegistrant').attr('disabled', false);
                $('#verifyRegistrant').text('Submit');
            }
        });
    });
</script>
