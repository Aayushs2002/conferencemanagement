@extends($layout)
@section('title')
    Setting
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="pages-account-settings-account.html"><i
                                class="icon-base ti tabler-users icon-sm me-1_5"></i> Account</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);"><i
                                class="icon-base ti tabler-lock icon-sm me-1_5"></i> Security</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="pages-account-settings-billing.html"><i
                                class="icon-base ti tabler-bookmark icon-sm me-1_5"></i> Billing & Plans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages-account-settings-notifications.html"><i
                                class="icon-base ti tabler-bell icon-sm me-1_5"></i> Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages-account-settings-connections.html"><i
                                class="icon-base ti tabler-link icon-sm me-1_5"></i> Connections</a>
                    </li> --}}
                </ul>
            </div>
            <!-- Change Password -->
            <div class="card mb-6">
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
        </div>
    </div>
@endsection
