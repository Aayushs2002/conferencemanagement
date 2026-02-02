@extends('backend.layouts.conference.main')

@section('title')
    Edit Workshop Registration
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header">
                Edit Workshop Registration - {{ $workshop->workshop_title }}</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ route('workshop.workshop-registration.update', [$society, $conference, $workshop, $registration->id]) }}"
                    id="registrationForm" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    @if(empty($registration->user_id))
                        <div class="alert alert-warning">
                            <strong>No User Linked!</strong> This registration has no user associated with it. You can either:
                            <ul>
                                <li>Link to an existing user, OR</li>
                                <li>Create a new user account</li>
                            </ul>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Choose an option:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="user_option" id="link_existing" value="link">
                                    <label class="form-check-label" for="link_existing">
                                        Link to Existing User
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="user_option" id="create_new" value="create" checked>
                                    <label class="form-check-label" for="create_new">
                                        Create New User
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="existing_user_section" class="row mb-4" style="display: none;">
                            <div class="col-md-12 form-group">
                                <label for="existing_user_id">Select Existing User <code>*</code></label>
                                <select name="existing_user_id" class="form-control" id="existing_user_id">
                                    <option value="" hidden>-- Select User --</option>
                                    @php
                                        // Get user IDs who are already registered for this workshop
                                        $registeredUserIds = $workshop->registrations
                                            ->where('status', 1)
                                            ->where('id', '!=', $registration->id) // Exclude current registration
                                            ->pluck('user_id')
                                            ->filter()
                                            ->toArray();
                                    @endphp
                                    @foreach($users as $user)
                                        @if(!in_array($user->id, $registeredUserIds))
                                            <option value="{{ $user->id }}">{{ $user->fullName($user) }} ({{ $user->email }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                <small class="text-muted">Only users not already registered for this workshop are shown</small>
                                @error('existing_user_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <div id="user_fields_section">
                        <h5 class="mb-3">User Information</h5>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="name_prefix_id">Name Prefix <code>*</code></label>
                                <select name="name_prefix_id" class="form-control" id="name_prefix_id" {{ $registration->user_id ? '' : '' }}>
                                    <option value="" hidden>-- Select Name Prefix --</option>
                                    @foreach ($prefixesAll as $prefix)
                                        <option value="{{ $prefix->id }}"
                                            @selected(old('name_prefix_id', $registration->user?->userDetail?->name_prefix_id) == $prefix->id)>
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
                                    <input type="radio" @checked(old('gender', $registration->user?->userDetail?->gender) == 1) id="male" name="gender"
                                        value="1">
                                    <label for="male">Male</label>
                                </span>
                                <span class="mr-3">
                                    <input type="radio" @checked(old('gender', $registration->user?->userDetail?->gender) == 2) id="female" name="gender"
                                        value="2" style="margin-left: 10px;">
                                    <label for="female">Female</label>
                                </span>
                                <span>
                                    <input type="radio" @checked(old('gender', $registration->user?->userDetail?->gender) == 3) id="other" name="gender"
                                        value="3" style="margin-left: 10px;">
                                    <label for="other">Other</label>
                                </span>
                                @error('gender')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="f_name">First Name <code>*</code></label>
                                <input type="text" class="form-control @error('f_name') is-invalid @enderror" name="f_name"
                                    id="f_name" value="{{ old('f_name', $registration->user?->f_name) }}"
                                    placeholder="Enter first name" />
                                @error('f_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="m_name">Middle Name </label>
                                <input type="text" class="form-control @error('m_name') is-invalid @enderror" name="m_name"
                                    id="m_name" value="{{ old('m_name', $registration->user?->m_name) }}"
                                    placeholder="Enter middle name" />
                                @error('m_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="l_name">Last Name <code>*</code></label>
                                <input type="text" class="form-control @error('l_name') is-invalid @enderror" name="l_name"
                                    id="l_name" value="{{ old('l_name', $registration->user?->l_name) }}"
                                    placeholder="Enter last name" />
                                @error('l_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="email">Email <code>*</code></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                    id="email" value="{{ old('email', $registration->user?->email) }}"
                                    placeholder="Enter email" />
                                @error('email')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="phone">Phone <code>*</code></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                                    id="phone" value="{{ old('phone', $registration->user?->userDetail?->phone) }}"
                                    placeholder="Enter phone" />
                                @error('phone')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="institution_id">Institution Name <code>*</code></label>
                                <select class="form-select" name="institution_id" id="institution_id">
                                    <option value="" hidden>-- Select Institution Name --</option>
                                    @foreach ($institutions as $institution)
                                        <option value="{{ $institution->id }}"
                                            @selected(old('institution_id', $registration->user?->userDetail?->institution_id) == $institution->id)>
                                            {{ $institution->name }}</option>
                                    @endforeach
                                    <option value="other" @selected(old('institution_id', $userInstitution ? 'other' : $registration->user?->userDetail?->institution_id) == 'other')>Others</option>
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
                                <select class="form-select" name="designation_id" id="designation_id">
                                    <option value="" hidden>-- Select Designation --</option>
                                    @foreach ($designations as $designation)
                                        <option value="{{ $designation->id }}"
                                            @selected(old('designation_id', $registration->user?->userDetail?->designation_id) == $designation->id)>
                                            {{ $designation->designation }}</option>
                                    @endforeach
                                    <option value="other" @selected(old('designation_id', $userDesignation ? 'other' : $registration->user?->userDetail?->designation_id) == 'other')>Others</option>
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
                                <select class="form-select" name="department_id" id="department_id">
                                    <option value="" hidden>-- Select Department --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            @selected(old('department_id', $registration->user?->userDetail?->department_id) == $department->id)>
                                            {{ $department->name }}</option>
                                    @endforeach
                                    <option value="other" @selected(old('department_id', $userDepartment ? 'other' : $registration->user?->userDetail?->department_id) == 'other')>Others</option>
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
                                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address"
                                    id="address" value="{{ old('address', $registration->user?->userDetail?->institute_address) }}"
                                    placeholder="Enter institute address" />
                                @error('address')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="country_id" class="form-label">Country <code>*</code></label>
                                <select class="form-select" name="country_id" id="country_id">
                                    <option value="" hidden>-- Select Country --</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            @selected(old('country_id', $registration->user?->userDetail?->country_id) == $country->id)>
                                            {{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="council_number">Council Number</label>
                                <input type="text" class="form-control @error('council_number') is-invalid @enderror" name="council_number"
                                    id="council_number" value="{{ old('council_number', $registration->user?->userDetail?->council_number) }}"
                                    placeholder="Enter council number" />
                                @error('council_number')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="member_type_id">Member Type <code>*</code></label>
                                @php
                                    $userSociety = $registration->user?->societies->first();
                                    $currentMemberTypeId = $userSociety?->pivot?->member_type_id;
                                @endphp
                                <select class="form-select" name="member_type_id" id="member_type_id">
                                    <option value="" hidden>-- Select Member Type --</option>
                                    @foreach ($memberTypes as $memberType)
                                        <option value="{{ $memberType->id }}"
                                            @selected(old('member_type_id', $currentMemberTypeId) == $memberType->id)>
                                            {{ $memberType->type }}</option>
                                    @endforeach
                                </select>
                                @error('member_type_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if($registration->registrant_type == 1)
                    <hr class="my-4">

                    <h5 class="mb-3">Workshop Registration Details</h5>
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="transaction_id">Transaction ID <code>*</code></label>
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" name="transaction_id"
                                id="transaction_id" value="{{ old('transaction_id', $registration->transaction_id) }}"
                                placeholder="Enter transaction ID" required />
                            @error('transaction_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="amount">Amount <code>*</code></label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount"
                                id="amount" value="{{ old('amount', $registration->amount) }}"
                                placeholder="Enter amount" required />
                            @error('amount')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="meal_type">Meal Type</label>
                            <select class="form-select" name="meal_type" id="meal_type">
                                <option value="" hidden>-- Select Meal Type --</option>
                                <option value="1" @selected(old('meal_type', $registration->meal_type) == 1)>Vegetarian</option>
                                <option value="2" @selected(old('meal_type', $registration->meal_type) == 2)>Non-Vegetarian</option>
                            </select>
                            @error('meal_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="payment_type">Payment Type <code>*</code></label>
                            <select class="form-select" name="payment_type" id="payment_type" required>
                                <option value="" hidden>-- Select Payment Type --</option>
                                <option value="1" @selected(old('payment_type', $registration->payment_type) == 1)>Fone-Pay</option>
                                <option value="2" @selected(old('payment_type', $registration->payment_type) == 2)>Moco</option>
                                <option value="3" @selected(old('payment_type', $registration->payment_type) == 3)>Esewa</option>
                                <option value="4" @selected(old('payment_type', $registration->payment_type) == 4)>Khalti</option>
                                <option value="5" @selected(old('payment_type', $registration->payment_type) == 5)>Card Payment</option>
                                <option value="6" @selected(old('payment_type', $registration->payment_type) == 6)>Bank Voucher</option>
                            </select>
                            @error('payment_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="payment_voucher">Payment Voucher</label>
                            <input type="file" class="form-control @error('payment_voucher') is-invalid @enderror" name="payment_voucher"
                                id="payment_voucher" accept=".jpg,.png,.pdf" />
                            @if($registration->payment_voucher)
                                <small class="text-muted">Current: <a href="{{ asset('storage/workshop/payment-voucher/' . $registration->payment_voucher) }}" target="_blank">View Voucher</a></small>
                            @endif
                            @error('payment_voucher')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="verified_status">Verified Status <code>*</code></label>
                            <select class="form-select" name="verified_status" id="verified_status" required>
                                <option value="" hidden>-- Select Status --</option>
                                <option value="0" @selected(old('verified_status', $registration->verified_status) == 0)>Pending</option>
                                <option value="1" @selected(old('verified_status', $registration->verified_status) == 1)>Verified</option>
                                <option value="2" @selected(old('verified_status', $registration->verified_status) == 2)>Rejected</option>
                            </select>
                            @error('verified_status')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Enter remarks">{{ old('remarks', $registration->remarks) }}</textarea>
                            @error('remarks')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update Registration</button>
                            <a href="{{ route('workshop.workshop-registration.index', [$society, $conference, $workshop]) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle institution dropdown
        function toggleOtherInstitution() {
            if ($('#institution_id').val() == 'other') {
                $('#otherInstitutionWrapper').show();
            } else {
                $('#otherInstitutionWrapper').hide();
            }
        }

        // Handle designation dropdown
        function toggleOtherDesignation() {
            if ($('#designation_id').val() == 'other') {
                $('#otherDesignationWrapper').show();
            } else {
                $('#otherDesignationWrapper').hide();
            }
        }

        // Handle department dropdown
        function toggleOtherDepartment() {
            if ($('#department_id').val() == 'other') {
                $('#otherDepartmentWrapper').show();
            } else {
                $('#otherDepartmentWrapper').hide();
            }
        }

        // Initial toggle on page load
        toggleOtherInstitution();
        toggleOtherDesignation();
        toggleOtherDepartment();

        // Event listeners
        $('#institution_id').on('change', toggleOtherInstitution);
        $('#designation_id').on('change', toggleOtherDesignation);
        $('#department_id').on('change', toggleOtherDepartment);

        // Handle user option radio buttons (for null user_id case)
        $('input[name="user_option"]').on('change', function() {
            if ($(this).val() == 'link') {
                $('#existing_user_section').show();
                $('#user_fields_section').hide();
                // Make existing_user_id required
                $('#existing_user_id').prop('required', true);
                // Remove required from user fields
                $('#user_fields_section input, #user_fields_section select').prop('required', false);
            } else {
                $('#existing_user_section').hide();
                $('#user_fields_section').show();
                // Remove required from existing_user_id
                $('#existing_user_id').prop('required', false);
                // Restore required to user fields
                $('#user_fields_section input[id$="_name"], #user_fields_section select').not('#m_name, #council_number, #other_institution_name, #other_designation, #other_department').prop('required', true);
            }
        });

        // Initialize the correct state on page load for null user_id
        @if(empty($registration->user_id))
            $('input[name="user_option"]:checked').trigger('change');
        @endif
    });
</script>
@endsection
