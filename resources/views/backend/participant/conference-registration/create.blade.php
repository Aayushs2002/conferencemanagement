@extends('backend.layouts.conference.main')

@section('content')
    <style>
        .registration-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .registration-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step:last-child::after {
            display: none;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            background: #007bff;
            color: white;
        }

        .step.completed .step-circle {
            background: #28a745;
            color: white;
        }

        .step.completed::after {
            background: #28a745;
        }

        .pricing-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .workshop-highlight {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 10px;
            padding: 15px;
            color: white;
            margin: 10px 0;
        }

        .payment-method-card {
            border: 2px solid transparent;
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }

        .payment-method-card:hover {
            border-color: #007bff;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
        }

        .payment-method-card.selected {
            border-color: #28a745;
            background-color: #f8fff9;
        }

        .btn-calculate {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-calculate:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .price-breakdown {
            /* background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); */
            border-radius: 15px;
            padding: 20px;
        }

        .summary-card {
            background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .alert-custom {
            border: none;
            border-radius: 10px;
            border-left: 5px solid #007bff;
        }

        .addon-checkbox:checked+label {
            background-color: #e7f3ff;
            border-color: #007bff !important;
        }

        .form-check:hover {
            background-color: #f8f9fa;
        }

        .card-body::-webkit-scrollbar {
            width: 6px;
        }

        .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .card-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .card-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    {{-- @dd($national_payemnt_setting) --}}

    @if (!old() && !isset($conference_registration))
        <div class="modal fade" id="openModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-md">
                <div class="modal-content registration-card">
                    <div class="modal-header  text-white">
                        <h5 class="modal-title" id="exampleModalCenterTitle">
                            <i class="fas fa-users"></i> Choose Your Registration Type
                        </h5>
                    </div>
                    <hr>
                    <div class="modal-body">
                        <div class="alert alert-custom alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Please select your participation type:</strong>
                            <ul class=" mb-0">
                                <li><strong>Attendee:</strong> Participate in conference sessions</li>
                                <li><strong>Speaker:</strong> Present your research/paper</li>
                            </ul>
                        </div>
                        <form action="" id="chooseRegistratantType">
                            <div class="form-group mb-3">
                                <label for="registrantType" class="fw-bold">Registration Type <span
                                        class="text-danger">*</span></label>
                                <select name="registrant_type" class="form-control mt-2" id="registrantType">
                                    <option value="" hidden>-- Select Registration Type --</option>
                                    <option value="1">👥 Attendee</option>
                                    <option value="2">🎤 Speaker</option>
                                </select>
                                <div class="text-end mt-4">
                                    <button type="submit" id="chooseRegistrantButton" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i> Continue
                                    </button>
                                    <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="container-fluid py-4">
        <!-- Progress Steps -->
        <div class="step-indicator">
            <div class="step active" id="step1">
                <div class="step-circle">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>Registration Details</div>
            </div>
            <div class="step" id="step2">
                <div class="step-circle">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>Price Calculation</div>
            </div>
            <div class="step" id="step3">
                <div class="step-circle">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>Payment</div>
            </div>
            <div class="step" id="step4">
                <div class="step-circle">
                    <i class="fas fa-check"></i>
                </div>
                <div>Confirmation</div>
            </div>
        </div>

        <!-- Main Registration Card -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card registration-card">
                    <div class="card-header  text-white" style="background-color: {{ $conference->primary_color }};">
                        <h3 class="mb-0">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="text-white">
                                {{ isset($conference_registration) ? 'Edit Conference Registration' : 'Conference Registration' }}
                            </span>
                        </h3>
                        <p class="mb-0 mt-2">
                            <strong>Event:</strong>
                            {{ !empty($conference) ? $conference->conference_name : 'No conference added yet.' }}
                        </p>
                    </div>
                    {{-- <hr> --}}
                    <div class="card-body">
                        <!-- Registration Summary Card -->
                        <div id="registrationSummary" class="summary-card mt-5" style="display: none;">
                            <h5 class="mb-3">
                                <i class="fas fa-clipboard-list"></i> Registration Summary
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>📋 Conference:</strong> {{ $conference->conference_name ?? 'N/A' }}</p>
                                    <p><strong>👤 Type:</strong> <span id="summaryRegistrantType">-</span></p>
                                    <p><strong>🎯 Workshop:</strong> <span id="summaryWorkshop">Not Selected</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>👥 Total Attendees:</strong> <span id="summaryAttendees">1</span></p>
                                    <p><strong>🍽️ Add-ons:</strong> <span id="summaryAddOns">None</span></p>
                                    <p><strong>💰 Estimated Total:</strong> <span id="summaryAmount"
                                            class="h5 text-success">-</span></p>
                                </div>
                            </div>
                        </div>

                        <form id="registrationForm" enctype="multipart/form-data">
                            @csrf
                            @isset($conference_registration)
                                @method('patch')
                            @endisset

                            <!-- Registration Options -->
                            <div class="alert alert-custom alert-info mb-4">
                                <i class="fas fa-lightbulb"></i>
                                <strong>Registration Options:</strong> Choose to register for conference only, or include a
                                workshop for enhanced learning experience.
                            </div>

                            <div class="row mb-4">
                                <!-- Workshop Selection -->
                                <div class="col-md-6 form-group mb-3">
                                    <label for="workshop_id" class="fw-bold">
                                        <i class="fas fa-chalkboard-teacher text-primary"></i> Workshop Selection
                                        <small class="text-muted">(Optional)</small>
                                    </label>
                                    <select name="workshop_id" id="workshop_id"
                                        class="form-control @error('workshop_id') is-invalid @enderror">
                                        <option value=""> Conference Only</option>
                                        @foreach ($workshops as $workshop)
                                            <option value="{{ $workshop->id }}" @selected(old('workshop_id', isset($conference_registration) ? $conference_registration->workshop_id : '') == $workshop->id)>
                                                {{ 'Conference + ' . $workshop->workshop_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('workshop_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Workshops provide hands-on learning and networking opportunities
                                    </small>
                                </div>

                                <!-- Multiple Add-ons Selection -->
                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">
                                        <i class="fas fa-utensils text-warning"></i> Add-ons
                                        <small class="text-muted">(Optional - Multiple Selection)</small>
                                    </label>
                                    <div class="card">
                                        <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                            @if ($conferenceAddons && $conferenceAddons->count() > 0)
                                                @foreach ($conferenceAddons as $addon)
                                                    <div class="form-check mb-2 p-2  rounded">
                                                        <input class="form-check-input addon-checkbox" type="checkbox"
                                                            name="selected_addons[]" value="{{ $addon->id }}"
                                                            data-name="{{ $addon->addon_name }}"
                                                            data-amount="{{ @$memberTypePrice->memberType->delegate == 1 ? $addon->addon_national_amount : $addon->addon_international_amount }}"
                                                            id="addon_{{ $addon->id }}"
                                                            @if (isset($conference_registration) && $conference_registration->registrationAddons->contains('addon_id', $addon->id)) checked @endif>
                                                        <label
                                                            class="form-check-label d-flex justify-content-between align-items-center w-100"
                                                            for="addon_{{ $addon->id }}">
                                                            <div>
                                                                <strong>{{ $addon->addon_name }}</strong>
                                                                @if ($addon->addon_description)
                                                                    <br><small
                                                                        class="text-muted">{{ $addon->addon_description }}</small>
                                                                @endif
                                                            </div>
                                                            <span class="badge bg-primary ms-2">
                                                                {{ @$memberTypePrice->memberType->delegate == 1 ? 'Rs. ' : '$ ' }}{{ number_format(@$memberTypePrice->memberType->delegate == 1 ? $addon->addon_national_amount : $addon->addon_international_amount, 2) }}
                                                                <small>/person</small>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center py-3">
                                                    <i class="fas fa-utensils fa-3x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">No add-ons available for this conference</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Selected add-ons will be applied to all attendees (you + guests)
                                    </small>
                                </div>
                            </div>

                            <!-- Additional Guests Selection -->
                            <div class="row mb-4">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="total_attendee" class="fw-bold">
                                        <i class="fas fa-users text-info"></i> Additional Guests
                                    </label>
                                    <select name="total_attendee" id="total_attendee"
                                        class="form-control @error('total_attendee') is-invalid @enderror">
                                        @if (!isset($conference_registration))
                                            <option value="">Select Guests</option>
                                            @for ($i = 0; $i <= 5; $i++)
                                                <option value="{{ $i }}" @selected(old('total_attendee') == $i)>
                                                    {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                                </option>
                                            @endfor
                                        @else
                                            <option value="">Select Guests</option>
                                            @for ($i = 0; $i <= 5; $i++)
                                                <option value="{{ $i }}" @selected($conference_registration->total_attendee - 1 == $i)>
                                                    {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                                </option>
                                            @endfor
                                        @endif
                                    </select>
                                    @error('total_attendee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">People accompanying you</small>
                                </div>

                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="alert alert-info w-100">
                                        <i class="fas fa-calculator"></i>
                                        <strong>Pricing Note:</strong> Add-ons are charged per person. If you select 1
                                        guest, add-ons will be charged for 2 people (you + 1 guest).
                                    </div>
                                </div>
                            </div>

                            <!-- Calculate Button -->
                            <div class="text-center mb-4">
                                <button type="button" id="calculatePrice" class="btn btn-primary btn-calculate btn-lg"
                                    {{ empty($conference) ? 'disabled' : '' }}>
                                    <i class="fas fa-calculator"></i> Calculate Total Price
                                </button>
                            </div>

                            <!-- Workshop Highlight -->
                            {{-- <div id="workshopHighlight" class="workshop-highlight" style="display: none;">
                                <h5><i class="fas fa-star"></i> Workshop Benefits</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="mb-0">
                                            <li>Hands-on practical sessions</li>
                                            <li>Expert-led demonstrations</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="mb-0">
                                            <li>Networking opportunities</li>
                                            <li>Certificate of participation</li>
                                        </ul>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Price Breakdown -->
                            <div id="priceTable" style="display: none;">
                                <div class="price-breakdown">
                                    <h4 class="mb-3">
                                        <i class="fas fa-money-bill-wave"></i> Price Breakdown
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="45%">Description</th>
                                                    <th width="15%">Quantity</th>
                                                    <th width="17%">Unit Price</th>
                                                    <th width="18%">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="calculatedData">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Accompanying Persons Details -->
                            <div class="row" id="accompanyPersonsDetail"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Section -->
        <div class="row justify-content-center" id="paymentSection" style="display: none;">
            <div class="col-lg-10">
                <div class="card registration-card mt-4">
                    <div class="card-header  text-white" style="background-color: {{ $conference->primary_color }}">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card"></i>
                            <span class="text-white">
                                Choose Payment Method
                            </span>
                        </h4>
                        <p class="mb-0 mt-1">Select your preferred payment option below</p>
                    </div>

                    <div class="card-body">
                        <div class="row mt-5">
                            @if (current_user()->userDetail->country_id == 125 && $national_payemnt_setting?->profile_id)
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card w-100" for="fonePayRadio"
                                        style="cursor:pointer;">
                                        <div class="card-body text-center">
                                            <h5 class="text-primary">📱 QR Scan</h5>
                                            <img src="{{ asset('default-image/fonepay.png') }}" class="img-fluid mb-2"
                                                style="max-height: 60px;">
                                            @if (current_user()->userDetail->country->country_name == 'India')
                                                <small class="text-muted">Cross Border Support</small>
                                            @endif
                                            <div class="form-check mt-3 d-flex justify-content-center">
                                                <input class="form-check-input" type="radio" name="paymentMode"
                                                    value="fonePay" id="fonePayRadio">
                                                <label class="form-check-label fw-bold ms-2" for="fonePayRadio">
                                                    Select
                                                </label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (current_user()->userDetail->country_id != 125 && !in_array(current_user()->userDetail->country_id, [78, 134, 165]))
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card w-100" for="dollarCardRadio"
                                        style="cursor:pointer;">
                                        <div class="card-body text-center">
                                            <h5 class="text-primary">💳 Card Payment</h5>
                                            <p class="small">We Accept</p>
                                            <img src="{{ asset('default-image/dollar-card.png') }}"
                                                class="img-fluid mb-2" style="max-height: 50px;">
                                            <div class="form-check mt-3 d-flex justify-content-center">
                                                <input class="form-check-input" type="radio" name="paymentMode"
                                                    value="dollarCard" id="dollarCardRadio">
                                                <label class="form-check-label fw-bold ms-2" for="dollarCardRadio">
                                                    Select
                                                </label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (in_array(current_user()->userDetail->country_id, [78, 134, 165]))
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card w-100" for="bankTransferRadio"
                                        style="cursor:pointer;">
                                        <div class="card-body text-center">
                                            <h5 class="text-primary">🏦 Bank Transfer</h5>
                                            <p class="small">We Accept</p>
                                            <img src="{{ asset('default-image/bankTransfer.jpg') }}"
                                                class="img-fluid mb-2" style="max-height: 50px;">
                                            <div class="form-check mt-3 d-flex justify-content-center">
                                                <input class="form-check-input" type="radio" name="paymentMode"
                                                    value="bankTransfer" id="bankTransferRadio">
                                                <label class="form-check-label fw-bold ms-2" for="bankTransferRadio">
                                                    Select
                                                </label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            @if (current_user()->userDetail->country_id == 125)
                                @if ($national_payemnt_setting?->moco_merchant_id)
                                    <div class="col-md-3 mb-3">
                                        <label class="card payment-method-card w-100" for="mocoRadio"
                                            style="cursor:pointer;">
                                            <div class="card-body text-center">
                                                <h5 class="text-primary">📲 Moco Pay</h5>
                                                <img src="{{ asset('default-image/moco.png') }}" class="img-fluid mb-2"
                                                    style="max-height: 60px;">
                                                <div class="form-check mt-3 d-flex justify-content-center">
                                                    <input class="form-check-input" type="radio" name="paymentMode"
                                                        value="moco" id="mocoRadio">
                                                    <label class="form-check-label fw-bold ms-2" for="mocoRadio">
                                                        Select
                                                    </label>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endif
                                @if ($national_payemnt_setting?->esewa_secret_key)
                                    <div class="col-md-3 mb-3">
                                        <label class="card payment-method-card w-100" for="esewaRadio"
                                            style="cursor:pointer;">
                                            <div class="card-body text-center">
                                                <h5 class="text-primary">💰 eSewa</h5>
                                                <img src="{{ asset('default-image/Esewa_logo.webp.png') }}"
                                                    class="img-fluid mb-2" style="max-height: 60px;">
                                                <div class="form-check mt-3 d-flex justify-content-center">
                                                    <input class="form-check-input" type="radio" name="paymentMode"
                                                        value="esewa" id="esewaRadio">
                                                    <label class="form-check-label fw-bold ms-2" for="esewaRadio">
                                                        Select
                                                    </label>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endif

                                @if ($national_payemnt_setting?->khalti_live_secret_key)
                                    <div class="col-md-3 mb-3">
                                        <div class="card payment-method-card" data-payment="khalti">
                                            <div class="card-body text-center">
                                                <h5 class="text-primary">🎯 Khalti</h5>
                                                <img src="{{ asset('default-image/khalti-logo.png') }}"
                                                    class="img-fluid mb-2" style="max-height: 60px;">
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" type="radio" name="paymentMode"
                                                        value="khalti" id="khaltiRadio">
                                                    <label class="form-check-label fw-bold"
                                                        for="khaltiRadio">Select</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Payment Processing Section -->
                        <div id="processingDiv" style="display: none;">
                            <hr class="my-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-info-circle"></i> Payment Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="bankTransferProcessingDiv">
                                                @if (current_user()->userDetail->country_id != 125)
                                                    <h6 class="text-info">Bank Transfer Details</h6>
                                                    <img src="{{ asset('default-image/bankTransfer.jpg') }}"
                                                        height="40" class="mb-2">
                                                    <div class="small">
                                                        {!! $international_payemnt_setting?->bank_detail !!}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-credit-card"></i> Complete Payment
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- FonePay Form -->
                                            @if (current_user()->userDetail->country_id == 125 || current_user()->userDetail->country->country_name == 'India')
                                                <div class="fonePayProcessingDiv" style="display: none;">
                                                    <form
                                                        action="{{ route('my-society.conference.fonepay', [$society, $conference]) }}"
                                                        method="POST" id="fonePayForm">
                                                        @csrf
                                                        <input type="hidden" name="registrant_type"
                                                            id="registrant_type_fonepay">
                                                        <input type="hidden" name="accompany_person"
                                                            id="accompany_person_fonepay">
                                                        <input type="hidden" name="workshop_id"
                                                            id="workshop_id_fonepay">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_fonepay">
                                                        <input type="hidden" name="payment_type" value="1">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="fonePayAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_fonepay">
                                                        <div class="d-grid">
                                                            <button type="submit" id="submitFonePay"
                                                                class="btn btn-primary btn-lg" disabled>
                                                                <i class="fas fa-qrcode"></i> Pay via QR Scan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            <!-- eSewa Form -->
                                            @if (current_user()->userDetail->country_id == 125)
                                                <div class="esewaProcessingDiv" style="display: none;">
                                                    <form
                                                        action="{{ route('my-society.conference.esewa', [$society, $conference]) }}"
                                                        method="POST" id="esewaForm">
                                                        @csrf
                                                        <input type="hidden" name="registrant_type"
                                                            id="registrant_type_esewa">
                                                        <input type="hidden" name="accompany_person"
                                                            id="accompany_person_esewa">
                                                        <input type="hidden" name="workshop_id" id="workshop_id_esewa">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_esewa">
                                                        <input type="hidden" name="payment_type" value="3">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="esewaAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_esewa">
                                                        <div class="d-grid">
                                                            <button type="submit" id="submitEsewa"
                                                                class="btn btn-success btn-lg" disabled>
                                                                <i class="fas fa-wallet"></i> Pay via eSewa
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            <!-- Khalti Form -->
                                            @if (current_user()->userDetail->country_id == 125)
                                                <div class="khaltiProcessingDiv" style="display: none;">
                                                    <form
                                                        action="{{ route('my-society.conference.khalti', [$society, $conference]) }}"
                                                        method="POST" id="khaltiForm">
                                                        @csrf
                                                        <input type="hidden" name="registrant_type"
                                                            id="registrant_type_khalti">
                                                        <input type="hidden" name="accompany_person"
                                                            id="accompany_person_khalti">
                                                        <input type="hidden" name="workshop_id" id="workshop_id_khalti">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_khalti">
                                                        <input type="hidden" name="payment_type" value="4">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="khaltiAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_khalti">
                                                        <div class="d-grid">
                                                            <button type="submit" id="submitKhalti"
                                                                class="btn btn-warning btn-lg" disabled>
                                                                <i class="fas fa-mobile-alt"></i> Pay via Khalti
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            <!-- Moco Form -->
                                            @if (current_user()->userDetail->country_id == 125)
                                                <div class="mocoProcessingDiv" style="display: none;">
                                                    <form method="POST" id="mocoForm">
                                                        @csrf
                                                        <input type="hidden" name="registrant_type"
                                                            id="registrant_type_moco">
                                                        <input type="hidden" name="accompany_person"
                                                            id="accompany_person_moco">
                                                        <input type="hidden" name="workshop_id" id="workshop_id_moco">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_moco">
                                                        <input type="hidden" name="payment_type" value="2">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="mocoAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_moco">

                                                        <div class="d-grid">
                                                            <button type="submit" id="submitMoco"
                                                                class="btn btn-info btn-lg" disabled>
                                                                <span class="spinner-border spinner-border-sm d-none"
                                                                    role="status"></span>
                                                                <i class="fas fa-mobile"></i> Pay via Moco
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            <!-- International Payment Form -->
                                            @if (current_user()->userDetail->country_id != 125)
                                                <div class="dollarCardProcessingDiv" style="display: none;">
                                                    <form
                                                        action="{{ route('my-society.conference.internationalPayment', [$society, $conference]) }}"
                                                        method="POST" id="internationalPaymentForm">
                                                        @csrf
                                                        <input type="hidden" name="registrant_type"
                                                            id="registrant_type_international">
                                                        <input type="hidden" name="accompany_person"
                                                            id="accompany_person_international">
                                                        <input type="hidden" name="workshop_id"
                                                            id="workshop_id_international">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_international">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="internationalAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_international">

                                                        <div class="d-grid">
                                                            <button type="submit" id="submitButtonInternationalPayment"
                                                                class="btn btn-primary btn-lg" disabled>
                                                                <i class="fas fa-credit-card"></i> Pay via Card
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>

                                                <!-- Bank Transfer Form -->
                                                <div class="bankTransferProcessingDiv" style="display: none;">
                                                    <form
                                                        action="{{ route('my-society.conference.store', [$society, $conference]) }}"
                                                        method="POST" enctype="multipart/form-data"
                                                        id="bankTranferForm">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-6 form-group mb-3">
                                                                <label for="transaction_id" class="fw-bold">
                                                                    <i class="fas fa-receipt"></i> Transaction ID/Reference
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    name="transaction_id" id="transaction_id"
                                                                    placeholder="Enter transaction ID" required>
                                                                @error('transaction_id')
                                                                    <p class="text-danger">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-6 form-group mb-3">
                                                                <label for="payment_voucher" class="fw-bold">
                                                                    <i class="fas fa-file-upload"></i> Payment Receipt
                                                                    <small class="text-muted">(JPG/PNG/PDF)</small>
                                                                </label>
                                                                <input type="file" class="form-control"
                                                                    name="payment_voucher" id="payment_voucher"
                                                                    accept=".jpg,.jpeg,.png,.pdf">
                                                                @error('payment_voucher')
                                                                    <p class="text-danger">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="registrant_type"
                                                            id="registrant_type_bank_transfer">
                                                        <input type="hidden" name="accompany_person"
                                                            id="accompany_person_bank">
                                                        <input type="hidden" name="workshop_id" id="workshop_id_bank">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_bank">
                                                        <input type="hidden" name="payment_type" value="6">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="bankAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_bank">
                                                        <div class="d-grid">
                                                            <button type="submit" id="submitButtonBankTransfer"
                                                                class="btn btn-success btn-lg" disabled>
                                                                <i class="fas fa-university"></i> Submit Bank Transfer
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MoCo QR Code Modal -->
    <div class="modal fade" id="mocoQrModal" tabindex="-1" aria-labelledby="mocoQrModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content registration-card">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="mocoQrModalLabel">
                        <i class="fas fa-qrcode"></i> Scan QR Code to Pay
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div id="mocoQrCode" class="mb-3"></div>
                    <div id="mocoPaymentDetails" class="alert alert-info text-start">
                        <p><strong>Reference:</strong> <span id="mocoRefNumber"></span></p>
                        <p><strong>Amount:</strong> Rs. <span id="mocoPayAmount"></span></p>
                        <p><strong>Status:</strong> <span id="mocoPayStatus" class="badge bg-warning">Pending</span></p>
                    </div>
                    <div class="alert alert-warning text-start">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i>
                            Please scan the QR code using your mobile banking app. Do not close this window until payment is
                            completed.
                        </small>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-primary" id="mocoCheckStatus">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            <i class="fas fa-sync-alt"></i> Check Status
                        </button>
                        <button type="button" class="btn btn-danger" id="mocoCancelPayment" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Global variables
            let totalPrice = 0;
            let calculatedAmount = 0;
            let isPriceCalculated = false;
            let workshopPrice = 0;
            let workshopGuestPrice = 0;
            let workshopAmount = 0;
            let currentWorkshopName = '';
            let currencySymbol = '{{ @$memberTypePrice->memberType->delegate == 1 ? 'Rs. ' : '$ ' }}';
            let selectedAddOns = []; // Store selected add-ons with their details

            // Initialize
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Show initial modal
            $("#openModal").modal('show');

            // Update step indicators
            function updateStepIndicator(currentStep) {
                $('.step').removeClass('active completed');
                for (let i = 1; i <= currentStep; i++) {
                    if (i < currentStep) {
                        $('#step' + i).addClass('completed');
                    } else if (i === currentStep) {
                        $('#step' + i).addClass('active');
                    }
                }
            }

            // Handle add-on checkbox changes
            function handleAddOnChange() {
                selectedAddOns = [];
                $('.addon-checkbox:checked').each(function() {
                    const addonId = $(this).val();
                    const addonName = $(this).data('name');
                    const addonAmount = parseFloat($(this).data('amount')) || 0;

                    selectedAddOns.push({
                        id: addonId,
                        name: addonName,
                        amount: addonAmount
                    });
                });

                updateRegistrationSummary();

                // Reset calculation if price was already calculated
                if (isPriceCalculated) {
                    resetPriceCalculation('add-on selection');
                }
            }

            // Reset price calculation
            function resetPriceCalculation(changeType) {
                $("#priceTable").fadeOut();
                $("#paymentSection").fadeOut();
                $("#processingDiv").hide();
                isPriceCalculated = false;
                $('input[name="paymentMode"]').prop('checked', false);
                $('.payment-method-card').removeClass('selected');

                notyf.info(`Please recalculate the price due to changes in ${changeType}.`);
                updateStepIndicator(1);
            }

            // Update registration summary
            function updateRegistrationSummary() {
                const workshopId = $('#workshop_id').val();
                const totalAttendees = parseInt($('#total_attendee').val() || 0) + 1;

                let workshopText = 'Not Selected';
                let addOnText = 'None';

                if (workshopId) {
                    workshopText = $('#workshop_id option:selected').text().replace('🔬 ', '');
                    $('#workshopHighlight').show();
                } else {
                    $('#workshopHighlight').hide();
                }

                // Handle multiple add-ons
                if (selectedAddOns.length > 0) {
                    addOnText = selectedAddOns.map(addon => addon.name).join(', ');
                }

                // Update summary display
                $('#summaryWorkshop').text(workshopText);
                $('#summaryAddOns').text(addOnText);
                $('#summaryAttendees').text(totalAttendees);

                // Show summary if any option is selected
                if (workshopId || selectedAddOns.length > 0 || $('#total_attendee').val()) {
                    $('#registrationSummary').fadeIn();
                } else {
                    $('#registrationSummary').fadeOut();
                }
            }

            // Enhanced price table row generation
            function generatePriceTableRow(index, description, quantity, unitPrice, total, currencySymbol, isTotal =
                false) {
                const rowClass = isTotal ? 'table-success fw-bold' : '';
                const quantityDisplay = quantity || '-';
                const unitPriceDisplay = unitPrice ? (currencySymbol + parseFloat(unitPrice).toFixed(2)) : '-';
                const totalDisplay = currencySymbol + parseFloat(total).toFixed(2);

                return `<tr class="${rowClass}">
            <td>${index}</td>
            <td>${description}</td>
            <td class="text-center">${quantityDisplay}</td>
            <td class="text-end">${unitPriceDisplay}</td>
            <td class="text-end">${totalDisplay}</td>
        </tr>`;
            }

            // Validate selections before calculation
            function validateSelections() {
                const errors = [];

                if ($('#total_attendee').val() === '') {
                    errors.push('Please select the number of additional guests (0 if none)');
                }

                if (errors.length > 0) {
                    errors.forEach(error => notyf.error(error));
                    return false;
                }

                return true;
            }

            // Fetch workshop pricing
            function fetchWorkshopPricing(workshopId, memberTypeId) {
                return new Promise((resolve, reject) => {
                    if (!workshopId) {
                        resolve({
                            success: true,
                            main_price: 0,
                            guest_price: 0,
                            workshop_name: ''
                        });
                        return;
                    }

                    $.ajax({
                        url: '{{ route('getWorkshopPricing') }}',
                        method: 'GET',
                        data: {
                            workshop_id: workshopId,
                            member_type_id: memberTypeId
                        },
                        success: function(response) {
                            if (response.success) {
                                resolve(response);
                            } else {
                                reject(response.message);
                            }
                        },
                        error: function(xhr) {
                            reject('Error fetching workshop pricing');
                        }
                    });
                });
            }

            // Calculate total add-on price
            function calculateAddOnTotal(delegate, totalAttendee) {
                let addOnTotal = 0;

                selectedAddOns.forEach(addon => {
                    // Each add-on applies to all attendees (main + guests)
                    addOnTotal += addon.amount * totalAttendee;
                });

                return addOnTotal;
            }

            // Enhanced price calculation
            $("#calculatePrice").click(async function(e) {
                e.preventDefault();

                if (!validateSelections()) {
                    return;
                }

                // Show loading state
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Calculating...');

                try {
                    const registrationPrice = parseFloat('{{ $amount }}') || 0;
                    const checkCountry = '{{ auth()->user()->userDetail->country->country_name }}';
                    const guestPrice = parseFloat('{{ @$memberTypePrice->guest_amount }}') || 0;
                    const additionalGuest = parseInt($("#total_attendee").val()) || 0;
                    const workshopId = $('#workshop_id').val();
                    const memberTypeId = '{{ @$memberTypePrice->memberType->id }}';
                    const delegate = '{{ @$memberTypePrice->memberType->delegate }}';
                    const currencyCondition = (delegate == 1 ? 'Rs. ' : '$ ');
                    const memberType = '{{ @$memberTypePrice->memberType->type }}';
                    const totalAttendee = additionalGuest + 1;

                    if (!registrationPrice) {
                        throw new Error("Conference price has not been updated by admin.");
                    }

                    // Fetch workshop pricing
                    const workshopData = await fetchWorkshopPricing(workshopId, memberTypeId);
                    workshopPrice = parseFloat(workshopData.main_price) || 0;
                    workshopGuestPrice = parseFloat(workshopData.guest_price) || 0;
                    currentWorkshopName = workshopData.workshop_name || '';

                    // Calculate add-on total price
                    const addOnTotalPrice = calculateAddOnTotal(delegate, totalAttendee);

                    // Initialize totals
                    let preTotalPrice = 0;
                    totalPrice = 0;

                    if (delegate == 2) {
                        preTotalPrice = registrationPrice + workshopPrice;
                    } else {
                        totalPrice = registrationPrice + workshopPrice;
                    }

                    // Build price table
                    const calculatedData = $("#calculatedData");
                    calculatedData.empty();

                    let rowNumber = 1;

                    // Conference registration row
                    calculatedData.append(generatePriceTableRow(
                        rowNumber++,
                        `🎓 Conference - ${memberType}`,
                        1,
                        registrationPrice,
                        registrationPrice,
                        currencySymbol
                    ));

                    // Workshop registration row
                    if (workshopId && workshopPrice > 0) {
                        calculatedData.append(generatePriceTableRow(
                            rowNumber++,
                            `🔬 Workshop - ${currentWorkshopName}`,
                            1,
                            workshopPrice,
                            workshopPrice,
                            currencyCondition
                        ));
                    }

                    // Additional guests pricing
                    if (additionalGuest > 0) {
                        // Conference guests
                        const guestsTotalPrice = additionalGuest * guestPrice;
                        if (delegate == 2) {
                            preTotalPrice += guestsTotalPrice;
                        } else {
                            totalPrice += guestsTotalPrice;
                        }

                        calculatedData.append(generatePriceTableRow(
                            rowNumber++,
                            `👥 Conference - Additional Guests`,
                            additionalGuest,
                            guestPrice,
                            guestsTotalPrice,
                            currencyCondition
                        ));

                        // Workshop guests
                        if (workshopId && workshopGuestPrice > 0) {
                            const workshopGuestsTotalPrice = additionalGuest * workshopGuestPrice;
                            if (delegate == 2) {
                                preTotalPrice += workshopGuestsTotalPrice;
                            } else {
                                totalPrice += workshopGuestsTotalPrice;
                            }

                            calculatedData.append(generatePriceTableRow(
                                rowNumber++,
                                `🔬 Workshop - Additional Guests`,
                                additionalGuest,
                                workshopGuestPrice,
                                workshopGuestsTotalPrice,
                                currencyCondition
                            ));
                        }
                    }

                    // Add-ons pricing (multiple add-ons)
                    if (selectedAddOns.length > 0) {
                        selectedAddOns.forEach(addon => {
                            const addonTotalForThisItem = addon.amount * totalAttendee;

                            if (delegate == 2) {
                                preTotalPrice += addonTotalForThisItem;
                            } else {
                                totalPrice += addonTotalForThisItem;
                            }

                            calculatedData.append(generatePriceTableRow(
                                rowNumber++,
                                `🎉 ${addon.name}`,
                                totalAttendee,
                                addon.amount,
                                addonTotalForThisItem,
                                currencyCondition
                            ));
                        });
                    }

                    // Service charge for international payments
                    if (delegate == 2) {
                        const additionalCharge = preTotalPrice * 0.035;
                        totalPrice = preTotalPrice + additionalCharge;

                        calculatedData.append(generatePriceTableRow(
                            rowNumber++,
                            `💳 Service Charge (3.5%)`,
                            '',
                            '',
                            additionalCharge,
                            currencyCondition
                        ));
                    }

                    // Calculate workshop amount separately for tracking
                    workshopAmount = workshopPrice;
                    if (additionalGuest > 0 && workshopGuestPrice > 0) {
                        workshopAmount += (additionalGuest * workshopGuestPrice);
                    }

                    // Final total row
                    calculatedData.append(generatePriceTableRow(
                        '',
                        '💰 <strong>TOTAL AMOUNT</strong>',
                        `<strong>${totalAttendee}</strong>`,
                        '',
                        totalPrice,
                        currencyCondition,
                        true
                    ));

                    // Store calculated values
                    calculatedAmount = totalPrice;
                    isPriceCalculated = true;

                    // Update all amount fields
                    $(".amount").val(totalPrice);

                    // Update workshop amount fields
                    $("input[name='workshop_amount']").val(workshopAmount);

                    // Update summary
                    $('#summaryAmount').text(currencyCondition + totalPrice);

                    // Show price table with animation
                    $("#priceTable").fadeIn(500);

                    // Update step indicator
                    updateStepIndicator(2);

                    // Show payment section
                    $("#paymentSection").fadeIn(500);

                    // Show success message
                    const workshopText = workshopId ? ' and workshop' : '';
                    const addOnText = selectedAddOns.length > 0 ?
                        ` with ${selectedAddOns.length} add-on(s)` : '';
                    notyf.success(
                        `Price calculated successfully! Conference${workshopText}${addOnText} total: ${currencyCondition}${totalPrice}`
                    );

                    // Scroll to payment section
                    $('html, body').animate({
                        scrollTop: $("#paymentSection").offset().top - 50
                    }, 1000);

                } catch (error) {
                    console.error('Price calculation error:', error);
                    notyf.error(error.message || 'Error calculating price. Please try again.');
                } finally {
                    // Reset button state
                    $btn.prop('disabled', false).html(originalText);
                }
            });

            // Event handlers for form changes
            $('#workshop_id, #total_attendee').change(function() {
                updateRegistrationSummary();

                // Reset calculation if price was already calculated
                if (isPriceCalculated) {
                    const changeType = $(this).attr('id') === 'workshop_id' ? 'workshop selection' :
                        'attendee count';
                    resetPriceCalculation(changeType);
                }
            });

            // Add-on checkbox change handler
            $(document).on('change', '.addon-checkbox', handleAddOnChange);

            // Function to update all hidden fields
            function updateAllHiddenFields() {
                const registrantType = $('#registrant_type').val() || ($('#registrantType').val() == 1 ? '1' : '2');
                const accompanyPerson = $('#total_attendee').val() || '0';
                const workshopId = $('#workshop_id').val() || '';

                // Create add-ons data string for form submission
                const addOnsData = selectedAddOns.map(addon => `${addon.id}:${addon.amount}`).join(',');

                // Update all registrant type fields
                $('input[name="registrant_type"]').val(registrantType);

                // Update all accompany person fields
                $('input[name="accompany_person"]').val(accompanyPerson);

                // Update all workshop fields
                $('input[name="workshop_id"]').val(workshopId);
                $('input[name="workshop_amount"]').val(workshopAmount);

                // Update add-ons data in hidden fields
                $('input[name="selected_addons"]').val(addOnsData);
            }

            // Payment method selection handler
            $('input[name="paymentMode"]').change(function() {
                if (!isPriceCalculated) {
                    notyf.error('Please calculate the price first before selecting payment method.');
                    $(this).prop('checked', false);
                    return;
                }

                const selectedValue = $(this).val();
                const checkCountry = '{{ auth()->user()->userDetail->country->country_name }}';
                const delegate = '{{ @$memberTypePrice->memberType->delegate }}';

                // Update visual selection
                $('.payment-method-card').removeClass('selected');
                $(this).closest('.payment-method-card').addClass('selected');

                // Update step indicator
                updateStepIndicator(3);

                $("#processingDiv").fadeIn();

                // Hide all processing divs
                $(".fonePayProcessingDiv, .dollarCardProcessingDiv, .mocoProcessingDiv, .esewaProcessingDiv, .khaltiProcessingDiv, .bankTransferProcessingDiv")
                    .hide();

                // Update all hidden fields with current values
                updateAllHiddenFields();

                // Show selected payment method processing div
                $(`.${selectedValue}ProcessingDiv`).fadeIn();

                // Enable appropriate submit buttons
                enablePaymentButton(selectedValue, delegate, checkCountry);
            });

            // Function to enable payment buttons based on method
            function enablePaymentButton(selectedValue, delegate, checkCountry) {
                // Disable all payment buttons first
                $("#submitFonePay, #submitEsewa, #submitKhalti, #submitMoco, #submitButtonInternationalPayment, #submitButtonBankTransfer")
                    .attr('disabled', true);

                if (selectedValue == "fonePay") {
                    // Handle India FonePay conversion for USD
                    if (delegate == 2 && checkCountry == 'India') {
                        convertUsdToInr(selectedValue);
                    } else {
                        $("#submitFonePay").attr('disabled', false);
                    }
                } else if (selectedValue == "dollarCard") {
                    $("#submitButtonInternationalPayment").attr('disabled', false);
                } else if (selectedValue == "moco") {
                    $("#submitMoco").attr('disabled', false);
                } else if (selectedValue == "esewa") {
                    $("#submitEsewa").attr('disabled', false);
                } else if (selectedValue == "khalti") {
                    $("#submitKhalti").attr('disabled', false);
                } else if (selectedValue == "bankTransfer") {
                    $("#submitButtonBankTransfer").attr('disabled', false);
                }
            }

            // Function to convert USD to INR for FonePay
            function convertUsdToInr(paymentMode) {
                const currencyData = {
                    'usd': calculatedAmount,
                    'paymentMode': paymentMode,
                    '_token': $('meta[name="csrf-token"]').attr('content')
                };

                $.post('{{ route('convertUsdToInr') }}', currencyData)
                    .done(function(response) {
                        if (response.type == 'success') {
                            $("#fonePayAmount").val(response.amount);
                            $("#submitFonePay").attr('disabled', false);

                            // Update the total in price table for INR
                            $("#calculatedData tr:last-child td:last-child").html(
                                '<strong>INR ' + response.amount + '</strong>'
                            );
                            notyf.success('Amount converted to INR: ₹' + response.amount);
                        } else {
                            notyf.error(response.message);
                        }
                    })
                    .fail(function() {
                        notyf.error('Error converting currency. Please try again.');
                    });
            }

            // Registrant type selection modal handler
            $("#chooseRegistrantButton").on('click', function(e) {
                e.preventDefault();
                const registrantValue = $("#registrantType").val();

                if (!registrantValue) {
                    notyf.error('Please select a registration type to continue.');
                    return;
                }

                const $btn = $(this);
                const originalText = $btn.html();

                if (registrantValue == 1) {
                    // Attendee
                    $("#openModal").modal('hide');
                    setRegistrantType('1');
                    $('#summaryRegistrantType').text('👥 Attendee');
                } else {
                    // Speaker - check if submission exists
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Checking...');

                    const data = new FormData($('#chooseRegistratantType')[0]);
                    $.ajax({
                        type: "POST",
                        url: '{{ route('my-society.conference.checkSubmission', [$society, $conference]) }}',
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.checkSubmission == 'not-submitted') {
                                notyf.error('Please submit your presentation first.');
                                setTimeout(function() {
                                    window.location.href =
                                        '{{ route('my-society.conference.submission.create', [$society, $conference]) }}';
                                }, 1500);
                            } else {
                                $("#openModal").modal('hide');
                                setRegistrantType('2');
                                $('#summaryRegistrantType').text('🎤 Speaker');
                            }
                        },
                        error: function() {
                            notyf.error('Error checking submission status.');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html(originalText);
                        }
                    });
                }
            });

            // Function to set registrant type in all forms
            function setRegistrantType(type) {
                $('#registrant_type').val(type);
                $('input[name="registrant_type"]').val(type);
            }

            // Form submission handlers
            $("#submitButtonInternationalPayment").click(function(e) {
                e.preventDefault();
                if (!isPriceCalculated) {
                    notyf.error('Please calculate the price first.');
                    return;
                }
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                $("#internationalPaymentForm").submit();
            });

            $("#submitButtonBankTransfer").click(function(e) {
                e.preventDefault();
                if (!isPriceCalculated) {
                    notyf.error('Please calculate the price first.');
                    return;
                }

                // Validate required fields
                const transactionId = $('#transaction_id').val();
                if (!transactionId) {
                    notyf.error('Please enter the transaction ID.');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
                $("#bankTranferForm").submit();
            });

            $("#submitFonePay, #submitEsewa, #submitKhalti").click(function(e) {
                if (!isPriceCalculated) {
                    e.preventDefault();
                    notyf.error('Please calculate the price first.');
                    return;
                }
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                $(this).closest('form').submit();
            });

            // MoCo payment handling
            let paymentCheckInterval;
            let mocoReferenceNumber = null;

            $("#mocoForm").on('submit', function(e) {
                e.preventDefault();

                if (!isPriceCalculated) {
                    notyf.error('Please calculate the price first.');
                    return;
                }

                const submitButton = $("#submitMoco");
                const spinner = submitButton.find('.spinner-border');

                submitButton.prop('disabled', true);
                spinner.removeClass('d-none');

                const formData = {
                    registrant_type: $("#registrant_type_moco").val(),
                    accompany_person: $("#accompany_person_moco").val(),
                    workshop_id: $("#workshop_id_moco").val(),
                    workshop_amount: $("#workshop_amount_moco").val(),
                    selected_addons: $("#selected_addons_moco").val(),
                    payment_type: 2,
                    amount: $("#mocoAmount").val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: "{{ route('my-society.conference.moco', [$society, $conference]) }}",
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            if (response.data.qr_data) {
                                $("#mocoQrCode").html(
                                    `<img src="${response.data.qr_data}" alt="QR Code" class="img-fluid" style="max-width: 300px;">`
                                );
                            } else {
                                $("#mocoQrCode").html(
                                    '<div class="alert alert-danger">QR Code could not be loaded</div>'
                                );
                            }

                            $("#mocoRefNumber").text(response.data.referenceNumber);
                            $("#mocoPayAmount").text(response.data.amount);
                            mocoReferenceNumber = response.data.referenceNumber;

                            const modal = new bootstrap.Modal(document.getElementById(
                                'mocoQrModal'));
                            modal.show();

                            startPaymentStatusCheck();
                            updateStepIndicator(4);
                        } else {
                            notyf.error('Error generating QR code: ' + (response.message ||
                                'Unknown error'));
                        }
                    },
                    error: function(xhr) {
                        console.error('MoCo API Error:', xhr);
                        let errorMessage = 'Failed to generate QR code. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        notyf.error(errorMessage);
                    },
                    complete: function() {
                        submitButton.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });

            // MoCo payment status checking
            function startPaymentStatusCheck() {
                paymentCheckInterval = setInterval(checkPaymentStatus, 30000);
            }

            function checkPaymentStatus() {
                if (!mocoReferenceNumber) return;

                const checkButton = $("#mocoCheckStatus");
                const spinner = checkButton.find('.spinner-border');
                spinner.removeClass('d-none');

                $.ajax({
                    url: "{{ route('my-society.conference.mocoCheckStatus', [$society, $conference]) }}",
                    method: 'POST',
                    data: {
                        reference_number: mocoReferenceNumber,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.txnStatus === 'success') {
                            $("#mocoPayStatus").removeClass('bg-warning bg-danger').addClass(
                                'bg-success').text('Completed');
                            clearInterval(paymentCheckInterval);
                            notyf.success('Payment completed successfully!');

                            setTimeout(function() {
                                const baseUrl =
                                    "{{ route('my-society.conference.mocoSuccess', [$society, $conference]) }}";
                                window.location.href =
                                    `${baseUrl}?txnID=${encodeURIComponent(response.txnID)}`;
                            }, 2000);
                        } else if (response.txnStatus === 'failed') {
                            $("#mocoPayStatus").removeClass('bg-warning bg-success').addClass(
                                'bg-danger').text('Failed');
                            clearInterval(paymentCheckInterval);
                            notyf.error('Payment failed. Please try again.');
                        } else {
                            $("#mocoPayStatus").removeClass('bg-success bg-danger').addClass(
                                'bg-warning').text('Pending');
                        }
                    },
                    error: function(xhr) {
                        console.error('Status check error:', xhr);
                        notyf.error('Error checking payment status.');
                    },
                    complete: function() {
                        spinner.addClass('d-none');
                    }
                });
            }

            $("#mocoCheckStatus").on('click', checkPaymentStatus);

            $("#mocoCancelPayment").on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to cancel this payment?')) {
                    clearInterval(paymentCheckInterval);
                    mocoReferenceNumber = null;
                    const modal = bootstrap.Modal.getInstance(document.getElementById('mocoQrModal'));
                    if (modal) modal.hide();
                }
            });

            // Bank transfer form submission with AJAX
            $('#bankTranferForm').on('submit', function(e) {
                e.preventDefault();

                const form = this;
                const formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response)
                        if (response.success) {
                            notyf.success('Registration completed successfully!');
                            updateStepIndicator(4);
                            setTimeout(function() {
                                window.location.href =
                                    '{{ route('my-society.conference.index', [$society, $conference]) }}';
                            }, 2000);
                        } else {
                            $('#submitButtonBankTransfer').prop('disabled', false).html(
                                '<i class="fas fa-university"></i> Submit Bank Transfer');
                            notyf.error(response.message ||
                                'Registration failed. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.status)
                        $('#submitButtonBankTransfer').prop('disabled', false).html(
                            '<i class="fas fa-university"></i> Submit Bank Transfer');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('.text-danger').remove();
                            for (let key in errors) {
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.after('<p class="text-danger">' + errors[key][0] +
                                    '</p>');
                            }
                        } else {
                            notyf.error('An error occurred. Please try again.');
                        }
                    }
                });
            });

            // File upload preview
            $('#payment_voucher').change(function() {
                const file = this.files[0];
                if (file) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    if (fileSize > 2) {
                        notyf.error('File size should be less than 2MB');
                        $(this).val('');
                        return;
                    }

                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                    if (!allowedTypes.includes(file.type)) {
                        notyf.error('Please upload only JPG, PNG, or PDF files');
                        $(this).val('');
                        return;
                    }

                    notyf.success(`File "${file.name}" selected (${fileSize}MB)`);
                }
            });

            // Cleanup on page unload
            $(window).on('beforeunload', function() {
                if (paymentCheckInterval) {
                    clearInterval(paymentCheckInterval);
                }
            });

            // Initial setup
            updateRegistrationSummary();
            updateStepIndicator(1);

            // Smooth scrolling for better UX
            $('html').css('scroll-behavior', 'smooth');
        });
        @if ($checkPayment == 'failed')
            $(document).ready(function() {
                notyf.error("Your payment has failed. Please try again.");
            });
        @endif

        @if ($checkPayment == 'cancelled')
            $(document).ready(function() {
                notyf.error("Your payment has been cancelled.");
            });
        @endif

        @if ($checkPayment == 'terminated')
            $(document).ready(function() {
                notyf.error("Your payment session has been terminated.");
            });
        @endif

        // Display validation errors
        @if ($errors->any())
            $(document).ready(function() {
                @foreach ($errors->all() as $error)
                    notyf.error('{{ $error }}');
                @endforeach
            });
        @endif

        // Handle old input for accompanying persons
        @if (old('person_name'))
            var personsValue = @json(old('person_name', []));
            var errorMessages = @json($errors->get('person_name.*'));
        @elseif (isset($conference_registration) && $conference_registration->accompanyPersons->where('status', 1))
            @php
                $accompanyingPersons = $conference_registration->accompanyPersons->where('status', 1)->pluck('person_name')->toArray();
            @endphp
            var personsValue = @json($accompanyingPersons);
            var errorMessages = @json([]);
        @else
            var personsValue = @json([]);
            var errorMessages = @json([]);
        @endif
    </script>
@endsection
