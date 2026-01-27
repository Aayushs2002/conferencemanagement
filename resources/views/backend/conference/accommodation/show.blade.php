@extends('backend.layouts.conference.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"><i class="ti ti-building-skyscraper me-2"></i>International Accommodation Details</h4>
                        <p class="text-muted mb-0">Complete accommodation and travel information for international participant</p>
                    </div>
                    <div>
                        <a href="{{ route('conference.accommodation.index', [$society, $conference]) }}" 
                           class="btn btn-secondary me-2">
                            <i class="ti ti-arrow-left"></i> Back to List
                        </a>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="ti ti-printer"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Participant Information Card -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ti ti-user me-2"></i>Participant Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Full Name:</div>
                            <div class="col-8">
                                {{ $accommodation->conferenceRegistration->user->f_name }}
                                {{ $accommodation->conferenceRegistration->user->l_name }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Email:</div>
                            <div class="col-8">
                                <a href="mailto:{{ $accommodation->conferenceRegistration->user->email }}">
                                    {{ $accommodation->conferenceRegistration->user->email }}
                                </a>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Phone:</div>
                            <div class="col-8">
                                {{ $accommodation->conferenceRegistration->user->userDetail->phone ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Country:</div>
                            <div class="col-8">
                                <span class="badge bg-label-primary">
                                    {{ $accommodation->conferenceRegistration->user->userDetail->country->country_name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Registration ID:</div>
                            <div class="col-8">
                                <code>#{{ $accommodation->conferenceRegistration->id }}</code>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-4 fw-semibold text-muted">Created By:</div>
                            <div class="col-8">
                                @if($accommodation->created_by_admin)
                                    <span class="badge bg-label-warning">Admin Created</span>
                                @else
                                    <span class="badge bg-label-success">Self-Filled</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flight & Travel Details Card -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="ti ti-plane me-2"></i>Flight & Travel Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Flight Number:</div>
                            <div class="col-8">
                                <strong>{{ $accommodation->flight_number ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ti ti-plane-arrival text-success me-2"></i>
                                <strong>Arrival Information</strong>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 text-muted ps-4">Date:</div>
                                <div class="col-8">
                                    <span class="badge bg-label-success">
                                        {{ \Carbon\Carbon::parse($accommodation->arrival_date)->format('D, M d, Y') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-muted ps-4">Time:</div>
                                <div class="col-8">
                                    {{ \Carbon\Carbon::parse($accommodation->arrival_time)->format('h:i A') }}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ti ti-plane-departure text-danger me-2"></i>
                                <strong>Departure Information</strong>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 text-muted ps-4">Date:</div>
                                <div class="col-8">
                                    <span class="badge bg-label-danger">
                                        {{ \Carbon\Carbon::parse($accommodation->departure_date)->format('D, M d, Y') }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-muted ps-4">Time:</div>
                                <div class="col-8">
                                    {{ \Carbon\Carbon::parse($accommodation->departure_time)->format('h:i A') }}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-0">
                            <div class="col-4 fw-semibold text-muted">Airport Pickup:</div>
                            <div class="col-8">
                                @if($accommodation->airport_pickup_required)
                                    <span class="badge bg-success">
                                        <i class="ti ti-check"></i> Required
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="ti ti-x"></i> Not Required
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hotel Accommodation Details Card -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="ti ti-building me-2"></i>Hotel Accommodation</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Hotel Name:</div>
                            <div class="col-8">
                                <strong>{{ $accommodation->hotel->name }}</strong>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Hotel Address:</div>
                            <div class="col-8">
                                {{ $accommodation->hotel->address ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Room Type:</div>
                            <div class="col-8">
                                @if($accommodation->room_type)
                                    <span class="badge bg-label-info">{{ ucfirst($accommodation->room_type) }}</span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Check-in Date:</div>
                            <div class="col-8">
                                @if($accommodation->check_in_date)
                                    <span class="badge bg-label-success">
                                        {{ \Carbon\Carbon::parse($accommodation->check_in_date)->format('D, M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Check-out Date:</div>
                            <div class="col-8">
                                @if($accommodation->check_out_date)
                                    <span class="badge bg-label-danger">
                                        {{ \Carbon\Carbon::parse($accommodation->check_out_date)->format('D, M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </div>
                        </div>
                        @if($accommodation->check_in_date && $accommodation->check_out_date)
                            <div class="row mb-0">
                                <div class="col-4 fw-semibold text-muted">Stay Duration:</div>
                                <div class="col-8">
                                    <span class="badge bg-label-primary">
                                        {{ \Carbon\Carbon::parse($accommodation->check_in_date)->diffInDays(\Carbon\Carbon::parse($accommodation->check_out_date)) }} 
                                        Night(s)
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Special Requirements & Additional Info Card -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="ti ti-notes me-2"></i>Special Requirements & Additional Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-4 fw-semibold text-muted">Status:</div>
                            <div class="col-8">
                                @if($accommodation->status)
                                    <span class="badge bg-success">
                                        <i class="ti ti-check"></i> {{ ucfirst($accommodation->status) }}
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="ti ti-clock"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="fw-semibold text-muted mb-2">Special Requirements:</div>
                            <div class="p-3 bg-light rounded">
                                @if($accommodation->special_requirements)
                                    {{ $accommodation->special_requirements }}
                                @else
                                    <em class="text-muted">No special requirements specified</em>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Created At:</div>
                            <div class="col-6">
                                <small>{{ $accommodation->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-6 text-muted">Last Updated:</div>
                            <div class="col-6">
                                <small>{{ $accommodation->updated_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Summary Card -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="ti ti-timeline me-2"></i>Timeline Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded">
                                    <i class="ti ti-plane-arrival display-6 text-success mb-2"></i>
                                    <h6 class="mb-1">Arrival</h6>
                                    <p class="mb-0 small text-muted">
                                        {{ \Carbon\Carbon::parse($accommodation->arrival_date)->format('M d, Y') }}
                                    </p>
                                    <p class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($accommodation->arrival_time)->format('h:i A') }}</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded">
                                    <i class="ti ti-door-enter display-6 text-primary mb-2"></i>
                                    <h6 class="mb-1">Check-in</h6>
                                    <p class="mb-0 small text-muted">
                                        @if($accommodation->check_in_date)
                                            {{ \Carbon\Carbon::parse($accommodation->check_in_date)->format('M d, Y') }}
                                        @else
                                            Not set
                                        @endif
                                    </p>
                                    <p class="mb-0 fw-bold">{{ $accommodation->hotel->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded">
                                    <i class="ti ti-door-exit display-6 text-warning mb-2"></i>
                                    <h6 class="mb-1">Check-out</h6>
                                    <p class="mb-0 small text-muted">
                                        @if($accommodation->check_out_date)
                                            {{ \Carbon\Carbon::parse($accommodation->check_out_date)->format('M d, Y') }}
                                        @else
                                            Not set
                                        @endif
                                    </p>
                                    <p class="mb-0 fw-bold">{{ $accommodation->hotel->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="p-3 border rounded">
                                    <i class="ti ti-plane-departure display-6 text-danger mb-2"></i>
                                    <h6 class="mb-1">Departure</h6>
                                    <p class="mb-0 small text-muted">
                                        {{ \Carbon\Carbon::parse($accommodation->departure_date)->format('M d, Y') }}
                                    </p>
                                    <p class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($accommodation->departure_time)->format('h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    @media print {
        .btn, .card-header .btn {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }
    }
</style>
@endsection