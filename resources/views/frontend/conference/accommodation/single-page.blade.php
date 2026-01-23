@extends('frontend.conference.layouts.main')
@section('title')
    {{ $conference->society->sub_domain_name }} | {{ $hotel->name }}
@endsection
@section('content')
    <div class="td_height_60 td_height_lg_60"></div>
    <section class="hotel-section container">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('conference.name', $conference->slug) }}#travel" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to Accommodation
            </a>
        </div>

        <div class="row g-5 position-relative">
            <!-- LEFT CONTENT --> 
            <div class="col-lg-8 position-relative">
                <div class="d-flex align-items-center mb-4">
                    @if ($hotel->featured_image)
                        <img src="{{ Storage::url('hotel/featured-image/' . $hotel->featured_image) }}" alt="{{ $hotel->name }}" style="height: 60px; margin-right: 15px; border-radius: 8px; object-fit: cover;">
                    @endif
                    <div>
                        <h2 class="section-title mb-0">{{ $hotel->name }}</h2>
                        <p class="section-subtitle mb-0">{{ $hotel->address }}</p>
                    </div>
                </div>

                @if ($hotel->images && count($hotel->images) > 0)
                    <div id="hotelCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-3" style="overflow: hidden; height: auto;">
                            @foreach ($hotel->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ Storage::url('hotel/images/' . $image['fileName']) }}" class="d-block w-100"
                                        style="object-fit: cover; max-height: 400px; width: 100%;" alt="{{ $hotel->name }} - {{ $image['room_type'] ?? 'Image' }}">
                                </div>
                            @endforeach
                        </div>

                        @if (count($hotel->images) > 1)
                            <!-- Prev Button -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#hotelCarousel" data-bs-slide="prev"
                                style="top: 50%; transform: translateY(-50%); width: 8%; height: 8%; opacity: 0.9; font-weight: 900;">
                                <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 100%; height: 100%;"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>

                            <!-- Next Button -->
                            <button class="carousel-control-next" type="button" data-bs-target="#hotelCarousel" data-bs-slide="next"
                                style="top: 50%; transform: translateY(-50%); width: 8%; height: 8%; opacity: 0.9;">
                                <span class="carousel-control-next-icon" aria-hidden="true"
                                    style="width: 100%; height: 100%; font-weight: 900;"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>
                @endif

                <div style="background-color: #F1F4FC; padding: 40px; border-radius: 20px;">
                    @if ($hotel->description || $hotel->facility)
                        <h5 class="subtitle">About Hotel</h5>

                        @if ($hotel->description)
                            <h6 class="span-text mt-4 mb-3">Overview</h6>
                            <div>
                                {!! $hotel->description !!}
                            </div>
                        @endif

                        @if ($hotel->facility)
                            <h6 class="span-text mt-4 mb-3">Facilities</h6>
                            <div>
                                {!! $hotel->facility !!}
                            </div>
                        @endif
                    @endif
                </div>

                @if ($hotel->website)
                    <div class="mt-4">
                        <a href="{{ $hotel->website }}" target="_blank" class="view-hotel-btn">
                            View Hotel Website <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                @endif

                @if ($hotel->google_map)
                    <h3 class="section-title mt-5">Google Map Location</h3>
                    <div class="map-wrapper mt-4" style="background-color:white; border-radius:12px; overflow:hidden;">
                        <iframe src="{{ $hotel->google_map }}" width="100%" height="400" style="border:0;"
                            allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                @endif
            </div>

            <!-- RIGHT SIDEBAR -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top:20px;">
                    <div style="background-color: #F1F4FC; padding: 30px; border-radius: 20px;">
                        <h5 class="subtitle mb-4">Booking Information</h5>

                        @if ($hotel->price)
                            <h6 class="span-text mb-3">Price</h6>
                            <div class="payment-list">
                                {!! $hotel->price !!}
                            </div>
                        @endif

                        @if ($hotel->contact_person || $hotel->phone || $hotel->email)
                            <h6 class="span-text mt-5 mb-3">Contact Person</h6>
                            <div class="contact-section" style="line-height: 1.2;">
                                @if ($hotel->contact_person)
                                    <p class="contact-info mb-2"><strong>Name:</strong> {{ $hotel->contact_person }}</p>
                                @endif
                                @if ($hotel->phone)
                                    <p class="contact-info"><strong>Whatsapp Number:</strong> {{ $hotel->phone }}</p>
                                @endif
                            </div>
                        @endif

                        @if ($hotel->promo_code)
                            <h6 class="span-text mt-5 mb-3">Promo Code</h6>
                            <p class="mb-3">{{ $hotel->promo_code }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="td_height_80 td_height_lg_80"></div>
@endsection
