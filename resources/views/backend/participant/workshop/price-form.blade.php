<div class="modal-header">
    <h5 class="modal-title">Set Registration Prices</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="mb-3">
        <b>Workshop: <span class="text-primary">{{ $workshop->title }}</span></b>
    </div>
    <div class="table-responsive">
        <form action="#" method="POST" id="workshopPriceForm">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Member Type</th>
                        <th>Regular Amount</th>
                        <th>Discount Amount <small class="text-muted">(if registered for conference)</small></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($memberTypes as $memberType)
                        <tr>
                            <td>
                                <label class="mb-0">{{ $loop->iteration }}. {{ $memberType->type }}
                                    <small class="text-danger">(Price in
                                        {{ $memberType->delegate == 1 ? 'Rs.' : '$' }})</small>
                                </label>
                                <input type="hidden" name="workshop_id" value="{{ $workshop->id }}">
                                <input type="hidden" name="member_type_id[]" value="{{ $memberType->id }}">
                                <input type="hidden" name="price_id[]" value="{{ $memberType->price_id }}">
                            </td>
                            <td>
                                <input type="text" name="price[]" value="{{ $memberType->price }}"
                                    placeholder="Enter Price" class="form-control numericValue"  />
                            </td>
                            <td>
                                <input type="text" name="discount_price[]" value="{{ $memberType->discount_price }}"
                                    placeholder="Enter Discount Price" class="form-control numericValue" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-end mt-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitData">
                    {{ empty($memberTypes[0]->price_id) ? 'Submit' : 'Update' }}
                </button>
            </div>
        </form>
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
        var data = new FormData($('#workshopPriceForm')[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route('my-society.conference.my-workshop.allocatePriceSubmit', [$society, $conference]) }}',
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
