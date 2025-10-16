@extends('frontend.main-page.layouts.main')
@section('content')
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-6">
                    <div class="hero-content glass-box">
                        <h1 class="hero-title">
                            MedConAlert:<br>
                            <span>Smarter Conference Management – Simplified</span>
                        </h1>
                        <p class="hero-subtitle">
                            All-in-one registrations, abstracts, sessions, workshops, sponsors,
                            Name TAG Card, QR and certifications used by leading medical institutions.
                        </p>
                        <a href="#" class="btn btn-outline-primary">
                            Upcoming Conferences
                            <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 hero-image">
                    <img src="{{ asset('frontend/assets/img/Group 9.png') }}" alt="Medical Conference" class="img-fluid">
                </div>
            </div>
        </div>
    </section>
    <section class="main-section">
        <section class="conference-section">
            <div class="container" style="background-color: rgba(241, 244, 252, 1); padding: 35px; border-radius: 30px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title">Upcoming Conferences</h2>
                    <a href="{{ route('conference') }}" class="btn default-btn">
                        View All Conferences <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                    </a>
                </div>
                <div class="row g-4 mb-5">
                    @foreach ($conferences as $conference)
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('conference.name', $conference->slug) }}"
                                class="text-decoration-none conference-link">
                                <div class="conference-card p-4 h-100">
                                    <div class="d-flex gap-2 mb-3">
                                        <img src="{{ Storage::url('society/logo/' . $conference->society->logo) }}"
                                            class="logo-img" alt="{{ $conference->conference_name }}" loading="lazy">
                                    </div>
                                    <h5 class="card-title">{{ $conference->conference_name }}</h5>
                                    <p class="text-muted small mb-3">
                                        {{ $conference->conference_theme }}
                                    </p> 

                                    <div class="countdown d-flex mb-3 justify-content-center countdown-box"
                                        data-start="{{ $conference->start_date }}" data-end="{{ $conference->end_date }}">
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
                                        {{ $conference->ConferenceVenueDetail->venue_name . ', ' . $conference->ConferenceVenueDetail->venue_address }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    </section>
    <div class="td_height_80 td_height_lg_80"></div>
    <section class="solutions-section">
        <div class="container text-center">
            <h2 class="section-title text-center">Our Solutions</h2>
            <p class="section-subtitle">
                MedConAlert streamlines medical conferences, boosting collaboration and
                innovation with real-time, data-driven solutions.
            </p>

            <h5 class="fw-semibold mb-4 mt-5" style="color: black;">
                Discover MedConAlert in Action
            </h5>

            @php
                $first = true;
            @endphp

            <ul class="nav nav-pills justify-content-center flex-wrap gap-2 mb-5" id="solutionsTabs" role="tablist">
                @foreach ($features as $index => $feature)
                    <li class="nav-item" role="presentation">
                        <button class="solutionsnav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $index }}"
                            data-bs-toggle="pill" data-bs-target="#pane-{{ $index }}" type="button" role="tab">
                            {{ $feature->title }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="solutionsTabsContent">
                @foreach ($features as $index => $feature)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $index }}"
                        role="tabpanel">
                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div class="feature-card d-flex flex-column flex-lg-row rounded-4 overflow-hidden">
                                    <div class="col-12 col-lg-6 feature-text p-5 text-start text-white">
                                        <h4 class="fw-bold mb-4">{{ $feature->title }}</h4>
                                        <ul class="mb-5 ps-3 py-3">
                                            {!! $feature->description !!}
                                            {{-- @foreach (explode("\n", $feature->description) as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach --}}
                                        </ul>
                                        <a href="{{ $feature->link ?? '#' }}" class="btn default-btn mt-5">
                                            Explore More <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                                        </a>
                                    </div>
                                    <div
                                        class="col-12 col-lg-6 feature-image d-flex align-items-center justify-content-center">
                                        <img src="{{ Storage::url('feature/image/' . $feature->image) }}"
                                            class="img-fluid p-4" alt="{{ $feature->title }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <div class="container mt-5">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-10">

                        <p class="author-quote mb-3">
                            “MedConAlert has transformed the way we manage our medical conferences. From registrations to
                            certificates, everything runs smoothly, saving our team countless hours and improving the
                            delegate
                            experience.”
                        </p>

                        <p class="author-name mt-3 ">
                            Conference Organizer, Nepal Medical Association
                        </p>
                    </div>
                </div>
            </div>
    </section>
    <div class="td_height_80 td_height_lg_80"></div>
    <section class="feature-banner position-relative overflow-hidden">
        <h2 class="section-title text-center">The MedConAlert<br>Features</h2>
        <div class="container position-relative text-center p-0">
            <img src="{{ asset('frontend/assets/img/conference banner 1.png') }}" class="img-fluid banner-img"
                alt="MedConAlert Banner">

            <div class="banner-text position-absolute top-0 start-50 translate-middle-x w-100 text-center">
                <p class="Feature-title mt-4">All Your Conference Management<br>in One Place</p>
            </div>

        </div>
    </section>


    <div class="td_height_80 td_height_lg_80"></div>
    <section class="why-choose-us">
        <div class="container">
            <div class="row align-items-start">
                <div class="col-lg-6">
                    <h2 class="section-title mb-3">Why Choose Us?</h2>
                    <p class="section-subtitle mb-5">
                        MedConAlert is designed to simplify every step of conference
                        management, from planning to execution.
                    </p>

                    <div id="textCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
                        data-bs-interval="3000">
                        <div class="carousel-inner">
                            @foreach ($whyChooseUs as $index => $whyChoose)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="carousel-text">
                                        <h3 class="carousel-title">
                                            <span class="number">{{ $loop->iteration }}.</span>
                                            {{ $whyChoose->title }}
                                        </h3>
                                        <p class="carousel-desc">
                                            {!! $whyChoose->description !!}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 d-flex justify-content-end">
                    <div id="imageCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
                        data-bs-interval="3000">
                        <div class="carousel-inner">
                            @foreach ($whyChooseUs as $index => $whyChoose)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <img src="{{ Storage::url('whyChooseUs/image/' . $whyChoose->image) }}"
                                        class="carousel-image img-fluid" alt="{{ $whyChoose->title }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="carousel-indicators-wrapper text-center mt-4">
                <div class="carousel-indicators">
                    @foreach ($whyChooseUs as $index => $whyChoose)
                        <button type="button" data-bs-target="#textCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $loop->first ? 'active' : '' }}">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>



    <div class="td_height_100 td_height_lg_80"></div>
    <section class="partners-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4">
                    <h2 class="section-title">Our Partners</h2>
                    <p class="section-subtitle">We collaborate with industry-leading organizations to bring you the best
                        services.
                    </p>
                </div>
                <div class="col-lg-8">
                    <div class="logo-slider-wrapper">
                        <div class="logo-slider">
                            @foreach ($societies as $society)
                                <div class="logo-item">
                                    <img src="{{ Storage::url('society/logo/' . $society->logo) }}"
                                        alt="{{ $society->abbrevation }}" class="img-fluid" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="td_height_80 td_height_lg_80"></div>
    <section class="testimonial-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title text-center">What Our Clients Say</h2>
                <p class="section-subtitle mx-auto" style="max-width: 800px;">
                    Trusted by leading organizers and institutions, MedConAlert has transformed the way conferences are
                    planned
                    and managed. Hear directly from our clients about their experience.
                </p>
            </div>

            <div class="testimonial-container">
                <div class="testimonial-slider">
                    @foreach ($testimonials as $testimonial)
                        <div class="testimonial-card mt-5">
                            <div class="testimonial-rectangle"></div>

                            <img src="{{ Storage::url('testimonial/image/' . $testimonial->image) }}"
                                alt="{{ $testimonial->name }}" loading="lazy">

                            <div class="testimonial-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $testimonial->rating)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>

                            <div class="testimonial-name">{{ $testimonial->name }}</div>

                            <div class="testimonial-position">
                                {{ $testimonial->designation . ', ' . $testimonial->organization_name }}
                            </div>

                            <div class="testimonial-content">
                                <i class="fas fa-quote-left fa-lg"></i>
                                <p>{!! $testimonial->description !!}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

    <div class="td_height_80 td_height_lg_80"></div>
    <section class="blog-section">
        <div class="container">
            <div class="container mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="section-title mb-2">Latest Insights & Blogs</h2>
                        <p class="section-subtitle mb-0">
                            Stay informed with expert insights, industry trends, and best practices for medical conference
                            management
                            and healthcare education.
                        </p>
                    </div>

                    <div class="col-md-6 text-md-end text-start mt-3 mt-md-0">
                        <a href="{{ route('blog') }}" class="btn default-btn">
                            View All Blogs <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                @foreach ($blogs as $blog)
                    <div class="blog-card col-6">
                        <div class="card position-relative text-white">
                            <img src="{{ Storage::url('blog/image/' . $blog->image) }}" class="card-img"
                                alt="{{ $blog->title }}">
                            <div class="card-overlay">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div class="card-left">
                                        <h4 class="blog-title text-white mb-2">{{ $blog->title }}</h4>
                                        <div class="card-date mb-2">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            <span>{{ \Carbon\Carbon::parse($blog->created_at)->format('Y/m/d') }}</span>
                                        </div>
                                        <a href="{{ route('blog.single-page', $blog->slug) }}"
                                            class="btn default-btn mb-2">
                                            View Details<i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                                        </a>
                                    </div>
                                    <div class="card-content mb-2" style="text-align:left;">
                                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($blog->description), 160, '...') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-bottom"></div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <div class="td_height_80 td_height_lg_80"></div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const textCarousel = new bootstrap.Carousel('#textCarousel', {
                interval: 3000,
                wrap: true
            });
            const imageCarousel = new bootstrap.Carousel('#imageCarousel', {
                interval: 3000,
                wrap: true
            });
            const indicators = document.querySelectorAll('.carousel-indicators button');

            function setActiveIndicator(index) {
                indicators.forEach((btn, i) => btn.classList.toggle('active', i === index));
            }

            document.querySelector('#textCarousel').addEventListener('slide.bs.carousel', e => {
                imageCarousel.to(e.to);
                setActiveIndicator(e.to);
            });

            indicators.forEach((btn, i) => btn.addEventListener('click', () => {
                textCarousel.to(i);
                imageCarousel.to(i);
                setActiveIndicator(i);
            }));
        });
    </script>
@endsection
