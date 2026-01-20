@extends('backend.layouts.society.main')
@section('title')
    Conference
@endsection
@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="ti tabler-calendar-event text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h1 class="display-5 fw-bold text-dark mb-2">Conferences</h1>
                    <p class="text-muted mb-0">Discover and manage your conferences</p>
                </div>
            </div>
        </div>

        <!-- Conference Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-pills justify-content-center gap-2" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4 py-2 rounded-3 d-flex align-items-center gap-2" id="active-tab"
                            data-bs-toggle="tab" data-bs-target="#active-conferences" type="button" role="tab"
                            aria-controls="active-conferences" aria-selected="true">
                            <i class="ti tabler-calendar-check"></i>
                            <span>Active</span>
                            <span
                                class="badge bg-primary bg-opacity-25 text-primary rounded-pill">{{ $activeConferences->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-2 rounded-3 d-flex align-items-center gap-2" id="archived-tab"
                            data-bs-toggle="tab" data-bs-target="#archived-conferences" type="button" role="tab"
                            aria-controls="archived-conferences" aria-selected="false">
                            <i class="ti tabler-archive"></i>
                            <span>Archived</span>
                            <span
                                class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill">{{ $archivedConferences->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Active Conferences Tab -->
            <div class="tab-pane fade show active" id="active-conferences" role="tabpanel" aria-labelledby="active-tab">
                @if ($activeConferences->isEmpty())
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-5">
                                    <div class="mb-3">
                                        <i class="ti tabler-calendar-x text-muted"
                                            style="font-size: 4rem; opacity: 0.3;"></i>
                                    </div>
                                    <h4 class="text-muted mb-2">No Active Conferences</h4>
                                    <p class="text-muted mb-0">There are currently no active conferences available.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($activeConferences as $conference)
                            @php
                                $now = \Carbon\Carbon::now();
                                $startDate = \Carbon\Carbon::parse($conference->start_date);
                                $endDate = \Carbon\Carbon::parse($conference->end_date);

                                if ($now->lt($startDate)) {
                                    $status = ['Upcoming', 'primary', 'bi-hourglass-split'];
                                } elseif ($now->between($startDate, $endDate)) {
                                    $status = ['Ongoing', 'success', 'bi-activity'];
                                } else {
                                    $status = ['Completed', 'secondary', 'bi-check-circle'];
                                }
                            @endphp

                            <div class="col-lg-4 col-md-6">
                                <div class="card border-0 shadow-sm h-100 hover-lift transition-all">
                                    <!-- Status Badge -->
                                    <div class="position-absolute top-0 end-0 m-3 z-3">
                                        <span class="badge bg-{{ $status[1] }} px-3 py-2 rounded-pill">
                                            <i class="bi {{ $status[2] }} me-1"></i>{{ $status[0] }}
                                        </span>
                                    </div>

                                    <!-- Conference Logo -->
                                    <div class="card-header bg-white border-0 text-center py-4">
                                        @if (!empty($conference->conference_logo))
                                            <img src="{{ asset('storage/conference/conference/logo/' . $conference->conference_logo) }}"
                                                alt="{{ $conference->conference_name }}"
                                                class="rounded-circle border border-3 border-primary shadow-sm"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 80px; height: 80px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="text-primary" style="font-size: 2rem;"
                                                    fill="currentColor" class="bi bi-mortarboard-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z" />
                                                    <path
                                                        d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z" />
                                                </svg>
                                                {{-- <i class="ti tabler-m text-primary" ></i> --}}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Conference Details -->
                                    <div class="card-body px-4">
                                        <div class="text-center mb-3">
                                            <h5 class="card-title fw-bold text-dark mb-2">{{ $conference->conference_name }}
                                            </h5>
                                            <p class="text-muted small mb-0">{{ $conference->conference_theme }}</p>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-success bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-calendar-check text-success"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">Start Date</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-danger bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-calendar-x text-danger"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">End Date</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-warning bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-clock text-warning"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">Start Time</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->start_time)->format('g:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-warning bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-clock-filled text-warning"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">End Time</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->end_time)->format('g:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Registration Deadlines -->
                                        <div class="bg-light rounded-3 p-3 mt-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="ti tabler-bell text-warning me-2"></i>
                                                <h6 class="mb-0 fw-semibold small">Registration Deadlines</h6>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-success rounded-pill px-2 py-1 small">Early
                                                    Bird</span>
                                                <small
                                                    class="text-muted fw-semibold">{{ \Carbon\Carbon::parse($conference->early_bird_registration_deadline)->format('M d, Y') }}</small>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary rounded-pill px-2 py-1 small">Regular</span>
                                                <small
                                                    class="text-muted fw-semibold">{{ \Carbon\Carbon::parse($conference->regular_registration_deadline)->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer bg-white border-0 p-3">
                                        <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                            class="btn btn-primary w-100 rounded-pill py-2">
                                            <i class="ti tabler-arrow-right-circle me-2"></i>
                                            Go To Conference
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Archived Conferences Tab -->
            <div class="tab-pane fade" id="archived-conferences" role="tabpanel" aria-labelledby="archived-tab">
                @if ($archivedConferences->isEmpty())
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-5">
                                    <div class="mb-3">
                                        <i class="ti tabler-archive text-muted"
                                            style="font-size: 4rem; opacity: 0.3;"></i>
                                    </div>
                                    <h4 class="text-muted mb-2">No Archived Conferences</h4>
                                    <p class="text-muted mb-0">There are no archived conferences at this time.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($archivedConferences as $conference)
                            <div class="col-lg-4 col-md-6">
                                <div class="card border-0 shadow-sm h-100 hover-lift transition-all"
                                    style="opacity: 0.9;">
                                    <!-- Archived Badge -->
                                    <div class="position-absolute top-0 end-0 m-3 z-3">
                                        <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                            <i class="ti tabler-archive me-1"></i>Archived
                                        </span>
                                    </div>

                                    <!-- Conference Logo -->
                                    <div class="card-header bg-white border-0 text-center py-4">
                                        @if (!empty($conference->conference_logo))
                                            <img src="{{ asset('storage/conference/conference/logo/' . $conference->conference_logo) }}"
                                                alt="{{ $conference->conference_name }}"
                                                class="rounded-circle border border-3 border-secondary shadow-sm"
                                                style="width: 80px; height: 80px; object-fit: cover; filter: grayscale(30%);">
                                        @else
                                            <div class="bg-secondary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 80px; height: 80px;">
                                                  <svg xmlns="http://www.w3.org/2000/svg" class="text-secondary" style="font-size: 2rem;"
                                                    fill="currentColor" class="bi bi-mortarboard-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z" />
                                                    <path
                                                        d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z" />
                                                </svg>
                                                {{-- <i class="ti tabler-mortarboard-fill text-secondary"
                                                    style="font-size: 2rem;"></i> --}}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Conference Details -->
                                    <div class="card-body px-4">
                                        <div class="text-center mb-3">
                                            <h5 class="card-title fw-bold text-dark mb-2">
                                                {{ $conference->conference_name }}</h5>
                                            <p class="text-muted small mb-2">{{ $conference->conference_theme }}</p>
                                            <div class="d-flex align-items-center justify-content-center gap-1 text-muted">
                                                <i class="ti tabler-clock small"></i>
                                                <small>Archived on
                                                    {{ $conference->archived_at ? $conference->archived_at->format('M d, Y') : 'N/A' }}</small>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-secondary bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-calendar-check text-secondary"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">Start Date</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-secondary bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-calendar-x text-secondary"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">End Date</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer bg-white border-0 p-3">
                                        <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                            class="btn btn-outline-secondary w-100 rounded-pill py-2">
                                            <i class="ti tabler-eye me-2"></i>
                                            View Conference
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link {
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-pills .nav-link:not(.active):hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .nav-pills .nav-link.active {
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .card {
            overflow: hidden;
        }

        .min-w-0 {
            min-width: 0;
        }
    </style>
@endsection
