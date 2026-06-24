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

            <!-- Filter Tabs -->
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" 
                        data-bs-target="#active-conferences" aria-controls="active-conferences" aria-selected="true">
                        <i class="icon-base ti tabler-calendar-check me-1"></i>
                        <span class="d-none d-sm-inline-block">Active Conferences</span>
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1">{{ $activeConferences->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" 
                        data-bs-target="#archived-conferences" aria-controls="archived-conferences" aria-selected="false">
                        <i class="icon-base ti tabler-archive me-1"></i>
                        <span class="d-none d-sm-inline-block">Archived Conferences</span>
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-secondary ms-1">{{ $archivedConferences->count() }}</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Active Conferences Tab -->
                <div class="tab-pane fade show active" id="active-conferences" role="tabpanel">
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
                            @foreach ($activeConferences as $conference)
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
                                                <a href="#" class="dropdown-item addOn" data-id="{{ $conference->id }}"
                                                    data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                        class="icon-base ti tabler-cash me-1"></i>Add On</a>
                                                <a href="#" class="dropdown-item conferenceSetting"
                                                    data-id="{{ $conference->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#pricingModal"><i
                                                        class="icon-base ti tabler-cash me-1"></i>Conference Setting</a>
                                                <hr>
                                                <form action="{{ route('conference.archive', [$society, $conference]) }}" method="POST">
                                                    @csrf
                                                    <a class="dropdown-item text-warning archive-conference" href="javascript:void(0);"><i
                                                            class="icon-base ti tabler-archive me-1"></i> Archive</a>
                                                </form>
                                            </div>
                                            <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                                class="btn btn-info btn-sm mt-1">Open Portal</a>
                                        </div>
                                    </td> 
                                </tr>
                            @endforeach
                           
                        </tbody>
                    </table>
                </div>

                <!-- Archived Conferences Tab -->
                <div class="tab-pane fade" id="archived-conferences" role="tabpanel">
                    <table class="datatables-basic table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Conference Theme</th>
                                <th>Start Date</th>
                                <th>Archived At</th>
                                <th>Venue Name</th>
                                <th>Organizer Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($archivedConferences as $conference)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $conference->conference_theme }}</td>
                                    <td>{{ $conference->start_date }}</td>
                                    <td>{{ $conference->archived_at ? $conference->archived_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                    <td>{{ $conference->ConferenceVenueDetail->venue_name }}</td>
                                    <td>{{ $conference->ConferenceOrganizer->organizer_name }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="icon-base ti tabler-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item viewData" data-id="{{ $conference->id }}"
                                                    data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                        class="icon-base ti tabler-eye me-1 "></i> View</a>
                                                <hr>
                                                <form action="{{ route('conference.unarchive', [$society, $conference]) }}" method="POST">
                                                    @csrf
                                                    <a class="dropdown-item text-success unarchive-conference" href="javascript:void(0);"><i
                                                            class="icon-base ti tabler-archive-off me-1"></i> Unarchive</a>
                                                </form>
                                            </div>
                                             <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                                class="btn btn-info btn-sm mt-1">Open Portal</a>
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
            $(document).on("click", ".addOn", function(e) {
                e.preventDefault();
                $(".modal-dialog").addClass('custom-modal-width');
                var url = '{{ route('conference.addon') }}';
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
            $(document).on("click", ".conferenceSetting", function(e) {
                e.preventDefault();
                $(".modal-dialog").addClass('custom-modal-width');
                var url = '{{ route('conference.setting') }}';
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

            // Archive conference handler
            $(document).on("click", ".archive-conference", function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                
                Swal.fire({
                    title: 'Archive Conference?',
                    text: "Are you sure you want to archive this conference?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, archive it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Unarchive conference handler
            $(document).on("click", ".unarchive-conference", function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                
                Swal.fire({
                    title: 'Unarchive Conference?',
                    text: "Are you sure you want to restore this conference?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, restore it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script> 
@endsection
