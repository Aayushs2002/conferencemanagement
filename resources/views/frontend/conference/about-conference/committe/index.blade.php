@extends('frontend.conference.layouts.main')
@section('title')
    {{ $conference->society->sub_domain_name }} | Committee
@endsection

@section('styles')
    @if($staticPageEnabled)
    <style>
        .committee-static-content {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            min-height: 400px;
        }

        .committee-static-content h1,
        .committee-static-content h2,
        .committee-static-content h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .committee-static-content p {
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        .committee-static-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1rem 0;
        }

        .committee-static-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .committee-static-content table td,
        .committee-static-content table th {
            padding: 0.75rem;
            border: 1px solid #ddd;
        }

        .committee-static-content table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .committee-static-content ul,
        .committee-static-content ol {
            margin-bottom: 1rem;
            padding-left: 2rem;
        }

        .committee-static-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
    </style>
    @endif
@endsection

@section('content')
    <section class="container">
        <div class="mb-2">
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>

        @if($staticPageEnabled)
            <!-- Static Page Content -->
            <div class="row">
                <div class="col-12">
                    <div class="committee-static-content">
                        {!! $staticContent !!}
                    </div>
                </div>
            </div>

            <!-- Payment Box for Static Page -->
            {{-- <div class="row mt-5">
                <div class="col-12">
                    <div class="payment-box p-4 rounded-4">
                        <h4 class="box-title text-center mb-4">Payment Methods</h4>
                        <h6 class="mb-3 fw-600 text-center">For Nepali Delegates</h6>
                        <div class="d-flex justify-content-center gap-3 flex-wrap align-items-center payment-logos mb-4">
                            @if ($conference->society->nationalPaymentSetting?->esewa_product_code)
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
                                <div class=""><img style="height: 80px !important;"
                                        src="{{ asset('frontend/assets/img/unnamed.png') }}" alt="Fone Pay" class="">
                                </div>
                            @endif
                        </div>
                        @if ($conference->society->internationalPaymentSetting)
                            <h6 class="mb-3 fw-600 text-center">For International Delegates</h6>
                            <div class="d-flex justify-content-center gap-3 flex-wrap align-items-center mb-4 payment-logos">
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_1.png') }}"
                                        alt="Visa" class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_2.png') }}"
                                        alt="Mastercard" class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_3.png') }}"
                                        alt="PayPal" class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_4.png') }}"
                                        alt="Amex" class="logo-img"></div>
                            </div>

                            <h6 class="mb-3 fw-600 text-center">For Indian Delegates</h6>
                            <div class="d-flex justify-content-center gap-3 flex-wrap align-items-center payment-logos">
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_4.png') }}"
                                        alt="Payment Method 1" class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_2.png') }}"
                                        alt="Payment Method 2" class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_3.png') }}"
                                        alt="Payment Method 3" class="logo-img"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div> --}}
        @else
            <!-- Dynamic Committee List -->
            <div class="row g-5">
                <aside class="col-lg-3 order-1 order-lg-2">
                    <div class="sidebar-sticky" style="position:sticky; top:20px;">
                        <div class="nav flex-column nav-pills mb-4" id="safogTabs" role="tablist" aria-orientation="vertical">
                            @foreach ($committees as $index => $committee)
                                <button class="nav-link d-flex align-items-center {{ $index === 0 ? 'active' : '' }}"
                                    id="tab-{{ $committee->slug }}" data-bs-toggle="pill"
                                    data-bs-target="#{{ $committee->slug }}" type="button" role="tab">
                                    <i class="fa-solid fa-people-group me-2"></i>
                                    <span>{{ $committee->committee_name }}</span>
                                </button>
                            @endforeach
                        </div>

                        {{-- Payment Box (static) --}}
                        <div class="payment-box p-3 rounded-4 text-center d-none d-lg-block">
                            <h4 class="box-title">Payment Methods</h4>
                            <h6 class="mb-2 fw-600">For Nepali Delegates</h6>
                            <div class="d-flex justify-content-between align-items-center payment-logos">
                                @if ($conference->society->nationalPaymentSetting?->esewa_product_code)
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
                                    <div class=""><img style="height: 80px !important;"
                                            src="{{ asset('frontend/assets/img/unnamed.png') }}" alt="Fone Pay" class="">
                                    </div>
                                @endif
                            </div>
                            @if ($conference->society->internationalPaymentSetting)
                                <h6 class="mb-2 fw-600">For International Delegates</h6>
                                <div class="d-flex justify-content-between align-items-center mb-3 payment-logos">
                                    <div class="logo-item"><img
                                            src="{{ asset('frontend/assets/img/international_delegate_1.png') }}"
                                            alt="Visa" class="logo-img"></div>
                                    <div class="logo-item"><img
                                            src="{{ asset('frontend/assets/img/international_delegate_2.png') }}"
                                            alt="Mastercard" class="logo-img"></div>
                                    <div class="logo-item"><img
                                            src="{{ asset('frontend/assets/img/international_delegate_3.png') }}"
                                            alt="PayPal" class="logo-img"></div>
                                    <div class="logo-item"><img
                                            src="{{ asset('frontend/assets/img/international_delegate_4.png') }}"
                                            alt="Amex" class="logo-img"></div>
                                </div>

                                <h6 class="mb-2 fw-600">For Indian Delegates</h6>
                                <div class="d-flex justify-content-between align-items-center mb-3 payment-logos">
                                    <div class="logo-item"><img
                                            src="{{ asset('frontend/assets/img/international_delegate_4.png') }}"
                                            alt="Payment Method 1" class="logo-img"></div>
                                    <div class="logo-item"><img
                                            src="{{ asset('frontend/assets/img/international_delegate_2.png') }}"
                                            alt="Payment Method 2" class="logo-img"></div>
                                    <div class="logo-item"><img
                                            src="{{ asset('frontend/assets/img/international_delegate_3.png') }}"
                                            alt="Payment Method 3" class="logo-img"></div>
                                </div>
                            @endif
                        </div>
                    </div>

                </aside>
                <!-- Tab Content -->
                <main class="col-lg-9 order-2 order-lg-1">
                    <div class="tab-content" id="safogTabsContent">
                        @foreach ($committees as $index => $committee)
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="{{ $committee->slug }}"
                                role="tabpanel">
                                <h2 class="section-title">{{ $committee->committee_name }}</h2>
                                <div class="row mt-3 align-items-center">
                                    @foreach ($committee->committeeMembers as $member)
                                        <div class="col-md-4">
                                            <div class="prof-card p-3 rounded-3 h-100 d-flex flex-column">
                                                <img src="{{ $member->user->userDetail && $member->user->userDetail->image
                                                    ? Storage::url('profile/image/' . $member->user->userDetail->image)
                                                    : asset('frontend/assets/img/user.jpg') }}"
                                                    alt="{{ $member->user->fullName($member->user) }}"
                                                    class="profile-img mb-3">

                                                <div class="w-100 d-flex align-items-center justify-content-center">
                                                    <h6 class="card-title mb-0">
                                                        {{ $member->user->fullName($member->user) }}
                                                    </h6>
                                                </div>

                                                <small class="card-subtitle text-center">
                                                    {{ $member->designation->designation ?? 'Member' }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="payment-box p-3 rounded-4 text-center d-lg-none mt-4">
                        <h4 class="box-title">Payment Methods</h4>
                        <h6 class="mb-2 fw-600">For Nepali Delegates</h6>
                        <div class="d-flex justify-content-between align-items-center payment-logos">
                            @if ($conference->society->nationalPaymentSetting?->esewa_product_code)
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
                                <div class=""><img style="height: 80px !important;"
                                        src="{{ asset('frontend/assets/img/unnamed.png') }}" alt="Fone Pay" class="">
                                </div>
                            @endif

                        </div>
                        @if ($conference->society->internationalPaymentSetting)
                            <h6 class="mb-2 fw-600">For International Delegates</h6>
                            <div class="d-flex justify-content-between align-items-center mb-3 payment-logos">
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_1.png') }}" alt="Visa"
                                        class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_2.png') }}"
                                        alt="Mastercard" class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_3.png') }}" alt="PayPal"
                                        class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_4.png') }}" alt="Amex"
                                        class="logo-img"></div>
                            </div>

                            <h6 class="mb-2 fw-600">For Indian Delegates</h6>
                            <div class="d-flex justify-content-between align-items-center mb-3 payment-logos">
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_4.png') }}"
                                        alt="Payment Method 1" class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_2.png') }}"
                                        alt="Payment Method 2" class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_3.png') }}"
                                        alt="Payment Method 3" class="logo-img"></div>
                            </div>
                        @endif

                    </div>
                </main>
            </div>

            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fa-solid fa-bars me-2"></i> Committee Members
            </button>
            <div class="mobile-sidebar" id="mobileSidebar">
                <button class="close-sidebar" id="closeSidebar">
                    <i class="fa-solid fa-times"></i>
                </button>

                <div class="nav flex-column nav-pills mb-4" id="mobileSafogTabs" role="tablist" aria-orientation="vertical">
                    @foreach ($committees as $index => $committee)
                        <button class="nav-link d-flex align-items-center mobile-tab-link {{ $index === 0 ? 'active' : '' }}"
                            data-bs-target="#{{ $committee->slug }}" type="button">
                            <i class="fa-solid fa-people-group me-2"></i>
                            <span>{{ $committee->committee_name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="sidebar-overlay" id="sidebarOverlay"></div>
        @endif
    </section>

    <div class="td_height_80 td_height_lg_80"></div>
@endsection
