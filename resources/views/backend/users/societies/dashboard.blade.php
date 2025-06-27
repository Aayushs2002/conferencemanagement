@extends('backend.layouts.society.main')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <!-- Card Border Shadow -->
            <div class="col-lg-3 col-sm-6">
                <div class="card  ">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class="icon-base ti tabler-truck icon-28px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $conferenceCount }}</h4>
                        </div>
                        <p class="mb-1">Total Conference</p>

                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-warning"><i
                                        class="icon-base ti tabler-alert-triangle icon-28px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $typeCount }}</h4>
                        </div>
                        <p class="mb-1">Total Member Type</p>

                    </div>
                </div>
            </div>
            <!--/ Orders by Countries -->

            <!-- On route vehicles Table -->
            <div class="col-12 order-5">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Latest Conference</h5>
                        </div>
                        {{-- <div class="dropdown">
                            <button class="btn btn-text-secondary rounded-pill p-2 me-n1" type="button" id="routeVehicles"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-base ti tabler-dots-vertical icon-md"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="routeVehicles">
                                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            </div>
                        </div> --}}
                    </div>
                    <div class="card-datatable border-top">
                        <table class="dt-route-vehicles table">
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
                                                    <a href="#" class="dropdown-item priceForm"
                                                        data-id="{{ $conference->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#pricingModal"><i
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
            </div>
            <!--/ On route vehicles Table -->
        </div>
    </div>
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
