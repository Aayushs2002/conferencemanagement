<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h5 class=" mb-4 " style="background: white;">Add User</h5>
        <form id="addUser">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-6 form-control-validation">
                        <label for="gender" style="padding-bottom:18px;">Select Gender <code>*</code></label><br />
                        <input type="radio" @if (old('gender') == 1) checked @endif id="male"
                            name="gender" value="1">
                        <label for="male" class="mr-3">Male</label>
                        <input type="radio" @if (old('gender') == 2) checked @endif id="female"
                            name="gender" value="2">
                        <label for="female" class="mr-3">Female</label>
                        <input type="radio" @if (old('gender') == '3') checked @endif id="other"
                            name="gender" value="3">
                        <label for="other">Other</label>
                        @error('gender')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6 form-control-validation">
                        <label for="name_prefix_id">Name Prefix <code>*</code></label>
                        <select class="form-control @error('name_prefix_id') is-invalid @enderror" name="name_prefix_id"
                            id="name_prefix_id">
                            <option value="" hidden>-- Select name Prefix --</option>
                            @foreach ($name_prefiexs as $name_prefix)
                                <option value="{{ $name_prefix->id }}" @selected(old('name_prefix_id') == $name_prefix->id)>
                                    {{ $name_prefix->prefix }}</option>
                            @endforeach
                        </select>
                        @error('name_prefix_id')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6 form-control-validation">
                        <label for="f_name">First Name <code>*</code></label>
                        <input type="text" class="form-control @error('f_name') is-invalid @enderror" id="f_name"
                            name="f_name"
                            value="{{ !empty(old('f_name')) ? old('f_name') : session()->get('f_name') }}" required
                            autocomplete="f_name" autofocus>
                        @error('f_name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6 form-control-validation">
                        <label for="m_name">Middle Name </label>
                        <input type="text"
                            class="form-control form-control-rounded @error('m_name') is-invalid @enderror"
                            id="m_name" name="m_name"
                            value="{{ !empty(old('m_name')) ? old('m_name') : session()->get('m_name') }}"
                            autocomplete="m_name" autofocus>
                        @error('m_name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6 form-control-validation">
                        <label for="l_name">Last Name <code>*</code></label>
                        <input type="text" class="form-control @error('l_name') is-invalid @enderror" id="l_name"
                            name="l_name"
                            value="{{ !empty(old('l_name')) ? old('l_name') : session()->get('l_name') }}" required
                            autocomplete="l_name" autofocus>
                        @error('l_name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="mb-6 form-control-validation">
                        <label for="email">Email <code>*</code></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ !empty(old('email')) ? old('email') : session()->get('email') }}"
                            required autocomplete="email" autofocus>
                        @error('email')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6 form-control-validation">
                        <label for="phone">Phone <code>*</code></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ !empty(old('phone')) ? old('phone') : session()->get('phone') }}"
                            required autocomplete="phone" autofocus>
                        @error('phone')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 form-control-validation">
                        <label for="country_id">Country <code>*</code></label>
                        <select class="form-control @error('country_id') is-invalid @enderror" name="country_id"
                            id="country_id">
                            <option value="" hidden>-- Select Country --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                                    {{ $country->country_name }}</option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 ">
                        <label for="member_type_id" class="form-label">Member Type<code>*</code></label>
                        <select class="form-select" name="member_type_id" id="member_type_id" required>
                            <option value="" hidden>-- Select Member Type --</option>

                        </select>

                        @error('member_type_id')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="text-end">
                    <button class="btn btn-primary d-grid" style="float:right; width:100px;"
                        id="submitForm">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#country_id').on('change', function() {
            var country_id = $(this).val();
            var memberTypeId = '{{ old('member_type_id') }}';
            if (!country_id) return;
            $.ajax({
                type: 'GET',
                url: '{{ route('memberType', [$society, $conference]) }}',
                data: {
                    country_id: country_id
                },
                success: function(response) {
                    $('#member_type_id').empty().append(
                        '<option value=""  hidden>-- Select Member Type --</option>');
                    var optionsHtml;
                    if (response.type === 'success' && response.data.length > 0) {
                        $.each(response.data, function(index, item) {
                            var selected = (item.id ==
                                memberTypeId) ? 'selected' : '';
                            optionsHtml += '<option value="' + item
                                .id + '" ' + selected + '>' + item
                                .type + '</option>';
                            $('#member_type_id').append(optionsHtml);
                        });
                    } else {
                        $('#member_type_id').append(
                            '<option disabled>No Member Types Found</option>');
                    }
                },
                error: function(xhr) {
                    console.log('AJAX Error:', xhr);
                }
            });
        });
        $("#country_id").trigger('change');


        $("#submitForm").on('click', function(e) {
            e.preventDefault();
            var data = new FormData($('#addUser')[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: '{{ route('signup-user.addUserSubmit', [$society, $conference]) }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#submitForm').attr('disabled', true);
                    $('#submitForm').append(
                        '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                    );
                },
                success: function(response) {
                    $('#submitForm').attr('disabled', false);
                    $('#submitForm').text('Submit');
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
                    $('#submitForm').attr('disabled', false).text('Add');

                    if (response.status === 422) { // Laravel validation errors
                        var errors = response.responseJSON.errors;

                        // Remove old errors
                        $('.text-danger').remove();
                        $('input, select').removeClass('border-danger');

                        $.each(errors, function(key, val) {
                            let input = $('[name="' + key + '"]');

                            if (input.attr('type') === 'radio') {
                                let group = input.closest(
                                    '.form-control-validation');

                                group.find('input').addClass('border-danger');
                                if (!group.find('.text-danger').length) {
                                    group.append('<p class="text-danger">' + val[
                                        0] + '</p>');
                                }

                                group.find('input').on('change', function() {
                                    group.find('input').removeClass(
                                        'border-danger');
                                    group.find('.text-danger').remove();
                                });
                            } else {
                                input.addClass('border-danger');

                                if (!input.next('.text-danger').length) {
                                    input.after('<p class="text-danger">' + val[0] +
                                        '</p>');
                                }

                                input.on('input change', function() {
                                    $(this).removeClass('border-danger');
                                    $(this).next('.text-danger').remove();
                                });
                            }
                        });
                    } else {
                        notyf.error('Something went wrong.');
                    }
                }


            });
        });
    });
</script>
