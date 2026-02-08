@extends('backend.layouts.conference.main')
@section('content')
    @if (current_user()->type == 1 || current_user()->type == 2)
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-4">
                <!-- Total Registrations Card -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-primary fs-4"></i>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Total</span>
                            </div> 
                            <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}">
                                <h3 class="fw-bold text-dark mb-2">{{ $conferenceRegistrationCount }}</h3>
                            </a>  
                            <p class="text-muted mb-0 fw-medium">Total Registrations</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- National Registrants Card -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-users text-success fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">National</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-3">National Registrants</h6>
                            <div class="list-group list-group-flush">
                                {{-- @dd($totalNationalRegistrants) --}}
                                @foreach ($totalNationalRegistrants as $nr_item)
                                    {{-- @dd($nr_item) --}}
                                    <a
                                        href="{{ route('conference.conference-registration.index', [$society, $conference, 'member_type_id' => $nr_item->id]) }}">
                                        <div
                                            class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                            <span class="text-muted fw-medium">{{ $nr_item->type }}</span>
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                                {{ $nr_item->user_count }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- International Registrants Card -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-world text-info fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">International</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-3">International Registrants</h6>
                            <div class="list-group list-group-flush">
                                @foreach ($totalInternationalRegistrants as $inr_item)
                                    <a
                                        href="{{ route('conference.conference-registration.index', [$society, $conference, 'member_type_id' => $inr_item->id]) }}">

                                        <div
                                            class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                            <span class="text-muted fw-medium">{{ $inr_item->type }}</span>
                                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">
                                                {{ $inr_item->user_count }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meal Distribution Card -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-chef-hat text-warning fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Meals</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-3">Conference Meal Distribution</h6>
                            <div class="list-group list-group-flush">
                                @foreach ($mealCounts as $m_item)
                                    <a
                                        href="{{ route('conference.conference-registration.index', [
                                            $society,
                                            $conference,
                                            'meal_type' => $m_item->meal_label === 'Veg' ? 1 : 2,
                                        ]) }}">
                                        <div
                                            class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                            <span class="text-muted fw-medium">{{ $m_item->meal_label }}</span>
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">
                                                {{ $m_item->count }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add-ons Distribution Card -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-purple bg-opacity-10 rounded-circle p-3" style="background-color: rgba(138, 43, 226, 0.1) !important;">
                                    <i class="icon-base ti tabler-circle-plus text-purple fs-4" style="color: #8a2be2 !important;"></i>
                                </div>
                                <span class="badge bg-purple bg-opacity-10 text-purple px-3 py-2 rounded-pill" style="background-color: rgba(138, 43, 226, 0.1) !important; color: #8a2be2 !important;">Add-ons</span>
                            </div>
                            <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}">
                                <h3 class="fw-bold text-dark mb-2">{{ $totalAddons }}</h3>
                            </a>
                            <p class="text-muted mb-0 fw-medium">Total Add-on Registrations</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar" style="width: 75%; background-color: #8a2be2;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accompanying Persons Card -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-users-group text-danger fs-4"></i>
                                </div>
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Guests</span>
                            </div>
                            <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}">
                                <h3 class="fw-bold text-dark mb-2">{{ $totalAccompanyingPersons }}</h3>
                            </a>
                            <p class="text-muted mb-0 fw-medium">Accompanying Persons</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-danger" style="width: 65%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Trends Chart -->
                <div class="col-lg-7 col-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="icon-base ti tabler-trending-up text-info fs-5"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-0" id="registrantTitle">Conference Registrants
                                        </h5>
                                    </div>
                                    <p class="text-muted mb-0">Registration trends over time</p>
                                </div>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-outline-secondary border-0 rounded-circle p-2"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-calendar fs-5"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" id="registrationFilters">
                                        <li><a class="dropdown-item date-dropdown rounded-3 mx-2 my-1" data-range="today">
                                                <i class="icon-base ti tabler-calendar-event me-2"></i>Today
                                            </a></li>
                                        <li><a class="dropdown-item date-dropdown rounded-3 mx-2 my-1"
                                                data-range="yesterday">
                                                <i class="icon-base ti tabler-calendar-minus me-2"></i>Yesterday
                                            </a></li>
                                        <li><a class="dropdown-item date-dropdown rounded-3 mx-2 my-1"
                                                data-range="last_7_days">
                                                <i class="icon-base ti tabler-calendar-week me-2"></i>Last 7 Days
                                            </a></li>
                                        <li><a class="dropdown-item date-dropdown rounded-3 mx-2 my-1"
                                                data-range="last_30_days">
                                                <i class="icon-base ti tabler-calendar-month me-2"></i>Last 30 Days
                                            </a></li>
                                        <li>
                                            <hr class="dropdown-divider mx-2">
                                        </li>
                                        <li><a class="dropdown-item date-dropdown rounded-3 mx-2 my-1"
                                                data-range="current_month">
                                                <i class="icon-base ti tabler-calendar-event me-2"></i>Current Month
                                            </a></li>
                                        <li><a class="dropdown-item date-dropdown rounded-3 mx-2 my-1"
                                                data-range="last_month">
                                                <i class="icon-base ti tabler-calendar-stats me-2"></i>Last Month
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="position-relative bg-light bg-opacity-50 rounded-4 p-3" style="height: 300px;">
                                <!-- Loading Spinner -->
                                <div id="loadingSpinner"
                                    class="position-absolute top-50 start-50 translate-middle text-center d-none">
                                    <div class="spinner-border text-primary mb-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted small mb-0">Loading chart data...</p>
                                </div>

                                <!-- Chart Canvas -->
                                <canvas id="registrationData" class="w-100 h-100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6">
                    <div class="card p-4">
                        <h5 class="mb-4" style="font-weight: bold;">Submission Overview</h5>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label for="categoryFilter" class="form-label">Filter by Category:</label>
                            <select id="categoryFilter" class="form-select w-auto">
                                <option value="">All</option>
                                @foreach ($submissionCategoryMajorTracks as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Chart -->
                        <canvas id="submissionsChart" height="320"></canvas>
                    </div>
                </div>

                <!-- Add-ons Detailed Statistics Card -->
                <div class="col-lg-7 col-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-purple bg-opacity-10 rounded-circle p-2 me-3" style="background-color: rgba(138, 43, 226, 0.1) !important;">
                                    <i class="icon-base ti tabler-chart-pie text-purple fs-5" style="color: #8a2be2 !important;"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-0">Add-ons Distribution</h5>
                            </div>
                            <p class="text-muted mb-0">Detailed breakdown of add-on selections</p>
                        </div>

                        <div class="card-body p-4">
                            @if($addonStats->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Add-on Name</th>
                                                <th class="text-center">Participants</th>
                                                <th class="text-center">Guests</th>
                                                <th class="text-center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($addonStats as $addon)
                                                <tr>
                                                    <td class="fw-semibold">{{ $addon->addon_name }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                                                            {{ $addon->participant_count }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">
                                                            {{ $addon->guest_count }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge rounded-pill px-3 py-1" style="background-color: rgba(138, 43, 226, 0.1); color: #8a2be2;">
                                                            {{ $addon->total_count }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="bg-purple bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                         style="width: 80px; height: 80px; background-color: rgba(138, 43, 226, 0.1) !important;">
                                        <i class="icon-base ti tabler-package-off fs-1" style="color: #8a2be2;"></i>
                                    </div>
                                    <h5 class="fw-semibold mb-2">No Add-ons Registered</h5>
                                    <p class="text-muted mb-0">No add-on selections have been made yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if (feature_enabled('workshop-management', getSociety(request()->segment(2))))
                    <div class="col-lg-5 col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                        <i class="ti tabler-chef-hat text-warning fs-4"></i>
                                    </div>
                                    <span
                                        class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Meals</span>
                                </div>

                                <h5 class="fw-semibold text-dark mb-3">Workshop Meal Distribution And Registration</h5>

                                <div class="dropdown mb-4" style="position: relative; z-index: 5000;">
                                    <button class="btn btn-outline-primary dropdown-toggle rounded-pill px-4" id="workshopFilterBtn"
                                        type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" 
                                        style="max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-flex; align-items: center;" 
                                        title="">
                                        <i class="ti tabler-calendar me-2 flex-shrink-0"></i>
                                        <span id="workshopFilterText" style="overflow: hidden; text-overflow: ellipsis;">Filter by Workshop</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" 
                                        style="position: absolute !important; right: 0 !important; left: auto !important; max-width: 300px; z-index: 9999 !important; transform: translate3d(0px, 0px, 0px) !important;"
                                        id="workshopMealFilterDropdown">
                                        @foreach ($workshops as $workshop)
                                            <li>
                                                <a href="#"
                                                    class="dropdown-item workshop-meal-count rounded-3 mx-2 my-1 text-wrap"
                                                    data-workshop-id="{{ $workshop->id }}"
                                                    title="{{ $workshop->workshop_title }}">
                                                    {{ $workshop->workshop_title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div id="mealCountList">
                                    @foreach ($workshops as $workshop)
                                        {{-- @dd($workshop) --}}
                                        @php
                                            $counts = $workshopMealCounts[$workshop->id] ?? null;
                                            $veg = $counts->veg ?? 0;
                                            $nonVeg = $counts->nonveg ?? 0;
                                            $total = $counts->total ?? 0;
                                        @endphp
                                        <div class="meal-count-group mb-3" data-workshop-id="{{ $workshop->id }}" style="display: {{ $loop->first ? 'block' : 'none' }};">
                                            <a
                                                href="{{ route('workshop.workshop-registration.index', [$society, $conference, $workshop]) . '?meal_type=1' }}"
                                                class="text-decoration-none">
                                                <div class="d-flex justify-content-between align-items-center py-2">
                                                    <span class="text-muted">Veg</span>
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">{{ $veg }}</span>
                                                </div>
                                            </a>
                                            <a
                                                href="{{ route('workshop.workshop-registration.index', [$society, $conference, $workshop]) . '?meal_type=2' }}"
                                                class="text-decoration-none">
                                                <div class="d-flex justify-content-between align-items-center py-2">
                                                    <span class="text-muted">Non-Veg</span>
                                                    <span
                                                        class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">{{ $nonVeg }}</span>
                                                </div>
                                            </a>
                                            <a
                                                href="{{ route('workshop.workshop-registration.index', [$society, $conference, $workshop]) }}"
                                                class="text-decoration-none">
                                                <div class="d-flex justify-content-between align-items-center py-2">
                                                    <span class="fw-bold text-dark">Total Registration</span>
                                                    <span
                                                        class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">{{ $total }}</span>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- <div class="row mt-5"> --}}
                <!-- Attendance & Meal Count Card -->
                <div class="col-lg-7 col-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="icon-base ti tabler-chart-bar text-primary fs-5"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-0">Attendance & Meal Count</h5>
                                    </div>
                                    <p class="text-muted mb-0">Daily attendance and meal statistics</p>
                                </div>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-primary dropdown-toggle rounded-pill px-4"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-calendar me-2"></i>Filter by Day
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" id="dayFilterDropdown">
                                        @foreach ($dates as $date)
                                            <li>
                                                <a href="#" class="dropdown-item day-filter rounded-3 mx-2 my-1"
                                                    data-date="{{ $date }}">
                                                    <i class="icon-base ti tabler-calendar-event me-2"></i>
                                                    Day {{ $loop->iteration }}
                                                </a>
                                            </li>
                                        @endforeach
                                        <li>
                                            <hr class="dropdown-divider mx-2">
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item day-filter rounded-3 mx-2 my-1"
                                                data-date="all">
                                                <i class="icon-base ti tabler-calendar me-2"></i>
                                                All Days
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="position-relative">
                                <!-- Loading Overlay -->
                                <div id="attendanceLoadingOverlay"
                                    class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-none rounded-4 d-flex align-items-center justify-content-center"
                                    style="z-index: 10;">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary mb-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted small mb-0">Updating data...</p>
                                    </div>
                                </div>

                                <div class="row g-3" id="attendanceStatsContainer">
                                    <!-- Attendance Stat -->
                                    <div class="col-md-4">
                                        <div class="bg-success bg-opacity-10 rounded-4 p-4 text-center position-relative">
                                            <div class="bg-success bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 60px; height: 60px;">
                                                <i class="icon-base ti tabler-users text-success fs-3"></i>
                                            </div>
                                            <h3 class="fw-bold text-success mb-1" id="attendance-count">0</h3>
                                            <p class="text-muted mb-0 fw-medium">Attendance</p>
                                            <!-- Individual loading spinner -->
                                            <div class="position-absolute top-50 start-50 translate-middle d-none"
                                                id="attendance-loading">
                                                <div class="spinner-border spinner-border-sm text-success" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lunch Stat -->
                                    <div class="col-md-4">
                                        <div class="bg-warning bg-opacity-10 rounded-4 p-4 text-center position-relative">
                                            <div class="bg-warning bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 60px; height: 60px;">
                                                <i class="icon-base ti tabler-sun text-warning fs-3"></i>
                                            </div>
                                            <h3 class="fw-bold text-warning mb-1" id="lunch-count">0</h3>
                                            <p class="text-muted mb-0 fw-medium">Lunch</p>
                                            <!-- Individual loading spinner -->
                                            <div class="position-absolute top-50 start-50 translate-middle d-none"
                                                id="lunch-loading">
                                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dinner Stat -->
                                    <div class="col-md-4">
                                        <div class="bg-info bg-opacity-10 rounded-4 p-4 text-center position-relative">
                                            <div class="bg-info bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 60px; height: 60px;">
                                                <i class="icon-base ti tabler-moon text-info fs-3"></i>
                                            </div>
                                            <h3 class="fw-bold text-info mb-1" id="dinner-count">0</h3>
                                            <p class="text-muted mb-0 fw-medium">Dinner</p>
                                            <!-- Individual loading spinner -->
                                            <div class="position-absolute top-50 start-50 translate-middle d-none"
                                                id="dinner-loading">
                                                <div class="spinner-border spinner-border-sm text-info" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-10">
                                <a href="{{ route('conference.viewAttendanceStatus', [$society, $conference]) }}"
                                    class="btn btn-primary btn-lg rounded-pill px-5">
                                    <i class="icon-base ti tabler-eye me-2"></i>
                                    View Detailed Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- </div> --}}
            </div>
        </div>
    @else
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Page Header --> 
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between text-white">
                                <div>
                                    <h4 class="text-white fw-bold mb-2">
                                        <i class="icon-base ti tabler-building me-2"></i>{{ $conference->conference_name }}
                                    </h4>
                                    <p class="mb-0 opacity-90">
                                        <i class="icon-base ti tabler-calendar-event me-2"></i>
                                        {{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }} - 
                                        {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}
                                    </p>
                                </div> 
                                <div class="text-end">
                                    @if (checkRegistrations($conference))
                                        <span class="badge bg-success bg-opacity-10 text-white px-4 py-2 fs-6">
                                            <i class="icon-base ti tabler-check me-2"></i>Registered
                                        </span>
                                    @else
                                        <a href="{{ route('my-society.conference.create', [$society, $conference]) }}" 
                                           class="btn btn-light btn-lg rounded-pill px-4 shadow-sm">
                                            <i class="icon-base ti tabler-user-plus me-2"></i>Register Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <!-- Registration Status Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-shadow transition-all" style="transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-user-check text-primary fs-4"></i>
                                </div>
                                @if (checkRegistrations($conference))
                                    <span class="badge bg-success rounded-pill px-3 py-2">Active</span>
                                @else
                                    <span class="badge bg-warning rounded-pill px-3 py-2">Pending</span>
                                @endif
                            </div>
                            <h6 class="fw-semibold text-muted mb-2">Registration Status</h6>
                            @if (checkRegistrations($conference))
                                <h5 class="fw-bold text-success mb-0">
                                    <i class="icon-base ti tabler-circle-check me-1"></i>Confirmed
                                </h5>
                            @else
                                <h5 class="fw-bold text-warning mb-0">Not Registered</h5>
                                <a href="{{ route('my-society.conference.create', [$society, $conference]) }}" 
                                   class="btn btn-sm btn-primary rounded-pill mt-3 px-3">
                                    <i class="icon-base ti tabler-plus me-1"></i>Register Now
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submissions Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-shadow transition-all" style="transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-file-text text-warning fs-4"></i>
                                </div>
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">
                                    {{ $submissionCount }} Total
                                </span>
                            </div>
                            <h6 class="fw-semibold text-muted mb-2">Abstract Submissions</h6>
                            <h3 class="fw-bold text-dark mb-3">{{ $submissionCount }}</h3>
                            <a href="{{ route('my-society.conference.submission.create', [$society, $conference]) }}" 
                               class="btn btn-sm btn-warning rounded-pill px-3">
                                <i class="icon-base ti tabler-plus me-1"></i>Submit Abstract
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Workshop Registration Card -->
                @if (feature_enabled('workshop-management', getSociety(request()->segment(2))))
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100 hover-shadow transition-all" style="transition: all 0.3s ease;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                        <i class="icon-base ti tabler-git-fork text-info fs-4"></i>
                                    </div>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">
                                        {{ $workshopRegistrationCount }} Total
                                    </span>
                                </div>
                                <h6 class="fw-semibold text-muted mb-2">Workshop Registrations</h6>
                                <h3 class="fw-bold text-dark mb-3">{{ $workshopRegistrationCount }}</h3>
                                <p class="text-muted small mb-0">
                                    <i class="icon-base ti tabler-info-circle me-1"></i>Active workshop enrollments
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Review Assignments Card -->
                @if ($reviewAssignmentCount > 0)
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100 hover-shadow transition-all" style="transition: all 0.3s ease;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                        <i class="icon-base ti tabler-clipboard-check text-danger fs-4"></i>
                                    </div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">
                                        {{ $reviewAssignmentCount }} Total
                                    </span>
                                </div>
                                <h6 class="fw-semibold text-muted mb-2">Review Assignments</h6>
                                <h3 class="fw-bold text-dark mb-3">{{ $reviewAssignmentCount }}</h3>
                                <a href="{{ route('my-society.conference.submission.submissionReview', [$society, $conference]) }}" 
                                   class="btn btn-sm btn-danger rounded-pill px-3">
                                    <i class="icon-base ti tabler-eye me-1"></i>View Reviews
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Activity Overview Chart -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="bg-gradient rounded-circle p-2 me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="icon-base ti tabler-chart-pie text-white fs-5"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-0">My Activity Overview</h5>
                                    </div>
                                    <p class="text-muted small mb-0 ms-5 ps-3">Your participation summary</p>
                                </div>
                                @php
                                    $totalActivities = (checkRegistrations($conference) ? 1 : 0) + $submissionCount + (feature_enabled('workshop-management', getSociety(request()->segment(2))) ? $workshopRegistrationCount : 0) + $reviewAssignmentCount;
                                @endphp
                                <div class="text-center">
                                    <h3 class="fw-bold text-primary mb-0">{{ $totalActivities }}</h3>
                                    <p class="text-muted small mb-0">Total Activities</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <div class="position-relative d-flex justify-content-center" style="height: 280px;">
                                        <canvas id="participantActivityChart" style="max-width: 280px; max-height: 280px;"></canvas>
                                        <div class="position-absolute top-50 start-50 translate-middle text-center" style="pointer-events: none;">
                                            <h2 class="fw-bold mb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $totalActivities }}</h2>
                                            <p class="text-muted small mb-0">Activities</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center p-3 rounded-3 hover-shadow transition-all" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%); border-left: 4px solid #0d6efd;">
                                                <div class="bg-primary rounded-circle p-3 me-3">
                                                    <i class="icon-base ti tabler-user-check text-white fs-4"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Conference Registration</p>
                                                    <h5 class="fw-bold text-primary mb-0">{{ checkRegistrations($conference) ? 'Completed' : 'Pending' }}</h5>
                                                </div>
                                                <div class="text-end">
                                                    <h3 class="fw-bold text-primary mb-0">{{ checkRegistrations($conference) ? '1' : '0' }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center p-3 rounded-3 hover-shadow transition-all" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%); border-left: 4px solid #ffc107;">
                                                <div class="bg-warning rounded-circle p-3 me-3">
                                                    <i class="icon-base ti tabler-file-text text-white fs-4"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Abstract Submissions</p>
                                                    <h5 class="fw-bold text-warning mb-0">{{ $submissionCount > 0 ? 'Active' : 'None' }}</h5>
                                                </div>
                                                <div class="text-end">
                                                    <h3 class="fw-bold text-warning mb-0">{{ $submissionCount }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        @if (feature_enabled('workshop-management', getSociety(request()->segment(2))))
                                        <div class="col-12">
                                            <div class="d-flex align-items-center p-3 rounded-3 hover-shadow transition-all" style="background: linear-gradient(135deg, rgba(13, 202, 240, 0.1) 0%, rgba(13, 202, 240, 0.05) 100%); border-left: 4px solid #0dcaf0;">
                                                <div class="bg-info rounded-circle p-3 me-3">
                                                    <i class="icon-base ti tabler-git-fork text-white fs-4"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Workshop Registrations</p>
                                                    <h5 class="fw-bold text-info mb-0">{{ $workshopRegistrationCount > 0 ? 'Enrolled' : 'None' }}</h5>
                                                </div>
                                                <div class="text-end">
                                                    <h3 class="fw-bold text-info mb-0">{{ $workshopRegistrationCount }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($reviewAssignmentCount > 0)
                                        <div class="col-12">
                                            <div class="d-flex align-items-center p-3 rounded-3 hover-shadow transition-all" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%); border-left: 4px solid #dc3545;">
                                                <div class="bg-danger rounded-circle p-3 me-3">
                                                    <i class="icon-base ti tabler-clipboard-check text-white fs-4"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Review Assignments</p>
                                                    <h5 class="fw-bold text-danger mb-0">{{ $reviewAssignmentCount > 0 ? 'Active' : 'None' }}</h5>
                                                </div>
                                                <div class="text-end">
                                                    <h3 class="fw-bold text-danger mb-0">{{ $reviewAssignmentCount }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="icon-base ti tabler-calendar-stats text-info fs-5"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-0">Conference Details</h5>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                        <i class="icon-base ti tabler-calendar text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Conference Dates</p>
                                        <p class="fw-semibold mb-0 small">
                                            {{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }} - 
                                            {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                        <i class="icon-base ti tabler-mail text-success"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Conference Email</p>
                                        <p class="fw-semibold mb-0 small">{{ $conference->conference_email }}</p>
                                    </div>
                                </div>
                            </div>
                            @if($conference->conference_theme)
                            <div class="mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                        <i class="icon-base ti tabler-bulb text-warning"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Theme</p>
                                        <p class="fw-semibold mb-0 small">{{ $conference->conference_theme }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div>
                                <div class="d-flex align-items-start">
                                    <div class="bg-danger bg-opacity-10 rounded p-2 me-3">
                                        <i class="icon-base ti tabler-clock text-danger"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Days Remaining</p>
                                        @php
                                            $daysRemaining = (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($conference->start_date), false);
                                        @endphp
                                        @if($daysRemaining > 0)
                                            <p class="fw-semibold mb-0 small text-danger">
                                                <i class="icon-base ti tabler-hourglass me-1"></i>{{ $daysRemaining }} {{ Str::plural('day', $daysRemaining) }}
                                            </p>
                                        @elseif($daysRemaining == 0)
                                            <p class="fw-semibold mb-0 small text-success">
                                                <i class="icon-base ti tabler-calendar-check me-1"></i>Today!
                                            </p>
                                        @else
                                            <p class="fw-semibold mb-0 small text-info">
                                                <i class="icon-base ti tabler-calendar-event me-1"></i>In Progress
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conference Information Section -->
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="icon-base ti tabler-list-check text-success fs-5"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-0">My Submissions Status</h5>
                            </div>
                            <p class="text-muted small mb-0">Track your abstract submissions</p>
                        </div>
                        <div class="card-body p-4">
                            @if($submissionCount > 0)
                                <div class="alert alert-success border-0 bg-success bg-opacity-10 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="icon-base ti tabler-circle-check text-success fs-4 me-3"></i>
                                        <div>
                                            <p class="fw-semibold mb-0">You have {{ $submissionCount }} active {{ Str::plural('submission', $submissionCount) }}</p>
                                            <small class="text-muted">Keep track of your submission status through the dashboard</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center" style="height: 150px;">
                                    <div class="text-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                             style="width: 80px; height: 80px;">
                                            <i class="icon-base ti tabler-file-check text-success" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h2 class="fw-bold text-success mb-2">{{ $submissionCount }}</h2>
                                        <p class="text-muted mb-0">Active {{ Str::plural('Submission', $submissionCount) }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                         style="width: 80px; height: 80px;">
                                        <i class="icon-base ti tabler-file-plus text-warning" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h5 class="fw-semibold mb-2">No Submissions Yet</h5>
                                    <p class="text-muted mb-3">Start by submitting your first abstract</p>
                                    <a href="{{ route('my-society.conference.submission.create', [$society, $conference]) }}" 
                                       class="btn btn-warning rounded-pill px-4">
                                        <i class="icon-base ti tabler-plus me-2"></i>Submit Your First Abstract
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- My Add-ons Card -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-purple bg-opacity-10 rounded-circle p-2 me-3" style="background-color: rgba(138, 43, 226, 0.1) !important;">
                                    <i class="icon-base ti tabler-circle-plus text-purple fs-5" style="color: #8a2be2 !important;"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-0">My Add-ons</h5>
                            </div>
                            <p class="text-muted small mb-0">Your selected add-ons</p>
                        </div>
                        <div class="card-body p-4">
                            @if(count($userAddons) > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($userAddons as $addon)
                                        <div class="list-group-item border-0 px-0 py-3">
                                            <h6 class="mb-1 fw-semibold">{{ $addon->addon_name }}</h6>
                                            <small class="text-muted">
                                                @if($addon->include_for_guests)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">For Guest</span>
                                                @else
                                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">For Participant</span>
                                                @endif
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="bg-purple bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                         style="width: 60px; height: 60px; background-color: rgba(138, 43, 226, 0.1) !important;">
                                        <i class="icon-base ti tabler-package-off" style="font-size: 1.8rem; color: #8a2be2;"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-1">No Add-ons Selected</h6>
                                    <p class="text-muted small mb-0">You haven't registered any add-ons</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- My Accompanying Persons Card -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="icon-base ti tabler-users-group text-danger fs-5"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-0">Accompanying Persons</h5>
                            </div>
                            <p class="text-muted small mb-0">Your registered guests</p>
                        </div>
                        <div class="card-body p-4">
                            @if(count($userAccompanyingPersons) > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($userAccompanyingPersons as $index => $person)
                                        <div class="list-group-item border-0 px-0 py-3 d-flex align-items-center">
                                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                                                 style="width: 40px; height: 40px;">
                                                <i class="icon-base ti tabler-user text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $person->person_name }}</h6>
                                                <small class="text-muted">Guest {{ $index + 1 }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 pt-3 border-top text-center">
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-4 py-2">
                                        <i class="icon-base ti tabler-users me-1"></i>
                                        {{ count($userAccompanyingPersons) }} {{ Str::plural('Person', count($userAccompanyingPersons)) }}
                                    </span>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                         style="width: 60px; height: 60px;">
                                        <i class="icon-base ti tabler-user-off text-danger" style="font-size: 1.8rem;"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-1">No Accompanying Persons</h6>
                                    <p class="text-muted small mb-0">You haven't registered any guests</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="icon-base ti tabler-rocket text-success fs-5"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-0">Quick Actions</h5>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-grid gap-2">
                                @if (!checkRegistrations($conference))
                                    <a href="{{ route('my-society.conference.create', [$society, $conference]) }}" 
                                       class="btn btn-primary rounded-pill">
                                        <i class="icon-base ti tabler-user-plus me-2"></i>Complete Registration
                                    </a>
                                @endif
                                <a href="{{ route('my-society.conference.submission.create', [$society, $conference]) }}" 
                                   class="btn btn-outline-warning rounded-pill">
                                    <i class="icon-base ti tabler-file-plus me-2"></i>Submit New Abstract
                                </a>
                                <a href="{{ route('my-society.conference.index', [$society, $conference]) }}" 
                                   class="btn btn-outline-info rounded-pill">
                                    <i class="icon-base ti tabler-list me-2"></i>View My Activities
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Show modal message if registration for speaker is not required --}}
            @php
                $confSetting = $conference->conferenceSetting ?? null;
            @endphp
            @if (
                $confSetting &&
                    isset($confSetting->speaker_registration_required) &&
                    $confSetting->speaker_registration_required == false)
                <!-- Modal Trigger Button (hidden, auto-triggered) -->
                <button type="button" id="autoSpeakerRegModalBtn" class="d-none" data-bs-toggle="modal"
                    data-bs-target="#autoSpeakerRegModal"></button>

                <!-- Modal -->
                <div class="modal fade" id="autoSpeakerRegModal" tabindex="-1"
                    aria-labelledby="autoSpeakerRegModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold" id="autoSpeakerRegModalLabel">
                                    <i class="icon-base ti tabler-info-circle text-info me-2"></i>Speaker Registration Notice
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body pt-2">
                                <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-0">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="icon-base ti tabler-alert-circle text-info fs-4"></i>
                                        </div>
                                        <div>
                                            <p class="fw-semibold mb-2">Important Information:</p>
                                            <ul class="mb-0 ps-3">
                                                <li class="mb-2">Speakers do <strong>not</strong> need to register for the conference separately.</li>
                                                <li class="mb-2">If your submission is <strong>accepted</strong>, you will be <strong>automatically registered</strong> for the conference.</li>
                                                <li class="mb-0">
                                                    <strong>Note:</strong> In case of abstract rejection, you must complete the regular registration process to attend the conference.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-primary rounded-pill px-4"
                                    data-bs-dismiss="modal">
                                    <i class="icon-base ti tabler-check me-2"></i>Got it
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Auto-trigger modal for speakers if registration is not required
                        document.getElementById('autoSpeakerRegModalBtn').click();
                    });
                </script>
            @endif
        </div>
    @endif
@endsection

@section('styles')
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }
        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
@endsection

@section('scripts')
    <script>
        const registrationData = document.getElementById('registrationData');
        const loadingSpinner = document.getElementById('loadingSpinner');
        let registrationDataVar;

        const renderChart = (labels, counts) => {
            if (registrationDataVar) {
                registrationDataVar.destroy();
            }

            registrationDataVar = new Chart(registrationData, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: '#0d6efd',
                        borderColor: 'transparent',
                        maxBarThickness: 20,
                        borderRadius: 8
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    },
                    elements: {
                        bar: {
                            borderRadius: {
                                topRight: 8,
                                bottomRight: 8
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#000',
                            bodyColor: '#333',
                            borderWidth: 1,
                            borderColor: '#e0e0e0',
                            cornerRadius: 8,
                            padding: 12
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            min: 0,
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                borderColor: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        }
                    }
                }
            });
        };

        const fetchDataAndRender = (range = 'last_7_days') => {
            loadingSpinner.classList.remove('d-none');
            fetch(`{{ route('dashboard.registrationData', ['conference_id' => $conference->id]) }}&range=${range}`)
                .then(response => response.json())
                .then(data => {
                    renderChart(data.labels, data.counts);

                    if (range == 'english') {
                        range = 'last_7_days'
                    }
                    const totalCount = data.counts.reduce((a, b) => a + b, 0);
                    document.getElementById('registrantTitle').innerHTML =
                        `${range.replace(/_/g, ' ')} <span class="badge rounded-pill bg-primary ms-2">${totalCount}</span>`;
                })
                .finally(() => {
                    loadingSpinner.classList.add('d-none');
                });
        };

        fetchDataAndRender();

        document.querySelectorAll('.date-dropdown').forEach(item => {
            item.addEventListener('click', function() {
                const selected = this.textContent.trim().toLowerCase().replace(/\s+/g, '_');
                fetchDataAndRender(selected);
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dropdownItems = document.querySelectorAll('#dayFilterDropdown .day-filter');
            const today = new Date().toISOString().split('T')[0];
            const conferenceId = {{ $conference->id }};
            const loadingOverlay = document.getElementById('attendanceLoadingOverlay');
            const filterButton = document.querySelector('#dayFilterDropdown').previousElementSibling;

            // Show/Hide loading state
            const showLoading = () => {
                loadingOverlay.classList.remove('d-none');
                filterButton.disabled = true;
                filterButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
            };

            const hideLoading = () => {
                loadingOverlay.classList.add('d-none');
                filterButton.disabled = false;
                filterButton.innerHTML = '<i class="icon-base ti tabler-calendar me-2"></i>Filter by Day';
            };

            // Animate numbers function with loading state
            const animateNumber = (element, targetNumber, loadingSpinner) => {
                const duration = 800;
                const start = parseInt(element.textContent) || 0;
                const increment = (targetNumber - start) / (duration / 16);
                let current = start;

                // Hide loading spinner for this specific stat
                if (loadingSpinner) {
                    loadingSpinner.classList.add('d-none');
                }

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= targetNumber) {
                        current = targetNumber;
                        clearInterval(timer);
                    }
                    element.textContent = Math.floor(current);
                }, 16);
            };

            // Update filter button text
            const updateFilterButtonText = (selectedText) => {
                setTimeout(() => {
                    filterButton.innerHTML =
                        `<i class="icon-base ti tabler-calendar me-2"></i>${selectedText}`;
                }, 100);
            };

            // Load today's data by default
            showLoading();
            fetch(`{{ route('conference.stats') }}?conference_id=${conferenceId}&date=${today}`)
                .then(res => res.json())
                .then(data => {
                    animateNumber(document.getElementById('attendance-count'), data.attendance_count ?? 0);
                    animateNumber(document.getElementById('lunch-count'), data.lunch_count ?? 0);
                    animateNumber(document.getElementById('dinner-count'), data.dinner_count ?? 0);
                })
                .catch(error => {
                    console.error('Error loading data:', error);
                    // Show error state
                    document.getElementById('attendance-count').textContent = '--';
                    document.getElementById('lunch-count').textContent = '--';
                    document.getElementById('dinner-count').textContent = '--';
                })
                .finally(() => {
                    setTimeout(hideLoading, 500); // Small delay to show loading state
                });

            // Add event listeners to dropdown items
            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const selectedDate = this.dataset.date;
                    const selectedText = this.textContent.trim();

                    showLoading();

                    setTimeout(() => {
                        fetch(
                                `{{ route('conference.stats') }}?conference_id=${conferenceId}&date=${selectedDate}`
                            )
                            .then(res => res.json())
                            .then(data => {
                                animateNumber(document.getElementById(
                                        'attendance-count'), data
                                    .attendance_count ?? 0);
                                animateNumber(document.getElementById('lunch-count'),
                                    data.lunch_count ?? 0);
                                animateNumber(document.getElementById('dinner-count'),
                                    data.dinner_count ?? 0);
                                updateFilterButtonText(selectedText);
                            })
                            .catch(error => {
                                console.error('Error loading data:', error);
                                // Show error state
                                document.getElementById('attendance-count')
                                    .textContent = '--';
                                document.getElementById('lunch-count').textContent =
                                    '--';
                                document.getElementById('dinner-count').textContent =
                                    '--';

                                console.log('Failed to load attendance data');
                            })
                            .finally(() => {
                                setTimeout(hideLoading, 300);
                            });
                    }, 100);
                });
            });

            // Workshop meal filtering
            const workshopFilterButton = document.getElementById('workshopFilterBtn');
            const workshopFilterText = document.getElementById('workshopFilterText');
            const workshopMealItems = document.querySelectorAll('.workshop-meal-count');
            
            // Function to truncate text
            const truncateText = (text, maxLength = 70) => {
                return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
            };
            
            // Set initial button text to first workshop
            if (workshopMealItems.length > 0) {
                const firstWorkshopName = workshopMealItems[0].textContent.trim();
                workshopFilterText.textContent = truncateText(firstWorkshopName);
                workshopFilterButton.setAttribute('title', firstWorkshopName);
            }
            
            workshopMealItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    let selectedId = this.getAttribute('data-workshop-id');
                    let selectedTitle = this.textContent.trim();
                    
                    // Update button text with truncation
                    workshopFilterText.textContent = truncateText(selectedTitle);
                    workshopFilterButton.setAttribute('title', selectedTitle);
                    
                    // Show/hide workshop meal groups
                    document.querySelectorAll('.meal-count-group').forEach(group => {
                        group.style.display = group.getAttribute('data-workshop-id') ===
                            selectedId ? 'block' : 'none';
                    });
                });
            });
        });
        let ctx = document.getElementById('submissionsChart').getContext('2d');

        let submissionsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Poster', 'Oral'],
                datasets: [{
                    label: 'Number of Submissions',
                    data: [0, 0], // Initial values
                    backgroundColor: ['#36A2EB', '#FF6384']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function loadChart(categoryId = '') {

            let url = "{{ route('conference.submissionsChart', [$society, $conference]) }}" + (categoryId ?
                '?category_id=' +
                categoryId : '');
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    submissionsChart.data.datasets[0].data = [data.poster, data.oral];
                    submissionsChart.update();
                });
        }

        // Load initial chart
        loadChart();

        // Reload when category changes
        document.getElementById('categoryFilter').addEventListener('change', function() {
            loadChart(this.value);
        });

        // Participant Activity Chart (only for participants)
        @if (!(current_user()->type == 1 || current_user()->type == 2))
            const participantActivityChart = document.getElementById('participantActivityChart');
            
            if (participantActivityChart) {
                const activityChart = new Chart(participantActivityChart, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Registration', 
                            'Submissions', 
                            @if (feature_enabled('workshop-management', getSociety(request()->segment(2))))
                            'Workshops',
                            @endif
                            @if ($reviewAssignmentCount > 0)
                            'Reviews'
                            @endif
                        ],
                        datasets: [{
                            data: [
                                {{ checkRegistrations($conference) ? 1 : 0 }}, 
                                {{ $submissionCount }}, 
                                @if (feature_enabled('workshop-management', getSociety(request()->segment(2))))
                                {{ $workshopRegistrationCount }},
                                @endif
                                @if ($reviewAssignmentCount > 0)
                                {{ $reviewAssignmentCount }}
                                @endif
                            ],
                            backgroundColor: [
                                'rgba(13, 110, 253, 0.9)',
                                'rgba(255, 193, 7, 0.9)',
                                @if (feature_enabled('workshop-management', getSociety(request()->segment(2))))
                                'rgba(13, 202, 240, 0.9)',
                                @endif
                                @if ($reviewAssignmentCount > 0)
                                'rgba(220, 53, 69, 0.9)'
                                @endif
                            ],
                            borderColor: '#fff',
                            borderWidth: 3,
                            hoverOffset: 10,
                            hoverBorderWidth: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.98)',
                                titleColor: '#000',
                                bodyColor: '#333',
                                borderWidth: 2,
                                borderColor: 'rgba(0,0,0,0.1)',
                                cornerRadius: 12,
                                padding: 16,
                                displayColors: true,
                                boxWidth: 12,
                                boxHeight: 12,
                                boxPadding: 6,
                                titleFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                        label += ' (' + percentage + '%)';
                                        return label;
                                    }
                                }
                            }
                        },
                        cutout: '75%',
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1000,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            }
        @endif
    </script>
@endsection
