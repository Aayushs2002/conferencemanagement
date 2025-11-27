@extends('backend.layouts.conference.main')

@section('title')
    Workshop
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Workshop</h5>
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
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Workshop'))
                            <a href="{{ route('workshop.create', [$society, $conference]) }}" class="btn btn-primary"
                                tabindex="0">
                                <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Date/Days</th>
                        <th>Time</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>No. Of Attendees</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workshops as $workshop)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $workshop->workshop_title }}</td>
                            <td>{{ \Carbon\Carbon::parse($workshop->start_date)->format('d M, Y') }}
                                {{ !empty($workshop->end_date) ? ' - ' . \Carbon\Carbon::parse($workshop->end_date)->format('d M, Y') : '' }}

                            </td>

                            <td>{{ $workshop->start_time }} - {{ $workshop->end_time }}</td>
                            <td>{{ !empty($workshop->registration_deadline) ? \Carbon\Carbon::parse($workshop->registration_deadline)->format('d M, Y') : '-' }}
                            </td>
                            <td>
                                <span class="badge {{ $workshop->getStatusBadgeClass() }}">
                                    {{ $workshop->getStatusLabel() }}
                                </span>
                                @if ($workshop->admin_remarks)
                                    <i class="ti tabler-message-circle text-info" data-bs-toggle="tooltip"
                                        title="{{ $workshop->admin_remarks }}"></i>
                                @endif
                            </td>
                            <td>
                                <a
                                    href="{{ route('workshop.workshop-registration.index', [$society, $conference, $workshop]) }}">
                                    {{ $workshop->registrations->where('verified_status', 1)->where('status', 1)->count() }}
                                </a>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Workshop'))
                                            <a class="dropdown-item"
                                                href="{{ route('workshop.edit', [$society, $conference, $workshop]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Workshop'))
                                            <a class="dropdown-item viewData" data-id="{{ $workshop->id }}"
                                                data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                    class="icon-base ti tabler-eye me-1 "></i> View</a>
                                        @endif

                                        {{-- Admin Approval Actions - Only for Type 1 & 2 (Admins) --}}
                                        @if (
                                            (current_user()->type == 1 || current_user()->type == 2) &&
                                                in_array($workshop->approval_status, ['pending', 'correction_needed']))
                                            <hr>
                                            <h6 class="dropdown-header">Approval Actions</h6>
                                            <a href="#" class="dropdown-item text-success approve-btn"
                                                data-workshop-id="{{ $workshop->id }}"
                                                data-workshop-title="{{ $workshop->workshop_title }}">
                                                <i class="icon-base ti tabler-check me-1"></i> Approve
                                            </a>
                                            <a href="#" class="dropdown-item text-warning request-correction-btn"
                                                data-workshop-id="{{ $workshop->id }}">
                                                <i class="icon-base ti tabler-edit me-1"></i> Request Correction
                                            </a>
                                            <a href="#" class="dropdown-item text-danger reject-btn"
                                                data-workshop-id="{{ $workshop->id }}">
                                                <i class="icon-base ti tabler-x me-1"></i> Reject
                                            </a>
                                            <hr>
                                        @endif

                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add/Update Registration Price'))
                                            <a href="#" class="dropdown-item allocatePrice"
                                                data-id="{{ $workshop->id }}" data-bs-toggle="modal"
                                                data-bs-target="#pricingModal"><i
                                                    class="icon-base ti tabler-cash me-1"></i>Registration Price</a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'View Workshop Trainer'))
                                            <a href="{{ route('workshop.workshop-trainer.index', [$society, $conference, $workshop]) }}"
                                                class="dropdown-item mb-1"><i
                                                    class="icon-base ti tabler-circle-letter-t me-1"></i>Trainers</a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'View Workshop Registrant'))
                                            @if ($workshop->registrations->where('status', 1)->isNotEmpty())
                                                <a href="{{ route('workshop.workshop-registration.index', [$society, $conference, $workshop]) }}"
                                                    class="dropdown-item  mb-1"><i
                                                        class="icon-base ti tabler-user me-1"></i>Registrants</a>
                                            @endif
                                        @endif
                                        <hr>
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Delete Workshop'))
                                            <form
                                                action="{{ route('workshop.destroy', [$society, $conference, $workshop->id]) }}"
                                                method="POST">
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
            <div class="modal-dialog modal-xl modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>

        {{-- Reject Workshop Modal --}}
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Workshop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="rejectForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="reject_remarks" class="form-label">Reason for Rejection <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="remarks" id="reject_remarks" rows="4"
                                    placeholder="Please provide reason for rejection..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject Workshop</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Request Correction Modal --}}
        <div class="modal fade" id="correctionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Request Correction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="correctionForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="correction_remarks" class="form-label">Corrections Needed <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="remarks" id="correction_remarks" rows="4"
                                    placeholder="Please specify what needs to be corrected..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">Send for Correction</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            $(document).on("click", ".allocatePrice", function(e) {
                e.preventDefault();
                $(".modal-dialog").removeClass('custom-modal-width');
                var url = '{{ route('workshop.allocatePriceForm', [$society, $conference]) }}';
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
                    }, 1000);
                });
            });

            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                $(".modal-dialog").removeClass('custom-modal-width');
                var url = '{{ route('workshop.view', [$society, $conference]) }}';
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
                    }, 1000);
                });
            });

            // Reject Workshop
            $(document).on("click", ".reject-btn", function(e) {
                e.preventDefault();
                var workshopId = $(this).data('workshop-id');
                // alert(workshopId);
                // console.log(workshopId)
                var actionUrl = '{{ route('workshop.reject', [$society, $conference, ':workshop']) }}';
                actionUrl = actionUrl.replace(':workshop', workshopId);
                $('#rejectForm').attr('action', actionUrl);
                $('#rejectModal').modal('show');
            });

            // Request Correction
            $(document).on("click", ".request-correction-btn", function(e) {
                e.preventDefault();
                var workshopId = $(this).data('workshop-id');
                var actionUrl =
                    '{{ route('workshop.requestCorrection', [$society, $conference, ':workshop']) }}';
                actionUrl = actionUrl.replace(':workshop', workshopId);
                $('#correctionForm').attr('action', actionUrl);
                $('#correctionModal').modal('show');
            });

            // Approve Workshop with SweetAlert
            $(document).on("click", ".approve-btn", function(e) {
                e.preventDefault();
                var workshopId = $(this).data('workshop-id');
                var workshopTitle = $(this).data('workshop-title');
                var approveUrl = '{{ route('workshop.approve', [$society, $conference, ':workshop']) }}';
                approveUrl = approveUrl.replace(':workshop', workshopId);

                Swal.fire({
                    title: 'Approve Workshop?',
                    html: 'Are you sure you want to approve<br><strong>"' + workshopTitle +
                        '"</strong>?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create and submit form
                        var form = $('<form>', {
                            'method': 'POST',
                            'action': approveUrl
                        });

                        var csrfToken = $('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        });

                        form.append(csrfToken);
                        $('body').append(form);
                        form.submit();
                    }
                });
            });

        });
    </script>
@endsection
