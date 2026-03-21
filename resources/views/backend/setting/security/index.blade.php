@extends($layout)
@section('title')
    Setting
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
                    <li class="nav-item">
                        <a class="nav-link active" id="tabChangePassword" href="javascript:void(0);" onclick="showTab('changePassword')"><i
                                class="icon-base ti tabler-lock icon-sm me-1_5"></i> Change Password</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tabProfileUpdate" href="javascript:void(0);" onclick="showTab('profileUpdate')"><i
                                class="icon-base ti tabler-user-edit icon-sm me-1_5"></i> Profile Update</a>
                    </li>
                    @if(!empty($society) && $hasMemberDetailApi && !empty($userMemberType))
                    <li class="nav-item">
                        <a class="nav-link" id="tabMemberVerification" href="javascript:void(0);" onclick="showTab('memberVerification')"><i
                                class="icon-base ti tabler-shield-check icon-sm me-1_5"></i> Member Type Verification</a>
                    </li>
                    @endif
                </ul>
            </div>
            <!-- Change Password -->
            <div class="card mb-6" id="sectionChangePassword">
                <h5 class="card-header">Change Password</h5>
                <div class="card-body pt-1">
                    <form id="formAccountSettings" method="POST" action="{{ route('security.password-change') }}">
                        @csrf
                        <div class="row mb-sm-6 mb-2">
                            <div class="col-md-6 form-password-toggle form-control-validation">
                                <label class="form-label" for="currentPassword">Current Password</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="password" name="currentPassword" id="currentPassword"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base ti tabler-eye-off icon-xs"></i></span>
                                </div>
 
                                @error('currentPassword')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="row gy-sm-6 gy-2 mb-sm-0 mb-2">
                            <div class="mb-6 col-md-6 form-password-toggle form-control-validation">
                                <label class="form-label" for="newPassword">New Password</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="password" id="newPassword" name="new_password" 
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base ti tabler-eye-off icon-xs"></i></span>
                                </div>
                                @error('newPassword')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6 col-md-6 form-password-toggle form-control-validation">
                                <label class="form-label" for="confirmPassword">Confirm New Password</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="password" name="new_password_confirmation"
                                        id="confirmPassword"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base ti tabler-eye-off icon-xs"></i></span>
                                </div>
                            </div>
                        </div>
                        {{-- <h6 class="text-body">Password Requirements:</h6>
                        <ul class="ps-4 mb-0">
                            <li class="mb-4">Minimum 8 characters long - the more, the better</li>
                            <li class="mb-4">At least one lowercase character</li>
                            <li>At least one number, symbol, or whitespace character</li>
                        </ul> --}}
                        <div class="mt-6 text-end"> 
                            <button type="submit" class="btn btn-primary me-3">Save changes</button>
                            <button type="reset" class="btn btn-label-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Change Password -->

            <!-- Profile Update -->
            <div class="card mb-6" id="sectionProfileUpdate" style="display:none;">
                <h5 class="card-header">Profile Update</h5>
                <div class="card-body pt-1">
                    @php
                        $contextSociety = null;
                        if (!empty($society)) {
                            $contextSociety = is_object($society) ? $society : \App\Models\User\Society::find($society);
                        }

                        $loadData = function ($relation, $model) use ($contextSociety) {
                            if ($contextSociety && $contextSociety->$relation()->exists()) {
                                return $contextSociety->$relation()->where('status', 1)->get();
                            }
                            return $model::where('status', 1)->get();
                        };

                        $institutions = $loadData('institutions', \App\Models\User\Institution::class);
                        $designations = $loadData('designations', \App\Models\User\Designation::class);
                        $departments = $loadData('departments', \App\Models\User\Department::class);

                        $profileFields = [
                            current_user()?->userDetail?->institution_id,
                            current_user()?->userDetail?->department_id,
                            current_user()?->userDetail?->institute_address,
                            current_user()?->userDetail?->image,
                            current_user()?->userDetail?->dob_ad,
                            current_user()?->userDetail?->designation_id,
                        ];

                        $filledFields = collect($profileFields)->filter(fn($value) => !empty($value))->count();
                        $profileCompletionPercent = (int) round(($filledFields / count($profileFields)) * 100);
                    @endphp

                    <div class="alert alert-info d-flex align-items-start" role="alert">
                        <i class="icon-base ti tabler-user-circle icon-sm me-2"></i>
                        <div>
                            Keep your profile updated for accurate conference registration, membership verification, and certificate details.
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100 bg-label-primary">
                                <div class="text-muted small mb-1">Profile Completion</div>
                                <h4 class="mb-1">{{ $profileCompletionPercent }}%</h4>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $profileCompletionPercent }}%;"
                                        aria-valuenow="{{ $profileCompletionPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small mb-1">Current Council Number</div>
                                <div class="fw-semibold">{{ current_user()?->userDetail?->council_number ?: 'Not Set' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small mb-1">Current Institution</div>
                                <div class="fw-semibold">{{ current_user()?->userDetail?->institution?->name ?: 'Not Set' }}</div>
                            </div>
                        </div>
                    </div>

                    <form id="formProfileUpdateSettings" class="needs-validation" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6" id="institutionWrapper"
                                @if (current_user()->userDetail->country_id != 125) style="display: none;" @endif>
                                <label for="institution_id" class="form-label">Institution Name <code>*</code></label>
                                <select class="form-select" name="institution_id" id="institution_id" required>
                                    <option value="" hidden>-- Select Institution Name --</option>
                                    @foreach ($institutions as $institution)
                                        <option value="{{ $institution->id }}" @selected(current_user()?->userDetail?->institution_id == $institution->id)>
                                            {{ $institution->name }}
                                        </option>
                                    @endforeach
                                    <option value="other">Others</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="otherInstitutionWrapper"
                                @if (current_user()->userDetail->country_id != 125) style="display: block;" @else style="display: none;" @endif>
                                <label for="other_institution_name" class="form-label">
                                    @if (current_user()->userDetail->country_id != 125)
                                        Institution Name
                                    @else
                                        Other Institution Name
                                    @endif
                                    <code>*</code>
                                </label>
                                <input type="text" class="form-control" name="other_institution_name" id="other_institution_name"
                                    placeholder="Enter Institution Name">
                            </div>

                            <div class="col-md-6" id="designationWrapper">
                                <label for="designation_id" class="form-label">Designation</label>
                                <select class="form-select" name="designation_id" id="designation_id">
                                    <option value="" hidden>-- Select Designation --</option>
                                    @foreach ($designations as $designation)
                                        <option value="{{ $designation->id }}" @selected(current_user()?->userDetail?->designation_id == $designation->id)>
                                            {{ $designation->designation }}
                                        </option>
                                    @endforeach
                                    <option value="other">Others</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="otherDesignationWrapper" style="display: none;">
                                <label for="other_designation" class="form-label">Other Designation <code>*</code></label>
                                <input type="text" class="form-control" name="other_designation" id="other_designation"
                                    placeholder="Enter Designation">
                            </div>

                            <div class="col-md-6" id="departmentWrapper">
                                <label for="department_id" class="form-label">Department <code>*</code></label>
                                <select class="form-select" name="department_id" id="department_id" required>
                                    <option value="" hidden>-- Select Department --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" @selected(current_user()?->userDetail?->department_id == $department->id)>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                    <option value="other">Others</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="otherDepartmentWrapper" style="display: none;">
                                <label for="other_department" class="form-label">Other Department <code>*</code></label>
                                <input type="text" class="form-control" name="other_department" id="other_department"
                                    placeholder="Enter Department">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="institute_address">Institution Address <code>*</code></label>
                                <input type="text" id="institute_address" name="institute_address" class="form-control"
                                    placeholder="Enter Institution Address"
                                    value="{{ current_user()?->userDetail?->institute_address }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="dob_ad">Date of Birth (AD)</label>
                                <input type="date" id="dob_ad" name="dob_ad" class="form-control"
                                    value="{{ current_user()?->userDetail?->dob_ad }}">
                            </div>

                            @if (
                                (current_user()->userDetail->name_prefix_id == 1 || current_user()->userDetail->name_prefix_id == 3) &&
                                    current_user()->userDetail->country_id == 125)
                                <div class="col-md-6">
                                    <label class="form-label" for="council_number">Medical Council Number <code>*</code></label>
                                    <input type="text" id="council_number" name="council_number" class="form-control"
                                        placeholder="Enter Medical Council Number"
                                        value="{{ current_user()?->userDetail?->council_number }}" required>
                                </div>
                            @else
                                <input type="hidden" name="council_number" value="{{ current_user()?->userDetail?->council_number }}">
                            @endif

                            <div class="col-md-6">
                                <label class="form-label" for="image">Photo <code>*(Passport Sized Image)</code></label>
                                <input type="file" id="image" name="image" class="form-control" required>
                                <small class="text-muted">Upload JPG, PNG, or JPEG image.</small>
                            </div>
                        </div>

                        <div class="mt-6 text-end">
                            <button type="submit" class="btn btn-primary" id="btnProfileUpdateSubmit">
                                <i class="icon-base ti tabler-device-floppy icon-sm me-1"></i>
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Profile Update -->

            @if(!empty($society) && $hasMemberDetailApi && !empty($userMemberType))
            <!-- Member Type Verification -->
            <div class="card mb-6" id="sectionMemberVerification" style="display:none;">
                <h5 class="card-header">Member Type Verification</h5>
                <div class="card-body pt-1">
                    <div class="alert alert-info" role="alert">
                        <i class="icon-base ti tabler-info-circle icon-sm me-2"></i>
                        Verify your member type against {{ $societyModel->name ?? 'society' }} records.
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Current Member Type</label>
                            <input type="text" class="form-control" value="{{ $userMemberType->type }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Council Number</label>
                            <input type="text" class="form-control" 
                                value="{{ current_user()?->userDetail?->council_number ?? 'Not Set' }}" readonly>
                        </div>
                    </div>

                    <div id="verificationResult" class="mb-4" style="display:none;"></div>
                    
                    <div id="memberTypeUpdateSection" style="display:none;">
                        <form id="formUpdateMemberType">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label" for="verifiedMemberType">Verified Member Type</label>
                                    <input type="text" class="form-control" id="verifiedMemberType" readonly>
                                    <input type="hidden" name="member_type_id" id="verifiedMemberTypeId">
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="icon-base ti tabler-check icon-sm me-1"></i>
                                    Update Member Type
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="contactAdminSection" style="display:none;">
                        <div class="alert alert-warning" role="alert">
                            <h6 class="alert-heading mb-2">
                                <i class="icon-base ti tabler-alert-triangle icon-sm me-2"></i>
                                Verification Failed
                            </h6>
                            <p id="contactAdminMessage" class="mb-2"></p>
                            <p class="mb-0">
                                <strong>Please contact the administrator for assistance:</strong><br>
                                Email: <a href="mailto:{{ $societyModel->contact_person_email ?? 'admin@society.com' }}">
                                    {{ $societyModel->contact_person_email ?? 'admin@society.com' }}
                                </a><br>
                                @if(!empty($societyModel->contact_person_phone))
                                Phone: {{ $societyModel->contact_person_phone }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="text-end" id="btnVerifyContainer">
                        <button type="button" class="btn btn-primary" id="btnVerifyMemberType">
                            <i class="icon-base ti tabler-shield-check icon-sm me-1"></i>
                            Verify Member Type
                        </button>
                    </div>
                </div>
            </div>
            <!--/ Member Type Verification -->
            @endif
        </div>
    </div>

    <script>
        // Tab switching function - define globally
        function showTab(tabName) {
            // Hide all sections
            document.getElementById('sectionChangePassword').style.display = 'none';
            document.getElementById('sectionProfileUpdate').style.display = 'none';
            const memberSection = document.getElementById('sectionMemberVerification');
            if (memberSection) {
                memberSection.style.display = 'none';
            }

            // Remove active class from all tabs
            document.getElementById('tabChangePassword').classList.remove('active');
            document.getElementById('tabProfileUpdate').classList.remove('active');
            const memberTab = document.getElementById('tabMemberVerification');
            if (memberTab) {
                memberTab.classList.remove('active');
            }

            // Show selected section and add active class
            if (tabName === 'changePassword') {
                document.getElementById('sectionChangePassword').style.display = 'block';
                document.getElementById('tabChangePassword').classList.add('active');
            } else if (tabName === 'profileUpdate') {
                document.getElementById('sectionProfileUpdate').style.display = 'block';
                document.getElementById('tabProfileUpdate').classList.add('active');
            } else if (tabName === 'memberVerification') {
                if (memberSection) {
                    memberSection.style.display = 'block';
                }
                if (memberTab) {
                    memberTab.classList.add('active');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const profileForm = document.getElementById('formProfileUpdateSettings');
            const institutionSelect = document.getElementById('institution_id');
            const designationSelect = document.getElementById('designation_id');
            const departmentSelect = document.getElementById('department_id');

            const otherInstitutionWrapper = document.getElementById('otherInstitutionWrapper');
            const otherDesignationWrapper = document.getElementById('otherDesignationWrapper');
            const otherDepartmentWrapper = document.getElementById('otherDepartmentWrapper');

            if (institutionSelect && otherInstitutionWrapper) {
                const countryId = @json(current_user()?->userDetail?->country_id);
                const toggleOtherInstitution = () => {
                    if (institutionSelect.value === 'other') {
                        otherInstitutionWrapper.style.display = 'block';
                    } else {
                        otherInstitutionWrapper.style.display = (countryId !== 125) ? 'block' : 'none';
                    }
                };
                institutionSelect.addEventListener('change', toggleOtherInstitution);
                toggleOtherInstitution();
            }

            if (designationSelect && otherDesignationWrapper) {
                const toggleOtherDesignation = () => {
                    otherDesignationWrapper.style.display = designationSelect.value === 'other' ? 'block' : 'none';
                };
                designationSelect.addEventListener('change', toggleOtherDesignation);
                toggleOtherDesignation();
            }

            if (departmentSelect && otherDepartmentWrapper) {
                const toggleOtherDepartment = () => {
                    otherDepartmentWrapper.style.display = departmentSelect.value === 'other' ? 'block' : 'none';
                };
                departmentSelect.addEventListener('change', toggleOtherDepartment);
                toggleOtherDepartment();
            }

            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    profileForm.querySelectorAll('.text-danger.dynamic-error').forEach(el => el.remove());
                    profileForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                    const submitBtn = document.getElementById('btnProfileUpdateSubmit');
                    const originalBtnHtml = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';

                    const formData = new FormData(profileForm);
                    formData.append('_method', 'PATCH');

                    fetch('{{ route('profile.update-profile') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => Promise.reject(data));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.type === 'success') {
                                notyf.success(data.message);
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                notyf.error(data.message || 'Unable to update profile.');
                            }
                        })
                        .catch(error => {
                            if (error.errors) {
                                Object.entries(error.errors).forEach(([key, messages]) => {
                                    const input = profileForm.querySelector(`[name="${key}"]`);
                                    if (input) {
                                        input.classList.add('is-invalid');
                                        const errorEl = document.createElement('p');
                                        errorEl.className = 'text-danger dynamic-error mt-1 mb-0';
                                        errorEl.textContent = messages[0];
                                        input.insertAdjacentElement('afterend', errorEl);
                                    }
                                });
                            } else {
                                notyf.error('An unexpected error occurred while updating profile.');
                            }
                            showTab('profileUpdate');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnHtml;
                        });
                });
            }

            const btnVerify = document.getElementById('btnVerifyMemberType');
            const btnVerifyContainer = document.getElementById('btnVerifyContainer');
            const verificationResult = document.getElementById('verificationResult');
            const memberTypeUpdateSection = document.getElementById('memberTypeUpdateSection');
            const contactAdminSection = document.getElementById('contactAdminSection');
            const formUpdateMemberType = document.getElementById('formUpdateMemberType');

            if (btnVerify) {
                btnVerify.addEventListener('click', function() {
                    btnVerify.disabled = true;
                    btnVerify.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Verifying...';
                    
                    // Hide previous results
                    verificationResult.style.display = 'none';
                    memberTypeUpdateSection.style.display = 'none';
                    contactAdminSection.style.display = 'none';

                    const verifyMemberTypeUrl = @json(!empty($society) ? route('security.verify-member-type', $society) : null);
                    if (!verifyMemberTypeUrl) {
                        verificationResult.className = 'alert alert-danger mb-4';
                        verificationResult.innerHTML = `
                            <i class="icon-base ti tabler-alert-circle icon-sm me-2"></i>
                            Member verification is only available inside a society context.
                        `;
                        verificationResult.style.display = 'block';
                        btnVerify.disabled = false;
                        btnVerify.innerHTML = '<i class="icon-base ti tabler-shield-check icon-sm me-1"></i>Verify Member Type';
                        return;
                    }

                    fetch(verifyMemberTypeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            verificationResult.className = 'alert alert-success mb-4';
                            verificationResult.innerHTML = `
                                <i class="icon-base ti tabler-circle-check icon-sm me-2"></i>
                                ${data.message}
                            `;
                            verificationResult.style.display = 'block';

                            // Hide verify button and show update section
                            if (data.memberType) {
                                btnVerifyContainer.style.display = 'none';
                                document.getElementById('verifiedMemberType').value = data.memberType.type;
                                document.getElementById('verifiedMemberTypeId').value = data.memberType.id;
                                memberTypeUpdateSection.style.display = 'block';
                            }
                        } else {
                            if (data.showContact) {
                                btnVerifyContainer.style.display = 'none';
                                document.getElementById('contactAdminMessage').textContent = data.error;
                                contactAdminSection.style.display = 'block';
                            } else {
                                verificationResult.className = 'alert alert-danger mb-4';
                                verificationResult.innerHTML = `
                                    <i class="icon-base ti tabler-alert-circle icon-sm me-2"></i>
                                    ${data.error}
                                `;
                                verificationResult.style.display = 'block';
                            }
                        }
                    })
                    .catch(error => {
                        verificationResult.className = 'alert alert-danger mb-4';
                        verificationResult.innerHTML = `
                            <i class="icon-base ti tabler-alert-circle icon-sm me-2"></i>
                            An error occurred during verification. Please try again.
                        `;
                        verificationResult.style.display = 'block';
                    })
                    .finally(() => {
                        btnVerify.disabled = false;
                        btnVerify.innerHTML = '<i class="icon-base ti tabler-shield-check icon-sm me-1"></i>Verify Member Type';
                    });
                });
            }

            if (formUpdateMemberType) {
                formUpdateMemberType.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...';

                    const formData = new FormData(this);

                    const updateMemberTypeUrl = @json(!empty($society) ? route('security.update-member-type', $society) : null);
                    if (!updateMemberTypeUrl) {
                        verificationResult.className = 'alert alert-danger mb-4';
                        verificationResult.innerHTML = `
                            <i class="icon-base ti tabler-alert-circle icon-sm me-2"></i>
                            Member type update is only available inside a society context.
                        `;
                        verificationResult.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        return;
                    }

                    fetch(updateMemberTypeUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            verificationResult.className = 'alert alert-success mb-4';
                            verificationResult.innerHTML = `
                                <i class="icon-base ti tabler-circle-check icon-sm me-2"></i>
                                ${data.message} Reloading page...
                            `;
                            verificationResult.style.display = 'block';
                            memberTypeUpdateSection.style.display = 'none';
                            
                            // Reload page after 2 seconds
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            verificationResult.className = 'alert alert-danger mb-4';
                            verificationResult.innerHTML = `
                                <i class="icon-base ti tabler-alert-circle icon-sm me-2"></i>
                                ${data.error}
                            `;
                            verificationResult.style.display = 'block';
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    })
                    .catch(error => {
                        verificationResult.className = 'alert alert-danger mb-4';
                        verificationResult.innerHTML = `
                            <i class="icon-base ti tabler-alert-circle icon-sm me-2"></i>
                            An error occurred while updating member type. Please try again.
                        `;
                        verificationResult.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
                });
            }
        });
    </script>
@endsection
