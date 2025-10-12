@extends('backend.layouts.conference.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Accommodation Details</h5>
                        <a href="{{ route('conference.accommodation.index', [$society, $conference]) }}" 
                           class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Back to List
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Participant Information -->
                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">Participant Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Name</th>
                                        <td>
                                            {{ $accommodation->conferenceRegistration->user->f_name }}
                                            {{ $accommodation->conferenceRegistration->user->l_name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td>{{ $accommodation->conferenceRegistration->user->userDetail->country->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $accommodation->conferenceRegistration->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>{{ $accommodation->conferenceRegistration->user->userDetail->phone ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Flight Details -->
                            <div class="col-md-6 mb-4">
                                <h6 class="mb-3">Flight Details</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Flight Number</th>
                                        <td>{{ $accommodation->flight_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Arrival</th>
                                        <td>
                                            {{ \Carbon\Carbon::parse($accommodation->arrival_date)->format('Y-m-d') }}
                                            at
                                            {{ \Carbon\Carbon::parse($accommodation->arrival_time)->format('H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Departure</th>
                                        <td>
                                            {{ \Carbon\Carbon::parse($accommodation->departure_date)->format('Y-m-d') }}
                                            at
                                            {{ \Carbon\Carbon::parse($accommodation->departure_time)->format('H:i') }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Hotel Details -->
                            <div class="col-md-6">
                                <h6 class="mb-3">Hotel Details</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Hotel Name</th>
                                        <td>{{ $accommodation->hotel->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Check-in</th>
                                        <td>{{ \Carbon\Carbon::parse($accommodation->check_in_date)->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Check-out</th>
                                        <td>{{ \Carbon\Carbon::parse($accommodation->check_out_date)->format('Y-m-d') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection