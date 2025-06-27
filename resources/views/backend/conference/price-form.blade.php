<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <b class="text-center mb-4">Allocate Price To Following Members For <code>(Conference:
                {{ $conference->conference_theme }})</code></b>

    </div>
    <div class="modal-body">
        <div class="table-responsive">
            <div class="table-responsive"> 
                <form action="#" method="POST" enctype="multipart/form-data" id="conferencePriceForm">
                    @csrf
                    <input type="hidden" name="conference_id" value="{{ $conference->id }}">
                    <table class="table table-bordered" id="dynamic_field">
                        <thead>
                            <tr>
                                <th>Member Type</th>
                                <th>Early Bird Amount</th>
                                <th>Regular Amount</th>
                                <th>On-Site Amount</th> 
                                <th>Guest Amount</th>
                            </tr>
                        </thead>
                        @foreach ($memberTypes as $memberType)
                            <tbody>
                                <tr>
                                    <td>
                                        <label>{{ $loop->iteration }} {{ $memberType->type }} <small
                                                class="text-danger">(Price in
                                                {{ $memberType->delegate == 1 ? 'Rs.' : '$' }})</small></label>
                                        <input type="hidden" name="member_type_id[]" value="{{ $memberType->id }}">
                                        <input type="hidden" name="price_id[]" value="{{ $memberType->price_id }}">
                                    </td>
                                    <td>
                                        <input type="number" name="early_bird_amount[]"
                                            value="{{ $memberType->early_bird_amount }}"
                                            placeholder="Enter early bird amount" class="form-control priceList" />
                                    </td>
                                    <td>
                                        <input type="number" name="regular_amount[]"
                                            value="{{ $memberType->regular_amount }}" placeholder="Enter regular amount"
                                            class="form-control priceList" />
                                    </td>
                                    <td>
                                        <input type="number" name="on_site_amount[]"
                                            value="{{ $memberType->on_site_amount }}" placeholder="Enter on-site amount"
                                            class="form-control priceList" />
                                    </td>
                                    <td>
                                        <input type="number" name="guest_amount[]"
                                            value="{{ $memberType->guest_amount }}" placeholder="Enter guest amount"
                                            class="form-control priceList" />
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary"
                            id="submitData">{{ empty($memberTypes[0]->price_id) ? 'Submit' : 'Update' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(".numericValue").on("keydown", function(event) {
        // Allow backspace, delete, tab, escape, and enter keys
        if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event
            .keyCode == 13 ||
            // Allow Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) ||
            // Allow home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39) ||
            // Allow numbers from the main keyboard (0-9) and the numpad (96-105)
            (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105)) {
            return;
        } else {
            event.preventDefault();
        }
    });

    $("#submitData").on('click', function(e) {
        e.preventDefault();
        var data = new FormData($('#conferencePriceForm')[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route('conference.priceSubmit') }}',
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
                $('#submitData').text('Update');
                if (response.type == 'success') {
                    $(".modal").modal("hide");
                    notyf.success(response.message);
                    // setTimeout(function() {
                    //     window.location.reload();
                    // }, 1000);
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
                    $('#' + key).on('change', function() {
                        $('.' + key).html('');
                        $('#' + key).removeClass('border-danger');
                    });
                });
                $('#submitData').attr('disabled', false);
                $('#submitData').text('Update');
            }
        });
    });
</script>
