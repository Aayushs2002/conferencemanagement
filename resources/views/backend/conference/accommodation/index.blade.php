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
                    </div>
                </div>
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
                var arrivalDate = moment(data[4], 'YYYY-MM-DD');
                var departureDate = moment(data[5], 'YYYY-MM-DD');
                
                if (
                    (arrivalDate.isSameOrAfter(start, 'day') && arrivalDate.isSameOrBefore(end, 'day')) ||
                    (departureDate.isSameOrAfter(start, 'day') && departureDate.isSameOrBefore(end, 'day')) ||
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
</script>
@endsection