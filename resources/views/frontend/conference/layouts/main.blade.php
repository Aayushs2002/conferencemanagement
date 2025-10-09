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
    <link rel="icon" href="assets/img/NESOG-logo.png">
    <!-- Site Title -->
    <title>{{ $conference->society->abbreviation }}</title>
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
</head>

<body>
    @include('frontend.conference.layouts.navbar')
    <section class="conference-hero" style="background-image: url('{{ asset('frontend/assets/img/BANNER.jpg') }}');">
        <div class="overlay"></div>
        <div class="container position-relative">

            <div class="row align-items-center justify-content-center hero-header mb-3">
                <div class="col-auto">
                    <img src="{{ Storage::url('society/logo/' . $conference->society->logo) }}" alt="NESOG Logo"
                        class="hero-logo">
                </div>

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

                <div class="col-auto">
                    <img src="{{ Storage::url('conference/conference/logo/' . $conference->conference_logo) }}"
                        alt="SAFOG Logo" class="hero-logo">
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
            <div class="row g-4 text-center dashboard-cards mb-5 align-items-center">
                <div class="col-md-3 col-6 position-relative">
                    <div class="dash-card p-4 rounded-4 shadow-sm">
                        <div class="dash-icon mb-2">
                            <img src="{{ asset('frontend/assets/img/frame 1.png') }}" alt="Conference Registration" />
                        </div>
                        <h5 class="dash-title mb-2">Conference Registration Open</h5>
                        <p class="dash-date mb-0">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($conference->created_at)->format('F j, Y') }}
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
                            {{ \Carbon\Carbon::parse($conference->submissionSetting->created_at)->format('F j, Y') }}

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
                    <i class="fa-solid fa-arrow-right-long dash-arrow d-none d-md-block"></i>
                </div>

                <div class="col-md-3 col-6">
                    <div class="dash-card p-4 rounded-4 shadow-sm">
                        <div class="dash-icon mb-2">
                            <img src="{{ asset('frontend/assets/img/frame 4.png') }}" alt="Workshops Registration" />
                        </div>
                        <h5 class="dash-title mb-2">Workshops Registration Open</h5>
                        <p class="dash-date mb-0">
                            <i class="fa-regular fa-calendar me-1"></i>
                            {{-- July 1, 2025 --}}
                            {{ \Carbon\Carbon::parse($conference->workshops->first()->created_at)->format('F j, Y') }}

                        </p>
                    </div>
                </div>
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
