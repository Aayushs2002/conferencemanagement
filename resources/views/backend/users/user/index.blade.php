@extends('backend.layouts.main')
@section('title')
    User
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
                        {{-- <div class="btn-group me-2">
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
                        </div> --}}
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
                        <th>Joined Society</th>
                        <th>Last Login</th>
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
                                {{-- {{ $user->societies->abbreviation }} --}}
                                @forelse ($user->societies as $society)
                                    {{ $society->abbreviation }}@if (!$loop->last)
                                        ,
                                    @endif
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td>
                                    {{ !empty($user->last_login_at) ? \Carbon\Carbon::parse($user->last_login_at)->format('d M, Y, h:i a') : '-' }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item viewData" data-id="{{ $user->id }}"
                                                data-bs-toggle="modal" data-bs-target="#pricingModal">
                                                <i class="icon-base ti tabler-eye me-1"></i> View
                                            </a>
                                            <a class="dropdown-item joinSociety" data-id="{{ $user->id }}"
                                                data-bs-toggle="modal" data-bs-target="#pricingModal">
                                                <i class="icon-base ti tabler-pencil me-1"></i> Join Society
                                            </a>
                                            @if (is_super_admin())
                                                <hr>
                                                <form action="{{ route('user.destroy', $user->id) }}" method="POST">
                                                    @method('delete')
                                                    @csrf
                                                    <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                            class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                                </form>
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

                $(document).on("click", ".viewData", function(e) {
                    e.preventDefault();
                    var url = '{{ route('user.show') }}';
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
                $(document).on("click", ".joinSociety", function(e) {
                    e.preventDefault();
                    var url = '{{ route('user.join-society') }}';
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

            });
        </script>
    @endsection
