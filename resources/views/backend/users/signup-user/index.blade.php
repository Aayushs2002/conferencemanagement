@extends('backend.layouts.conference.main')

@section('title')
    Signed Up Users
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6"> Signed Up Users</h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="icon-base ti tabler-upload icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Export</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportTo('excel')">Export to Excel</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="exportTo('pdf')">Export to PDF</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportTo('csv')">Export to CSV</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="window.print()">Print</a></li>
                            </ul>
                        </div>
                        {{-- <a href="{{ route('society.create') }}" class="btn btn-primary" tabindex="0">
                            <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New</span>
                        </a> --}}
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Has Registered Conference</th>
                        <th>Number of Workshops Registration</th>
                        <th>Number of Submission</th>
                        <th>Last Login</th>
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Assign Expert'))
                            <th>Is Expert?</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($users as $user)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $user->fullName($user) }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if (!empty($user->conferenceRegistration->where('conference_id', $conference->id)->first()))
                                    <span class="badge bg-success" style="font-size: 10px;">Registered</span>
                                @else
                                    <span class="badge bg-warning" style="font-size: 10px;">Not Registered</span>
                                @endif
                            </td>

                            @php
                                $workshop = DB::table('workshops')
                                    ->where([
                                        'conference_id' => $conference->id,
                                        'status' => 1,
                                    ])
                                    ->pluck('id');
                            @endphp

                            <td>{{ $user->workshopRegistration->whereIn('workshop_id', $workshop)->count() }}</td>

                            <td>{{ $user->submission->where('conference_id', $conference->id)->count() }}</td>

                            <td>
                                {{ !empty($user->last_login_at) ? \Carbon\Carbon::parse($user->last_login_at)->format('d M, Y, h:i a') : '-' }}
                            </td>

                            {{-- for expert start --}}
                            @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Assign Expert'))
                                <td>
                                    @php
                                        $isExpert = DB::table('experts')
                                            ->where([
                                                'user_id' => $user->id,
                                                'conference_id' => $conference->id,
                                            ])
                                            ->first();
                                    @endphp
                                    @if (!empty($isExpert))
                                        @if ($isExpert->status == 1)
                                            <a href="#" class="btn btn-success btn-sm mt-1 makeExpert"
                                                data-id="{{ $user->id }}" data-name="{{ $user->fullName($user) }}"
                                                data-type="remove"><i class="icon-base ti tabler-circle-check"></i></a>
                                        @else
                                            <a href="#" class="btn btn-warning btn-sm mt-1 makeExpert"
                                                data-id="{{ $user->id }}" data-name="{{ $user->fullName($user) }}"
                                                data-type="assign"><i class="icon-base ti tabler-circle-x"></i></a>
                                        @endif
                                    @else
                                        <a href="#" class="btn btn-warning btn-sm mt-1 makeExpert"
                                            data-id="{{ $user->id }}" data-name="{{ $user->fullName($user) }}"
                                            data-type="assign"><i class="icon-base ti tabler-circle-x"></i></a>
                                    @endif
                                </td>
                            @endif

                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit User'))
                                            <a class="dropdown-item editProfile" data-id="{{ $user->id }}"
                                                data-bs-toggle="modal" data-bs-target="#pricingModal">
                                                <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                            </a>
                                        @endif

                                        <a class="dropdown-item viewData" data-id="{{ $user->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal">
                                            <i class="icon-base ti tabler-eye me-1"></i> View
                                        </a>
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Invite For Conference'))
                                            @if (empty($user->conferenceRegistration->where('conference_id', $conference->id)->first()))
                                                <a class="dropdown-item inviteForConference" data-id="{{ $user->id }}"
                                                    data-bs-toggle="modal" data-bs-target="#pricingModal">
                                                    <i class="icon-base ti tabler-pointer me-1"></i> Invite For Conference
                                                </a>
                                            @endif
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Pass Designation'))
                                            <a class="dropdown-item passDesgination" data-id="{{ $user->id }}"
                                                data-bs-toggle="modal" data-bs-target="#pricingModal">
                                                <i class="icon-base ti tabler-id-badge-2 me-1"></i> Pass Designation
                                            </a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Merge User'))
                                            <a class="dropdown-item mergeUser" data-id="{{ $user->id }}"
                                                data-bs-toggle="modal" data-bs-target="#pricingModal">
                                                <i class="icon-base ti tabler-user me-1"></i> Merge User
                                            </a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Reset Password'))
                                            <a class="dropdown-item resetPassword" data-id="{{ $user->id }}">
                                                <i class="icon-base ti tabler-restore me-1"></i>Reset Password
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('click', '.makeExpert', function(e) {
                e.preventDefault();
                var name = $(this).data('name');
                var userId = $(this).data('id');
                var type = $(this).data('type');
                Swal.fire({
                    title: "Do you want to " + type + " '" + name + "' as an Expert?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Assign!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = '{{ route('signup-user.makeExpert', [$society, $conference]) }}';
                        var data = {
                            userId: userId
                        };
                        $.post(url, data, function(response) {
                            if (response.type == 'success') {
                                notyf.success(response.message);
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                notyf.error(response.message);
                            }
                        });
                    }
                })
            });

            $(document).on("click", ".inviteForConference", function(e) {
                e.preventDefault();
                var url = '{{ route('signup-user.inviteForConference', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $('#pricingModal .modal-dialog').removeClass('modal-lg');
                $('#pricingModal .modal-dialog').addClass('modal-md');
                $('#pricingModal .modal-dialog').removeClass('modal-xl');

                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });
            $(document).on("click", ".passDesgination", function(e) {
                e.preventDefault();
                var url = '{{ route('signup-user.passDesgination', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $('#pricingModal .modal-dialog').removeClass('modal-lg');
                $('#pricingModal .modal-dialog').addClass('modal-md');
                $('#pricingModal .modal-dialog').removeClass('modal-xl');

                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

            $(document).on("click", ".editProfile", function(e) {
                e.preventDefault();
                var url = '{{ route('signup-user.editProfile', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $('#pricingModal .modal-dialog').removeClass('modal-md');
                $('#pricingModal .modal-dialog').removeClass('modal-lg');
                $('#pricingModal .modal-dialog').addClass('modal-xl');
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                var url = '{{ route('signup-user.show', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $('#pricingModal .modal-dialog').removeClass('modal-md');
                $('#pricingModal .modal-dialog').addClass('modal-lg');
                $('#pricingModal .modal-dialog').removeClass('modal-xl');
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });
            $(document).on("click", ".mergeUser", function(e) {
                e.preventDefault();
                var url = '{{ route('signup-user.mergeUser', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $('#pricingModal .modal-dialog').removeClass('modal-lg');
                $('#pricingModal .modal-dialog').addClass('modal-md');
                $('#pricingModal .modal-dialog').removeClass('modal-xl');
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

            $(".resetPassword").click(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "Are you sure to reset password of this member?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Assign!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var userId = $(this).data('id');
                        var url =
                            '{{ route('signup-user.resetPassword', [$society, $conference]) }}';
                        var data = {
                            userId: userId
                        };
                        $.post(url, data, function(response) {
                            if (response.type == 'success') {
                                notyf.success(response.message);
                            } else {
                                notyf.error(response.message);
                            }
                            window.location.reload();
                        });
                    }
                });
            });
        });
    </script>
@endsection
