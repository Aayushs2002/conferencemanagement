@extends('frontend.conference.layouts.main')
@section('title')
    {{ $conference->society->sub_domain_name }} | Committee Member
@endsection
@section('content')
    <section class="container">
        <div class="mb-2">
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>

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
                            {{--                             
                            <div class="logo-item"><img src="{{ asset('frontend/assets/img/esewa-icon-large.png') }}"
                                    alt="eSewa" class="logo-img">
                            </div>
                            <div class="logo-item"><img src="{{ asset('frontend/assets/img/khalti-ime-logo.png') }}"
                                    alt="Khalti" class="logo-img">
                            </div>
                            <div class="logo-item"><img src="{{ asset('frontend/assets/img/logo-1 (1).png') }}"
                                    alt="Bank Transfer" class="logo-img"></div> --}}
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
                        {{-- @dd($committee) --}}
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="{{ $committee->slug }}"
                            role="tabpanel">
                            <h2 class="section-title">{{ $committee->committee_name }}</h2>
                            {{-- @dd($committee->committeeMembers) --}}
                            @php
                                $groupedMembers = $committee->committeeMembers->groupBy(
                                    fn($m) => $m->designation->designation ?? 'Members',
                                );
                            @endphp
                            {{-- @dd($groupedMembers) --}}

                            @foreach ($groupedMembers as $designationName => $members)
                                {{-- <p class="span-text mt-5">{{ $designationName }}</p> --}}
                                <div class="row mt-3 align-items-center">
                                    @foreach ($members as $member)
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
                                                    {{ $designationName }}<br>
                                                    {{-- {{ $committee->committee_name }} --}}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

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
    </section>
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

    <div class="td_height_80 td_height_lg_80"></div>
@endsection
