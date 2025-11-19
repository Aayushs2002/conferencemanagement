@extends('frontend.conference.layouts.main')
@section('content')
    <section class="container my-5">
        <div class="mb-2">
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>
        <div class="row g-5">
            <!-- Sidebar -->
            <aside class="col-lg-3 order-1 order-lg-2">
                <div class="sidebar-sticky" style="position:sticky; top:20px;">
                    <div class="nav flex-column nav-pills mb-4" id="safogTabs" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active d-flex align-items-center" data-filter="all" type="button">
                            <i class="fa-solid fa-users me-2"></i> <span>All Speakers</span>
                        </button>
                        {{-- <button class="nav-link d-flex align-items-center" data-filter="faculty" type="button">
                            <i class="fa-solid fa-user-graduate me-2"></i> <span>Faculty</span>
                        </button> --}}
                        <button class="nav-link d-flex align-items-center" data-filter="international" type="button">
                            <i class="fa-solid fa-globe me-2"></i> <span>International</span>
                        </button>
                        <button class="nav-link d-flex align-items-center" data-filter="national" type="button">
                            <i class="fa-solid fa-flag me-2"></i> <span>National</span>
                        </button>
                    </div>
                    <div class="payment-box p-3 rounded-4  text-center">
                        <h4 class="box-title">Payment Methods</h4>
                        @if ($conference->society->internationalPaymentSetting)
                            <h6 class="mb-2 fw-600">For International Delegates</h6>
                            <div class="d-flex justify-content-between align-items-center mb-3 payment-logos">
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_1.png') }}" alt="Logo 1"
                                        class="logo-img">
                                </div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_2.png') }}" alt="Logo 2"
                                        class="logo-img">
                                </div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_3.png') }}" alt="Logo 3"
                                        class="logo-img">
                                </div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_4.png') }}" alt="Logo 4"
                                        class="logo-img">
                                </div>
                            </div>

                            <h6 class="mb-2 fw-600">For Indian Delegates</h6>
                            <div class="d-flex justify-content-between align-items-center mb-3 payment-logos">
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_4.png') }}" alt="Logo 1"
                                        class="logo-img">
                                </div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_2.png') }}" alt="Logo 2"
                                        class="logo-img">
                                </div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_3.png') }}" alt="Logo 3"
                                        class="logo-img">
                                </div>
                            </div>
                        @endif


                        <h6 class="mb-2 fw-600">For Nepali Delegates</h6>
                        <div class="d-flex justify-content-between align-items-center payment-logos">
                            {{-- <div class="logo-item"><img src="{{ asset('frontend/assets/img/esewa-icon-large.png') }}"
                                    alt="eSewa" class="logo-img">
                            </div>
                            <div class="logo-item"><img src="{{ asset('frontend/assets/img/khalti-ime-logo.png') }}"
                                    alt="Khalti" class="logo-img">
                            </div>
                            <div class="logo-item"><img src="{{ asset('frontend/assets/img/logo-1 (1).png') }}"
                                    alt="Bank Transfer" class="logo-img"></div> --}}
                            @if ($conference->society->nationalPaymentSetting?->esewa_product_key)
                                <div class="logo-item"><img src="{{ asset('frontend/assets/img/esewa-icon-large.png') }}"
                                        alt="eSewa" class="logo-img">
                                </div>
                            @endif
                            @if ($conference->society->nationalPaymentSetting?->khalti_live_secret_key)
                                <div class="logo-item"><img src="{{ asset('frontend/assets/img/khalti-ime-logo.png') }}"
                                        alt="Khalti" class="logo-img">
                                </div>
                            @endif
                            @if ($conference->society->nationalPaymentSetting?->moco_shared_key)
                                <div class="logo-item"><img src="{{ asset('frontend/assets/img/logo-1 (1).png') }}"
                                        alt="Bank Transfer" class="logo-img"></div>
                            @endif

                            @if (
                                $conference->society->nationalPaymentSetting?->profile_id &&
                                    $conference->society->nationalPaymentSetting?->secret_key)
                                <div class="logo-item"><img src="{{ asset('frontend/assets/img/unnamed.png') }}"
                                        alt="Fone Pay" class="logo-img"></div>
                            @endif

                        </div>
                    </div>
            </aside>

            <!-- Main Content -->
            <main class="col-lg-9 order-2 order-lg-1">
                <div style="background:#F1F4FC;padding:40px;border-radius:20px;">
                    <h2 class="section-title mb-4">Our Distinguished Speakers</h2>
                    <p class="span-text mt-5">All Speakers</p>
                    <div class="row g-4" id="speakersGrid">
                        @foreach ($allSpeaker as $speaker)
                            @php
                                $user = $speaker->user;
                                $detail = $user->userDetail;
                                $country = optional($detail->country)->country_name;
                                $isNational = $detail && $detail->country_id == 125;
                            @endphp

                            <div class="col-md-4 speaker-card"
                                data-category="{{ $isNational ? 'national' : 'international' }}">
                                <div class="prof-card p-3 d-flex flex-column h-100">
                                    <img src="{{ $detail && $detail->image ? Storage::url('profile/image/' . $detail->image) : asset('frontend/assets/img/user.jpg') }}"
                                        alt="{{ $user->f_name }}" class="profile-img mb-3">
                                    <h6 class="card-title d-flex align-items-center mb-0">
                                        {{ $user->fullName($user) }}
                                        <span
                                            class="country-flag flag-{{ strtolower(str_replace(' ', '-', $country)) }}"></span>
                                    </h6>
                                    <small class="card-subtitle">
                                        {{ $detail->designation->designation ?? '' }}<br>
                                        {{ $detail->institution->name ?? '' }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </main>
        </div>
    </section>
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fa-solid fa-users-rectangle"></i> Speakers Filter
    </button>
    <div class="mobile-sidebar" id="mobileSidebar">
        <button class="close-sidebar" id="closeSidebar">
            <i class="fa-solid fa-times"></i>
        </button>
        <div class="nav flex-column nav-pills mb-4" id="safogTabsMobile" role="tablist">
            <button class="nav-link active d-flex align-items-center" data-filter="all" type="button">
                <i class="fa-solid fa-users me-2"></i> <span>All Speakers</span>
            </button>
            {{-- <button class="nav-link d-flex align-items-center" data-filter="faculty" type="button">
                <i class="fa-solid fa-user-graduate me-2"></i> <span>Faculty</span>
            </button> --}}
            <button class="nav-link d-flex align-items-center" data-filter="international" type="button">
                <i class="fa-solid fa-globe me-2"></i> <span>International</span>
            </button>
            <button class="nav-link d-flex align-items-center" data-filter="national" type="button">
                <i class="fa-solid fa-flag me-2"></i> <span>National</span>
            </button>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="td_height_80 td_height_lg_80"></div>
@endsection
