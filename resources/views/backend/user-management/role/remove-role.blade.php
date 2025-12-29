<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Remove Role From <span class="text-danger">(User:
                {{ $user->fullName($user) }})</span></h5>
        <hr class="py-4">

        <form id="removeRoleForm">
            @csrf
            <div class="row">
                <input type="hidden" id="userId" name="id" value="{{ $user->id }}">
                <div class="col-md-12 form-group mb-3" id="decisionDiv">
                    <label for="role">Select Role to Remove <code>*</code></label>
                    <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror">
                        <option value="" hidden>-- Select Role --</option>
                        @foreach ($userRoles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-danger role_id"></p>
                </div>

                <div class="col-md-12 text-end">
                    <button type="submit" id="removeRole" class="btn btn-danger">Remove Role</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#removeRole").on('click', function(e) {
            e.preventDefault();
            var data = new FormData($('#removeRoleForm')[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: '{{ route('removeRoleFormSubmit', [$society, $conference]) }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#removeRole').attr('disabled', true);
                    $('#removeRole').append(
                        '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                    );
                },
                success: function(response) {
                    $(".modal").modal("hide");
                    notyf.success(response.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
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

                    $('#removeRole').attr('disabled', false);
                    $('#removeRole').text('Remove Role');
                }
            });
        });
    });
</script>
