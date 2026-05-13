<!DOCTYPE html>
<html class="no-js" lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <!-- Favicon Icon -->
    <link rel="icon" href="{{ Storage::url('society/logo/' . $conference->society->logo) }}">
    <!-- Site Title -->
    <title>
        @section('title') @show
    </title>
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/slick.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/odometer.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/Custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --white-01-color: #fff;
            --white-02-color: #f2f2f2;
            --white-03-color: #f9f9f9;
            --hover-blue: #000A26;
            --Dark-blue: #00154E;
            --primary-color: {{ $conference->primary_color }};
            --body-color: #555;
            --blue01-color: #CEDDFF;
            --blue02-color: #F1F4FC;
            --Secondary-color: {{ $conference->secendary_color }};
            --black-01_color: #131313;
            --black-02_color: #00001B;
            --gray-03-color: #888888;
            --gray-04-color: #A4A3A1;
            --gray-05-color: #DDDDDD;
            --gray-06-color: #E9E9E9;
            --heading-font: "Sora", sans-serif;
            --body-font: "Arial", sans-serif;

        }
    </style>

    @php
        $customCss = $conference->customCss->first();
    @endphp

    @if ($customCss && $customCss->status && $customCss->custom_css)
        <style>
            {!! $customCss->custom_css !!}
        </style>
    @endif
</head>

