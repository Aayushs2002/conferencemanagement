@extends('backend.layouts.conference.main')

@section('title')
    Pay Outstanding Conference Amount
@endsection

@section('styles')
    <style>
        .pay-now-page {
            --pay-primary: {{ $conference->primary_color }};
            --pay-secondary: {{ $conference->secondary_color ?? $conference->primary_color }};
            --pay-surface: #ffffff;
            --pay-border: #e7e9ee;
            --pay-muted: #6b7280;
            --pay-bg: #f6f8fc;
            --pay-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            background: var(--pay-bg);
            border-radius: 14px;
            padding: 22px;
        }

        .pay-now-hero {
            border: 1px solid var(--pay-border);
            border-radius: 14px;
            box-shadow: var(--pay-shadow);
            overflow: hidden;
        }

        .pay-now-hero .hero-head {
            background: linear-gradient(135deg, var(--pay-primary), var(--pay-secondary));
            color: #fff;
            padding: 18px 22px;
        }

        .pay-now-hero .hero-head h4 {
            margin-bottom: 4px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .summary-tile {
            border: 1px solid var(--pay-border);
            border-radius: 12px;
            padding: 14px;
            background: #fff;
            height: 100%;
        }

        .summary-tile .small {
            color: var(--pay-muted) !important;
        }

        .pay-section {
            border: 1px solid var(--pay-border);
            border-radius: 14px;
            box-shadow: var(--pay-shadow);
            overflow: hidden;
        }

        .pay-section .card-header {
            background: #fff;
            border-bottom: 1px solid var(--pay-border);
            padding: 16px 20px;
        }

        .pay-section .card-header h5 {
            font-weight: 700;
            margin-bottom: 2px;
        }

        .payment-method-card {
            border: 1px solid var(--pay-border);
            border-radius: 12px;
            transition: all 0.25s ease;
            background: var(--pay-surface);
        }

        .payment-method-card .card-body {
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .payment-method-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            border-color: #c9d3e4;
        }

        .payment-method-card.is-selected {
            border-color: var(--pay-primary) !important;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        }

        .payment-method-card h6 {
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--pay-primary);
        }

        .payment-method-card h6.text-primary {
            color: var(--pay-primary) !important;
        }

        .payment-panel-card {
            border: 1px solid var(--pay-border);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .payment-panel-head {
            background: linear-gradient(135deg, var(--pay-primary), var(--pay-secondary));
            color: #fff;
            font-weight: 600;
            padding: 12px 16px;
        }

        .payment-panel-body {
            padding: 14px 16px;
        }

        .status-unpaid {
            color: var(--pay-secondary);
        }

        #processingDiv .card {
            border-radius: 12px;
        }

        #processingDiv .card-header {
            font-weight: 600;
        }

        .pay-now-page .btn {
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 14px;
        }

        .pay-now-page .btn-primary,
        .pay-now-page .btn-success,
        .pay-now-page .btn-warning,
        .pay-now-page .btn-info {
            background: var(--pay-primary);
            border-color: var(--pay-primary);
            color: #fff;
        }

        .pay-now-page .btn-primary:hover,
        .pay-now-page .btn-success:hover,
        .pay-now-page .btn-warning:hover,
        .pay-now-page .btn-info:hover {
            background: var(--pay-secondary);
            border-color: var(--pay-secondary);
            color: #fff;
        }

        .pay-now-page .btn-danger {
            background: #fff;
            border-color: var(--pay-border);
            color: #374151;
        }

        .pay-now-page .btn-danger:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: #111827;
        }

        .pay-now-page .form-check-input:checked {
            background-color: var(--pay-primary);
            border-color: var(--pay-primary);
        }

        .pay-now-page .modal-brand-header {
            background: linear-gradient(135deg, var(--pay-primary), var(--pay-secondary));
            color: #fff;
        }

        .pay-now-page .moco-details {
            border: 1px solid var(--pay-border);
            background: #f8fafc;
        }

        .pay-now-page .moco-status {
            background: var(--pay-secondary);
            color: #fff;
        }
    </style>
@endsection

