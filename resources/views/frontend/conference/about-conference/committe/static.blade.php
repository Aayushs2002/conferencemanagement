@extends('frontend.conference.layouts.main')
@section('title')
    {{ $conference->society->sub_domain_name }} | Committee
@endsection

@section('content')
    <section class="container">
        <div class="mb-2">
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>

        <div class="row">
            <!-- Static Content -->
            <div class="col-12">
                <div class="committee-static-content">
                    {!! $staticContent !!}
                </div>
            </div>
        </div>

        <!-- Payment Box -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="payment-box p-4 rounded-4">
                    <h4 class="box-title text-center mb-4">Payment Methods</h4>
                    <h6 class="mb-3 fw-600">For Nepali Delegates</h6>
                    <div class="d-flex justify-content-center gap-3 align-items-center payment-logos mb-4">
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
                        <h6 class="mb-3 fw-600">For International Delegates</h6>
                        <div class="d-flex justify-content-center gap-3 align-items-center mb-4 payment-logos">
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

                        <h6 class="mb-3 fw-600">For Indian Delegates</h6>
                        <div class="d-flex justify-content-center gap-3 align-items-center payment-logos">
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
        </div>
    </section>

    <div class="td_height_80 td_height_lg_80"></div>
@endsection

@section('styles')
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
    </style>
@endsection
