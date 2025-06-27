@extends('backend.layouts.conference.main')

@section('title')
    Add Registration/Invitations
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header">
                Add Registration/Invitations</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ route('conference.conference-registration.registrationOrInvitationSubmit', [$society, $conference]) }}"
                    id="registrationForm" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-2 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="invited_guest" id="invited_guest"
                                value="1" @if (old('invited_guest') == 1) checked @endif />
                            <label for="invited_guest" class="form-check-label">Is Invited Guest ? </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="name_prefix_id">Name Prefix <code>*</code></label>
                            <select name="name_prefix_id" class="form-control" id="name_prefix_id" required>
                                <option value="" hidden>-- Select Name Prefix --</option>
                                @foreach ($prefixesAll as $prefix)
                                    <option value="{{ $prefix->id }}" @selected(old('name_prefix_id') == $prefix->id)>
                                        {{ $prefix->prefix }}</option>
                                @endforeach
                            </select>
                            @error('name_prefix_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="gender">Select Gender <code>*</code></label><br>
                            <span class="mr-3">
                                <input type="radio" @if (old('gender') == 1) checked @endif id="male"
                                    name="gender" value="1">
                                <label for="male">Male</label>
                            </span>
                            <span class="mr-3">
                                <input type="radio" @if (old('gender') == 2) checked @endif id="female"
                                    name="gender" value="2" style="margin-left: 10px;">
                                <label for="female">Female</label>
                            </span>
                            <span>
                                <input type="radio" @if (old('gender') == 3) checked @endif id="other"
                                    name="gender" value="3" style="margin-left: 10px;">
                            </span>
                            <label for="other">Other</label>
                            @error('gender')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="f_name">First Name <code>*</code></label>
                            <input type="text" class="form-control @error('f_name') is-invalid @enderror" name="f_name"
                                id="f_name" value="{{ old('f_name') }}" placeholder="Enter first name" required />
                            @error('f_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="m_name">Middle Name </label>
                            <input type="text" class="form-control @error('m_name') is-invalid @enderror" name="m_name"
                                id="m_name" value="{{ old('m_name') }}" placeholder="Enter first name" />
                            @error('m_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="l_name">Last Name <code>*</code></label>
                            <input type="text" class="form-control @error('l_name') is-invalid @enderror" name="l_name"
                                id="l_name" value="{{ old('l_name') }}" placeholder="Enter first name" required />
                            @error('l_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="email">Email <code>*</code></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                id="email" value="{{ old('email') }}" placeholder="Enter email" required />
                            @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="phone">Phone <code>*</code></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                                id="phone" value="{{ old('phone') }}" placeholder="Enter phone" required />
                            @error('phone')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="institution_id">Institution Name <code>*</code></label>
                            <select class="form-select" name="institution_id" id="institution_id" required>
                                <option value="" hidden>-- Select Institution Name --</option>
                                @foreach ($institutions as $institution)
                                    <option value="{{ $institution->id }}" @selected(old('institution_id') == $institution->id)>
                                        {{ $institution->name }}</option>
                                @endforeach
                                <option value="other" @selected(old('institution_id') == 'other')>Others</option>
                            </select>
                            @error('institution_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3" id="otherInstitutionWrapper" style="display: none;">
                            <label for="other_institution_name" class="form-label">Other Institution Name</label>
                            <input type="text" class="form-control" name="other_institution_name"
                                id="other_institution_name" placeholder="Enter Institution Name"
                                value="{{ old('other_institution_name') }}">
                            @error('other_institution_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="designation_id" class="form-label">Designation <code>*</code></label>
                            <select class="form-select" name="designation_id" id="designation_id" required>
                                <option value="" hidden>-- Select Designation --</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}" @selected(old('designation_id') == $designation->id)>
                                        {{ $designation->designation }}</option>
                                @endforeach
                            </select>
                            @error('designation_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="department_id" class="form-label">Department <code>*</code></label>
                            <select class="form-select" name="department_id" id="department_id" required>
                                <option value="" hidden>-- Select Department --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                        {{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="address">Institute Address <code>*</code></label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                name="address" id="address" value="{{ old('address') }}"
                                placeholder="Enter institute address" required />
                            @error('address')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="council_number">Council Number</label>
                            <input type="text" class="form-control @error('council_number') is-invalid @enderror"
                                name="council_number" id="council_number" value="{{ old('council_number') }}"
                                placeholder="Enter council number" />
                            @error('council_number')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="country_id">Country <code>*</code></label>
                            <select class="form-control" name="country_id" id="country_id" required>
                                <option value="">-- Select Country --</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                                        {{ $country->country_name }}</option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="member_type_id">Member Type <code>*</code></label>
                            <select name="member_type_id" class="form-control member_type_id" id="member_type_id"
                                required></select>
                            @error('member_type_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="registrant_type">Registrant Type <code>*</code></label>
                            <select name="registrant_type" class="form-control" id="registrant_type">
                                <option value="" hidden>-- Select Registrant Type --</option>
                                <option value="1" @selected(old('registrant_type') == '1')>Attendee</option>
                                <option value="2" @selected(old('registrant_type') == '2')>Speaker/Presenter</option>
                                <option value="2" @selected(old('registrant_type') == '3')>Session Chair</option>
                                <option value="2" @selected(old('registrant_type') == '3')>Special Guest</option>
                            </select>
                            @error('registrant_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3 hideDiv">
                            <label for="payment_voucher">Payment Voucher <code>(Only JPG/PNG/PDF) (Max: 250
                                    KB)</code></label>
                            <input type="file" class="form-control @error('payment_voucher') is-invalid @enderror"
                                name="payment_voucher" id="image2" />
                            @error('payment_voucher')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div class="row" id="imgPreview2"></div>
                        </div>
                        <div class="col-md-4 form-group mb-3 hideDiv">
                            <label for="amount">Amount <code>* (Only Numeric Value)</code></label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror numericValue"
                                name="amount" id="amount" value="{{ old('amount') }}" placeholder="Enter amount"
                                required />
                            @error('amount')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3 hideDiv" id="hideDiv">
                            <label for="transaction_id">Transaction ID/Bill No/Reference Code <code>*</code></label>
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror"
                                name="transaction_id" id="transaction_id" value="{{ old('transaction_id') }}"
                                placeholder="Enter transaction id or bill number" required />
                            @error('transaction_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="meal_type">Meal Preference <code>*</code></label>
                            <select name="meal_type" class="form-control" id="meal_type">
                                <option value="" hidden>-- Select Veg/Non-veg --</option>
                                <option value="1"
                                    @if (isset($conference_registration)) {{ $conference_registration->meal_type == '1' ? 'selected' : '' }} @else @selected(old('meal_type') == '1') @endif>
                                    Veg</option>
                                <option value="2"
                                    @if (isset($conference_registration)) {{ $conference_registration->meal_type == '2' ? 'selected' : '' }} @else @selected(old('meal_type') == '2') @endif>
                                    Non-veg</option>
                            </select>
                            @error('meal_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="additional_guests">Number Of Guests <code>(Excluding Registrant)</code></label>
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
                        <div class="col-md-4 form-group mb-3 certificateRequired" hidden>
                            <label for="certificate_required" class="">Is Certificate Required?
                                <code>*</code></label>
                            <select name="certificate_required" id="certificate_required"
                                class="form-control @error('certificate_required') is-invalid @enderror">
                                <option value="">-- Select Certificate Required --</option>
                                <option value="1">
                                    Yes
                                </option>
                                <option value="0">
                                    No
                                </option>
                            </select>

                            @error('certificate_required')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3 speakerAdditionalSection" hidden>
                            <label for="short_cv">Short CV <code>*</code></label>
                            <textarea class="form-control ckeditor" name="short_cv" id="short_cv" cols="30" rows="5">{{ isset($participant) ? $participant->short_cv : old('short_cv') }}</textarea>
                            @error('short_cv')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <div class="row" id="accompanyPersonsDetail">

                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" id="submitButton" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
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
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $("#invited_guest").change(function(e) {
                e.preventDefault();
                if ($(this).is(":checked")) {
                    $('.hideDiv').attr('hidden', true)
                    $('#transaction_id').attr('required', false)
                    $('#amount').attr('required', false)
                    $('#councilNumberRequired').text('')
                    $('.certificateRequired').attr('hidden', false)
                } else {
                    $('#transaction_id').attr('required', true)
                    $('#amount').attr('required', true)
                    $('.hideDiv').attr('hidden', false)
                    $('#councilNumberRequired').text('*')
                    $('.certificateRequired').attr('hidden', true)
                }
            });
            $("#invited_guest").trigger('change');

            $("#registrant_type").change(function(e) {
                e.preventDefault();
                if ($(this).val() == 2) {
                    $(".speakerAdditionalSection").attr('hidden', false);
                } else {
                    $(".speakerAdditionalSection").attr('hidden', true);
                }
            });

            $("#registrant_type").trigger("change");


            $("#additional_guests").change(function(e) {
                $("#accompanyPersonsDetail").empty();
                var totalAccompanyPersons = $(this).val();
                if (totalAccompanyPersons >= 1) {
                    var title =
                        '<div class="col-md-12 mt-3"><h3 class="text-danger">Accompanying Person Details:</h3><h5 class="text-danger">Note: All names are reuired</h5></div>';
                    $("#accompanyPersonsDetail").append(title);
                    for (let index = 0; index < totalAccompanyPersons; index++) {
                        var oldValue = personsValue[index] || '';
                        var errorMessage = errorMessages['person_name.' + index] ? errorMessages[
                            'person_name.' + index][0] : '';;
                        var htmlCode = '<div class="col-md-7 form-group mb-3">' +
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


            const $institutionSelect = $('#institution_id');
            const $otherInstitutionWrapper = $('#otherInstitutionWrapper');

            function toggleOtherInstitution() {
                if ($institutionSelect.val() === 'other') {
                    $otherInstitutionWrapper.show();
                } else {
                    $otherInstitutionWrapper.hide();
                }
            }

            $institutionSelect.on('change', toggleOtherInstitution);
            toggleOtherInstitution();

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

            $(".numericValue").on("keydown", function(event) {
                // Allow backspace, delete, tab, escape, and enter keys
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode ==
                    27 || event.keyCode == 13 ||
                    // Allow Ctrl+A
                    (event.keyCode == 65 && event.ctrlKey === true) ||
                    // Allow home, end, left, right
                    (event.keyCode >= 35 && event.keyCode <= 39) ||
                    // Allow numbers from the main keyboard (0-9) and the numpad (96-105)
                    (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <=
                        105)) {
                    return;
                } else {
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
