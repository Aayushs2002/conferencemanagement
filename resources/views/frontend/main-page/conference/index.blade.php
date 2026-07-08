@extends('frontend.main-page.layouts.main')
@section('content')
    <section class="banner d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Clients</li>
                        </ol>
                    </nav>
                    <h1 class="banner-title">MedConAlert: Conferences<br> That Drive Change</h1>
                    <p class="banner-sub">
                        Browse conferences that bring experts, organizers, and participants together to share knowledge,
                        discuss innovations, and create impactful solutions for tomorrow. </p>
                </div>
            </div>
        </div>
    </section>
    <section class="main-section">
        <section class="about-section ">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-3">
                        <h2 class="section-title">MedConAlert
                            Conferences</h2>
                    </div>
                    <div class="col-lg-7">
                        <p class="section-subtitle mb-4">
                            At MedConAlert Conferences, we bring together upcoming and past events that connect
                            organizers, speakers, sponsors, and attendees across the medical and scientific community.
                            From registration to workshops, our platform simplifies every stage of conference planning
                            and participation, ensuring seamless experiences for everyone involved.</p>
                        <p class="section-subtitle">
                            Whether you're looking to attend, organize, or collaborate, MedConAlert makes it easy to
                            explore events, engage with experts, and be part of conferences that drive knowledge,
                            innovation, and meaningful change.</p>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <div class="td_height_60 td_height_lg_60"></div>
    <section class="conference-filter-section">
        <div class="container">
            <h2 class="section-title text-center mb-5">Conferences List</h2>

            <!-- Filter Form -->
            <div class="row g-3 align-items-center mb-4 filter-row">
                <div class="col-lg-6 col-md-6">
                    <div class="search-box position-relative">
                        <input type="text" id="searchInput" class="form-control ps-5"
                            placeholder="Search conference names..." value="{{ request('search') }}">
                        <span class="search-icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3">
                    <div class="custom-dropdown">
                        <select id="organizationSelect" class="form-select">
                            <option value="">Organization</option>
                            @foreach ($societies as $society)
                                <option value="{{ $society->id }}"
                                    {{ request('organization') == $society->id ? 'selected' : '' }}>
                                    {{ $society->abbreviation }}
                                </option>
                            @endforeach
                        </select>
                        <span class="chevron"></span>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3">
                    <div class="custom-dropdown">
                        <select id="tagsSelect" class="form-select">
                            <option value="">Tags</option>
                            <option value="health" {{ request('tags') == 'health' ? 'selected' : '' }}>Health</option>
                            <option value="women" {{ request('tags') == 'women' ? 'selected' : '' }}>Women</option>
                            <option value="gynecology" {{ request('tags') == 'gynecology' ? 'selected' : '' }}>Gynecology
                            </option>
                        </select>
                        <span class="chevron"></span>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3">
                    <div class="custom-dropdown">
                        <select id="monthSelect" class="form-select">
                            <option value="">Month</option>
                            <option value="January" {{ request('month') == 'January' ? 'selected' : '' }}>January</option>
                            <option value="February" {{ request('month') == 'February' ? 'selected' : '' }}>February
                            </option>
                            <option value="March" {{ request('month') == 'March' ? 'selected' : '' }}>March</option>
                            <option value="April" {{ request('month') == 'April' ? 'selected' : '' }}>April</option>
                            <option value="May" {{ request('month') == 'May' ? 'selected' : '' }}>May</option>
                            <option value="June" {{ request('month') == 'June' ? 'selected' : '' }}>June</option>
                            <option value="July" {{ request('month') == 'July' ? 'selected' : '' }}>July</option>
                            <option value="August" {{ request('month') == 'August' ? 'selected' : '' }}>August</option>
                            <option value="September" {{ request('month') == 'September' ? 'selected' : '' }}>September
                            </option>
                            <option value="October" {{ request('month') == 'October' ? 'selected' : '' }}>October</option>
                            <option value="November" {{ request('month') == 'November' ? 'selected' : '' }}>November
                            </option>
                            <option value="December" {{ request('month') == 'December' ? 'selected' : '' }}>December
                            </option>
                        </select>
                        <span class="chevron"></span>
                    </div>
                </div>
            </div>

            <!-- Clear Filters Button (only show when filters are active) -->
            <div class="row mb-4" id="clearFiltersRow" style="display: none;">
                <div class="col-12 text-center">
                    <button type="button" id="clearFiltersBtn" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-refresh me-1"></i>Clear All Filters
                    </button>
                </div>
            </div>

            <div id="conferenceCardsContainer"> 
                @include('frontend.main-page.conference.partials.conference-cards')
            </div>

            @if (session('error'))
                <div class="alert alert-warning mt-4 text-center">
                    {{ session('error') }}
                </div>
            @endif

            @if ($conferences->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fa-solid fa-search fa-3x text-muted"></i>
                    </div>
                    <h4 class="text-muted">No conferences found</h4>
                    <p class="text-muted">Try adjusting your search criteria or clear filters to see all conferences.</p>
                </div>
            @endif

        </div>
    </section>

    @if (!isset($jqueryLoaded))
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @endif

    <script>
        (function() {
            function initializeFilters() {
                console.log('Initializing filters...');

                let searchTimeout;
                let isFilterActive = false;

                checkActiveFilters();

                $('#searchInput').on('input', function() {
                    console.log('Search input changed:', $(this).val());
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        filterConferences();
                    }, 300);
                });

                $('#organizationSelect, #tagsSelect, #monthSelect').on('change', function() {
                    console.log('Dropdown changed:', $(this).attr('id'), $(this).val());
                    filterConferences();
                });

                $('#clearFiltersBtn').on('click', function() {
                    console.log('Clear filters clicked');
                    $('#searchInput').val('');
                    $('#organizationSelect').val('');
                    $('#tagsSelect').val('');
                    $('#monthSelect').val('');
                    filterConferences();
                });

                function filterConferences() {
                    const formData = {
                        search: $('#searchInput').val(),
                        organization: $('#organizationSelect').val(),
                        tags: $('#tagsSelect').val(),
                        month: $('#monthSelect').val()
                    };

                    console.log('Filtering with data:', formData);

                    checkActiveFilters();

                    showLoadingState();

                    $.ajax({
                        url: '{{ route('conference.filter') }}',
                        method: 'GET',
                        data: formData,
                        success: function(response) {
                            console.log('Filter response received');
                            $('#conferenceCardsContainer').html(response);
                            initializeCountdowns();
                            updateURL(formData);
                        },
                        error: function(xhr, status, error) {
                            console.error('Filter error:', error);
                            $('#conferenceCardsContainer').html(`
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-exclamation-triangle fa-3x text-warning"></i>
                                    </div>
                                    <h4 class="text-warning">Error loading conferences</h4>
                                    <p class="text-muted">Please try again or refresh the page.</p>
                                    <p class="small text-muted">Error: ${error}</p>
                                </div>
                            `);
                        }
                    });
                }

                function showLoadingState() {
                    if (!$('#loadingOverlay').length) {
                        $('#conferenceCardsContainer').prepend(`
                            <div id="loadingOverlay" class="position-relative">
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(255,255,255,0.8); z-index: 10;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        `);

                        setTimeout(() => {
                            $('#loadingOverlay').remove();
                        }, 2000);
                    }
                }

                function checkActiveFilters() {
                    isFilterActive = $('#searchInput').val() !== '' ||
                        $('#organizationSelect').val() !== '' ||
                        $('#tagsSelect').val() !== '' ||
                        $('#monthSelect').val() !== '';

                    if (isFilterActive) {
                        $('#clearFiltersRow').show();
                    } else {
                        $('#clearFiltersRow').hide();
                    }
                }

                function updateURL(formData) {
                    if (typeof window.history !== 'undefined') {
                        const url = new URL(window.location);

                        Object.keys(formData).forEach(key => {
                            if (formData[key] && formData[key] !== '') {
                                url.searchParams.set(key, formData[key]);
                            } else {
                                url.searchParams.delete(key);
                            }
                        });

                        window.history.replaceState({}, '', url);
                    }
                }

                // function initializeCountdowns() {
                //     $('.countdown').each(function() {
                //         const existingInterval = $(this).data('interval');
                //         if (existingInterval) {
                //             clearInterval(existingInterval);
                //         }
                //     });

                //     $('.countdown').each(function() {
                //         const $this = $(this);
                //         const startDate = new Date($this.data('start')).getTime();
                //         const endDate = new Date($this.data('end')).getTime();
                //         const now = new Date().getTime();

                //         if (isNaN(startDate) || isNaN(endDate)) {
                //             return;
                //         }

                //         let targetDate = now < startDate ? startDate : endDate;

                //         const timer = setInterval(function() {
                //             const now = new Date().getTime();
                //             const distance = targetDate - now;

                //             if (distance < 0) {
                //                 clearInterval(timer);
                //                 $this.html('<span class="text-muted small">Event Ended</span>');
                //                 return;
                //             }

                //             const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                //             const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 *
                //                 60));
                //             const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                //             const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                //             $this.find('.days').text(String(days).padStart(2, '0'));
                //             $this.find('.hours').text(String(hours).padStart(2, '0'));
                //             $this.find('.minutes').text(String(minutes).padStart(2, '0'));
                //             $this.find('.seconds').text(String(seconds).padStart(2, '0'));
                //         }, 1000);

                //         $this.data('interval', timer);
                //     });
                // }

                // initializeCountdowns();

                if (typeof window.addEventListener !== 'undefined') {
                    window.addEventListener('popstate', function(event) {
                        location.reload();
                    });
                }
            }

            if (typeof jQuery !== 'undefined') {
                $(document).ready(function() {
                    console.log('jQuery loaded, DOM ready');
                    initializeFilters();
                });
            } else if (typeof $ !== 'undefined') {
                $(document).ready(function() {
                    console.log('$ loaded, DOM ready');
                    initializeFilters();
                });
            } else {
                document.addEventListener('DOMContentLoaded', function() {
                    console.log('No jQuery found, using vanilla JS fallback');
                    alert('jQuery is not loaded. Please include jQuery for filtering functionality to work.');
                });
            }
        })();
    </script>
@endsection
