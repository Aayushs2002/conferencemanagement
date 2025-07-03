@extends('backend.layouts.society.main')
@section('title')
    Conference
@endsection
@section('content')
    <div class="container-fluid py-5 ">
        <div class="row mb-5">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold text-primary mb-3">
                        <i class="bi bi-calendar-event me-3"></i>Conferences
                    </h1>
                    <p class="lead text-muted">Discover upcoming conferences and expand your knowledge</p>
                    <div class="border-bottom border-primary mx-auto" style="width: 100px; border-width: 3px !important;">
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach ($conferences as $conference)
                @php
                    $now = \Carbon\Carbon::now();
                    $startDate = \Carbon\Carbon::parse($conference->start_date);
                    $endDate = \Carbon\Carbon::parse($conference->end_date);

                    if ($now->lt($startDate)) {
                        $status = ['Upcoming', 'primary'];
                    } elseif ($now->between($startDate, $endDate)) {
                        $status = ['Ongoing', 'success'];
                    } else {
                        $status = ['Completed', 'secondary'];
                    }
                @endphp

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-lg border-0 position-relative overflow-hidden">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient opacity-10 z-0"></div>

                        <div class="card-header bg-white border-0 text-center py-4">
                            @if (!empty($conference->conference_logo))
                                <div class="mx-auto d-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px;">
                                    <img src="{{ asset('storage/conference/conference/logo/' . $conference->conference_logo) }}"
                                        alt="{{ $conference->conference_name }}"
                                        class="img-fluid rounded-circle border border-3 border-primary"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                            @else
                                <div class="bg-primary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px;">
                                    <i class="bi bi-mortarboard-fill text-primary fs-1"></i>
                                </div>
                            @endif
                        </div>

                        <div class="card-body p-4 position-relative z-2">
                            <div class="text-center mb-3">
                                <h3 class="card-title fw-bold text-dark mb-2">{{ $conference->conference_name }}</h3>
                                <p class="text-muted mb-0">{{ $conference->conference_theme }}</p>
                            </div>

                            <!-- Status Tag -->
                            <div class="text-center mb-3">
                                <span class="badge bg-{{ $status[1] }} px-3 py-2">{{ $status[0] }}</span>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-calendar-check text-success"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Start Date</small>
                                            <strong
                                                class="text-dark">{{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-calendar-x text-danger"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">End Date</small>
                                            <strong
                                                class="text-dark">{{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-clock text-warning"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Start Time</small>
                                            <strong
                                                class="text-dark">{{ \Carbon\Carbon::parse($conference->start_time)->format('g:i A') }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-lightning text-info"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Early Bird</small>
                                            <strong
                                                class="text-dark">{{ \Carbon\Carbon::parse($conference->early_bird_registration_deadline)->format('M d, Y') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-light rounded-3 p-3 mb-4">
                                <h6 class="text-dark mb-3 fw-semibold">
                                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                    Registration Deadlines
                                </h6>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-success rounded-pill px-3 py-2">Early Bird</span>
                                            <small
                                                class="text-muted fw-semibold">{{ \Carbon\Carbon::parse($conference->early_bird_registration_deadline)->format('F d, Y') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary rounded-pill px-3 py-2">Regular</span>
                                            <small
                                                class="text-muted fw-semibold">{{ \Carbon\Carbon::parse($conference->regular_registration_deadline)->format('F d, Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 p-4">
                            <div class="d-grid">
                                <a href="{{ checkRegistrations($conference) ? route('my-society.conference.index', [$society, $conference]) : route('my-society.conference.create', [$society, $conference]) }}"
                                    class="btn btn-primary btn-lg rounded-pill shadow-sm text-decoration-none">
                                    <i class="bi bi-arrow-right-circle me-2"></i>
                                    Go To Conference
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
