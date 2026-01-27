@extends('backend.layouts.conference.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-1"><i class="ti ti-building-skyscraper me-2"></i>International Accommodation Management</h4>
                <p class="text-muted">Manage and monitor accommodation details for all international participants</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Accommodations
                                </div>
                                <div class="h5 mb-0 font-weight-bold">{{ $accommodations->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="ti ti-building display-4 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Awaiting Admin Setup
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-warning">{{ $invitedAwaitingAccommodation->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="ti ti-user-plus display-4 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Pending Self-Fill
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-info">{{ $selfRegisteredNeedingAccommodation->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="ti ti-alert-triangle display-4 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Airport Pickup Required
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-success">
                                    {{ $accommodations->where('airport_pickup_required', true)->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="ti ti-car display-4 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Accommodation Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <h5 class="mb-0">Accommodation Records</h5>
                        <div>
                            <a href="{{ route('conference.accommodation.export', [$society, $conference]) }}" class="btn btn-success btn-sm me-2">
                                <i class="ti ti-file-spreadsheet"></i> Export Excel
                            </a>
                            <button class="btn btn-primary btn-sm" onclick="window.print()">
                                <i class="ti ti-printer"></i> Print
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Advanced Filters -->
                        <div class="row mb-4 g-3">
                            <div class="col-md-3">
                                <label for="countryFilter" class="form-label fw-semibold">
                                    <i class="ti ti-world me-1"></i>Filter by Country
                                </label>
                                <select class="form-select" id="countryFilter">
                                    <option value="">All Countries</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->country_name }}">{{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="hotelFilter" class="form-label fw-semibold">
                                    <i class="ti ti-building me-1"></i>Filter by Hotel
                                </label>
                                <select class="form-select" id="hotelFilter">
                                    <option value="">All Hotels</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->name }}">{{ $hotel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="dateRangeFilter" class="form-label fw-semibold">
                                    <i class="ti ti-calendar me-1"></i>Filter by Date Range
                                </label>
                                <input type="text" class="form-control" id="dateRangeFilter" placeholder="Select date range">
                            </div>
                            <div class="col-md-3">
                                <label for="pickupFilter" class="form-label fw-semibold">
                                    <i class="ti ti-car me-1"></i>Airport Pickup
                                </label>
                                <select class="form-select" id="pickupFilter">
                                    <option value="">All</option>
                                    <option value="required">Required</option>
                                    <option value="not-required">Not Required</option>
                                </select>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="accommodationTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th>Participant</th>
                                        <th>Country</th>
                                        <th>Hotel</th>
                                        <th>Room Type</th>
                                        <th>Arrival</th>
                                        <th>Departure</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th class="text-center">Pickup</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($accommodations as $accommodation)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong>
                                                        {{ $accommodation->conferenceRegistration->user->f_name }}
                                                        {{ $accommodation->conferenceRegistration->user->l_name }}
                                                    </strong>
                                                    <small class="text-muted">
                                                        <i class="ti ti-mail"></i> 
                                                        {{ $accommodation->conferenceRegistration->user->email }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-primary">
                                                    {{ $accommodation->conferenceRegistration->user->userDetail->country->country_name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $accommodation->hotel->name }}</td>
                                            <td>
                                                @if($accommodation->room_type)
                                                    <span class="badge bg-label-info">{{ ucfirst($accommodation->room_type) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ \Carbon\Carbon::parse($accommodation->arrival_date)->format('M d, Y') }}</span>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($accommodation->arrival_time)->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ \Carbon\Carbon::parse($accommodation->departure_date)->format('M d, Y') }}</span>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($accommodation->departure_time)->format('h:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($accommodation->check_in_date)
                                                    {{ \Carbon\Carbon::parse($accommodation->check_in_date)->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($accommodation->check_out_date)
                                                    {{ \Carbon\Carbon::parse($accommodation->check_out_date)->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($accommodation->airport_pickup_required)
                                                    <span class="badge bg-success" title="Airport pickup required">
                                                        <i class="ti ti-check"></i>
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary" title="No airport pickup">
                                                        <i class="ti ti-x"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($accommodation->created_by_admin ?? false)
                                                    <span class="badge bg-label-warning">
                                                        <i class="ti ti-user-shield"></i> Admin
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-success">
                                                        <i class="ti ti-user-check"></i> Self-filled
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('conference.accommodation.show', [$society, $conference, $accommodation]) }}" 
                                                   class="btn btn-sm btn-info" title="View Details">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center py-4">
                                                <i class="ti ti-inbox display-4 text-muted d-block mb-2"></i>
                                                <p class="text-muted">No accommodation records found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($invitedAwaitingAccommodation->count() > 0)
                            <div class="mt-4">
                                <div class="alert alert-danger border-left-danger shadow">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ti ti-user-plus display-6 me-3"></i>
                                        <div>
                                            <h5 class="mb-0">
                                                Invited Participants Awaiting Admin Setup
                                            </h5>
                                            <p class="mb-0 small">{{ $invitedAwaitingAccommodation->count() }} participant(s) need accommodation details filled by admin</p>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered bg-white">
                                            <thead class="table-danger">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Country</th>
                                                    <th>Invitation Accepted</th>
                                                    <th>Registrant Type</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($invitedAwaitingAccommodation as $registration)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <strong>{{ $registration->user->f_name }} {{ $registration->user->l_name }}</strong>
                                                        </td>
                                                        <td>
                                                            <small>{{ $registration->user->email }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-label-primary">
                                                                {{ $registration->user->userDetail->country->country_name ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small>{{ $registration->invitation_accepted_at->format('M d, Y H:i') }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $registration->registrant_type_text }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-primary" onclick="createAccommodation({{ $registration->user->id }})">
                                                                <i class="ti ti-plus"></i> Create
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($selfRegisteredNeedingAccommodation->count() > 0)
                            <div class="mt-4">
                                <div class="alert alert-warning border-left-warning shadow">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ti ti-alert-triangle display-6 me-3"></i>
                                        <div>
                                            <h5 class="mb-0">
                                                Self-Registered Participants Needing to Fill Details
                                            </h5>
                                            <p class="mb-0 small">{{ $selfRegisteredNeedingAccommodation->count() }} participant(s) need to fill their own accommodation details</p>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered bg-white">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Country</th>
                                                    <th>Registration Date</th>
                                                    <th>Registrant Type</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selfRegisteredNeedingAccommodation as $registration)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <strong>{{ $registration->user->f_name }} {{ $registration->user->l_name }}</strong>
                                                        </td>
                                                        <td>
                                                            <small>{{ $registration->user->email }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-label-primary">
                                                                {{ $registration->user->userDetail->country->country_name ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small>{{ $registration->created_at->format('M d, Y H:i') }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">{{ $registration->registrant_type_text }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-warning" onclick="sendReminder({{ $registration->user->id }})">
                                                                <i class="ti ti-mail"></i> Remind
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal for Accommodation Creation -->
<div class="modal fade" id="accommodationModal" tabindex="-1" aria-labelledby="accommodationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="accommodationModalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }
    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }
    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }
    @media print {
        .btn, .card-header .btn, .alert {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with enhanced options
        var table = $('#accommodationTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[1, 'asc']], // Sort by participant name
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-secondary btn-sm'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success btn-sm',
                    title: 'International Accommodations - {{ $conference->name }}',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    title: 'International Accommodations',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }
                },
                {
                    extend: 'print',
                    className: 'btn btn-info btn-sm',
                    title: 'International Accommodations - {{ $conference->name }}',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                    }
                }
            ],
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    column.search('').draw();
                });
            }
        });

        // Move buttons to custom location
        table.buttons().container().appendTo('#accommodationTable_wrapper .col-md-6:eq(0)');

        // Initialize Date Range Picker
        $('#dateRangeFilter').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'MMM DD, YYYY'
            }
        });

        $('#dateRangeFilter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY'));
            filterByDateRange(picker.startDate, picker.endDate);
        });

        $('#dateRangeFilter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $.fn.dataTable.ext.search.pop();
            table.draw();
        });

        // Country Filter
        $('#countryFilter').on('change', function() {
            table.column(2).search(this.value).draw();
        });

        // Hotel Filter
        $('#hotelFilter').on('change', function() {
            table.column(3).search(this.value).draw();
        });

        // Airport Pickup Filter
        $('#pickupFilter').on('change', function() {
            var value = this.value;
            if (value === '') {
                table.column(9).search('').draw();
            } else if (value === 'required') {
                table.column(9).search('✓', true, false).draw();
            } else if (value === 'not-required') {
                table.column(9).search('✕', true, false).draw();
            }
        });

        // Custom filtering function for date range
        function filterByDateRange(start, end) {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                // Column indexes: Arrival=5, Departure=6, Check-in=7, Check-out=8
                var arrivalDate = moment(data[5], 'MMM DD, YYYY');
                var departureDate = moment(data[6], 'MMM DD, YYYY');
                var checkInDate = moment(data[7], 'MMM DD, YYYY');
                var checkOutDate = moment(data[8], 'MMM DD, YYYY');
                
                if (
                    (arrivalDate.isValid() && arrivalDate.isSameOrAfter(start, 'day') && arrivalDate.isSameOrBefore(end, 'day')) ||
                    (departureDate.isValid() && departureDate.isSameOrAfter(start, 'day') && departureDate.isSameOrBefore(end, 'day')) ||
                    (checkInDate.isValid() && checkInDate.isSameOrAfter(start, 'day') && checkInDate.isSameOrBefore(end, 'day')) ||
                    (checkOutDate.isValid() && checkOutDate.isSameOrAfter(start, 'day') && checkOutDate.isSameOrBefore(end, 'day')) ||
                    (arrivalDate.isValid() && departureDate.isValid() && arrivalDate.isSameOrBefore(start, 'day') && departureDate.isSameOrAfter(end, 'day'))
                ) {
                    return true;
                }
                return false;
            });
            table.draw();
        }
    });

    // Function to send accommodation reminder
    function sendReminder(userId) {
        Swal.fire({
            title: 'Send Accommodation Reminder?',
            text: 'This will send an email reminder to the participant to fill their accommodation details.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, send reminder!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route("conference.accommodation.sendReminder", [$society, $conference]) }}',
                    type: 'POST',
                    data: { user_id: userId },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Sending...',
                            text: 'Please wait while we send the reminder.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let message = 'An error occurred while sending the reminder.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            }
        });
    }

    // Function to create accommodation for invited participant
    function createAccommodation(userId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Load the accommodation creation form
        $.ajax({
            url: '{{ route("conference.accommodation.createForInvited", [$society, $conference]) }}',
            type: 'POST',
            data: { user_id: userId },
            beforeSend: function() {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Please wait while we load the accommodation form.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.close();
                // Show the form in a modal
                $('#accommodationModalContent').html(response);
                $('#accommodationModal').modal('show');
            },
            error: function(xhr) {
                let message = 'An error occurred while loading the form.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    }
</script>
@endsection