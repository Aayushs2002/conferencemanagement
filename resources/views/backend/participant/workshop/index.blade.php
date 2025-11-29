@extends('backend.layouts.conference.main')

@section('title')
    My Workshop Applications
@endsection

@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">My Workshop Applications</h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <a href="{{ route('my-society.conference.my-workshop.create', [$society, $conference]) }}"
                            class="btn btn-primary" tabindex="0">
                            <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Apply for Workshop</span>
                        </a>
                    </div>
                </div>
            </div>

            @if ($workshops->isEmpty())
                <div class="card-body text-center py-5">
                    <i class="ti tabler-calendar-x text-muted" style="font-size: 64px;"></i>
                    <h5 class="mt-3">No Workshop Applications Yet</h5>
                    <p class="text-muted">You haven't submitted any workshop applications. Click "Apply for Workshop" to get
                        started.</p>
                    <a href="{{ route('my-society.conference.my-workshop.create', [$society, $conference]) }}"
                        class="btn btn-primary mt-2">
                        <i class="ti tabler-plus me-1"></i> Apply for Workshop
                    </a>
                </div>
            @else
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Workshop Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Admin Feedback</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($workshops as $workshop)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>
                                    <div class="fw-bold">{{ $workshop->workshop_title }}</div>
                                    <small class="text-muted">{{ $workshop->workshop_type == 1 ? 'Paid' : 'Free' }}</small>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($workshop->start_date)->format('d M, Y') }}
                                    @if ($workshop->end_date)
                                        - {{ \Carbon\Carbon::parse($workshop->end_date)->format('d M, Y') }}
                                    @endif
                                </td>
                                <td>{{ $workshop->start_time }} - {{ $workshop->end_time }}</td>
                                <td>
                                    <span class="badge {{ $workshop->getStatusBadgeClass() }}">
                                        {{ $workshop->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @if ($workshop->admin_remarks)
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                            data-bs-target="#remarksModal{{ $workshop->id }}">
                                            <i class="ti tabler-message-circle me-1"></i> View Feedback
                                        </button>

                                        <!-- Remarks Modal -->
                                        <div class="modal fade" id="remarksModal{{ $workshop->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Admin Feedback</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <i class="ti tabler-info-circle me-2"></i>
                                                            <strong>Status:</strong> {{ $workshop->getStatusLabel() }}
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Feedback:</label>
                                                            <p>{{ $workshop->admin_remarks }}</p>
                                                        </div>
                                                        @if ($workshop->reviewed_by)
                                                            <small class="text-muted">
                                                                Reviewed on
                                                                {{ $workshop->reviewed_at->format('d M, Y h:i A') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        @if ($workshop->approval_status === 'correction_needed')
                                                            <a href="{{ route('my-society.conference.my-workshop.edit', [$society, $conference, $workshop]) }}"
                                                                class="btn btn-primary">
                                                                <i class="ti tabler-edit me-1"></i> Edit & Resubmit
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item viewData" data-id="{{ $workshop->id }}"
                                                data-bs-toggle="modal" data-bs-target="#viewModal">
                                                <i class="icon-base ti tabler-eye me-1"></i> View Details
                                            </a>

                                            @if (in_array($workshop->approval_status, ['pending', 'correction_needed']))
                                                <a class="dropdown-item"
                                                    href="{{ route('my-society.conference.my-workshop.edit', [$society, $conference, $workshop]) }}">
                                                    <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                                </a>
                                            @endif

                                            {{-- @if ($workshop->approval_status === 'approved') --}}
                                            <hr>
                                            <h6 class="dropdown-header">Workshop Management</h6>
                                            @if ($workshop->workshop_type == 1)
                                                <a href="#" class="dropdown-item allocatePrice"
                                                    data-id="{{ $workshop->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#pricingModal">
                                                    <i class="icon-base ti tabler-cash me-1"></i> Registration Price
                                                </a>
                                            @endif
                                            <a href="{{ route('my-society.conference.my-workshop.trainer.index', [$society, $conference, $workshop]) }}"
                                                class="dropdown-item">
                                                <i class="icon-base ti tabler-circle-letter-t me-1"></i> Manage Trainers
                                            </a>
                                            @if ($workshop->registrations->where('status', 1)->where('registant_type', 1)->isNotEmpty())
                                                <a href="{{ route('my-society.conference.my-workshop.registration.index', [$society, $conference, $workshop]) }}"
                                                    class="dropdown-item">
                                                    <i class="icon-base ti tabler-user me-1"></i> View Registrants
                                                    ({{ $workshop->registrations->where('verified_status', 1)->where('registant_type', 1)->where('status', 1)->count() }})
                                                </a>
                                            @endif
                                            {{-- @endif --}}

                                            @if (in_array($workshop->approval_status, ['pending', 'rejected']))
                                                <hr>
                                                <form
                                                    action="{{ route('my-society.conference.my-workshop.destroy', [$society, $conference, $workshop]) }}"
                                                    method="POST">
                                                    @method('delete')
                                                    @csrf
                                                    <a class="dropdown-item text-danger delete" href="javascript:void(0);">
                                                        <i class="icon-base ti tabler-trash me-1"></i> Delete
                                                    </a>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- View Details Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" id="modalContent">
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Modal -->
        <div class="modal fade" id="pricingModal" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="pricingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" id="pricingModalContent">
                    <div class="loader-box">
                        <div class="loader-5"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // View workshop details
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                var url = '{{ route('my-society.conference.my-workshop.view', [$society, $conference]) }}';
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
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 500);
                });
            });

            // Allocate pricing
            $(document).on("click", ".allocatePrice", function(e) {
                e.preventDefault();
                var url =
                    '{{ route('my-society.conference.my-workshop.allocatePriceForm', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                $('#pricingModalContent').html(`
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
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#pricingModalContent').html(response);
                    }, 500);
                }).fail(function(xhr) {
                    // console.log(xhr)
                    var errorMsg = xhr.responseJSON?.error || 'Failed to load pricing form';
                    $('#pricingModalContent').html(`
                        <div class="modal-header">
                            <h5 class="modal-title">Error</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger">${errorMsg}</div>
                        </div>
                    `);
                });
            });
        });
    </script>
@endsection
