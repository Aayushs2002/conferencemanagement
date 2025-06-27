<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class=" mb-4 " style="background: white;">Verify Registrant <span class="text-danger">(Registrant
                Name:
                {{ $registration->user->fullName($registration->user) }})</span></h4>
        <form id="verifyForm">
            @csrf
            <div class="row">
                <input type="hidden" id="registrationId" name="id" value="{{ $registration->id }}">
                <div class="col-md-12 form-group mb-3">
                    <label for="additional_guests" class="mb-2">Number Of Guests <code>(Excluding
                            Registrant)</code></label>
                    <select name="additional_guests" id="additional_guests"
                        class="form-control @error('additional_guests') is-invalid @enderror">
                        <option value="">-- Select Number Of Guests --</option>
                        <option value="1" @selected(old('additional_guests') == 1)>1</option>
                        <option value="2" @selected(old('additional_guests') == 2)>2</option>
                        <option value="3" @selected(old('additional_guests') == 3)>3</option>
                        <option value="4" @selected(old('additional_guests') == 4)>4</option>
                        <option value="5" @selected(old('additional_guests') == 5)>5</option>
                    </select>
                    @error('additional_guests')
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
    $("#additional_guests").change(function(e) {
        $("#accompanyPersonsDetail").empty();
        var totalAccompanyPersons = $(this).val();
        if (totalAccompanyPersons >= 1) {
            var title =
                '<div class="col-md-12 mt-3"><h3 class="text-danger">Accompanying Person Details:</h3><h5 class="text-danger">Note: All names are required</h5></div>';
            $("#accompanyPersonsDetail").append(title);
            for (let index = 0; index < totalAccompanyPersons; index++) {
                var oldValue = personsValue[index] || '';
                var errorMessage = errorMessages['person_name.' + index] ? errorMessages[
                    'person_name.' + index][0] : '';;
                var htmlCode = '<div class="col-md-12 form-group mb-3">' +
                    '<label for="person_name">Name <code>*</code></label>' +
                    '<input type="text" class="form-control" name="person_name[]" value="' +
                    oldValue + '" placeholder="Enter accompany person name" required/>' +
                    '<p class="text-danger">' + errorMessage + '</p>' +
                    '</div>';

                $("#accompanyPersonsDetail").append(htmlCode);
            }
        }
    });
    $("#additional_guests").trigger("change");

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
            url: '{{ route('conference.conference-registration.addPersonSubmit', [$society, $conference]) }}',
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
