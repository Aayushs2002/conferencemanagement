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
                            <h3 class="fw-bold text-dark mb-2">{{ $conferenceRegistrationCount }}</h3>
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
                                @foreach ($totalNationalRegistrants as $nr_item)
                                    <div
                                        class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                        <span class="text-muted fw-medium">{{ $nr_item->type }}</span>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                            {{ $nr_item->user_count }}
                                        </span>
                                    </div>
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
                                    <div
                                        class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                        <span class="text-muted fw-medium">{{ $inr_item->type }}</span>
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">
                                            {{ $inr_item->user_count }}
                                        </span>
                                    </div>
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
                                    <div
                                        class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                        <span class="text-muted fw-medium">{{ $m_item->meal_label }}</span>
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">
                                            {{ $m_item->count }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="ti tabler-chef-hat text-warning fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Meals</span>
                            </div>

                            <h5 class="fw-semibold text-dark mb-3">Workshop Meal Distribution</h5>

                            <div class="dropdown mb-4">
                                <button class="btn btn-outline-primary dropdown-toggle rounded-pill px-4" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="ti tabler-calendar me-2"></i>Filter by Workshop
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg"
                                    id="workshopMealFilterDropdown">
                                    @foreach ($workshops as $workshop)
                                        <li>
                                            <a href="#" class="dropdown-item workshop-meal-count rounded-3 mx-2 my-1"
                                                data-workshop-id="{{ $workshop->id }}">
                                                {{ $workshop->workshop_title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div id="mealCountList">
                                @foreach ($workshops as $workshop)
                                    @php
                                        $counts = $workshopMealCounts[$workshop->id] ?? collect();
                                        $veg = $counts->firstWhere('meal_type', 1)?->count ?? 0;
                                        $nonVeg = $counts->firstWhere('meal_type', 2)?->count ?? 0;
                                    @endphp
                                    <div class="meal-count-group mb-4" data-workshop-id="{{ $workshop->id }}">
                                        <h6 class="text-primary fw-bold">{{ $workshop->workshop_title }}</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Veg</span>
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">{{ $veg }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="text-muted">Non-Veg</span>
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">{{ $nonVeg }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <!-- Attendance & Meal Count Card -->
                    <div class="col-lg-6 col-12">
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
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg"
                                            id="dayFilterDropdown">
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
                                            <div
                                                class="bg-success bg-opacity-10 rounded-4 p-4 text-center position-relative">
                                                <div class="bg-success bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="icon-base ti tabler-users text-success fs-3"></i>
                                                </div>
                                                <h3 class="fw-bold text-success mb-1" id="attendance-count">0</h3>
                                                <p class="text-muted mb-0 fw-medium">Attendance</p>
                                                <!-- Individual loading spinner -->
                                                <div class="position-absolute top-50 start-50 translate-middle d-none"
                                                    id="attendance-loading">
                                                    <div class="spinner-border spinner-border-sm text-success"
                                                        role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Lunch Stat -->
                                        <div class="col-md-4">
                                            <div
                                                class="bg-warning bg-opacity-10 rounded-4 p-4 text-center position-relative">
                                                <div class="bg-warning bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="icon-base ti tabler-sun text-warning fs-3"></i>
                                                </div>
                                                <h3 class="fw-bold text-warning mb-1" id="lunch-count">0</h3>
                                                <p class="text-muted mb-0 fw-medium">Lunch</p>
                                                <!-- Individual loading spinner -->
                                                <div class="position-absolute top-50 start-50 translate-middle d-none"
                                                    id="lunch-loading">
                                                    <div class="spinner-border spinner-border-sm text-warning"
                                                        role="status">
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
                                                    <div class="spinner-border spinner-border-sm text-info"
                                                        role="status">
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

                    <!-- Registration Trends Chart -->
                    <div class="col-lg-6 col-12">
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
                                        <button type="button"
                                            class="btn btn-outline-secondary border-0 rounded-circle p-2"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-calendar fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg"
                                            id="registrationFilters">
                                            <li><a class="dropdown-item date-dropdown rounded-3 mx-2 my-1"
                                                    data-range="today">
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
                                <div class="position-relative bg-light bg-opacity-50 rounded-4 p-3"
                                    style="height: 300px;">
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
                </div>
            </div>
        </div>
    @else
        <div class="row">

            <div class="col-lg-3 col-sm-6">
                <div class="card ">
                    <div class="card-body">
                        <p class="mb-1">Conference Registered Status</p>
                        <div class="my-4">
                            @if (checkRegistrations($conference))
                                <span class="badge bg-success">Registered</span>
                            @else
                                <a href="{{ route('my-society.conference.create', [$society, $conference]) }}">
                                    <span class="badge bg-danger">Register Now</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card ">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-1">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-warning"><i
                                        class="icon-base ti tabler-alert-triangle icon-28px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $submissionCount }}</h4>
                        </div>
                        <p class="mb-1">Submission</p>

                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-1">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded bg-label-danger"><i
                                        class="icon-base ti tabler-git-fork icon-28px"></i></span>
                            </div>
                            <h4 class="mb-0">{{ $workshopRegistrationCount }}</h4>
                        </div>
                        <p class="mb-1">WorkShop Registration</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
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

            document.querySelectorAll('.workshop-meal-count').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    let selectedId = this.getAttribute('data-workshop-id');
                    document.querySelectorAll('.meal-count-group').forEach(group => {
                        group.style.display = group.getAttribute('data-workshop-id') ===
                            selectedId ? 'block' : 'none';
                    });
                });
            });
        });
    </script>
@endsection
