@extends('backend.layouts.conference.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Accommodation Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="countryFilter" class="form-label">Filter by Country</label>
                                <select class="form-select" id="countryFilter">
                                    <option value="">All Countries</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->country_name }}">{{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="hotelFilter" class="form-label">Filter by Hotel</label>
                                <select class="form-select" id="hotelFilter">
                                    <option value="">All Hotels</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->name }}">{{ $hotel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="dateRangeFilter" class="form-label">Filter by Date Range</label>
                                <input type="text" class="form-control" id="dateRangeFilter" placeholder="Select date range">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="accommodationTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Participant Name</th>
                                        <th>Country</th>
                                        <th>Hotel</th>
                                        <th>Arrival</th>
                                        <th>Departure</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accommodations as $accommodation)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $accommodation->conferenceRegistration->user->f_name }}
                                                {{ $accommodation->conferenceRegistration->user->l_name }}
                                            </td>
                                            <td>{{ $accommodation->conferenceRegistration->user->userDetail->country->country_name ?? 'N/A' }}</td>
                                            <td>{{ $accommodation->hotel->name }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($accommodation->arrival_date)->format('Y-m-d') }}
                                                <br>
                                                <small>{{ \Carbon\Carbon::parse($accommodation->arrival_time)->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($accommodation->departure_date)->format('Y-m-d') }}
                                                <br>
                                                <small>{{ \Carbon\Carbon::parse($accommodation->departure_time)->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($accommodation->check_in_date)
                                                    {{ \Carbon\Carbon::parse($accommodation->check_in_date)->format('Y-m-d') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($accommodation->check_out_date)
                                                    {{ \Carbon\Carbon::parse($accommodation->check_out_date)->format('Y-m-d') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Completed</span>
                                                @if($accommodation->created_by_admin ?? false)
                                                    <br><small class="text-muted">Created by Admin</small>
                                                @else
                                                    <br><small class="text-muted">Self-filled</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('conference.accommodation.show', [$society, $conference, $accommodation]) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="ti ti-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($invitedAwaitingAccommodation->count() > 0)
                            <div class="mt-4">
                                <h6 class="text-danger">
                                    <i class="ti ti-user-plus"></i> 
                                    Invited Participants Awaiting Admin Setup ({{ $invitedAwaitingAccommodation->count() }})
                                </h6>
                                <div class="alert alert-danger">
                                    <p class="mb-2">The following invited participants need accommodation details filled by admin:</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Country</th>
                                                    <th>Invitation Accepted</th>
                                                    <th>Registrant Type</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($invitedAwaitingAccommodation as $registration)
                                                    <tr>
                                                        <td>
                                                            {{ $registration->user->f_name }} {{ $registration->user->l_name }}
                                                        </td>
                                                        <td>{{ $registration->user->userDetail->country->country_name ?? 'N/A' }}</td>
                                                        <td>
                                                            <small>{{ $registration->invitation_accepted_at->format('M d, Y H:i') }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $registration->registrant_type_text }}</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary" onclick="createAccommodation({{ $registration->user->id }})">
                                                                <i class="ti ti-plus"></i> Create Accommodation
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
                                <h6 class="text-warning">
                                    <i class="ti ti-alert-triangle"></i> 
                                    Self-Registered Participants Needing to Fill Details ({{ $selfRegisteredNeedingAccommodation->count() }})
                                </h6>
                                <div class="alert alert-warning">
                                    <p class="mb-2">The following self-registered participants need to fill their own accommodation details:</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Country</th>
                                                    <th>Registration Date</th>
                                                    <th>Registrant Type</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selfRegisteredNeedingAccommodation as $registration)
                                                    <tr>
                                                        <td>
                                                            {{ $registration->user->f_name }} {{ $registration->user->l_name }}
                                                        </td>
                                                        <td>{{ $registration->user->userDetail->country->country_name ?? 'N/A' }}</td>
                                                        <td>
                                                            <small>{{ $registration->created_at->format('M d, Y H:i') }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">{{ $registration->registrant_type_text }}</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning" onclick="sendReminder({{ $registration->user->id }})">
                                                                <i class="ti ti-mail"></i> Send Reminder
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#accommodationTable').DataTable({
            // dom: 'Bfrtip',
            // // buttons: [
            // //     'copy', 'csv', 'excel', 'pdf', 'print'
            // // ],
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    column.search('').draw();
                });
            }
        });

        // Initialize Date Range Picker
        $('#dateRangeFilter').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#dateRangeFilter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            filterByDateRange(picker.startDate, picker.endDate);
        });

        $('#dateRangeFilter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
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

        // Custom filtering function for date range
        function filterByDateRange(start, end) {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                // Updated column indexes: Arrival=4, Departure=5, Check-in=6, Check-out=7
                var arrivalDate = moment(data[4], 'YYYY-MM-DD');
                var departureDate = moment(data[5], 'YYYY-MM-DD');
                var checkInDate = moment(data[6], 'YYYY-MM-DD');
                var checkOutDate = moment(data[7], 'YYYY-MM-DD');
                
                if (
                    (arrivalDate.isSameOrAfter(start, 'day') && arrivalDate.isSameOrBefore(end, 'day')) ||
                    (departureDate.isSameOrAfter(start, 'day') && departureDate.isSameOrBefore(end, 'day')) ||
                    (checkInDate.isValid() && checkInDate.isSameOrAfter(start, 'day') && checkInDate.isSameOrBefore(end, 'day')) ||
                    (checkOutDate.isValid() && checkOutDate.isSameOrAfter(start, 'day') && checkOutDate.isSameOrBefore(end, 'day')) ||
                    (arrivalDate.isSameOrBefore(start, 'day') && departureDate.isSameOrAfter(end, 'day'))
                ) {
                    return true;
                }
                return false;
            });
            table.draw();
            $.fn.dataTable.ext.search.pop(); // Clean up the filter after use
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