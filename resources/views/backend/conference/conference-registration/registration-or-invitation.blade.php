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
                                <option value="other" @selected(old('designation_id') == 'other')>Others</option>
                            </select>
                            @error('designation_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3" id="otherDesignationWrapper" style="display: none;">
                            <label for="other_designation" class="form-label">Other Designation <code>*</code></label>
                            <input type="text" class="form-control" name="other_designation"
                                id="other_designation" placeholder="Enter Designation"
                                value="{{ old('other_designation') }}">
                            @error('other_designation')
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
                                <option value="other" @selected(old('department_id') == 'other')>Others</option>
                            </select>
                            @error('department_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3" id="otherDepartmentWrapper" style="display: none;">
                            <label for="other_department" class="form-label">Other Department <code>*</code></label>
                            <input type="text" class="form-control" name="other_department"
                                id="other_department" placeholder="Enter Department"
                                value="{{ old('other_department') }}">
                            @error('other_department')
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

                        <div class="col-md-4 form-group mb-3 hideDiv paymentProofDiv">
                            <label for="payment_voucher">Payment Voucher <code>(Only JPG/PNG/PDF) (Max: 250
                                    KB)</code></label>
                            <input type="file" class="form-control @error('payment_voucher') is-invalid @enderror"
                                name="payment_voucher" id="payment_voucher" />
                            @error('payment_voucher')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div class="row" id="imgPreview2"></div>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="payment_status">Payment Status <code>*</code></label>
                            <select name="payment_status" class="form-control" id="payment_status" required>
                                <option value="" hidden>-- Select Payment Status --</option>
                                <option value="paid" @selected(old('payment_status', old('invited_guest') ? 'unpaid' : 'paid') == 'paid')>Paid</option>
                                <option value="unpaid" @selected(old('payment_status', old('invited_guest') ? 'unpaid' : 'paid') == 'unpaid')>Unpaid</option>
                            </select>
                            @error('payment_status')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="amount" id="amountLabel">Amount <code>* (Only Numeric Value)</code></label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror numericValue"
                                name="amount" id="amount" value="{{ old('amount') }}" placeholder="Enter amount"
                                required />
                            @error('amount')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3 hideDiv paymentTxnDiv" id="hideDiv">
                            <label for="transaction_id">Transaction ID/Bill No/Reference Code <code>*</code></label>
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror"
                                name="transaction_id" id="transaction_id" value="{{ old('transaction_id') }}"
                                placeholder="Enter transaction id or bill number"  />
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
                        <div class="col-md-12 form-group mb-3">
                            <label for="addons_section">Add Ons</label>
                            <div id="addons_container" class="row">
                                <div class="col-md-12 text-muted">
                                    <em>Please select a member type to view available add-ons</em>
                                </div>
                            </div>
                            <input type="hidden" name="selected_addons" id="selected_addons">
                            @error('conference_addon_id')
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
                    $('#payment_status').val('unpaid').trigger('change');
                    $('#councilNumberRequired').text('')
                    $('.certificateRequired').attr('hidden', false)
                } else {
                    if (!$('#payment_status').val()) {
                        $('#payment_status').val('paid');
                    }
                    $('#payment_status').trigger('change');
                    $('#councilNumberRequired').text('*')
                    $('.certificateRequired').attr('hidden', true)
                }
            });
            $("#invited_guest").trigger('change');

            function togglePaymentFieldsByStatus() {
                let paymentStatus = $('#payment_status').val() || 'paid';
                if ($('#invited_guest').is(':checked') && paymentStatus === 'paid') {
                    $('#payment_status').val('unpaid');
                    paymentStatus = 'unpaid';
                }
                const isUnpaid = paymentStatus === 'unpaid';

                if (isUnpaid) {
                    $('.paymentProofDiv, .paymentTxnDiv').attr('hidden', true);
                    $('#transaction_id').attr('required', false);
                    $('#amount').attr('required', true);
                    $('#amountLabel').html('Due/Credit Amount <code>* (Only Numeric Value)</code>');
                    if (!$('#transaction_id').val()) {
                        $('#transaction_id').val('UNPAID-' + Date.now());
                    }
                } else {
                    $('.paymentProofDiv, .paymentTxnDiv').attr('hidden', false);
                    $('#transaction_id').attr('required', !$('#invited_guest').is(':checked'));
                    $('#amount').attr('required', true);
                    $('#amountLabel').html('Amount <code>* (Only Numeric Value)</code>');
                    if ($('#transaction_id').val() && $('#transaction_id').val().startsWith('UNPAID-')) {
                        $('#transaction_id').val('');
                    }
                }
            }

            $('#payment_status').on('change', togglePaymentFieldsByStatus);
            togglePaymentFieldsByStatus();

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

            const $designationSelect = $('#designation_id');
            const $otherDesignationWrapper = $('#otherDesignationWrapper');

            const $departmentSelect = $('#department_id');
            const $otherDepartmentWrapper = $('#otherDepartmentWrapper');

            function toggleOtherInstitution() {
                if ($institutionSelect.val() === 'other') {
                    $otherInstitutionWrapper.show();
                } else {
                    $otherInstitutionWrapper.hide();
                }
            }

            function toggleOtherDesignation() {
                if ($designationSelect.val() === 'other') {
                    $otherDesignationWrapper.show();
                } else {
                    $otherDesignationWrapper.hide();
                }
            }

            function toggleOtherDepartment() {
                if ($departmentSelect.val() === 'other') {
                    $otherDepartmentWrapper.show();
                } else {
                    $otherDepartmentWrapper.hide();
                }
            }

            $institutionSelect.on('change', toggleOtherInstitution);
            toggleOtherInstitution();

            $designationSelect.on('change', toggleOtherDesignation);
            toggleOtherDesignation();

            $departmentSelect.on('change', toggleOtherDepartment);
            toggleOtherDepartment();

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

            // Load addons when member type is selected
            $('#member_type_id').on('change', function() {
                var memberTypeId = $(this).val();
                if (!memberTypeId) {
                    $('#addons_container').html('<div class="col-md-12 text-muted"><em>Please select a member type to view available add-ons</em></div>');
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: '{{ route('conference.conference-registration.getMemberTypeAddons', [$society, $conference]) }}',
                    data: {
                        member_type_id: memberTypeId
                    },
                    success: function(response) {
                        if (response.success && response.addons.length > 0) {
                            var html = '';
                            $.each(response.addons, function(index, addon) {
                                html += '<div class="col-md-6 mb-3">';
                                html += '<div class="card">';
                                html += '<div class="card-body">';
                                html += '<div class="form-check">';
                                html += '<input class="form-check-input addon-checkbox" type="checkbox" ';
                                html += 'name="conference_addon_id[]" value="' + addon.id + '" ';
                                html += 'id="addon_' + addon.id + '" ';
                                html += 'data-addon-id="' + addon.id + '" ';
                                html += 'data-name="' + addon.addon_name + '" ';
                                html += 'data-amount="' + addon.amount + '" ';
                                html += 'data-guest-amount="' + addon.guest_amount + '">';
                                html += '<label class="form-check-label" for="addon_' + addon.id + '">';
                                html += '<strong>' + addon.addon_name + '</strong><br>';
                                html += '<small>Main: Rs. ' + addon.amount + ' | Guest: Rs. ' + addon.guest_amount + '</small>';
                                html += '</label>';
                                html += '</div>';
                                html += '<div id="guest_option_' + addon.id + '" class="mt-2" style="display: none;">';
                                html += '<div class="form-check">';
                                html += '<input class="form-check-input addon-guest-checkbox" type="checkbox" ';
                                html += 'id="include_guest_' + addon.id + '" data-addon-id="' + addon.id + '" checked>';
                                html += '<label class="form-check-label" for="include_guest_' + addon.id + '">Include for guests</label>';
                                html += '</div></div></div></div></div>';
                            });
                            $('#addons_container').html(html);
                        } else {
                            $('#addons_container').html('<div class="col-md-12 text-muted"><em>No add-ons available for this member type</em></div>');
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr);
                        $('#addons_container').html('<div class="col-md-12 text-danger"><em>Error loading add-ons</em></div>');
                    }
                });
            });

            // Handle addon checkbox changes
            $(document).on('change', '.addon-checkbox', function() {
                const addonId = $(this).data('addon-id');
                const guestOption = $(`#guest_option_${addonId}`);
                
                if ($(this).is(':checked')) {
                    guestOption.slideDown(200);
                } else {
                    guestOption.slideUp(200);
                }
                
                updateSelectedAddons();
            });

            // Handle guest inclusion checkbox changes
            $(document).on('change', '.addon-guest-checkbox', function() {
                updateSelectedAddons();
            });

            // Update selected addons hidden field
            function updateSelectedAddons() {
                const selectedAddons = [];
                $('.addon-checkbox:checked').each(function() {
                    const addonId = $(this).val();
                    const addonAmount = $(this).data('amount');
                    const guestAmount = $(this).data('guest-amount');
                    const includeGuest = $(`#include_guest_${addonId}`).is(':checked') ? '1' : '0';
                    
                    selectedAddons.push(`${addonId}:${addonAmount}:${guestAmount}:${includeGuest}`);
                });
                
                $('#selected_addons').val(selectedAddons.join(','));
            }

            // Update on form submit
            $('#registrationForm').on('submit', function() {
                if (($('#payment_status').val() || 'paid') === 'unpaid' && !$('#transaction_id').val()) {
                    $('#transaction_id').val('UNPAID-' + Date.now());
                }
                updateSelectedAddons();
            });

            $(".numericValue").on("keydown", function(event) {
                // Allow backspace, delete, tab, escape, and enter keys
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode ==
                    27 || event.keyCode == 13 ||
                    // Allow Ctrl+A
                    (event.keyCode == 65 && event.ctrlKey === true) ||
                    // Allow home, end, left, right
                    (event.keyCode >= 35 && event.keyCode <= 39) ||
                    // Allow minus keys for credit amount
                    event.keyCode == 189 || event.keyCode == 109 ||
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
