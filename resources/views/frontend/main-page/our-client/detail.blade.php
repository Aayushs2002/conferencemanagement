@extends('frontend.main-page.layouts.main')
@section('content')
    <section class="banner d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                            <li class="breadcrumb-item"><a href="index.html">Our Clients</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $society->abbreviation }}</li>
                        </ol>
                    </nav>
                    <h1 class="banner-title">{{ $society->abbreviation }}: Advancing <br> Women's Health</h1>
                    <p class="banner-sub">
                        NESOG's conferences empower obstetricians and gynecologists, fostering collaboration and
                        innovation to enhance reproductive health for Nepalese women.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="main-section">
        <section class="about-section">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-3 d-flex align-items-center">
                        <img {{ Storage::url('society/logo/' . $society->logo) }}" alt="{{ $society->name }}" class="me-3"
                            style="width:80px; height:auto;">
                        <h2 class="section-title mb-0">{{ $society->abbreviation }}
                            Overview</h2>
                    </div>
                    <div class="col-lg-7">
                        {{-- <p class="section-subtitle mb-4">
                            NESOG was established in Nov 14, 1989 with commitment of highly motivated team of
                            Obstetrician and Gynaecologist, Prof. D. S. Malla, Prof. S. M. Dali, Dr. Bhola Rijal, Dr.
                            Annapurna Shrestha, Dr. June Thapa, Dr. Mahodadhi Shrestha, Dr. Swaraj Rajbhandari and all
                            the well-wishers of NESOG from abroad including Prof. H. Soma from Japan, late Prof. S. S.
                            Ratnam from Singapore
                        </p>
                        <p class="section-subtitle">
                            Presently NESOG is working with government, NGOs and INGOs to uplift the status of
                            reproductive health of Nepalese women as an independent, non-profit oriented professional
                            organization, consisting of 16 executive members in a total of 700+ members besides 9
                            honorary members.
                        </p> --}}
                        {!! $society->description !!}
                    </div>
                </div>
            </div>
        </section>
    </section>

    <div class="td_height_60 td_height_lg_60"></div>
    <section class="conference-section">
        <div class="container">
            <h2 class="section-title text-center">Conferences List</h2>
            <ul class="nav nav-tabs mb-4 mt-5" id="conferenceTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'current' ? 'active' : '' }}" id="current-tab"
                        data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab" aria-controls="current"
                        aria-selected="{{ $activeTab === 'current' ? 'true' : 'false' }}">
                        Current Conference
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'previous' ? 'active' : '' }}" id="previous-tab"
                        data-bs-toggle="tab" data-bs-target="#previous" type="button" role="tab"
                        aria-controls="previous" aria-selected="{{ $activeTab === 'previous' ? 'true' : 'false' }}">
                        Previous Conference
                    </button>
                </li>
            </ul>


            <div class="tab-content" id="conferenceTabContent">
                <div class="tab-pane fade {{ $activeTab === 'current' ? 'show active' : '' }}" id="current"
                    role="tabpanel" aria-labelledby="current-tab">
                    <div class="row g-4">
                        @forelse ($currentConferences as $conference)
                            <div class="col-lg-4 col-md-6">
                                <a href="{{ route('conference.name', $conference->slug) }}"
                                    class="text-decoration-none conference-link">
                                    <div class="conference-card p-4 h-100">
                                        <div class="d-flex gap-2 mb-3">
                                            <img src="{{ Storage::url('society/logo/' . $conference->society->logo) }}"
                                                class="logo-img" alt="{{ $conference->conference_name }}" loading="lazy">
                                        </div>

                                        <h5 class="card-title">{{ $conference->conference_name }}</h5>
                                        <p class="text-muted small mb-3">{{ $conference->conference_theme }}</p>

                                        <div class="countdown d-flex mb-3 justify-content-center countdown-box"
                                            data-start="{{ $conference->start_date }}"
                                            data-end="{{ $conference->end_date }}">
                                            <div class="time-box"><span class="days">00</span><br><span>Days</span></div>
                                            <span class="sep">:</span>
                                            <div class="time-box"><span class="hours">00</span><br><span>Hrs</span></div>
                                            <span class="sep">:</span>
                                            <div class="time-box"><span class="minutes">00</span><br><span>Mins</span></div>
                                            <span class="sep">:</span>
                                            <div class="time-box"><span class="seconds">00</span><br><span>Secs</span></div>
                                        </div>

                                        <div class="mb-3">
                                            @foreach (explode(',', $conference->tags) as $tag)
                                                <span class="badge rounded-pill bg-primary me-1">{{ trim($tag) }}</span>
                                            @endforeach
                                        </div>

                                        <p class="small mb-1">
                                            <i class="fa-regular fa-calendar-days me-1"></i>
                                            {{ \Carbon\Carbon::parse($conference->start_date)->format('F jS, Y') }} -
                                            {{ \Carbon\Carbon::parse($conference->end_date)->format('F jS, Y') }}
                                        </p>
                                        <p class="small text-muted mb-0">
                                            <i class="fa-solid fa-location-dot me-1"></i>
                                            {{ $conference->ConferenceVenueDetail->venue_name ?? '' }},
                                            {{ $conference->ConferenceVenueDetail->venue_address ?? '' }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="text-center text-muted">No current conferences available.</p>
                        @endforelse
                    </div>

                    {{-- @if ($currentConferences->hasPages()) --}}

                    {{-- @endif --}}
                    {{ $currentConferences->links('vendor.pagination.custom') }}
                </div>



                <div class="tab-pane fade {{ $activeTab === 'previous' ? 'show active' : '' }}" id="previous"
                    role="tabpanel" aria-labelledby="previous-tab">
                    <div class="row g-4">
                        @forelse ($previousConferences as $conference)
                            <div class="col-lg-4 col-md-6">
                                <a href="{{ route('conference.name', $conference->slug) }}"
                                    class="text-decoration-none conference-link">
                                    <div class="conference-card p-4 h-100">
                                        <div class="d-flex gap-2 mb-3">
                                            <img src="{{ Storage::url('society/logo/' . $conference->society->logo) }}"
                                                class="logo-img" alt="{{ $conference->conference_name }}"
                                                loading="lazy">
                                        </div>

                                        <h5 class="card-title">{{ $conference->conference_name }}</h5>
                                        <p class="text-muted small mb-3">{{ $conference->conference_theme }}</p>

                                        <div class="mb-3">
                                            @foreach (explode(',', $conference->tags) as $tag)
                                                <span
                                                    class="badge rounded-pill bg-primary me-1">{{ trim($tag) }}</span>
                                            @endforeach
                                        </div>

                                        <p class="small mb-1">
                                            <i class="fa-regular fa-calendar-days me-1"></i>
                                            {{ \Carbon\Carbon::parse($conference->start_date)->format('F jS, Y') }} -
                                            {{ \Carbon\Carbon::parse($conference->end_date)->format('F jS, Y') }}
                                        </p>
                                        <p class="small text-muted mb-0">
                                            <i class="fa-solid fa-location-dot me-1"></i>
                                            {{ $conference->ConferenceVenueDetail->venue_name ?? '' }},
                                            {{ $conference->ConferenceVenueDetail->venue_address ?? '' }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="text-center text-muted">No previous conferences found.</p>
                        @endforelse
                    </div>
                    {{-- 
                    @if ($previousConferences->hasPages())
                        <div class="mt-4 d-flex justify-content-center"> --}}
                    {{ $previousConferences->links('vendor.pagination.custom') }}

                </div>
                {{-- @endif --}}
            </div>


        </div>
        </div>
        {{-- <nav aria-label="Pagination" class="my-4">
            <ul class="pagination justify-content-center">
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Previous">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>
                </li>
                <li class="page-item active">
                    <a class="page-link" href="#">01</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">02</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                </li>
            </ul>
        </nav> --}}
    </section>
    <div class="td_height_60 td_height_lg_60"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle hash navigation (optional, for manual hash changes)
            const hash = window.location.hash;

            if (hash === '#previous') {
                const prevTab = document.querySelector('#previous-tab');
                if (prevTab) new bootstrap.Tab(prevTab).show();
            } else if (hash === '#current') {
                const currTab = document.querySelector('#current-tab');
                if (currTab) new bootstrap.Tab(currTab).show();
            }
        });
    </script>
@endsection
