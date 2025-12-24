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
                <div style="background-color: #F1F4FC; padding: 40px; border-radius: 20px;">
                    <h2 class="section-title">{{ $hotel->name }}</h2>
                    <p class="section-subtitle">{{ $hotel->address }}</p>

                    @if ($hotel->price)
                        <h5 class="subtitle mt-4">Booking Information</h5>
                        <p class="span-text mt-4">Price</p>
                        <div class="mt-3">
                            {!! $hotel->price !!}
                        </div>
                    @endif

                    @if ($hotel->contact_person || $hotel->phone || $hotel->email)
                        <p class="span-text mt-5">Contact Person</p>
                        <div class="contact-section mt-4" style="line-height: 0.8;">
                            <p class="contact-info"><strong>Name:</strong> {{ $hotel->contact_person }}</p>
                            <p class="contact-info"><strong>Whatsapp Number:</strong>{{ $hotel->phone }}</p>
                        </div>
                    @endif
                    @if ($hotel->promo_code)
                        <p class="span-text mt-5">Promo Code</p>
                        <p>{{ $hotel->promo_code }}</p>
                    @endif
                    {{-- <div class="contact-section mt-4" style="line-height: 0.8;">
                    <p class="contact-info"><strong>Note:</strong> 30% off using promo code on the displayed rate on
                        website.
                    </p>
                    <p class="contact-info"><strong>Valid:</strong> 2nd April – 7th April 2025</p>
                    <p class="contact-info"><strong>Bookable:</strong> Open Now</p>
                </div> --}}
                    @if ($hotel->description || $hotel->facility)
                        @if ($hotel->description)
                            <h5 class="subtitle mt-5">About Hotel</h5>
                            <p class="span-text mt-4 mb-4">Overview</p>
                            <div class="mt-3">
                                {!! $hotel->description !!}
                            </div>
                        @endif
                        @if ($hotel->facility)
                            <h5 class="span-text mt-4 mb-4">Facilities</h5>
                            <div class="mt-3">
                                {!! $hotel->facility !!}
                            </div>
                            {{-- <ul class="hotel-list">
                        <li>Pick up and drop from/to airport and conference site (Hyatt Regency) → Shuttle Bus</li>
                        <li>Bed, Breakfast & WIFI included</li>
                        <li>Swimming pool, spa & wellness center</li>
                        <li>Lush garden spaces for relaxation</li>
                        <li>Multiple dining options serving local & international cuisine</li>
                    </ul> --}}
                        @endif
                    @endif

                    {{-- <h5 class="span-text mt-4">Location</h5>
                    <p>Main Conference Venue</p> --}}
                </div>
                @if ($hotel->google_map)
                    <h3 class="section-title mt-5">Google Map Location</h3>
                    <div class="map-wrapper mt-4" style="background-color:white; border-radius:12px; overflow:hidden;">

                        <iframe src="{{ $hotel->google_map }}" width="100%" height="400" style="border:0;"
                            allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                @endif

            </div>
            <div class="col-lg-4">
                <div class="sticky-top" style="top:20px;">
                    @if ($hotel->featured_image)
                        <img src="{{ Storage::url('hotel/featured-image/' . $hotel->featured_image) }}"
                            alt="{{ $hotel->name }}" class="hotel-img1 mb-4 img-fluid rounded">
                    @endif

                    @if ($hotel->images && count($hotel->images) > 0)
                        <div class="mb-4">
                            @foreach ($hotel->images as $index => $image)
                                <img src="{{ Storage::url('hotel/images/' . $image['fileName']) }}"
                                    alt="{{ $hotel->name }} - {{ $image['room_type'] ?? 'Image' }}"
                                    class="hotel-img1 mb-3 img-fluid rounded">
                                @if (isset($image['room_type']))
                                    <p class="text-center mb-3"><strong>{{ $image['room_type'] }}</strong></p>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if ($hotel->website)
                        <a href="{{ $hotel->website }}" target="_blank" class="view-hotel-btn mb-4">
                            View Hotel Website <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="td_height_80 td_height_lg_80"></div>
@endsection
