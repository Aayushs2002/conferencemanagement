@extends('backend.layouts.society.main')

@section('title')
    Conference
@endsection
@section('content')
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Conference</h5>
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
                        <a href="{{ route('conference.create', $society) }}" class="btn btn-primary" tabindex="0">
                            <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New</span>
                        </a>
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Conference Theme</th>
                        <th>Start Date</th>
                        <th>Venue Name</th>
                        <th>Organizer Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($conferences as $conference)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $conference->conference_theme }}</td>
                            <td>{{ $conference->start_date }}</td>
                            <td>{{ $conference->ConferenceVenueDetail->venue_name }}</td>
                            <td>{{ $conference->ConferenceOrganizer->organizer_name }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ route('conference.edit', [$society, $conference]) }}"><i
                                                class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        <a class="dropdown-item viewData" data-id="{{ $conference->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                class="icon-base ti tabler-eye me-1 "></i> View</a>
                                        <a href="#" class="dropdown-item priceForm" data-id="{{ $conference->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                class="icon-base ti tabler-cash me-1"></i>Registration Price</a>
                                        {{-- <hr> --}}
                                        {{-- <form action="{{ route('conference.destroy', $conference->id) }}" method="POST">
                                            @method('delete')
                                            @csrf
                                            <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                    class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                        </form> --}}
                                    </div>
                                    <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                        class="btn btn-info btn-sm mt-1">Open Portal</a>
                                </div>
                                {{-- <a href="#" class="btn btn-success btn-sm mt-3 priceForm"
                                    data-id="{{ $conference->id }}" data-bs-toggle="modal"
                                    data-bs-target="#pricingModal">Registration Price</a> --}}
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
    </div>
    <style>
        ::-webkit-scrollbar {
            display: none;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                var url = '{{ route('conference.show') }}';
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

            $(document).on("click", ".priceForm", function(e) {
                e.preventDefault();
                $(".modal-dialog").addClass('custom-modal-width');
                var url = '{{ route('conference.priceForm') }}';
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
        });
    </script>
@endsection
