@extends('backend.layouts.society.main')
@section('content')
    <style>
        /* Professional Dashboard Styles - Minimal Color Palette */
        :root {
            --primary-color: #696cff;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #e9ecef;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #696cff 0%, #5f61ff 100%);
            box-shadow: 0 8px 30px rgba(105, 108, 255, 0.2);
        }

        .stat-card {
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            overflow: hidden;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        a.card-link {
            display: block;
            text-decoration: none;
            color: inherit;
            position: relative;
            z-index: 1;
        }

        a.card-link:hover {
            text-decoration: none;
            color: inherit;
        }

        a.card-link:focus {
            outline: none;
        }

        a.card-link .stat-card {
            cursor: pointer;
            pointer-events: auto;
        }

        a.card-link * {
            color: inherit !important;
            pointer-events: none;
        }

        a.card-link .badge {
            pointer-events: none;
        }

        /* Disabled card links */
        a.card-link.disabled-card-link {
            cursor: not-allowed !important;
            opacity: 0.6;
        }

        a.card-link.disabled-card-link .stat-card {
            cursor: not-allowed !important;
        }

        /* Ensure disabled cards don't interfere */
        div[style*="cursor: not-allowed"] {
            pointer-events: none;
        }

        div[style*="cursor: not-allowed"] .stat-card {
            opacity: 0.6;
            cursor: not-allowed !important;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, currentColor 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card.card-primary::before { color: #696cff; }
        .stat-card.card-success::before { color: #28c76f; }
        .stat-card.card-info::before { color: #00cfe8; }
        .stat-card.card-warning::before { color: #ff9f43; }
        .stat-card.card-secondary::before { color: #82868b; }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .fade-out {
            opacity: 0.6;
            transition: opacity 0.3s ease;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .stat-icon.icon-primary {
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.1) 0%, rgba(105, 108, 255, 0.15) 100%);
        }

        .stat-icon.icon-primary i {
            color: #696cff;
        }

        .stat-icon.icon-success {
            background: linear-gradient(135deg, rgba(40, 199, 111, 0.1) 0%, rgba(40, 199, 111, 0.15) 100%);
        }

        .stat-icon.icon-success i {
            color: #28c76f;
        }

        .stat-icon.icon-info {
            background: linear-gradient(135deg, rgba(0, 207, 232, 0.1) 0%, rgba(0, 207, 232, 0.15) 100%);
        }

        .stat-icon.icon-info i {
            color: #00cfe8;
        }

        .stat-icon.icon-warning {
            background: linear-gradient(135deg, rgba(255, 159, 67, 0.1) 0%, rgba(255, 159, 67, 0.15) 100%);
        }

        .stat-icon.icon-warning i {
            color: #ff9f43;
        }

        .stat-icon.icon-secondary {
            background: linear-gradient(135deg, rgba(130, 134, 139, 0.1) 0%, rgba(130, 134, 139, 0.15) 100%);
        }

        .stat-icon.icon-secondary i {
            color: #82868b;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-loading {
            position: relative;
            pointer-events: none;
        }

        .card-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .stats-loading {
            position: relative;
        }

        .stats-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 24px;
            height: 24px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #696cff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 11;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .avatar-md {
            width: 48px;
            height: 48px;
        }

        .avatar-md .avatar-initial {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-md {
            font-size: 24px;
        }

        .card {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .form-select {
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .form-select:focus {
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.2);
            border-color: #696cff;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.75em;
        }

        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(105, 108, 255, 0.05);
            transform: scale(1.01);
        }

        #conferenceFilter {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            font-weight: 500;
        }

        .text-white-50 {
            opacity: 0.7;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: slideIn 0.4s ease-out;
        }

        .card:nth-child(1) { animation-delay: 0.05s; }
        .card:nth-child(2) { animation-delay: 0.1s; }
        .card:nth-child(3) { animation-delay: 0.15s; }
        .card:nth-child(4) { animation-delay: 0.2s; }
        .card:nth-child(5) { animation-delay: 0.25s; }
        .card:nth-child(6) { animation-delay: 0.3s; }

        .icon-lg {
            font-size: 28px;
        }

        .border-4 {
            border-width: 4px !important;
        }

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* Minimal Badge Styles */
        .badge-soft {
            background-color: #f8f9fa;
            color: var(--text-muted);
            font-weight: 500;
            padding: 0.4em 0.85em;
            border: 1px solid #e9ecef;
        }

        .badge-primary-soft {
            background-color: rgba(105, 108, 255, 0.08);
            color: #696cff;
            font-weight: 500;
            padding: 0.4em 0.85em;
            border: 1px solid rgba(105, 108, 255, 0.15);
        }

        .badge-success-soft {
            background-color: rgba(40, 199, 111, 0.08);
            color: #28c76f;
            font-weight: 500;
            padding: 0.4em 0.85em;
            border: 1px solid rgba(40, 199, 111, 0.15);
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f0f2f5;
            padding: 1.25rem 1.5rem;
        }

        .chart-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }
    </style>
    
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header with Filter -->
        @if (auth()->user()->type != 3)
            
        <div class="row align-items-center mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <h3 class="fw-bold mb-1" style="color: var(--text-dark); font-size: 1.75rem;">Dashboard</h3>
                <p class="mb-0" style="color: var(--text-muted); font-size: 0.9375rem;">Real-time insights and analytics</p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end align-items-center gap-3">
                    <label for="conferenceFilter" class="mb-0 text-muted" style="font-size: 0.875rem; white-space: nowrap;">
                        <i class="ti tabler-filter me-1"></i> Conference:
                    </label>
                    <select id="conferenceFilter" class="form-select" style="min-width: 280px; max-width: 400px; border: 1px solid #e9ecef; border-radius: 8px; padding: 0.625rem 1rem;">
                        @if($selectedConference)
                            <option value="{{ $selectedConference->id }}" data-hashid="{{ $selectedConference->getRouteKey() }}" selected>
                                {{ $selectedConference->abbreviation ?? $selectedConference->conference_name }}
                            </option>
                        @endif
                        @foreach($allConferences as $conf)
                            @if(!$selectedConference || $conf->id != $selectedConference->id)
                                <option value="{{ $conf->id }}" data-hashid="{{ $conf->getRouteKey() }}">
                                    {{ $conf->abbreviation ?? $conf->conference_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if($selectedConference)
        <!-- Selected Conference Info Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px solid #e9ecef; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 48px; height: 48px; background: linear-gradient(135deg, #696cff 0%, #7d7fff 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti tabler-presentation" style="font-size: 24px; color: #ffffff;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <h5 class="mb-0 fw-bold" id="selectedConferenceName" style="color: var(--text-dark);">
                                        {{ $selectedConference->conference_theme ?? $selectedConference->conference_name }}
                                    </h5>
                                    @php
                                        $startDate = \Carbon\Carbon::parse($selectedConference->start_date);
                                        $endDate = \Carbon\Carbon::parse($selectedConference->end_date);
                                        $now = \Carbon\Carbon::now();
                                    @endphp
                                    @if($now->lt($startDate))
                                        <span class="badge badge-soft">
                                            <i class="ti tabler-calendar-clock me-1"></i>Upcoming
                                        </span>
                                    @elseif($now->between($startDate, $endDate))
                                        <span class="badge badge-success-soft">
                                            <i class="ti tabler-live-photo me-1"></i>Live Now
                                        </span>
                                    @else
                                        <span class="badge badge-soft">
                                            <i class="ti tabler-check me-1"></i>Completed
                                        </span>
                                    @endif
                                    <span class="badge badge-primary-soft">
                                        <i class="ti tabler-calendar me-1"></i>
                                        {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Statistics Cards Row 1 - Main Metrics -->
        <div class="row g-4 mb-4">
            <!-- Total Conferences -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="{{ route('conference.index', $society) }}" class="card-link">
                    <div class="card stat-card card-primary h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-primary">
                                    <i class="ti tabler-briefcase" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Total Conferences</h6>
                            <h2 class="stat-value mb-2">{{ $conferenceCount }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-success-soft">
                                    {{ $activeConferenceCount }} Active
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Ongoing Conferences -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="{{ route('conference.index', $society) }}" class="card-link">
                    <div class="card stat-card card-success h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-success">
                                    <i class="ti tabler-hourglass-high" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Ongoing</h6>
                            <h2 class="stat-value mb-2">{{ $ongoingConferences }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-primary-soft">
                                    Live Events
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Upcoming Conferences -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="{{ route('conference.index', $society) }}" class="card-link">
                    <div class="card stat-card card-info h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-info">
                                    <i class="ti tabler-calendar-event" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Upcoming</h6>
                            <h2 class="stat-value mb-2">{{ $upcomingConferences }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-soft">
                                    Scheduled
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Completed Conferences -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="{{ route('conference.index', $society) }}" class="card-link">
                    <div class="card stat-card card-secondary h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-secondary">
                                    <i class="ti tabler-check" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Completed</h6>
                            <h2 class="stat-value mb-2">{{ $completedConferences }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-soft">
                                    Past Events
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Registrations -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                @if($selectedConference)
                    <a href="{{ route('conference.conference-registration.index', [$society, $selectedConference]) }}" class="card-link" id="registrationsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="conference.conference-registration.index">
                @else
                    <a href="#" class="card-link disabled-card-link" id="registrationsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="conference.conference-registration.index" onclick="return false;" style="cursor: not-allowed;" title="Please select a conference">
                @endif
                    <div class="card stat-card card-success h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-success">
                                    <i class="ti tabler-users" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Registrations</h6>
                            <h2 class="stat-value mb-2" data-stat="totalRegistrations">{{ $totalRegistrations }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-soft">
                                    <span data-stat="pendingRegistrations">{{ $pendingRegistrations }}</span> Pending
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Submissions -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                @if($selectedConference)
                    <a href="{{ route('submission.index', [$society, $selectedConference]) }}" class="card-link" id="submissionsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="submission.index">
                @else
                    <a href="#" class="card-link disabled-card-link" id="submissionsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="submission.index" onclick="return false;" style="cursor: not-allowed;" title="Please select a conference">
                @endif
                    <div class="card stat-card card-info h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-info">
                                    <i class="ti tabler-file-text" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Submissions</h6>
                            <h2 class="stat-value mb-2" data-stat="totalSubmissions">{{ $totalSubmissions }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-success-soft">
                                    <span data-stat="acceptedSubmissions">{{ $acceptedSubmissions }}</span> Accepted
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Statistics Cards Row 2 - Additional Metrics -->
        <div class="row g-4 mb-4">
            <!-- Pending Submissions -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                @if($selectedConference)
                    <a href="{{ route('submission.index', [$society, $selectedConference]) }}" class="card-link" id="pendingSubmissionsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="submission.index">
                @else
                    <a href="#" class="card-link disabled-card-link" id="pendingSubmissionsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="submission.index" onclick="return false;" style="cursor: not-allowed;" title="Please select a conference">
                @endif
                    <div class="card stat-card card-warning h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-warning">
                                    <i class="ti tabler-clock" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Pending Review</h6>
                            <h2 class="stat-value mb-2" data-stat="pendingSubmissions">{{ $pendingSubmissions }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-soft">
                                    Awaiting
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Rejected Submissions -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                @if($selectedConference)
                    <a href="{{ route('submission.index', [$society, $selectedConference]) }}" class="card-link" id="rejectedSubmissionsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="submission.index">
                @else
                    <a href="#" class="card-link disabled-card-link" id="rejectedSubmissionsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="submission.index" onclick="return false;" style="cursor: not-allowed;" title="Please select a conference">
                @endif
                    <div class="card stat-card card-secondary h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-secondary">
                                    <i class="ti tabler-x" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Rejected</h6>
                            <h2 class="stat-value mb-2" data-stat="rejectedSubmissions">{{ $rejectedSubmissions }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-soft">
                                    Declined
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Workshops -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                @if($selectedConference)
                    <a href="{{ route('workshop.index', [$society, $selectedConference]) }}" class="card-link" id="workshopsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="workshop.index">
                @else
                    <a href="#" class="card-link disabled-card-link" id="workshopsCardLink" data-society-hashid="{{ $society->getRouteKey() }}" data-route-name="workshop.index" onclick="return false;" style="cursor: not-allowed;" title="Please select a conference">
                @endif
                    <div class="card stat-card card-primary h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-primary">
                                    <i class="ti tabler-presentation" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Workshops</h6>
                            <h2 class="stat-value mb-2" data-stat="workshopCount">{{ $workshopCount }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-soft">
                                    <span data-stat="workshopRegistrations">{{ $workshopRegistrations }}</span> Participants
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            @if (current_user()->type != 3)
            <!-- Member Types -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="{{ route('memberType.index', $society) }}" class="card-link">
                    <div class="card stat-card card-info h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="stat-icon icon-info">
                                    <i class="ti tabler-user-check" style="font-size: 22px;"></i>
                                </div>
                            </div>
                            <h6 class="stat-label mb-2">Member Types</h6>
                            <h2 class="stat-value mb-2">{{ $typeCount }}</h2>
                            <div class="mt-3">
                                <span class="badge badge-soft">
                                    Active Types
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        </div>

        <!-- Charts and Conference List Row -->
        <div class="row g-4 mb-4">
            <!-- Monthly Registrations Trend -->
            <div class="col-xl-8 col-12">
                <div class="card chart-card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold" style="color: var(--text-dark); font-size: 1.125rem;">
                                    Registration Trends
                                </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.875rem;">Last 6 months registration activity</p>
                            </div>
                            <div>
                                <span class="badge badge-soft" style="font-size: 0.8125rem;">
                                    <i class="ti tabler-calendar ti-xs me-1"></i>6 Months
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="monthlyRegistrationsChart"></div>
                    </div>
                </div>
            </div>

            <!-- Conference Registration Distribution -->
            <div class="col-xl-4 col-12">
                <div class="card chart-card h-100">
                    <div class="card-header">
                        <div>
                            <h5 class="mb-1 fw-bold" style="color: var(--text-dark); font-size: 1.125rem;">
                                Top Conferences
                            </h5>
                            <p class="mb-0 text-muted" style="font-size: 0.875rem;">By registration count</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="conferenceDistributionChart"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Conferences Overview Table -->
        <div class="row">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1 fw-bold" style="color: var(--text-dark); font-size: 1.125rem;">
                                    Conferences Overview
                                </h5>
                                <p class="mb-0 text-muted" style="font-size: 0.875rem;">All active conferences with key metrics</p>
                            </div>
                            <div>
                                <span class="badge badge-primary-soft" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                    {{ $conferences->count() }} Total
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-bold">#</th>
                                    <th class="fw-bold">Conference</th>
                                    <th class="fw-bold">Date</th>
                                    <th class="fw-bold">Venue</th>
                                    <th class="fw-bold">Organizer</th>
                                    @if(auth()->user()->type != 3)
                                    <th class="text-center fw-bold">Registrations</th>
                                    <th class="text-center fw-bold">Submissions</th>
                                    @endif
                                    <th class="fw-bold">Status</th>
                                    <th class="text-center fw-bold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($conferences as $conference)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    @if($conference->conference_logo)
                                                        <img src="{{ asset('storage/' . $conference->conference_logo) }}" 
                                                             alt="logo" class="rounded" style="width: 30px; height: 30px; object-fit: cover;">
                                                    @else
                                                        <div class="avatar avatar-sm">
                                                            <span class="avatar-initial rounded bg-label-primary">
                                                                {{ substr($conference->abbreviation ?? $conference->conference_name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="fw-medium">{{ Str::limit($conference->conference_theme, 40) }}</span>
                                                    @if($conference->abbreviation)
                                                        <br><small class="text-muted">{{ $conference->abbreviation }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">
                                                {{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td>{{ $conference->ConferenceVenueDetail->venue_name ?? 'N/A' }}</td>
                                        <td>{{ $conference->ConferenceOrganizer->organizer_name ?? 'N/A' }}</td>
                                        @if (auth()->user()->type != 3)
                                            
                                        <td class="text-center">
                                            <span class="badge bg-label-success rounded-pill">
                                                {{ $conference->conference_registrations_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-info rounded-pill">
                                                {{ $conference->submissions_count ?? 0 }}
                                            </span>
                                        </td>
                                        @endif
                                        <td>
                                            @php
                                                $startDate = \Carbon\Carbon::parse($conference->start_date);
                                                $endDate = \Carbon\Carbon::parse($conference->end_date);
                                                $now = \Carbon\Carbon::now();
                                            @endphp
                                            @if($now->lt($startDate))
                                                <span class="badge bg-label-primary">Upcoming</span>
                                            @elseif($now->between($startDate, $endDate))
                                                <span class="badge bg-label-success">Ongoing</span>
                                            @else
                                                <span class="badge bg-label-secondary">Completed</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                                   class="btn btn-sm btn-label-primary" title="Open Portal">
                                                    <i class="ti tabler-external-link icon-sm"></i>
                                                </a>
                                                @if (auth()->user()->type != 3)
                                                    
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-icon btn-label-secondary dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('conference.edit', [$society, $conference]) }}">
                                                                <i class="icon-base ti tabler-pencil me-2"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item viewData" href="javascript:void(0);" 
                                                            data-id="{{ $conference->id }}" data-bs-toggle="modal" data-bs-target="#pricingModal">
                                                                <i class="icon-base ti tabler-eye me-2"></i> View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item priceForm" href="javascript:void(0);" 
                                                            data-id="{{ $conference->id }}" data-bs-toggle="modal" data-bs-target="#pricingModal">
                                                                <i class="icon-base ti tabler-cash me-2"></i> Registration Price
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="misc-wrapper">
                                                <div class="avatar avatar-lg mx-auto mb-3">
                                                    <span class="avatar-initial rounded-circle bg-label-secondary">
                                                        <i class="icon-base ti tabler-folder-off icon-lg"></i>
                                                    </span>
                                                </div>
                                                <h5 class="mb-1">No conferences found</h5>
                                                <p class="mb-0 text-muted">Start by creating your first conference</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for View/Price Form -->
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var monthlyRegistrationsChart;
        var conferenceDistributionChart;

        $(document).ready(function() {
            // Initialize charts
            initializeCharts();

            // Conference filter change handler
            $('#conferenceFilter').on('change', function() {
                var conferenceId = $(this).val();
                var $selectedOption = $(this).find('option:selected');
                var conferenceHashid = $selectedOption.data('hashid');
                
                console.log('Conference filter changed:', {
                    id: conferenceId,
                    hashid: conferenceHashid
                });
                
                if (conferenceId && conferenceHashid) {
                    // Immediately update card links with hashid from dropdown
                    updateCardLinks(conferenceHashid);
                    // Then load the statistics data
                    loadConferenceData(conferenceId);
                }
            });

            // Modal handlers
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                var url = '{{ route('conference.show') }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                
                $.post(url, {_token: _token, id: id}, function(response) {
                    $('#modalContent').html(response);
                });
            });

            $(document).on("click", ".priceForm", function(e) {
                e.preventDefault();
                $(".modal-dialog").addClass('custom-modal-width');
                var url = '{{ route('conference.priceForm') }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                
                $('#modalContent').html(`
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                
                $.post(url, {_token: _token, id: id}, function(response) {
                    $('#modalContent').html(response);
                });
            });
        });

        function initializeCharts() {
            // Monthly Registrations Chart
            var monthlyData = @json($monthlyRegistrations);
            var months = monthlyData.map(item => item.month);
            var counts = monthlyData.map(item => item.count);

            var monthlyRegistrationsOptions = {
                series: [{
                    name: 'Registrations',
                    data: counts
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#696cff'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.5,
                        opacityTo: 0.1,
                    }
                },
                xaxis: {
                    categories: months,
                    labels: {
                        style: {
                            colors: '#a1acb8',
                            fontSize: '13px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#a1acb8',
                            fontSize: '13px'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 5
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + " registrations";
                        }
                    }
                }
            };

            monthlyRegistrationsChart = new ApexCharts(
                document.querySelector("#monthlyRegistrationsChart"),
                monthlyRegistrationsOptions
            );
            monthlyRegistrationsChart.render();

            // Conference Distribution Chart
            var conferenceData = @json($conferenceRegistrationData);
            var conferenceNames = conferenceData.map(item => item.abbreviation || item.conference_name.substring(0, 20));
            var conferenceCounts = conferenceData.map(item => item.conference_registrations_count);

            var conferenceDistributionOptions = {
                series: conferenceCounts,
                chart: {
                    height: 300,
                    type: 'donut',
                },
                labels: conferenceNames,
                colors: ['#696cff', '#8592a3', '#71dd37', '#ffab00', '#ff3e1d'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '16px',
                                    fontWeight: 600,
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    fontSize: '13px',
                    labels: {
                        colors: '#a1acb8'
                    },
                    markers: {
                        width: 10,
                        height: 10
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function(val) {
                            return val + " registrations";
                        }
                    }
                }
            };

            conferenceDistributionChart = new ApexCharts(
                document.querySelector("#conferenceDistributionChart"),
                conferenceDistributionOptions
            );
            conferenceDistributionChart.render();
        }

        function loadConferenceData(conferenceId) {
            console.log('Loading data for conference ID:', conferenceId);
            
            // Add loading state to stat cards only
            $('.stat-card h3[data-stat]').addClass('stats-loading');
            $('.stat-card .badge span[data-stat]').addClass('stats-loading');
            
            // Disable filter dropdown
            $('#conferenceFilter').prop('disabled', true);

            $.ajax({
                url: '{{ route('society.dashboard.data', $society) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    conference_id: conferenceId
                },
                success: function(response) {
                    console.log('AJAX Success:', response);
                    
                    if (response.success) {
                        updateDashboard(response.data);
                    } else {
                        console.error('Response success is false:', response);
                        showError('Invalid response from server');
                    }
                    
                    // Remove loading state
                    $('.stats-loading').removeClass('stats-loading');
                    $('#conferenceFilter').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    
                    // Remove loading state
                    $('.stats-loading').removeClass('stats-loading');
                    $('#conferenceFilter').prop('disabled', false);
                    
                    // Show error notification
                    showError('Failed to load conference data. Please try again.');
                }
            });
        }

        function updateCardLinks(conferenceId) {
            console.log('Updating card links for conference ID:', conferenceId);
            
            // Update each card link with the new conference ID
            const cardLinks = [
                '#registrationsCardLink',
                '#submissionsCardLink',
                '#pendingSubmissionsCardLink',
                '#rejectedSubmissionsCardLink',
                '#workshopsCardLink'
            ];
            
            cardLinks.forEach(function(linkId) {
                const $link = $(linkId);
                if ($link.length) {
                    const routePattern = $link.data('route-pattern');
                    if (routePattern) {
                        const newHref = routePattern.replace('__CONF_ID__', conferenceId);
                        $link.attr('href', newHref);
                        $link.removeClass('disabled-card-link');
                        $link.attr('onclick', '');
                        $link.closest('.col-xl-2').find('div[style*="cursor: not-allowed"]').remove();
                    }
                }
            });
        }

        function showError(message) {
            const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show position-fixed" role="alert" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                '<i class="ti tabler-alert-circle me-2"></i>' +
                '<strong>Error!</strong> ' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            $('body').append(errorAlert);
            
            setTimeout(function() {
                errorAlert.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        function updateCardLinks(conferenceHashid) {
            console.log('Updating card links for conference hashid:', conferenceHashid);
            
            if (!conferenceHashid) {
                console.error('Conference hashid is missing!');
                return;
            }
            
            // Route mapping - base URLs
            const routes = {
                'conference.conference-registration.index': '/society/{society}/conference/{conference}/conference-registration/registrant',
                'submission.index': '/society/{society}/conference/{conference}/submission',
                'workshop.index': '/society/{society}/conference/{conference}/workshop'
            };
            
            // Update each card link with the new conference hashid
            const cardLinks = [
                '#registrationsCardLink',
                '#submissionsCardLink',
                '#pendingSubmissionsCardLink',
                '#rejectedSubmissionsCardLink',
                '#workshopsCardLink'
            ];
            
            cardLinks.forEach(function(linkId) {
                const $link = $(linkId);
                if ($link.length) {
                    const societyHashid = $link.data('society-hashid');
                    const routeName = $link.data('route-name');
                    
                    if (societyHashid && routeName && routes[routeName]) {
                        // Build the URL with hashids
                        const newHref = routes[routeName]
                            .replace('{society}', societyHashid)
                            .replace('{conference}', conferenceHashid);
                        
                        console.log('Updating', linkId, 'to:', newHref);
                        $link.attr('href', newHref);
                        $link.removeClass('disabled-card-link');
                        $link.removeAttr('onclick');
                        $link.css('cursor', 'pointer');
                        $link.removeAttr('title');
                        $link.css('opacity', '1');
                    } else {
                        console.warn('Missing data for', linkId, {societyHashid, routeName});
                    }
                } else {
                    console.warn('Link not found:', linkId);
                }
            });
            
            console.log('All card links updated successfully');
        }

        function updateDashboard(data) {
            console.log('Updating dashboard with data:', data);
            
            // Card links are already updated from dropdown hashid
            // Just update the statistics and other data
            
            // Update conference name in banner
            if (data.conferenceTheme || data.conferenceName) {
                $('#selectedConferenceName').text(data.conferenceTheme || data.conferenceName);
            }
            
            // Update conference dates and status badge if dates are provided
            if (data.startDate && data.endDate) {
                var startDate = new Date(data.startDate);
                var endDate = new Date(data.endDate);
                var now = new Date();
                
                // Format dates
                var dateOptions = { year: 'numeric', month: 'short', day: 'numeric' };
                var formattedStartDate = startDate.toLocaleDateString('en-US', dateOptions);
                var formattedEndDate = endDate.toLocaleDateString('en-US', dateOptions);
                
                // Update date badge
                $('#selectedConferenceName').parent().find('.badge-primary-soft').html(
                    '<i class="ti tabler-calendar me-1"></i>' + formattedStartDate + ' - ' + formattedEndDate
                );
                
                // Update status badge
                var statusBadge = '';
                if (now < startDate) {
                    statusBadge = '<span class="badge badge-soft"><i class="ti tabler-calendar-clock me-1"></i>Upcoming</span>';
                } else if (now >= startDate && now <= endDate) {
                    statusBadge = '<span class="badge badge-success-soft"><i class="ti tabler-live-photo me-1"></i>Live Now</span>';
                } else {
                    statusBadge = '<span class="badge badge-soft"><i class="ti tabler-check me-1"></i>Completed</span>';
                }
                
                // Replace the status badge (it's between the name and date badge)
                var badgeContainer = $('#selectedConferenceName').parent();
                var existingStatusBadge = badgeContainer.find('.badge:not(.badge-primary-soft)').first();
                if (existingStatusBadge.length) {
                    existingStatusBadge.replaceWith(statusBadge);
                }
            }
            
            // Update statistics cards directly (no fade animation to avoid getting stuck)
            $('[data-stat="totalRegistrations"]').text(data.totalRegistrations || 0);
            $('[data-stat="pendingRegistrations"]').text(data.pendingRegistrations || 0);
            $('[data-stat="totalSubmissions"]').text(data.totalSubmissions || 0);
            $('[data-stat="acceptedSubmissions"]').text(data.acceptedSubmissions || 0);
            $('[data-stat="pendingSubmissions"]').text(data.pendingSubmissions || 0);
            $('[data-stat="rejectedSubmissions"]').text(data.rejectedSubmissions || 0);
            $('[data-stat="workshopCount"]').text(data.workshopCount || 0);
            $('[data-stat="workshopRegistrations"]').text(data.workshopRegistrations || 0);

            // Update monthly registrations chart
            if (data.monthlyRegistrations && data.monthlyRegistrations.length > 0) {
                console.log('Updating chart with:', data.monthlyRegistrations);
                
                var months = data.monthlyRegistrations.map(item => item.month);
                var counts = data.monthlyRegistrations.map(item => parseInt(item.count) || 0);
                
                console.log('Chart data - Months:', months, 'Counts:', counts);
                
                // Update chart with animation
                try {
                    monthlyRegistrationsChart.updateOptions({
                        xaxis: {
                            categories: months,
                            labels: {
                                style: {
                                    colors: '#a1acb8',
                                    fontSize: '13px'
                                }
                            }
                        }
                    }, false, true);
                    
                    monthlyRegistrationsChart.updateSeries([{
                        name: 'Registrations',
                        data: counts
                    }], true);
                    
                    console.log('Chart updated successfully');
                } catch (error) {
                    console.error('Error updating chart:', error);
                }
            } else {
                console.warn('No monthly registrations data available');
            }
        }
    </script>
@endsection
