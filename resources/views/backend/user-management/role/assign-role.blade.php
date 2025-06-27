<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Assign Role To <span class="text-danger">(User:
                {{ $user->fullName($user) }})</span></h5>
        <hr class="py-4">

        <form id="assignRoleForm">
            @csrf
            <div class="row">
                <input type="hidden" id="userId" name="id" value="{{ $user->id }}">
                <div class="col-md-12 form-group mb-3" id="decisionDiv">
                    <label for="role">Role <code>*</code></label>
                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                        <option value="" hidden>-- Select Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-danger role"></p>
                </div>

                <div class="col-md-12 text-end">
                    <button type="submit" id="assignRole" class="btn btn-primary">Assign Role</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#assignRole").on('click', function(e) {
            e.preventDefault();
            var data = new FormData($('#assignRoleForm')[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: '{{ route('assignRoleFormSubmit', [$society, $conference]) }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#assignRole').attr('disabled', true);
                    $('#assignRole').append(
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

                    $('#assignRole').attr('disabled', false);
                    $('#assignRole').text('Update');
                }
            });
        });
    });
</script>
