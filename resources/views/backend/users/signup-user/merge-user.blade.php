<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div>
        <h5 class="mb-4" style="background: white;">
            Merge User
            <span class="text-danger">(User Name: {{ $user->fullName($user) }})</span>
        </h5>

        <form id="verifyForm">
            @csrf
            <div class="row">
                <input type="hidden" id="userId" name="user_id" value="{{ $user->id }}">

                <div class="col-md-12 form-group mb-3">
                    <label for="second_user_id">User <code>*</code></label>
                    <select name="second_user_id" class="form-control select2" id="second_user_id" required>
                        <option value="" hidden>-- Select User --</option>
                        @foreach ($users->where('id', '!=', $user->id) as $otherUser)
<option value="{{ $otherUser->id }}">
                                {{ $otherUser->fullName($otherUser) }}
                            </option>
@endforeach
                    </select>
                    @error('second_user_id')
<p class="text-danger">{{ $message }}</p>
@enderror
            </div>

            <div class="col-md-12 text-end">
                <button type="submit" id="mergeUser" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
</div>

{{-- JS --}}
<script>
   $('#second_user_id').select2({
        dropdownParent: $('#pricingModal')
    });

    $("#mergeUser").on('click', function(e) {
        e.preventDefault();
        var data = new FormData($('#verifyForm')[0]);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: '{{ route('signup-user.mergeUserSubmit', [$society, $conference]) }}',
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#mergeUser').attr('disabled', true);
                $('#mergeUser').append(
                    '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                );
            },
            success: function(response) {
                $('#mergeUser').attr('disabled', false).text('Submit');
                if (response.type === 'success') {
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
                $('#mergeUser').attr('disabled', false).text('Submit');
            }
        });
    });
</script>

