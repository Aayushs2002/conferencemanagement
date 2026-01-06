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

    @if(!empty($society) && $hasMemberDetailApi)
    <script>
        // Tab switching function
        function showTab(tabName) {
            // Hide all sections
            document.getElementById('sectionChangePassword').style.display = 'none';
            const memberSection = document.getElementById('sectionMemberVerification');
            if (memberSection) {
                memberSection.style.display = 'none';
            }

            // Remove active class from all tabs
            document.getElementById('tabChangePassword').classList.remove('active');
            const memberTab = document.getElementById('tabMemberVerification');
            if (memberTab) {
                memberTab.classList.remove('active');
            }

            // Show selected section and add active class
            if (tabName === 'changePassword') {
                document.getElementById('sectionChangePassword').style.display = 'block';
                document.getElementById('tabChangePassword').classList.add('active');
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

                    fetch('{{ route("security.verify-member-type", $society) }}', {
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

                    fetch('{{ route("security.update-member-type", $society) }}', {
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
    @endif
@endsection