<body>
    @include('frontend.conference.layouts.navbar')
    <section class="conference-hero"
        style="background-image: url('{{ $conference->conference_banner ? Storage::url('conference/conference/banner/' . $conference->conference_banner) : asset('frontend/assets/img/BANNER.jpg') }}');">
        <div class="overlay"></div>
        <div class="container position-relative">

            <div class="d-flex align-items-center justify-content-center gap-4 hero-logos mb-4">
                <img src="{{ Storage::url('society/logo/' . $conference->society->logo) }}" alt="NESOG Logo"
                    class="hero-logo main-logo">
                    @if (is_array($conference->partner_logos) && count($conference->partner_logos) > 0)
                        <div class="d-flex align-items-center gap-3">
                            @foreach ($conference->partner_logos as $logo)
                                <img src="{{ Storage::url('conference/partner-logos/' . $logo) }}" alt="Partner Logo"
                                    class="hero-logo">
                            @endforeach
                        </div>
                    @endif
            </div>
            <div class="row align-items-center justify-content-center hero-header mb-3">

                <div class="col-auto text-center">
                    <h1 class="hero-title mb-1">
                        {{-- 15<sup>th</sup> SAF0GCON 2025 --}}
                        {{ $conference->conference_name }}
                    </h1>
                    <p class="hero-subtitle text-center">
                        {{-- Advancing Women’s Health in South Asia – Quality, Innovation <br>and Sustainability --}}
                        {{ $conference->conference_theme }}
                    </p>
                </div>



            </div>

            <div class="row align-items-center hero-info position-relative">
                <div class="col-lg-4 position-absolute start-0 top-50 translate-middle-y">
                    <div class="info-box p-4">
                        <h4 class="info-title mb-2">Conference Info:</h4>
                        <div class="info-container">
                            <p class="info-item mb-2">
                                <i class="fa-regular fa-calendar-days"></i>
                                @php
                                    use Carbon\Carbon;

                                    $start = Carbon::parse($conference->start_date);
                                    $end = Carbon::parse($conference->end_date);

                                    if ($start->format('F Y') === $end->format('F Y')) {
                                        $formattedDate = $start->format('jS') . ' – ' . $end->format('jS F, Y');
                                    } else {
                                        $formattedDate = $start->format('jS F, Y') . ' – ' . $end->format('jS F, Y');
                                    }
                                @endphp

                                <strong>Date:</strong> {{ $formattedDate }}
                            </p>
                            <p class="info-item mb-0">
                                <i class="fa-solid fa-location-dot"></i>
                                <span><strong>Venue:</strong> {{ $conference->ConferenceVenueDetail->venue_name }},
                                    {{ $conference->ConferenceVenueDetail->venue_address }}</span>
                            </p>

                        </div>

                    </div>
                </div>

                <div class="col-lg-4 position-absolute end-0 top-50 translate-middle-y text-center">
                    <div class="countdown-container">
                        <h3 class="countdown-title">Conference Countdown:</h3>
                        <div class="countdown-box mt-0" data-start="{{ $conference->start_date }}"
                            data-end="{{ $conference->end_date }}">
                            <div class="time-wrapper">
                                <div class="time-box days" id="days">00</div>
                                <span class="time-label">Days</span>
                            </div>
                            <span class="time-sep">:</span>
                            <div class="time-wrapper">
                                <div class="time-box hours" id="hours">00</div>
                                <span class="time-label">Hrs</span>
                            </div>
                            <span class="time-sep">:</span>
                            <div class="time-wrapper">
                                <div class="time-box minutes" id="minutes">00</div>
                                <span class="time-label">Mins</span>
                            </div>
                            <span class="time-sep">:</span>
                            <div class="time-wrapper">
                                <div class="time-box seconds" id="seconds">00</div>
                                <span class="time-label">Secs</span>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </section>
    <section class="main-section">
        <section class="container">
            <div class="row g-4 text-center dashboard-cards mb-5 align-items-center justify-content-center">
                <div class="col-md-3 col-6 position-relative">
                    <div class="dash-card p-4 rounded-4 shadow-sm">
                        <div class="dash-icon mb-2">
                            <img src="{{ asset('frontend/assets/img/frame 1.png') }}" alt="Conference Registration" />
                        </div>
                        <h5 class="dash-title mb-2">Conference Registration Open</h5>
                        <p class="dash-date mb-0">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{-- {{ \Carbon\Carbon::parse($conference->created_at)->format('F j, Y') }} --}}
                            {{ $conference->conferenceSetting?->registration_open_date ? \Carbon\Carbon::parse($conference->conferenceSetting->registration_open_date)->format('F j, Y') : \Carbon\Carbon::parse($conference->created_at)->format('F j, Y') }}

                            {{-- @dd($conference) --}}
                        </p>
                    </div>
                    <i class="fa-solid fa-arrow-right-long dash-arrow d-none d-md-block"></i>
                </div> 

                <div class="col-md-3 col-6 position-relative">
                    <div class="dash-card p-4 rounded-4 shadow-sm">
                        <div class="dash-icon mb-2">
                            <img src="{{ asset('frontend/assets/img/frame 2.png') }}" alt="Abstract Submission" />
                        </div>
                        <h5 class="dash-title mb-2">Abstract Submission Open</h5>
                        <p class="dash-date mb-0">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{-- July 1, 2025 --}}

                            {{ $conference->submissionSetting?->submission_open_date ? \Carbon\Carbon::parse($conference->submissionSetting->submission_open_date)->format('F j, Y') : \Carbon\Carbon::parse($conference->submissionSetting?->created_at)->format('F j, Y') }}

                        </p>
                    </div>
                    <i class="fa-solid fa-arrow-right-long dash-arrow d-none d-md-block"></i>
                </div>

                <div class="col-md-3 col-6 position-relative">
                    <div class="dash-card p-4 rounded-4 shadow-sm">
                        <div class="dash-icon mb-2">
                            <img src="{{ asset('frontend/assets/img/frame 3.png') }}"
                                alt="Early Bird Registration" />
                        </div>
                        <h5 class="dash-title mb-2">Early-bird Registration Till</h5>
                        <p class="dash-date mb-0">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($conference->early_bird_registration_deadline)->format('F j, Y') }}

                        </p>
                    </div>
                    @php
                        $workshop = $conference->workshops?->where('conference_id', $conference->id)->first();
                    @endphp
                    @if ($workshop)
                        <i class="fa-solid fa-arrow-right-long dash-arrow d-none d-md-block"></i>
                    @endif
                </div>
                @if ($workshop)
                    <div class="col-md-3 col-6">
                        <div class="dash-card p-4 rounded-4 shadow-sm">
                            <div class="dash-icon mb-2">
                                <img src="{{ asset('frontend/assets/img/frame 4.png') }}"
                                    alt="Workshops Registration" />
                            </div>
                            <h5 class="dash-title mb-2">Workshops Registration Open</h5>
                            <p class="dash-date mb-0">
                                <i class="fa-regular fa-calendar me-1"></i>

                                {{-- @php
                                $workshop = $conference->workshops?->where('conference_id', $conference->id)->first();
                            @endphp --}}

                                {{ $conference->conferenceSetting?->workshop_registration_open_date ? \Carbon\Carbon::parse($conference->conferenceSetting->workshop_registration_open_date)->format('F j, Y') : ($workshop?->created_at ? $workshop->created_at->format('F j, Y') : 'N/A') }}


                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </section>



    @yield('content')
    @include('frontend.conference.layouts.footer')


    <!-- End Scroll Up Button -->
    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.slick.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/odometer.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>
    <script>
        const counters = document.querySelectorAll('.stat-number');

        counters.forEach(counter => {
            counter.innerText = '0';
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const increment = Math.ceil(target / 200);

                if (count < target) {
                    counter.innerText = count + increment;
                    setTimeout(updateCount, 50);
                } else {
                    counter.innerText = target + '+';
                }
            };
            updateCount();
        });
    </script>
</body>

</html>
