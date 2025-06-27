@extends('backend.layouts.conference.main')
@section('title')
    Create Roles
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-1">Roles List</h4>

        <p class="mb-6">
            A role provided access to predefined menus and features so that depending on <br />
            assigned role an administrator can have access to what user needs.
        </p>
        <!-- Role cards -->
        <div class="row g-6">
            @foreach ($roles as $role)
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-normal mb-0 text-body">Total 4 users</h6>
                            </div>
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="role-heading">
                                    <h5 class="mb-1">{{ $role->name }}</h5>
                                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'Edit Role'))
                                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#pricingModal"
                                            data-role-id="{{ $role->id }}" class="role-edit-modal"><span>Edit
                                                Role</span></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @if (auth()->user()->hasConferencePermissionBlade($conference, 'Add Role'))
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card h-100">
                        <div class="row h-100">
                            <div class="col-sm-5">
                                <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4">
                                    <img src="{{ asset('backend/assets/img/illustrations/add-new-roles.png') }}"
                                        class="img-fluid" alt="Image" width="83" />
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="card-body text-sm-end text-center ps-sm-0">
                                    <button data-bs-toggle="modal" data-bs-target="#pricingModal"
                                        class="btn btn-sm btn-primary mb-4 text-nowrap add-new-role">
                                        Add New Role
                                    </button>
                                    <p class="mb-0">
                                        Add new role, <br />
                                        if it doesn't exist.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-12">
                <h4 class="mt-6 mb-1">Total users with their roles</h4>
                <p class="mb-0">Find all of your company’s administrator accounts and their associate roles.</p>
            </div>
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                        <div
                            class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                            <div class="dt-buttons btn-group flex-wrap mb-0">
                            </div>
                        </div>
                    </div>
                    <table class="datatables-basic table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                @php
                                    $role = $user
                                        ->conferenceRoles()
                                        ->wherePivot('conference_id', $conference->id)
                                        ->first();
                                @endphp

                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $user->fullName($user) }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($role)
                                            {{ $role->name }}
                                        @else
                                            User
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="icon-base ti tabler-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if (auth()->user()->hasConferencePermissionBlade($conference, 'Assign Role'))
                                                    <a class="dropdown-item assignrole"href="javascript:;"
                                                        data-bs-toggle="modal" data-bs-target="#pricingModal"
                                                        data-id="{{ $user->id }}"><i
                                                            class="icon-base ti tabler-pencil me-1"></i>Assign Role</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>

    </div>
@endsection
@section('scripts')
    <script>
        const editRoleBaseUrl = "{{ route('roles.edit', [$society, $conference, 'ROLE_ID']) }}";
    </script>

    <script>
        $(document).ready(function() {

            $(document).on("click", ".add-new-role", function(e) {
                e.preventDefault();
                var url =
                    '{{ route('roles.create', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                $('#pricingModal .modal-dialog').removeClass('modal-md');
                $('#pricingModal .modal-dialog').addClass('modal-xl');
                $.get(url, function(response) {
                    setTimeout(function() { 
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });
            $(document).on("click", ".role-edit-modal", function(e) {
                e.preventDefault();

                const roleId = $(this).data('role-id');
                const url = editRoleBaseUrl.replace('ROLE_ID', roleId);

                $('#modalContent').html(`
        <div class="modal-body text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
                $('#pricingModal .modal-dialog').removeClass('modal-md');
                $('#pricingModal .modal-dialog').addClass('modal-xl');
                $.get(url, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

            $(document).on("click", ".assignrole", function(e) {
                e.preventDefault();

                var url = '{{ route('assignRoleForm', [$society, $conference]) }}';
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
                $('#pricingModal .modal-dialog').removeClass('modal-xl');
                $('#pricingModal .modal-dialog').addClass('modal-md');
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

        });
    </script>
@endsection