@section('content')
    @include('backend.layouts.conference-navigation')

    @php
        $outstandingAmount = (float) ($registration->amount ?? 0);
        $countryId = current_user()->userDetail->country_id;
        $countryName = current_user()->userDetail->country->country_name ?? '';
        $delegateType = (int) ($memberTypePrice?->memberType?->delegate ?? 1);
        $accompanyPersonCount = max(((int) $registration->total_attendee) - 1, 0);
    @endphp

    <div class="container-fluid py-4 pay-now-page">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card pay-now-hero mb-4">
                    <div class="hero-head">
                        <h4 class="mb-1 text-white">
                            Complete Outstanding Payment
                        </h4>
                        <p class="mb-0 opacity-75">Registration ID: {{ $registration->registration_id ?? ('CR-' . $registration->id) }}</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="summary-tile">
                                    <div class="small text-muted">Current Status</div>
                                    <div class="fw-bold status-unpaid">Unpaid</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-tile">
                                    <div class="small text-muted">Outstanding Amount</div>
                                    <div class="fw-bold" id="displayOutstandingAmount">
                                        {{ $delegateType === 1 ? 'Rs.' : '$' }} {{ number_format(abs($outstandingAmount), 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-tile">
                                    <div class="small text-muted">Registrant Type</div>
                                    <div class="fw-bold">{{ $registration->registrant_type == 2 ? 'Speaker/Presenter' : 'Attendee' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card pay-section">
                    <div class="card-header">
                        <h5 class="mb-0">Choose Payment Method</h5>
                        <small class="text-muted">Select one secure payment option to complete your registration.</small>
                    </div>
                    <div class="card-body">
                        <div class="row" id="paymentMethodCards">
                            @if ($countryId == 125 && $national_payemnt_setting?->profile_id)
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card h-100" for="fonePayRadio" style="cursor: pointer;">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">QR Scan</h6>
                                            <img src="{{ asset('default-image/fonepay.png') }}" class="img-fluid mb-2" style="max-height: 56px;">
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input" type="radio" name="paymentMode" value="fonePay" id="fonePayRadio">
                                                <label class="form-check-label ms-2" for="fonePayRadio">Select</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if ($countryId != 125 && $static_qr_payment_setting && $static_qr_payment_setting->countries && $static_qr_payment_setting->countries->contains('id', $countryId))
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card h-100" for="staticQrRadio" style="cursor: pointer;">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">Static QR</h6>
                                            <div class="mb-2" style="font-size: 38px; line-height: 1;">QR</div>
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input" type="radio" name="paymentMode" value="staticQr" id="staticQrRadio">
                                                <label class="form-check-label ms-2" for="staticQrRadio">Select</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if ($countryId != 125 && $international_payemnt_setting && $international_payemnt_setting->countries && $international_payemnt_setting->countries->contains('id', $countryId))
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card h-100" for="dollarCardRadio" style="cursor: pointer;">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">Card Payment</h6>
                                            <img src="{{ asset('default-image/dollar-card.png') }}" class="img-fluid mb-2" style="max-height: 48px;">
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input" type="radio" name="paymentMode" value="dollarCard" id="dollarCardRadio">
                                                <label class="form-check-label ms-2" for="dollarCardRadio">Select</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if ($countryId != 125 && $international_bank_transfer?->bank_detail)
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card h-100" for="bankTransferRadio" style="cursor: pointer;">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">Bank Transfer</h6>
                                            <img src="{{ asset('default-image/bankTransfer.jpg') }}" class="img-fluid mb-2" style="max-height: 48px;">
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input" type="radio" name="paymentMode" value="bankTransfer" id="bankTransferRadio">
                                                <label class="form-check-label ms-2" for="bankTransferRadio">Select</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if ($countryId == 125 && $national_payemnt_setting?->account_detail)
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card h-100" for="bankTransferRadio" style="cursor: pointer;">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">Bank Transfer</h6>
                                            <img src="{{ asset('default-image/bankTransfer.jpg') }}" class="img-fluid mb-2" style="max-height: 48px;">
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input" type="radio" name="paymentMode" value="bankTransfer" id="bankTransferRadio">
                                                <label class="form-check-label ms-2" for="bankTransferRadio">Select</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if ($countryId == 125 && $national_payemnt_setting?->moco_merchant_id)
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card h-100" for="mocoRadio" style="cursor: pointer;">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">MoCo</h6>
                                            <img src="{{ asset('default-image/moco.png') }}" class="img-fluid mb-2" style="max-height: 56px;">
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input" type="radio" name="paymentMode" value="moco" id="mocoRadio">
                                                <label class="form-check-label ms-2" for="mocoRadio">Select</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if ($countryId == 125 && $national_payemnt_setting?->esewa_secret_key)
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card h-100" for="esewaRadio" style="cursor: pointer;">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">eSewa</h6>
                                            <img src="{{ asset('default-image/Esewa_logo.webp.png') }}" class="img-fluid mb-2" style="max-height: 56px;">
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input" type="radio" name="paymentMode" value="esewa" id="esewaRadio">
                                                <label class="form-check-label ms-2" for="esewaRadio">Select</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if ($countryId == 125 && $national_payemnt_setting?->khalti_live_secret_key)
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card h-100" for="khaltiRadio" style="cursor: pointer;">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">Khalti</h6>
                                            <img src="{{ asset('default-image/khalti-logo.png') }}" class="img-fluid mb-2" style="max-height: 56px;">
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input" type="radio" name="paymentMode" value="khalti" id="khaltiRadio">
                                                <label class="form-check-label ms-2" for="khaltiRadio">Select</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if ($countryId == 125 && $national_payemnt_setting?->connectips_merchant_id)
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card h-100" for="connectipsRadio" style="cursor: pointer;">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary">ConnectIPS</h6>
                                            <div class="mb-2" style="font-size: 32px; line-height: 1;">CIPS</div>
                                            <div class="form-check d-flex justify-content-center mt-2">
                                                <input class="form-check-input" type="radio" name="paymentMode" value="connectips" id="connectipsRadio">
                                                <label class="form-check-label ms-2" for="connectipsRadio">Select</label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>

                        <div id="processingDiv" style="display:none;">
                            <hr>
                            <div class="row">
                                <div class="col-md-4 mb-3 paymentInfoCard" style="display:none;">
                                    <div class="card payment-panel-card h-100">
                                        <div class="payment-panel-head">Payment Information</div>
                                        <div class="payment-panel-body small">
                                            <div class="bankTransferInfo" style="display:none;">
                                                @if ($countryId != 125 && $international_bank_transfer?->bank_detail)
                                                    {!! $international_bank_transfer?->bank_detail !!}
                                                @elseif ($countryId == 125 && $national_payemnt_setting?->account_detail)
                                                    {!! $national_payemnt_setting?->account_detail !!}
                                                @endif
                                            </div>
                                            <div class="staticQrInfo" style="display:none;">
                                                @if ($countryId != 125 && $static_qr_payment_setting?->qr_details)
                                                    {!! $static_qr_payment_setting?->qr_details !!}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card payment-panel-card">
                                        <div class="payment-panel-head">Complete Payment</div>
                                        <div class="card-body">
                                            @if ($countryId == 125 || $countryName == 'India')
                                                <div class="fonePayProcessingDiv" style="display:none;">
                                                    <form action="{{ route('my-society.conference.fonepay', [$society, $conference]) }}" method="POST" id="fonePayForm">
                                                        @csrf
                                                        <input type="hidden" name="registration_id_to_pay" value="{{ $registration->id }}">
                                                        <input type="hidden" name="registrant_type" value="{{ $registration->registrant_type }}">
                                                        <input type="hidden" name="accompany_person" value="{{ $accompanyPersonCount }}">
                                                        <input type="hidden" name="selected_workshops" value="">
                                                        <input type="hidden" name="workshop_amount" value="0">
                                                        <input type="hidden" name="conference_base_amount" value="{{ abs($outstandingAmount) }}">
                                                        <input type="hidden" name="selected_addons" value="">
                                                        <input type="hidden" name="payment_type" value="1">
                                                        <input type="hidden" name="amount" id="fonePayAmount" value="{{ abs($outstandingAmount) }}">
                                                        <input type="hidden" name="payment_currency" id="fonePayCurrency" value="USD">
                                                        <button type="submit" id="submitFonePay" class="btn btn-primary" disabled>Pay via QR Scan</button>
                                                    </form>
                                                </div>
                                            @endif

                                            @if ($countryId == 125)
                                                <div class="esewaProcessingDiv" style="display:none;">
                                                    <form action="{{ route('my-society.conference.esewa', [$society, $conference]) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="registration_id_to_pay" value="{{ $registration->id }}">
                                                        <input type="hidden" name="registrant_type" value="{{ $registration->registrant_type }}">
                                                        <input type="hidden" name="accompany_person" value="{{ $accompanyPersonCount }}">
                                                        <input type="hidden" name="selected_workshops" value="">
                                                        <input type="hidden" name="workshop_amount" value="0">
                                                        <input type="hidden" name="conference_base_amount" value="{{ abs($outstandingAmount) }}">
                                                        <input type="hidden" name="selected_addons" value="">
                                                        <input type="hidden" name="payment_type" value="3">
                                                        <input type="hidden" name="amount" value="{{ abs($outstandingAmount) }}">
                                                        <button type="submit" id="submitEsewa" class="btn btn-success" disabled>Pay via eSewa</button>
                                                    </form>
                                                </div>

                                                <div class="khaltiProcessingDiv" style="display:none;">
                                                    <form action="{{ route('my-society.conference.khalti', [$society, $conference]) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="registration_id_to_pay" value="{{ $registration->id }}">
                                                        <input type="hidden" name="registrant_type" value="{{ $registration->registrant_type }}">
                                                        <input type="hidden" name="accompany_person" value="{{ $accompanyPersonCount }}">
                                                        <input type="hidden" name="selected_workshops" value="">
                                                        <input type="hidden" name="workshop_amount" value="0">
                                                        <input type="hidden" name="conference_base_amount" value="{{ abs($outstandingAmount) }}">
                                                        <input type="hidden" name="selected_addons" value="">
                                                        <input type="hidden" name="payment_type" value="4">
                                                        <input type="hidden" name="amount" value="{{ abs($outstandingAmount) }}">
                                                        <button type="submit" id="submitKhalti" class="btn btn-warning" disabled>Pay via Khalti</button>
                                                    </form>
                                                </div>

                                                <div class="mocoProcessingDiv" style="display:none;">
                                                    <form method="POST" id="mocoForm">
                                                        @csrf
                                                        <input type="hidden" name="registration_id_to_pay" value="{{ $registration->id }}">
                                                        <input type="hidden" name="registrant_type" value="{{ $registration->registrant_type }}">
                                                        <input type="hidden" name="accompany_person" value="{{ $accompanyPersonCount }}">
                                                        <input type="hidden" name="selected_workshops" value="">
                                                        <input type="hidden" name="workshop_amount" value="0">
                                                        <input type="hidden" name="conference_base_amount" value="{{ abs($outstandingAmount) }}">
                                                        <input type="hidden" name="selected_addons" value="">
                                                        <input type="hidden" name="payment_type" value="2">
                                                        <input type="hidden" name="amount" id="mocoAmount" value="{{ abs($outstandingAmount) }}">
                                                        <button type="submit" id="submitMoco" class="btn btn-info" disabled>
                                                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                            Pay via MoCo
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="connectipsProcessingDiv" style="display:none;">
                                                    <form action="{{ route('my-society.conference.connectips', [$society, $conference]) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="registration_id_to_pay" value="{{ $registration->id }}">
                                                        <input type="hidden" name="registrant_type" value="{{ $registration->registrant_type }}">
                                                        <input type="hidden" name="accompany_person" value="{{ $accompanyPersonCount }}">
                                                        <input type="hidden" name="selected_workshops" value="">
                                                        <input type="hidden" name="workshop_amount" value="0">
                                                        <input type="hidden" name="conference_base_amount" value="{{ abs($outstandingAmount) }}">
                                                        <input type="hidden" name="selected_addons" value="">
                                                        <input type="hidden" name="payment_type" value="7">
                                                        <input type="hidden" name="amount" value="{{ abs($outstandingAmount) }}">
                                                        <button type="submit" id="submitConnectIPS" class="btn btn-primary" disabled>Pay with ConnectIPS</button>
                                                    </form>
                                                </div>
                                            @endif

                                            @if ($countryId != 125)
                                                <div class="dollarCardProcessingDiv" style="display:none;">
                                                    <form action="{{ route('my-society.conference.internationalPayment', [$society, $conference]) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="registration_id_to_pay" value="{{ $registration->id }}">
                                                        <input type="hidden" name="registrant_type" value="{{ $registration->registrant_type }}">
                                                        <input type="hidden" name="accompany_person" value="{{ $accompanyPersonCount }}">
                                                        <input type="hidden" name="selected_workshops" value="">
                                                        <input type="hidden" name="workshop_amount" value="0">
                                                        <input type="hidden" name="conference_base_amount" value="{{ abs($outstandingAmount) }}">
                                                        <input type="hidden" name="selected_addons" value="">
                                                        <input type="hidden" name="amount" value="{{ abs($outstandingAmount) }}">
                                                        <input type="hidden" name="payment_currency" value="USD">
                                                        <button type="submit" id="submitButtonInternationalPayment" class="btn btn-primary" disabled>Pay via Card</button>
                                                    </form>
                                                </div>
                                            @endif

                                            @if ($countryId != 125 && $static_qr_payment_setting)
                                                <div class="staticQrFormProcessingDiv" style="display:none;">
                                                    <form action="{{ route('my-society.conference.submitOutstandingOffline', [$society, $conference, $registration]) }}" method="POST" enctype="multipart/form-data" id="staticQrForm">
                                                        @csrf
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Transaction ID <code>*</code></label>
                                                                <input type="text" class="form-control" name="transaction_id" id="static_qr_transaction_id" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Payment Voucher</label>
                                                                <input type="file" class="form-control" name="payment_voucher" id="static_qr_payment_voucher" accept=".jpg,.jpeg,.png,.pdf">
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="payment_type" value="8">
                                                        <input type="hidden" name="amount" id="staticQrAmount" value="{{ abs($outstandingAmount) }}">
                                                        <input type="hidden" name="payment_currency" id="staticQrCurrency" value="USD">
                                                        <button type="submit" id="submitButtonStaticQr" class="btn btn-success" disabled>Submit Static QR Payment</button>
                                                    </form>
                                                </div>
                                            @endif

                                            <div class="bankTransferProcessingDiv" style="display:none;">
                                                <form action="{{ route('my-society.conference.submitOutstandingOffline', [$society, $conference, $registration]) }}" method="POST" enctype="multipart/form-data" id="bankTransferForm">
                                                    @csrf
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Transaction ID <code>*</code></label>
                                                            <input type="text" class="form-control" name="transaction_id" id="transaction_id" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Payment Voucher</label>
                                                            <input type="file" class="form-control" name="payment_voucher" id="payment_voucher" accept=".jpg,.jpeg,.png,.pdf">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="payment_type" value="6">
                                                    <input type="hidden" name="amount" id="bankAmount" value="{{ abs($outstandingAmount) }}">
                                                    <input type="hidden" name="payment_currency" value="USD">
                                                    <button type="submit" id="submitButtonBankTransfer" class="btn btn-success" disabled>Submit Bank Transfer</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('my-society.conference.index', [$society, $conference]) }}" class="btn btn-outline-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mocoQrModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header modal-brand-header">
                    <h5 class="modal-title text-white">Scan QR Code to Pay</h5>
                </div>
                <div class="modal-body text-center">
                    <div id="mocoQrCode" class="mb-3"></div>
                    <div id="mocoPaymentDetails" class="alert moco-details text-start">
                        <p><strong>Reference:</strong> <span id="mocoRefNumber"></span></p>
                        <p><strong>Amount:</strong> Rs. <span id="mocoPayAmount"></span></p>
                        <p><strong>Status:</strong> <span id="mocoPayStatus" class="badge moco-status">Pending</span></p>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-primary" id="mocoCheckStatus">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Check Status
                        </button>
                        <button type="button" class="btn btn-danger" id="mocoCancelPayment" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let calculatedAmount = parseFloat('{{ abs($outstandingAmount) }}');
            let calculatedAmountUSD = parseFloat('{{ abs($outstandingAmount) }}');
            let isConvertedToINR = false;
            let mocoReferenceNumber = null;

            $('input[name="paymentMode"]').on('change', function() {
                const selectedValue = $(this).val();
                const checkCountry = '{{ $countryName }}';
                const delegate = '{{ $delegateType }}';

                $('.payment-method-card').removeClass('is-selected');
                $(this).closest('.payment-method-card').addClass('is-selected');

                $('#processingDiv').fadeIn();
                $('.paymentInfoCard, .bankTransferInfo, .staticQrInfo').hide();
                $('.fonePayProcessingDiv, .dollarCardProcessingDiv, .mocoProcessingDiv, .esewaProcessingDiv, .khaltiProcessingDiv, .connectipsProcessingDiv, .bankTransferProcessingDiv, .staticQrFormProcessingDiv').hide();

                if (selectedValue === 'bankTransfer') {
                    $('.paymentInfoCard, .bankTransferInfo, .bankTransferProcessingDiv').show();
                } else if (selectedValue === 'staticQr') {
                    $('.paymentInfoCard, .staticQrInfo, .staticQrFormProcessingDiv').show();
                } else {
                    $(`.${selectedValue}ProcessingDiv`).show();
                }

                enablePaymentButton(selectedValue, delegate, checkCountry);
            });

            function enablePaymentButton(selectedValue, delegate, checkCountry) {
                $('#submitFonePay, #submitEsewa, #submitKhalti, #submitMoco, #submitConnectIPS, #submitButtonInternationalPayment, #submitButtonBankTransfer, #submitButtonStaticQr').prop('disabled', true);

                const needsINRConversion = (delegate == 2 && checkCountry == 'India' && (selectedValue == 'fonePay' || selectedValue == 'staticQr'));

                if (needsINRConversion) {
                    convertUsdToInr(selectedValue);
                    return;
                }

                if (delegate == 2 && isConvertedToINR) {
                    calculatedAmount = calculatedAmountUSD;
                    $('#displayOutstandingAmount').text('$ ' + parseFloat(calculatedAmountUSD).toFixed(2));
                    $('#fonePayAmount').val(parseFloat(calculatedAmountUSD).toFixed(2));
                    $('#staticQrAmount').val(parseFloat(calculatedAmountUSD).toFixed(2));
                    $('#fonePayCurrency').val('USD');
                    $('#staticQrCurrency').val('USD');
                    isConvertedToINR = false;
                }

                if (selectedValue == 'fonePay') {
                    $('#submitFonePay').prop('disabled', false);
                } else if (selectedValue == 'dollarCard') {
                    $('#submitButtonInternationalPayment').prop('disabled', false);
                } else if (selectedValue == 'moco') {
                    $('#submitMoco').prop('disabled', false);
                } else if (selectedValue == 'esewa') {
                    $('#submitEsewa').prop('disabled', false);
                } else if (selectedValue == 'khalti') {
                    $('#submitKhalti').prop('disabled', false);
                } else if (selectedValue == 'connectips') {
                    $('#submitConnectIPS').prop('disabled', false);
                } else if (selectedValue == 'bankTransfer') {
                    $('#submitButtonBankTransfer').prop('disabled', false);
                } else if (selectedValue == 'staticQr') {
                    $('#submitButtonStaticQr').prop('disabled', false);
                }
            }

            function convertUsdToInr(paymentMode) {
                $.post('{{ route('convertUsdToInr') }}', {
                    usd: calculatedAmountUSD,
                    paymentMode: paymentMode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }).done(function(response) {
                    if (response.type === 'success') {
                        calculatedAmount = parseFloat(response.amount);
                        $('#displayOutstandingAmount').text('INR ' + calculatedAmount.toFixed(2));
                        if (paymentMode === 'fonePay') {
                            $('#fonePayAmount').val(calculatedAmountUSD.toFixed(2));
                            $('#fonePayCurrency').val('INR');
                            $('#submitFonePay').prop('disabled', false);
                        }
                        if (paymentMode === 'staticQr') {
                            $('#staticQrAmount').val(calculatedAmountUSD.toFixed(2));
                            $('#staticQrCurrency').val('INR');
                            $('#submitButtonStaticQr').prop('disabled', false);
                        }
                        isConvertedToINR = true;
                    } else {
                        notyf.error(response.message || 'Unable to convert currency.');
                    }
                }).fail(function() {
                    notyf.error('Error converting amount to INR.');
                });
            }

            $('#submitButtonBankTransfer, #submitButtonStaticQr').on('click', function(e) {
                const isStaticQr = $(this).attr('id') === 'submitButtonStaticQr';
                const transactionInputId = isStaticQr ? '#static_qr_transaction_id' : '#transaction_id';
                if (!$(transactionInputId).val()) {
                    e.preventDefault();
                    notyf.error('Please enter transaction ID before submitting.');
                }
            });

            $('#fonePayForm').on('submit', function() {
                $('#submitFonePay').prop('disabled', true).text('Processing...');
            });

            $('#submitEsewa, #submitKhalti, #submitConnectIPS').on('click', function() {
                $(this).prop('disabled', true).text('Processing...');
            });

            $('#mocoForm').on('submit', function(e) {
                e.preventDefault();
                const submitButton = $('#submitMoco');
                const spinner = submitButton.find('.spinner-border');
                submitButton.prop('disabled', true);
                spinner.removeClass('d-none');

                $.ajax({
                    url: "{{ route('my-society.conference.moco', [$society, $conference]) }}",
                    method: 'POST',
                    data: {
                        registration_id_to_pay: '{{ $registration->id }}',
                        registrant_type: '{{ $registration->registrant_type }}',
                        accompany_person: '{{ $accompanyPersonCount }}',
                        selected_workshops: '',
                        workshop_amount: 0,
                        selected_addons: '',
                        payment_type: 2,
                        amount: $('#mocoAmount').val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success' && response.data.qr_data) {
                            $('#mocoQrCode').html(`<img src="${response.data.qr_data}" class="img-fluid" style="max-width:260px;">`);
                            $('#mocoRefNumber').text(response.data.referenceNumber);
                            $('#mocoPayAmount').text(response.data.amount);
                            mocoReferenceNumber = response.data.referenceNumber;
                            new bootstrap.Modal(document.getElementById('mocoQrModal')).show();
                        } else {
                            notyf.error(response.message || 'Unable to generate MoCo QR code.');
                            submitButton.prop('disabled', false);
                            spinner.addClass('d-none');
                        }
                    },
                    error: function() {
                        notyf.error('Unable to connect to MoCo gateway.');
                        submitButton.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });

            $('#mocoCheckStatus').on('click', function() {
                if (!mocoReferenceNumber) {
                    notyf.error('Reference not found. Please generate QR again.');
                    return;
                }

                const checkBtn = $(this);
                checkBtn.find('.spinner-border').removeClass('d-none');

                $.ajax({
                    url: "{{ route('my-society.conference.mocoCheckStatus', [$society, $conference]) }}",
                    method: 'POST',
                    data: {
                        reference_number: mocoReferenceNumber,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        const txnStatus = (response.txnStatus || '').toLowerCase();
                        if (txnStatus === 'success' || txnStatus === 'completed') {
                            $('#mocoPayStatus').text('Completed');
                            window.location.href = "{{ route('my-society.conference.mocoSuccess', [$society, $conference]) }}?txnID=" + (response.txnID || mocoReferenceNumber);
                        } else {
                            notyf.info('Payment is still pending.');
                        }
                    },
                    error: function() {
                        notyf.error('Unable to check MoCo payment status.');
                    },
                    complete: function() {
                        checkBtn.find('.spinner-border').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endsection
