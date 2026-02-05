@extends('backend.layouts.conference.main')

@section('title')
    Edit Conference Registration
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header">
                Edit Conference Registration</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ route('conference.conference-registration.update', [$society, $conference, $registrant->id]) }}"
                    id="registrationForm" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    @if(empty($registrant->user_id))
                        <div class="alert alert-warning" role="alert">
                            <strong>No User Linked!</strong> This is a dummy registration. You can either link it to an existing user or create a new user.
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label><strong>Choose an option:</strong></label>
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="user_option" id="linkExisting" value="link" checked>
                                            <label class="form-check-label" for="linkExisting">
                                                Link to Existing User
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="user_option" id="createNew" value="create">
                                            <label class="form-check-label" for="createNew">
                                                Create New User
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4" id="existingUserSection">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="existing_user_id">Select Existing User <code>*</code></label>
                                    <select name="existing_user_id" class="form-control select2" id="existing_user_id">
                                        <option value="">-- Select User --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->f_name }} {{ $user->m_name }} {{ $user->l_name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('existing_user_id')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="newUserSection" style="display: none;">
                            <div class="alert alert-info" role="alert">
                                Fill in the user details below to create a new user account.
                            </div>
                        </div>

                        <hr class="mb-4">
                    @endif

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="name_prefix_id">Name Prefix <code>*</code></label>
                            <select name="name_prefix_id" class="form-control" id="name_prefix_id" required>
                                <option value="" hidden>-- Select Name Prefix --</option>
                                @foreach ($prefixesAll as $prefix)
                                    <option value="{{ $prefix->id }}"
                                        @selected(old('name_prefix_id', $registrant->user?->userDetail->name_prefix_id) == $prefix->id)>
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
                                <input type="radio" @checked(old('gender', $registrant->user?->userDetail->gender) == 1) id="male" name="gender"
                                    value="1">
                                <label for="male">Male</label>
                            </span>
                            <span class="mr-3">
                                <input type="radio" @checked(old('gender', $registrant->user?->userDetail->gender) == 2) id="female" name="gender"
                                    value="2" style="margin-left: 10px;">
                                <label for="female">Female</label>
                            </span>
                            <span>
                                <input type="radio" @checked(old('gender', $registrant->user?->gender) == 3) id="other" name="gender"
                                    value="3" style="margin-left: 10px;">
                            </span>
                            <label for="other">Other</label>
                            @error('gender')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="f_name">First Name <code>*</code></label>
                            <input type="text" class="form-control @error('f_name') is-invalid @enderror" name="f_name"
                                id="f_name" value="{{ old('f_name', $registrant->user?->f_name) }}"
                                placeholder="Enter first name" required />
                            @error('f_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="m_name">Middle Name </label>
                            <input type="text" class="form-control @error('m_name') is-invalid @enderror" name="m_name"
                                id="m_name" value="{{ old('m_name', $registrant->user?->m_name) }}"
                                placeholder="Enter middle name" />
                            @error('m_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="l_name">Last Name <code>*</code></label>
                            <input type="text" class="form-control @error('l_name') is-invalid @enderror" name="l_name"
                                id="l_name" value="{{ old('l_name', $registrant->user?->l_name) }}"
                                placeholder="Enter last name" required />
                            @error('l_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="email">Email <code>*</code></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                id="email" value="{{ old('email', $registrant->user?->email) }}"
                                placeholder="Enter email" required />
                            @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="phone">Phone <code>*</code></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                                id="phone" value="{{ old('phone', $registrant->user?->userDetail->phone) }}"
                                placeholder="Enter phone" required />
                            @error('phone')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="institution_id">Institution Name <code>*</code></label>
                            <select class="form-select" name="institution_id" id="institution_id" required>
                                <option value="" hidden>-- Select Institution Name --</option>
                                @foreach ($institutions as $institution)
                                    <option value="{{ $institution->id }}"
                                        @selected(old('institution_id', $registrant->user?->userDetail->institution_id) == $institution->id)>
                                        {{ $institution->name }}</option>
                                @endforeach
                                <option value="other" @selected(old('institution_id', $userInstitution ? 'other' : $registrant->user?->userDetail->institution_id) == 'other')>Others</option>
                            </select>
                            @error('institution_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3" id="otherInstitutionWrapper" style="display: none;">
                            <label for="other_institution_name" class="form-label">Other Institution Name</label>
                            <input type="text" class="form-control" name="other_institution_name"
                                id="other_institution_name" placeholder="Enter Institution Name"
                                value="{{ old('other_institution_name', $userInstitution?->institution_name) }}">
                            @error('other_institution_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="designation_id" class="form-label">Designation <code>*</code></label>
                            <select class="form-select" name="designation_id" id="designation_id" required>
                                <option value="" hidden>-- Select Designation --</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}"
                                        @selected(old('designation_id', $registrant->user?->userDetail->designation_id) == $designation->id)>
                                        {{ $designation->designation }}</option>
                                @endforeach
                                <option value="other" @selected(old('designation_id', $userDesignation ? 'other' : $registrant->user?->userDetail->designation_id) == 'other')>Others</option>
                            </select>
                            @error('designation_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3" id="otherDesignationWrapper" style="display: none;">
                            <label for="other_designation" class="form-label">Other Designation</label>
                            <input type="text" class="form-control" name="other_designation"
                                id="other_designation" placeholder="Enter Designation"
                                value="{{ old('other_designation', $userDesignation?->designation_name) }}">
                            @error('other_designation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="department_id" class="form-label">Department <code>*</code></label>
                            <select class="form-select" name="department_id" id="department_id" required>
                                <option value="" hidden>-- Select Department --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        @selected(old('department_id', $registrant->user?->userDetail->department_id) == $department->id)>
                                        {{ $department->name }}</option>
                                @endforeach
                                <option value="other" @selected(old('department_id', $userDepartment ? 'other' : $registrant->user?->userDetail->department_id) == 'other')>Others</option>
                            </select>
                            @error('department_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3" id="otherDepartmentWrapper" style="display: none;">
                            <label for="other_department" class="form-label">Other Department</label>
                            <input type="text" class="form-control" name="other_department"
                                id="other_department" placeholder="Enter Department"
                                value="{{ old('other_department', $userDepartment?->department_name) }}">
                            @error('other_department')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="address">Institute Address <code>*</code></label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                name="address" id="address"
                                value="{{ old('address', $registrant->user?->userDetail->institute_address) }}"
                                placeholder="Enter institute address" required />
                            @error('address')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="council_number">Council Number</label>
                            <input type="text" class="form-control @error('council_number') is-invalid @enderror"
                                name="council_number" id="council_number"
                                value="{{ old('council_number', $registrant->user?->userDetail->council_number) }}"
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
                                    <option value="{{ $country->id }}"
                                        @selected(old('country_id', $registrant->user?->userDetail->country_id) == $country->id)>
                                        {{ $country->country_name }}</option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        @php
                            $userSociety = $registrant->user?->societies->first();
                            $currentMemberType = $userSociety?->pivot?->member_type_id;
                        @endphp

                        <div class="col-md-4 form-group mb-3">
                            <label for="member_type_id">Member Type <code>*</code></label>
                            <select name="member_type_id" class="form-control member_type_id" id="member_type_id"
                                required>
                                <option value="">-- Select Member Type --</option>
                                @foreach ($memberTypes as $type)
                                    <option value="{{ $type->id }}"
                                        @selected(old('member_type_id', $currentMemberType) == $type->id)>
                                        {{ $type->type }}</option>
                                @endforeach
                            </select>
                            @error('member_type_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="registrant_type">Registrant Type <code>*</code></label>
                            <select name="registrant_type" class="form-control" id="registrant_type">
                                <option value="" hidden>-- Select Registrant Type --</option>
                                <option value="1" @selected(old('registrant_type', $registrant->registrant_type) == '1')>Attendee</option>
                                <option value="2" @selected(old('registrant_type', $registrant->registrant_type) == '2')>Speaker/Presenter</option>
                                <option value="3" @selected(old('registrant_type', $registrant->registrant_type) == '3')>Session Chair</option>
                                <option value="4" @selected(old('registrant_type', $registrant->registrant_type) == '4')>Special Guest</option>
                            </select>
                            @error('registrant_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="payment_type">Payment Type <code>*</code></label>
                            <select name="payment_type" class="form-control" id="payment_type" required>
                                <option value="" hidden>-- Select Payment Type --</option>
                                <option value="1" @selected(old('payment_type', $registrant->payment_type) == 1)>Fone Pay</option>
                                <option value="2" @selected(old('payment_type', $registrant->payment_type) == 2)>Moco Payment</option>
                                <option value="3" @selected(old('payment_type', $registrant->payment_type) == 3)>Esewa</option>
                                <option value="4" @selected(old('payment_type', $registrant->payment_type) == 4)>Khalti</option>
                                <option value="5" @selected(old('payment_type', $registrant->payment_type) == 5)>Card Payment</option>
                                <option value="6" @selected(old('payment_type', $registrant->payment_type) == 6)>Voucher Payment</option>
                                <option value="7" @selected(old('payment_type', $registrant->payment_type) == 7)>ConnectIPS</option>
                            </select>
                            @error('payment_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="payment_voucher">Payment Voucher <code>(Only JPG/PNG/PDF) (Max: 250
                                    KB)</code></label>
                            <input type="file" class="form-control @error('payment_voucher') is-invalid @enderror"
                                name="payment_voucher" id="image2" />
                            @if ($registrant->payment_voucher)
                                <div class="mt-2 d-flex align-items-center" id="voucherSection">
                                    <span class="me-2">Current: <a
                                        href="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                        target="_blank">View Current Voucher</a></span>
                                    <button type="button" class="btn btn-sm btn-danger" id="deleteVoucher" 
                                        data-id="{{ $registrant->id }}"
                                        data-url="{{ route('conference.conference-registration.deleteVoucher', [$society, $conference, $registrant->id]) }}">
                                        <i class="icon-base ti tabler-trash"></i> Delete
                                    </button>
                                </div>
                            @endif
                            @error('payment_voucher')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div class="row" id="imgPreview2"></div>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="amount">Amount <code>* (Only Numeric Value)</code></label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror numericValue"
                                name="amount" id="amount" value="{{ old('amount', $registrant->amount) }}"
                                placeholder="Enter amount" required />
                            @error('amount')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3" id="hideDiv">
                            <label for="transaction_id">Transaction ID/Bill No/Reference Code <code>*</code></label>
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror"
                                name="transaction_id" id="transaction_id"
                                value="{{ old('transaction_id', $registrant->transaction_id) }}"
                                placeholder="Enter transaction id or bill number" required />
                            @error('transaction_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="meal_type">Meal Preference <code>*</code></label>
                            <select name="meal_type" class="form-control" id="meal_type">
                                <option value="" hidden>-- Select Veg/Non-veg --</option>
                                <option value="1" @selected(old('meal_type', $registrant->meal_type) == '1')>
                                    Veg</option>
                                <option value="2" @selected(old('meal_type', $registrant->meal_type) == '2')>
                                    Non-veg</option>
                            </select>
                            @error('meal_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="meal_type">Add On</label>
                            @php
                                $selectedAddons = $registrant->addons->pluck('conference_addon_id')->toArray();
                            @endphp
                            <select name="conference_addon_id[]" class="form-control select2" id="conference_addon_id"
                                multiple>
                                @foreach ($conferenceAddons as $addon)
                                    <option value="{{ $addon->id }}" @selected(in_array($addon->id, old('conference_addon_id', $selectedAddons)))>
                                        {{ $addon->addon_name }}</option>
                                @endforeach
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            @error('conference_addon_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="additional_guests">Number Of Guests <code>(Excluding Registrant)</code></label>
                            @php
                                $accompanyCount = $registrant->accompanyPersons->where('status', 1)->count();
                            @endphp
                            <select name="additional_guests" id="additional_guests"
                                class="form-control @error('additional_guests') is-invalid @enderror">
                                <option value="">-- Select Number Of Guests --</option>
                                <option value="1" @selected(old('additional_guests', $accompanyCount) == 1)>1</option>
                                <option value="2" @selected(old('additional_guests', $accompanyCount) == 2)>2</option>
                                <option value="3" @selected(old('additional_guests', $accompanyCount) == 3)>3</option>
                                <option value="4" @selected(old('additional_guests', $accompanyCount) == 4)>4</option>
                                <option value="5" @selected(old('additional_guests', $accompanyCount) == 5)>5</option>
                            </select>
                            @error('additional_guests')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($registrant->accompanyPersons->where('status', 1)->count() > 0)
                            <div class="col-md-12 mb-4" id="existingAccompanySection">
                                <h5 class="text-primary mb-3">Existing Accompany Persons:</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($registrant->accompanyPersons->where('status', 1) as $person)
                                                <tr id="person-row-{{ $person->id }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $person->person_name }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger deleteAccompanyPerson" 
                                                            data-id="{{ $person->id }}"
                                                            data-url="{{ route('conference.conference-registration.deleteAccompanyPerson', [$society, $conference, $person->id]) }}">
                                                            <i class="icon-base ti tabler-trash"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-12 form-group mb-3 speakerAdditionalSection" hidden>
                            <label for="description">Short CV <code>*</code></label>
                            <textarea class="form-control ckeditor" name="description" id="description" cols="30" rows="5">{{ old('description', $registrant->short_cv) }}</textarea>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <div class="row" id="accompanyPersonsDetail">

                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" id="submitButton" class="btn btn-primary">Update</button>
                            <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}"
                                class="btn btn-danger">Cancel</a>
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
            @php
                $accompanyPersons = $registrant->accompanyPersons->where('status', 1)->pluck('person_name')->toArray();
            @endphp
            var personsValue = @json($accompanyPersons);
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

            // Initialize select2 for existing user dropdown
            $('#existing_user_id').select2({
                placeholder: '-- Select User --',
                allowClear: true
            });

            // Handle voucher deletion
            $("#deleteVoucher").on("click", function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');
                
                if (confirm('Are you sure you want to delete this payment voucher?')) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#voucherSection').remove();
                                alert('Payment voucher deleted successfully!');
                            }
                        },
                        error: function(xhr) {
                            alert('Failed to delete voucher. Please try again.');
                        }
                    });
                }
            });

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
                        '<div class="col-md-12 mt-3"><h3 class="text-danger">Accompanying Person Details:</h3><h5 class="text-danger">Note: All names are required</h5></div>';
                    $("#accompanyPersonsDetail").append(title);
                    for (let index = 0; index < totalAccompanyPersons; index++) {
                        var oldValue = personsValue[index] || '';
                        var errorMessage = errorMessages['person_name.' + index] ? errorMessages[
                            'person_name.' + index][0] : '';
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

            const $designationSelect = $('#designation_id');
            const $otherDesignationWrapper = $('#otherDesignationWrapper');
            const $departmentSelect = $('#department_id');
            const $otherDepartmentWrapper = $('#otherDepartmentWrapper');

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

            $designationSelect.on('change', toggleOtherDesignation);
            toggleOtherDesignation();
            $departmentSelect.on('change', toggleOtherDepartment);
            toggleOtherDepartment();

            $('#country_id').on('change', function() {
                var country_id = $(this).val();
                var memberTypeId = '{{ old('member_type_id', $currentMemberType) }}';
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
                        var optionsHtml = '';
                        if (response.type === 'success' && response.data.length > 0) {
                            $.each(response.data, function(index, item) {
                                var selected = (item.id ==
                                    memberTypeId) ? 'selected' : '';
                                optionsHtml += '<option value="' + item
                                    .id + '" ' + selected + '>' + item
                                    .type + '</option>';
                            });
                            $('#member_type_id').append(optionsHtml);
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

            // Delete accompany person
            $(".deleteAccompanyPerson").on('click', function(e) {
                e.preventDefault();
                var personId = $(this).data('id');
                var deleteUrl = $(this).data('url');
                
                if (confirm('Are you sure you want to delete this accompany person?')) {
                    $.ajax({
                        type: "DELETE",
                        url: deleteUrl,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            $(this).attr('disabled', true);
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#person-row-' + personId).fadeOut(300, function() {
                                    $(this).remove();
                                    // Check if table is empty and remove it
                                    if ($('tbody tr:visible').length === 0) {
                                        $('#existingAccompanySection').fadeOut(300, function() {
                                            $(this).remove();
                                        });
                                    }
                                });
                                notyf.success(response.message);
                                
                                // Update the additional_guests dropdown
                                var currentCount = parseInt($('#additional_guests option:selected').val()) || 0;
                                if (currentCount > 0) {
                                    $('#additional_guests').val(currentCount - 1).trigger('change');
                                }
                            } else {
                                notyf.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            notyf.error('Failed to delete accompany person. Please try again.');
                        }
                    });
                }
            });

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

            // Toggle between link existing user and create new user
            $('input[name="user_option"]').on('change', function() {
                var selectedOption = $(this).val();
                if (selectedOption === 'link') {
                    $('#existingUserSection').show();
                    $('#newUserSection').hide();
                    $('#existing_user_id').prop('required', true);
                    // Make user form fields not required when linking
                    $('#registrationForm input, #registrationForm select').not('#existing_user_id').prop('required', false);
                } else {
                    $('#existingUserSection').hide();
                    $('#newUserSection').show();
                    $('#existing_user_id').prop('required', false);
                    // Make user form fields required when creating new
                    $('#registrationForm input[required], #registrationForm select[required]').prop('required', true);
                }
            });
        });
    </script>
@endsection
