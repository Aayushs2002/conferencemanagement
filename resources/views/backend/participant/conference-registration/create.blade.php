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

        .addon-checkbox:checked+label,
        .workshop-checkbox:checked+label {
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

        .workshop-selection-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .workshop-selection-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
        }

        .workshop-selection-card.selected {
            border-color: #28a745;
            background-color: #f8fff9;
        }
    </style>
    {{-- @if (!old() && !isset($conference_registration))
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
    @endif --}}

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
                    <div class="card-body">
                        @if (!empty($requiresStudentVerification))
                            <div class="alert alert-warning mb-4">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Verification Required:</strong>
                                This member type requires student/resident verification. Your conference registration
                                will remain pending until your documents are reviewed.
                            </div>
                        @endif

                        <!-- Registration Summary Card -->
                        <div id="registrationSummary" class="summary-card mt-5" style="display: none;">
                            <h5 class="mb-3">
                                <i class="fas fa-clipboard-list"></i> Registration Summary
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>📋 Conference:</strong> {{ $conference->conference_name ?? 'N/A' }}</p>
                                    <p><strong>👤 Type:</strong> <span id="summaryRegistrantType">-</span></p>
                                    <p><strong>🎯 Registration Type:</strong> <span id="summaryWorkshops">
                                            {{ $galaDinnerEnabled ? 'Conference + Gala Dinner' : 'Conference Only' }}
                                        </span></p>
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
                            @if (feature_enabled('workshop-management', getSociety(request()->segment(2))))
                                <div class="alert alert-custom alert-info mb-4">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Registration Options:</strong> Choose to register for conference only, or
                                    include
                                    workshops for enhanced learning experience.
                                </div>

                                <div class="row mb-4">
                                    <!-- Multiple Workshop Selection -->
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="fw-bold">
                                            <i class="fas fa-chalkboard-teacher text-primary"></i>
                                            Workshop Selection
                                            <small class="text-muted">(Optional - Multiple Selection)</small>

                                        </label>
                                        <div class="card">
                                            <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                                                <!-- Conference Only / Gala Dinner Option -->
                                                <div class="form-check mb-2 p-2 rounded workshop-selection-card">
                                                    <input class="form-check-input workshop-checkbox" type="checkbox"
                                                        name="selected_workshops[]" value="" id="conference_only"
                                                        checked>
                                                    <label
                                                        class="form-check-label d-flex justify-content-between align-items-center w-100"
                                                        for="conference_only">
                                                        <div>
                                                            <strong>
                                                                {{ $galaDinnerEnabled ? 'Conference + Gala Dinner' : 'Conference Only' }}
                                                            </strong>
                                                            <br><small class="text-muted">Attend conference sessions without
                                                                workshops
                                                                {{ $galaDinnerEnabled ? 'and enjoy gala dinner' : '' }}</small>
                                                        </div>
                                                        <span class="badge bg-success ms-2">Included</span>
                                                    </label>
                                                </div>

                                                @if ($workshops && $workshops->count() > 0)
                                                    @foreach ($workshops as $workshop)
                                                        <div class="form-check mb-2 p-2 rounded workshop-selection-card">
                                                            <input class="form-check-input workshop-checkbox"
                                                                type="checkbox" name="selected_workshops[]"
                                                                value="{{ $workshop->id }}"
                                                                data-name="{{ $workshop->workshop_title }}"
                                                                id="workshop_{{ $workshop->id }}"
                                                                @if (isset($conference_registration) &&
                                                                        $conference_registration->registrationWorkshops &&
                                                                        $conference_registration->registrationWorkshops->contains('workshop_id', $workshop->id)) checked @endif>
                                                            <label
                                                                class="form-check-label d-flex justify-content-between align-items-center w-100"
                                                                for="workshop_{{ $workshop->id }}">
                                                                <div>
                                                                    <strong>{{ $workshop->workshop_title }}</strong>
                                                                    {{-- @if ($workshop->workshop_description)
                                                                    <br><small
                                                                        class="text-muted">{{ Str::limit($workshop->workshop_description, 80) }}</small>
                                                                @endif
                                                                @if ($workshop->workshop_date)
                                                                    <br><small class="text-info">
                                                                        <i class="fas fa-calendar"></i>
                                                                        {{ date('M j, Y', strtotime($workshop->workshop_date)) }}
                                                                    </small>
                                                                @endif --}}
                                                                </div>
                                                                <span class="badge bg-primary ms-2"
                                                                    id="workshop_price_{{ $workshop->id }}">
                                                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center py-3">
                                                        <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-2"></i>
                                                        <p class="text-muted mb-0">No workshops available for this
                                                            conference
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            You can select multiple workshops to enhance your learning experience
                                        </small>
                                    </div>

                                    <!-- Multiple Add-ons Selection -->
                                    @if ($conferenceAddons && $conferenceAddons->count() > 0)
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="fw-bold">
                                                <i class="fas fa-utensils text-warning"></i> Add-ons
                                                <small class="text-muted">(Optional - Multiple Selection)</small>
                                            </label>
                                            <div class="card">
                                                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                                                    @foreach ($conferenceAddons as $addon)
                                                        @php
                                                            // Determine which amount to show based on registration period
                                                            $addonAmount = 0;
                                                            if (
                                                                $conference->early_bird_registration_deadline >=
                                                                date('Y-m-d')
                                                            ) {
                                                                $addonAmount = $addon->early_bird_amount ?? 0;
                                                            } elseif (
                                                                $conference->regular_registration_deadline >=
                                                                date('Y-m-d')
                                                            ) {
                                                                $addonAmount = $addon->regular_amount ?? 0;
                                                            } elseif (
                                                                !empty($conference->late_registration_deadline) &&
                                                                $conference->late_registration_deadline >= date('Y-m-d')
                                                            ) {
                                                                $addonAmount = $addon->late_amount ?? 0;
                                                            } else {
                                                                $addonAmount = $addon->on_site_amount ?? 0;
                                                            }
                                                            $addonGuestAmount = $addon->guest_amount ?? 0;
                                                        @endphp
                                                        <div class="form-check mb-3 p-2 rounded border">
                                                            <input class="form-check-input addon-checkbox" type="checkbox"
                                                                name="selected_addons[]" value="{{ $addon->id }}"
                                                                data-name="{{ $addon->addon_name }}"
                                                                data-amount="{{ $addonAmount }}"
                                                                data-guest-amount="{{ $addonGuestAmount }}"
                                                                id="addon_{{ $addon->id }}"
                                                                @if (isset($conference_registration) &&
                                                                        $conference_registration->registrationAddons &&
                                                                        $conference_registration->registrationAddons->contains('addon_id', $addon->id)) checked @endif>
                                                            <label
                                                                class="form-check-label d-flex justify-content-between align-items-start w-100"
                                                                for="addon_{{ $addon->id }}">
                                                                <div class="flex-grow-1">
                                                                    <strong>{{ $addon->addon_name }}</strong>
                                                                    @if ($addon->addon_description)
                                                                        <br><small
                                                                            class="text-muted">{{ $addon->addon_description }}</small>
                                                                    @endif
                                                                </div>
                                                                <span class="badge bg-primary ms-2">
                                                                    @if(isset($addonAvailability) && $addonAvailability === 'accompany_only')
                                                                        {{ @$memberTypePrice->memberType->delegate == 1 ? 'Rs. ' : '$ ' }}{{ number_format($addonGuestAmount > 0 ? $addonGuestAmount : $addonAmount, 2) }}
                                                                        <small>/guest</small>
                                                                    @elseif(isset($addonAvailability) && $addonAvailability === 'participant_only')
                                                                        {{ @$memberTypePrice->memberType->delegate == 1 ? 'Rs. ' : '$ ' }}{{ number_format($addonAmount, 2) }}
                                                                        <small>/participant</small>
                                                                    @else
                                                                        {{ @$memberTypePrice->memberType->delegate == 1 ? 'Rs. ' : '$ ' }}{{ number_format($addonAmount, 2) }}
                                                                        <small>/person</small>
                                                                        @if ($addonGuestAmount > 0 && $addonGuestAmount != $addonAmount)
                                                                            <br><small>Guest:
                                                                                {{ @$memberTypePrice->memberType->delegate == 1 ? 'Rs. ' : '$ ' }}{{ number_format($addonGuestAmount, 2) }}</small>
                                                                        @endif
                                                                    @endif
                                                                </span>
                                                            </label>

                                                            <!-- Guest Inclusion Option - Show based on addon_availability setting -->
                                                            @php
                                                                $showGuestOption = true;
                                                                $guestChecked = true;
                                                                $guestDisabled = false;
                                                                
                                                                // Handle addon availability settings
                                                                if (isset($addonAvailability)) {
                                                                    if ($addonAvailability === 'participant_only') {
                                                                        $showGuestOption = false;
                                                                        $guestChecked = false;
                                                                    } elseif ($addonAvailability === 'accompany_only') {
                                                                        $guestChecked = true;
                                                                        $guestDisabled = true;
                                                                    }
                                                                }
                                                            @endphp
                                                            
                                                            @if($showGuestOption)
                                                                <div class="ms-4 mt-2 addon-guest-option"
                                                                    id="guest_option_{{ $addon->id }}"
                                                                    style="display: none;">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input addon-guest-checkbox"
                                                                            type="checkbox"
                                                                            id="include_guest_{{ $addon->id }}"
                                                                            data-addon-id="{{ $addon->id }}" 
                                                                            {{ $guestChecked ? 'checked' : '' }}
                                                                            {{ $guestDisabled ? 'disabled' : '' }}>
                                                                        <label class="form-check-label text-muted small"
                                                                            for="include_guest_{{ $addon->id }}">
                                                                            <i class="fas fa-user-friends"></i> 
                                                                            @if($addonAvailability === 'accompany_only')
                                                                                Only for accompanying persons
                                                                            @else
                                                                                Include for guests
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                @if(isset($addonAvailability))
                                                    @if($addonAvailability === 'participant_only')
                                                        Add-ons are available for participants only
                                                    @elseif($addonAvailability === 'accompany_only')
                                                        Add-ons are available for accompanying persons only
                                                    @else
                                                        You can choose whether add-ons apply to guests or only to you
                                                    @endif
                                                @else
                                                    You can choose whether add-ons apply to guests or only to you
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            @endif

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
                                @if (($workshops && $workshops->count() > 0) || ($conferenceAddons && $conferenceAddons->count() > 0))
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="alert alert-info w-100">
                                            <i class="fas fa-calculator"></i>
                                            <strong>Pricing Note:</strong>
                                            Workshops and add-ons are charged per person.
                                            If
                                            you select 1 guest, they will be charged for 2 people (you + 1 guest).
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Calculate Button -->
                            <div class="text-center mb-4">
                                <button type="button" id="calculatePrice" class="btn btn-primary btn-calculate btn-lg">
                                    <i class="fas fa-calculator"></i> Calculate Total Price
                                </button>
                            </div>

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

                            @if (current_user()->userDetail->country_id != 125 && 
                                 $static_qr_payment_setting && 
                                 $static_qr_payment_setting->countries && 
                                 $static_qr_payment_setting->countries->contains('id', current_user()->userDetail->country_id))
                                <div class="col-md-3 mb-3">
                                    <label class="card payment-method-card w-100" for="staticQrRadio"
                                        style="cursor:pointer;">
                                        <div class="card-body text-center">
                                            <h5 class="text-primary">📱 Static QR</h5>
                                            <img src="{{ asset('default-image/qr-code.png') }}" class="img-fluid mb-2"
                                                style="max-height: 60px;" 
                                                onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                                            <div style="display:none; font-size: 48px; margin: 10px 0;">📱</div>
                                            @if (current_user()->userDetail->country->country_name == 'India')
                                                <small class="text-muted">INR Payment</small>
                                            @endif
                                            <div class="form-check mt-3 d-flex justify-content-center">
                                                <input class="form-check-input" type="radio" name="paymentMode"
                                                    value="staticQr" id="staticQrRadio">
                                                <label class="form-check-label fw-bold ms-2" for="staticQrRadio">
                                                    Select
                                                </label>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                            @if (current_user()->userDetail->country_id != 125 && 
                                 $international_payemnt_setting && 
                                 $international_payemnt_setting->countries && 
                                 $international_payemnt_setting->countries->contains('id', current_user()->userDetail->country_id))
                            {{-- && !in_array(current_user()->userDetail->country_id, [78, 134, 165]) --}}
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

                            @if (current_user()->userDetail->country_id != 125 && $international_bank_transfer?->bank_detail)
                            {{-- in_array(current_user()->userDetail->country_id, [78, 134, 165]) && --}}
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

                            @if (current_user()->userDetail->country_id == 125 && $national_payemnt_setting?->account_detail)
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

                                @if ($national_payemnt_setting?->connectips_merchant_id)
                                    <div class="col-md-3 mb-3">
                                        <label class="card payment-method-card w-100" for="connectipsRadio"
                                            style="cursor:pointer;">
                                            <div class="card-body text-center">
                                                <h5 class="text-primary">🔐 ConnectIPS</h5>
                                                <img src="{{ asset('default-image/connectips-logo.png') }}"
                                                    class="img-fluid mb-2" style="max-height: 60px;"
                                                    onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                                                <div style="display:none; font-size: 24px; margin: 10px 0;">💳</div>
                                                <div class="form-check mt-3 d-flex justify-content-center">
                                                    <input class="form-check-input" type="radio" name="paymentMode"
                                                        value="connectips" id="connectipsRadio">
                                                    <label class="form-check-label fw-bold ms-2" for="connectipsRadio">
                                                        Select
                                                    </label>
                                                </div>
                                                <small class="text-muted d-block mt-2">
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#connectipsUrlModal" 
                                                        class="text-info" onclick="event.stopPropagation();">
                                                        <i class="fas fa-info-circle"></i> View Setup URLs
                                                    </a>
                                                </small>
                                            </div>
                                        </label>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Payment Processing Section -->
                        <div id="processingDiv" style="display: none;">
                            <hr class="my-4">
                            <div class="row">
                                <div class="col-md-4 paymentHeader mb-4">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-info-circle"></i>
                                                <span class="text-white">
                                                    Payment Information
                                                </span>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="bankTransferProcessingDiv">
                                                @if (current_user()->userDetail->country_id != 125 && $international_bank_transfer?->bank_detail)
                                                    <h6 class="text-info">Bank Transfer Details</h6>
                                                    <img src="{{ asset('default-image/bankTransfer.jpg') }}"
                                                        height="40" class="mb-2">
                                                    <div class="small">
                                                        {!! $international_bank_transfer?->bank_detail !!}
                                                    </div>
                                                @endif
                                                @if (current_user()->userDetail->country_id == 125 && $national_payemnt_setting?->account_detail)
                                                    <h6 class="text-info">Bank Transfer Details</h6>
                                                    <img src="{{ asset('default-image/bankTransfer.jpg') }}"
                                                        height="40" class="mb-2">
                                                    <div class="small">
                                                        {!! $national_payemnt_setting?->account_detail !!}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="staticQrProcessingDiv" style="display: none;">
                                                @if (current_user()->userDetail->country_id != 125 && $static_qr_payment_setting?->qr_details)
                                                    <h6 class="text-info">Static QR Payment Details</h6>
                                                    <div class="small">
                                                        {!! $static_qr_payment_setting?->qr_details !!}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class=" border-success">
                                        <div class="card-header bg-success text-white paymentHeader" id="paymentHeader">
                                            <h6 class="mb-0">
                                                <i class="fas fa-credit-card"></i>
                                                <span class="text-white">
                                                    Complete Payment
                                                </span>
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
                                                        <input type="hidden" name="selected_workshops"
                                                            id="selected_workshops_fonepay">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_fonepay">
                                                        <input type="hidden" name="conference_base_amount"
                                                            id="conference_base_amount_fonepay">
                                                        <input type="hidden" name="payment_type" value="1">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="fonePayAmount">
                                                        <input type="hidden" name="payment_currency" id="fonePayCurrency" value="USD">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_fonepay">
                                                        <div class="d-grid d-flex justify-content-center">
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
                                                        <input type="hidden" name="selected_workshops"
                                                            id="selected_workshops_esewa">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_esewa">
                                                        <input type="hidden" name="conference_base_amount"
                                                            id="conference_base_amount_esewa">
                                                        <input type="hidden" name="payment_type" value="3">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="esewaAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_esewa">
                                                        <div class="d-grid d-flex justify-content-center">
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
                                                        <input type="hidden" name="selected_workshops"
                                                            id="selected_workshops_khalti">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_khalti">
                                                        <input type="hidden" name="conference_base_amount"
                                                            id="conference_base_amount_khalti">
                                                        <input type="hidden" name="payment_type" value="4">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="khaltiAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_khalti">
                                                        <div class="d-grid d-flex justify-content-center">
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
                                                        <input type="hidden" name="selected_workshops"
                                                            id="selected_workshops_moco">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_moco">
                                                        <input type="hidden" name="conference_base_amount"
                                                            id="conference_base_amount_moco">
                                                        <input type="hidden" name="payment_type" value="2">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="mocoAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_moco">

                                                        <div class="d-grid d-flex justify-content-center">
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

                                            <!-- ConnectIPS Form -->
                                            @if (current_user()->userDetail->country_id == 125)
                                                <div class="connectipsProcessingDiv" style="display: none;">
                                                    <form
                                                        action="{{ route('my-society.conference.connectips', [$society, $conference]) }}"
                                                        method="POST" id="connectipsForm">
                                                        @csrf
                                                        <input type="hidden" name="registrant_type"
                                                            id="registrant_type_connectips">
                                                        <input type="hidden" name="accompany_person"
                                                            id="accompany_person_connectips">
                                                        <input type="hidden" name="selected_workshops"
                                                            id="selected_workshops_connectips">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_connectips">
                                                        <input type="hidden" name="conference_base_amount"
                                                            id="conference_base_amount_connectips">
                                                        <input type="hidden" name="payment_type" value="7">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="connectipsAmount">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_connectips">
                                                        <div class="d-grid d-flex justify-content-center">
                                                            <button type="submit" id="submitConnectIPS"
                                                                class="btn btn-primary btn-lg" disabled>
                                                                <i class="fas fa-lock"></i> Pay with ConnectIPS
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
                                                        <input type="hidden" name="selected_workshops"
                                                            id="selected_workshops_international">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_international">
                                                        <input type="hidden" name="conference_base_amount"
                                                            id="conference_base_amount_international">
                                                        <input type="hidden" name="amount" class="amount"
                                                            id="internationalAmount">
                                                        <input type="hidden" name="payment_currency" value="USD">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_international">
                                                            

                                                        <div class="d-grid d-flex justify-content-center">
                                                            <button type="submit" id="submitButtonInternationalPayment"
                                                                class="btn btn-primary btn-lg" disabled>
                                                                <i class="fas fa-credit-card"></i> Pay via Card
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            <!-- Static QR Form -->
                                            @if (current_user()->userDetail->country_id != 125 && $static_qr_payment_setting)
                                                <div class="staticQrFormProcessingDiv" style="display: none;">
                                                    <form
                                                        action="{{ route('my-society.conference.store', [$society, $conference]) }}"
                                                        method="POST" enctype="multipart/form-data" id="staticQrForm">
                                                        @csrf
                                                        <div class="row my-4">
                                                            <div class="col-md-6 form-group mb-3">
                                                                <label for="static_qr_transaction_id" class="fw-bold">
                                                                    <i class="fas fa-receipt"></i> Transaction ID/Reference
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    name="transaction_id" id="static_qr_transaction_id"
                                                                    placeholder="Enter transaction ID" required>
                                                                @error('transaction_id')
                                                                    <p class="text-danger">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-6 form-group mb-3">
                                                                <label for="static_qr_payment_voucher" class="fw-bold">
                                                                    <i class="fas fa-file-upload"></i> Payment Receipt
                                                                    <small class="text-muted">(JPG/PNG/PDF)</small>
                                                                </label>
                                                                <input type="file" class="form-control"
                                                                    name="payment_voucher" id="static_qr_payment_voucher"
                                                                    accept=".jpg,.jpeg,.png,.pdf">
                                                                @error('payment_voucher')
                                                                    <p class="text-danger">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="registrant_type"
                                                            id="registrant_type_static_qr">
                                                        <input type="hidden" name="accompany_person"
                                                            id="accompany_person_static_qr">
                                                        <input type="hidden" name="selected_workshops"
                                                            id="selected_workshops_static_qr">
                                                        <input type="hidden" name="workshop_amount"
                                                            id="workshop_amount_static_qr">
                                                        <input type="hidden" name="conference_base_amount"
                                                            id="conference_base_amount_static_qr">
                                                        <input type="hidden" name="payment_type" value="8">
                                                        <input type="hidden" name="amount" class="amount" id="staticQrAmount">
                                                        <input type="hidden" name="payment_currency" id="staticQrCurrency" value="USD">
                                                        <input type="hidden" name="selected_addons"
                                                            id="selected_addons_static_qr">
                                                        <div class="d-grid d-flex justify-content-center">
                                                            <button type="submit" id="submitButtonStaticQr"
                                                                class="btn btn-success btn-lg" disabled>
                                                                <i class="fas fa-qrcode"></i> Submit Static QR Payment
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                            <!-- Bank Transfer Form -->
                                            <div class="bankTransferProcessingDiv" style="display: none;">
                                                <form
                                                    action="{{ route('my-society.conference.store', [$society, $conference]) }}"
                                                    method="POST" enctype="multipart/form-data" id="bankTranferForm">
                                                    @csrf
                                                    <div class="row my-4">
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
                                                    <input type="hidden" name="selected_workshops"
                                                        id="selected_workshops_bank">
                                                    <input type="hidden" name="workshop_amount"
                                                        id="workshop_amount_bank">
                                                    <input type="hidden" name="conference_base_amount"
                                                        id="conference_base_amount_bank">
                                                    <input type="hidden" name="payment_type" value="6">
                                                    <input type="hidden" name="amount" class="amount" id="bankAmount">
                                                    <input type="hidden" name="payment_currency" value="USD">
                                                    <input type="hidden" name="selected_addons"
                                                        id="selected_addons_bank">
                                                    <div class="d-grid d-flex justify-content-center">
                                                        <button type="submit" id="submitButtonBankTransfer"
                                                            class="btn btn-success btn-lg" disabled>
                                                            <i class="fas fa-university"></i> Submit Bank Transfer
                                                        </button>
                                                    </div>
                                                </form>
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
    </div>

    <!-- MoCo QR Code Modal -->
    <div class="modal fade" id="mocoQrModal" tabindex="-1" aria-labelledby="mocoQrModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content registration-card">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="mocoQrModalLabel">
                        <i class="ti tabler-qrcode text-white"></i>
                        <span class="text-white">
                            Scan QR Code to Pay
                        </span>
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div id="mocoQrCode" class="mb-3"></div>
                    <div id="mocoUserNote" class="alert alert-warning py-2 small text-center">
                        <i class="ti tabler-info-circle"></i>
                        After payment, please click <strong>Check Status</strong> to confirm your transaction.
                    </div>
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

    <!-- ConnectIPS URL Information Modal -->
    <div class="modal fade" id="connectipsUrlModal" tabindex="-1" aria-labelledby="connectipsUrlModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content registration-card">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title" id="connectipsUrlModalLabel">
                        <i class="fas fa-link"></i> ConnectIPS Integration URLs
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Important:</strong> Please provide these URLs to ConnectIPS technical team for integration setup.
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-check-circle"></i> Success URL (Return URL)
                        </div>
                        <div class="card-body">
                            <div class="input-group">
                                <input type="text" class="form-control" id="connectipsSuccessUrl" 
                                    value="{{ route('my-society.conference.connectipsSuccess', [$society, $conference]) }}" 
                                    readonly>
                                <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard('connectipsSuccessUrl')">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-danger text-white">
                            <i class="fas fa-times-circle"></i> Failure URL
                        </div>
                        <div class="card-body">
                            <div class="input-group">
                                <input type="text" class="form-control" id="connectipsFailureUrl" 
                                    value="{{ route('my-society.conference.connectipsFailure', [$society, $conference]) }}" 
                                    readonly>
                                <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard('connectipsFailureUrl')">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Note:</strong> ConnectIPS must whitelist these URLs before payment processing will work.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Copy to clipboard function
        function copyToClipboard(elementId) {
            const input = document.getElementById(elementId);
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            
            notyf.success('URL copied to clipboard!');
        }

        $(document).ready(function() {
            // Global variables
            let totalPrice = 0;
            let calculatedAmount = 0;
            let calculatedAmountUSD = 0; // Store original USD amount
            let isConvertedToINR = false; // Track if currently in INR
            let isPriceCalculated = false;
            let currencySymbol = '{{ @$memberTypePrice->memberType->delegate == 1 ? 'Rs. ' : '$ ' }}';
            let selectedAddOns = []; // Store selected add-ons with their details
            let selectedWorkshops = []; // Store selected workshops with their details
            let workshopPricing = {}; // Store workshop pricing data
            let conferenceBaseAmount = 0; // Store the calculated conference base amount
            const addonAvailability = '{{ $addonAvailability ?? 'both' }}'; // Get addon availability setting
            
            // Log the addon availability setting for debugging
            console.log('Addon Availability Setting:', addonAvailability);

            // Initialize
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Reset all payment buttons to their original state
            function resetAllPaymentButtons() {
                $("#submitFonePay").prop('disabled', false).html(
                    '<i class="fas fa-mobile-alt"></i> Pay with FonePay');
                $("#submitEsewa").prop('disabled', false).html('<i class="fas fa-wallet"></i> Pay with eSewa');
                $("#submitKhalti").prop('disabled', false).html('<i class="fas fa-wallet"></i> Pay with Khalti');
                $("#submitMoco").prop('disabled', false).html('<i class="fas fa-qrcode"></i> Pay with MoCo');
                $("#submitConnectIPS").prop('disabled', false).html('<i class="fas fa-lock"></i> Pay with ConnectIPS');
                $("#submitButtonInternationalPayment").prop('disabled', false).html(
                    '<i class="fas fa-credit-card"></i> Proceed to Payment');
                $("#submitButtonBankTransfer").prop('disabled', false).html(
                    '<i class="fas fa-university"></i> Submit Bank Transfer');
                $("#submitButtonStaticQr").prop('disabled', false).html(
                    '<i class="fas fa-qrcode"></i> Submit Static QR Payment');
            }

            // Handle browser back/forward button - reset buttons when page is shown from cache
            window.addEventListener('pageshow', function(event) {
                if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                    // Page was loaded from cache (back/forward button)
                    resetAllPaymentButtons();
                }
            });

            // Reset buttons on page load
            resetAllPaymentButtons();

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

            // Load workshop pricing when page loads
            function loadWorkshopPricing() {
                const memberTypeId = '{{ @$memberTypePrice->memberType->id }}';
                const delegate = '{{ @$memberTypePrice->memberType->delegate }}';
                const currencySymbol = delegate == 1 ? 'Rs. ' : '$ ';

                $('.workshop-checkbox').each(function() {
                    const workshopId = $(this).val();
                    if (workshopId && workshopId !== '') {
                        $.ajax({
                            url: '{{ route('getWorkshopPricing') }}',
                            method: 'GET',
                            data: {
                                workshop_id: workshopId,
                                member_type_id: memberTypeId
                            },
                            success: function(response) {
                                if (response.success) {
                                    workshopPricing[workshopId] = {
                                        main_price: parseFloat(response.main_price) || 0,
                                        guest_price: parseFloat(response.guest_price) || 0,
                                        workshop_name: response.workshop_name || ''
                                    };

                                    // Update price display
                                    const mainPrice = workshopPricing[workshopId].main_price;
                                    $(`#workshop_price_${workshopId}`).html(currencySymbol +
                                        mainPrice.toFixed(2) + '<br><small>/person</small>');
                                }
                            },
                            error: function() {
                                $(`#workshop_price_${workshopId}`).text('Price N/A');
                            }
                        });
                    }
                });
            }

            // Handle workshop selection changes
            function handleWorkshopChange() {
                selectedWorkshops = [];

                $('.workshop-checkbox:checked').each(function() {
                    const workshopId = $(this).val();
                    const workshopName = $(this).data('name');

                    if (workshopId && workshopId !== '' && workshopPricing[workshopId]) {
                        selectedWorkshops.push({
                            id: workshopId,
                            name: workshopName,
                            main_price: workshopPricing[workshopId].main_price,
                            guest_price: workshopPricing[workshopId].guest_price
                        });
                    }
                });

                updateRegistrationSummary();

                // Reset calculation if price was already calculated
                if (isPriceCalculated) {
                    resetPriceCalculation('workshop selection');
                }
            }

            // Handle "Conference Only" checkbox logic
            $('#conference_only').change(function() {
                if ($(this).is(':checked')) {
                    // Uncheck all workshop checkboxes
                    $('.workshop-checkbox:not(#conference_only)').prop('checked', false);
                    $('.workshop-selection-card').removeClass('selected');
                    $(this).closest('.workshop-selection-card').addClass('selected');
                }
                handleWorkshopChange();
            });

            // Handle workshop checkbox changes
            $('.workshop-checkbox:not(#conference_only)').change(function() {
                if ($(this).is(':checked')) {
                    // Uncheck "Conference Only" if any workshop is selected
                    $('#conference_only').prop('checked', false);
                    $('#conference_only').closest('.workshop-selection-card').removeClass('selected');
                    $(this).closest('.workshop-selection-card').addClass('selected');
                } else {
                    $(this).closest('.workshop-selection-card').removeClass('selected');
                    // If no workshops are selected, check "Conference Only"
                    if ($('.workshop-checkbox:not(#conference_only):checked').length === 0) {
                        $('#conference_only').prop('checked', true);
                        $('#conference_only').closest('.workshop-selection-card').addClass('selected');
                    }
                }
                handleWorkshopChange();
            });

            // Handle add-on checkbox changes
            function handleAddOnChange() {
                selectedAddOns = [];
                $('.addon-checkbox:checked').each(function() {
                    const addonId = $(this).val();
                    const addonName = $(this).data('name');
                    const addonAmount = parseFloat($(this).data('amount')) || 0;
                    const addonGuestAmount = parseFloat($(this).data('guest-amount')) || 0;
                    
                    // Determine include_guest based on addon availability setting
                    let includeGuest = false;
                    if (addonAvailability === 'participant_only') {
                        includeGuest = false; // Never include guest
                    } else if (addonAvailability === 'accompany_only') {
                        includeGuest = true; // Always include guest only
                    } else {
                        // 'both' - check the checkbox
                        includeGuest = $(`#include_guest_${addonId}`).is(':checked');
                    }

                    console.log(`Addon: ${addonName}, Mode: ${addonAvailability}, Include Guest: ${includeGuest}`);

                    selectedAddOns.push({
                        id: addonId,
                        name: addonName,
                        amount: addonAmount,
                        guest_amount: addonGuestAmount,
                        include_guest: includeGuest
                    });
                });

                updateRegistrationSummary();

                // Reset calculation if price was already calculated
                if (isPriceCalculated) {
                    resetPriceCalculation('add-on selection');
                }
            }

            // Show/hide guest option when addon is checked/unchecked
            $(document).on('change', '.addon-checkbox', function() {
                const addonId = $(this).val();
                const guestOption = $(`#guest_option_${addonId}`);

                if ($(this).is(':checked')) {
                    // Always show guest option when addon is checked (if it exists)
                    if (guestOption.length > 0) {
                        guestOption.slideDown(200);
                    }
                } else {
                    guestOption.slideUp(200);
                }

                handleAddOnChange();
            });

            // Handle guest inclusion checkbox changes
            $(document).on('change', '.addon-guest-checkbox', function() {
                handleAddOnChange();
            });

            // Reset price calculation
            function resetPriceCalculation(changeType) {
                $("#priceTable").fadeOut();
                $("#paymentSection").fadeOut();
                $("#processingDiv").hide();
                isPriceCalculated = false;
                calculatedAmount = 0;
                calculatedAmountUSD = 0;
                $('input[name="paymentMode"]').prop('checked', false);
                $('.payment-method-card').removeClass('selected');

                notyf.info(`Please recalculate the price due to changes in ${changeType}.`);
                updateStepIndicator(1);
            }

            // Update registration summary
            function updateRegistrationSummary() {
                const totalAttendees = parseInt($('#total_attendee').val() || 0) + 1;

                let workshopText = @json($galaDinnerEnabled ? 'Conference + Gala Dinner' : 'Conference Only');
                let addOnText = 'None';

                // Handle multiple workshops
                if (selectedWorkshops.length > 0) {
                    workshopText = selectedWorkshops.map(w => w.name).join(', ');
                    if (workshopText.length > 50) {
                        workshopText = selectedWorkshops.length + ' workshop' + (selectedWorkshops.length > 1 ?
                            's' : '') + ' selected';
                    } 
                }

                // Handle multiple add-ons
                if (selectedAddOns.length > 0) {
                    addOnText = selectedAddOns.map(addon => addon.name).join(', ');
                    if (addOnText.length > 50) {
                        addOnText = selectedAddOns.length + ' add-on' + (selectedAddOns.length > 1 ? 's' : '') +
                            ' selected';
                    }
                }

                // Update summary display
                $('#summaryWorkshops').text(workshopText);
                $('#summaryAddOns').text(addOnText);
                $('#summaryAttendees').text(totalAttendees);

                // Show summary if any option is selected
                if (selectedWorkshops.length > 0 || selectedAddOns.length > 0 || $('#total_attendee').val()) {
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

            // Calculate total add-on price
            function calculateAddOnTotal(delegate, totalAttendee, additionalGuest) {
                let addOnTotal = 0;

                selectedAddOns.forEach(addon => {
                    // Handle different addon availability settings
                    if (addonAvailability === 'participant_only') {
                        // Only main attendee gets the addon
                        addOnTotal += addon.amount;
                    } else if (addonAvailability === 'accompany_only') {
                        // Only guests get the addon
                        if (additionalGuest > 0) {
                            const guestPrice = addon.guest_amount > 0 ? addon.guest_amount : addon.amount;
                            addOnTotal += guestPrice * additionalGuest;
                        }
                    } else {
                        // 'both' - main attendee gets the addon
                        addOnTotal += addon.amount;

                        // Guests get the guest price only if include_guest is true
                        if (additionalGuest > 0 && addon.include_guest) {
                            const guestPrice = addon.guest_amount > 0 ? addon.guest_amount : addon.amount;
                            addOnTotal += guestPrice * additionalGuest;
                        }
                    }
                });

                return addOnTotal;
            }

            // Enhanced price calculation for multiple workshops
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
                    const guestPrice = parseFloat('{{ @$memberTypePrice->guest_amount }}') || 0;
                    const additionalGuest = parseInt($("#total_attendee").val()) || 0;
                    const delegate = '{{ @$memberTypePrice->memberType->delegate }}';
                    const currencyCondition = (delegate == 1 ? 'Rs. ' : '$ ');
                    const memberType = '{{ @$memberTypePrice->memberType->type }}';
                    const totalAttendee = additionalGuest + 1;

                    // Store the conference base amount for later use
                    conferenceBaseAmount = registrationPrice;

                    if (!registrationPrice) {
                        throw new Error("Conference price has not been updated by admin.");
                    }

                    // Calculate workshop pricing
                    let totalWorkshopPrice = 0;
                    let totalWorkshopGuestPrice = 0;

                    selectedWorkshops.forEach(workshop => {
                        totalWorkshopPrice += workshop.main_price;
                        totalWorkshopGuestPrice += workshop.guest_price;
                    });

                    // Calculate add-on total price (no longer used for total, calculated per item below)
                    const addOnTotalPrice = calculateAddOnTotal(delegate, totalAttendee,
                        additionalGuest);

                    // Initialize totals
                    let preTotalPrice = 0;
                    totalPrice = 0;

                    if (delegate == 2) {
                        preTotalPrice = registrationPrice + totalWorkshopPrice;
                    } else {
                        totalPrice = registrationPrice + totalWorkshopPrice;
                    }

                    // Build price table
                    const calculatedData = $("#calculatedData");
                    calculatedData.empty();

                    let rowNumber = 1;

                    // Conference registration row
                    calculatedData.append(generatePriceTableRow(
                        rowNumber++,
                        `Conference - ${memberType}`,
                        1,
                        registrationPrice,
                        registrationPrice,
                        currencySymbol
                    ));

                    // Workshop registration rows (multiple workshops)
                    selectedWorkshops.forEach(workshop => {
                        if (workshop.main_price > 0) {
                            calculatedData.append(generatePriceTableRow(
                                rowNumber++,
                                `Workshop: ${workshop.name}`,
                                1,
                                workshop.main_price,
                                workshop.main_price,
                                currencyCondition
                            ));
                        }
                    });

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
                            `Conference - Additional Guests`,
                            additionalGuest,
                            guestPrice,
                            guestsTotalPrice,
                            currencyCondition
                        ));

                        // Workshop guests (for each workshop)
                        selectedWorkshops.forEach(workshop => {
                            if (workshop.guest_price > 0) {
                                const workshopGuestsTotalPrice = additionalGuest * workshop
                                    .guest_price;
                                if (delegate == 2) {
                                    preTotalPrice += workshopGuestsTotalPrice;
                                } else {
                                    totalPrice += workshopGuestsTotalPrice;
                                }

                                calculatedData.append(generatePriceTableRow(
                                    rowNumber++,
                                    `${workshop.name} - Additional Guests`,
                                    additionalGuest,
                                    workshop.guest_price,
                                    workshopGuestsTotalPrice,
                                    currencyCondition
                                ));
                            }
                        });
                    }

                    // Add-ons pricing (multiple add-ons)
                    if (selectedAddOns.length > 0) {
                        selectedAddOns.forEach(addon => {
                            // Handle different addon availability settings
                            if (addonAvailability === 'participant_only') {
                                // Only main attendee gets the addon
                                const mainAddonPrice = addon.amount;

                                if (delegate == 2) {
                                    preTotalPrice += mainAddonPrice;
                                } else {
                                    totalPrice += mainAddonPrice;
                                }

                                calculatedData.append(generatePriceTableRow(
                                    rowNumber++,
                                    `Add-on: ${addon.name} (Participant Only)`,
                                    1,
                                    addon.amount,
                                    mainAddonPrice,
                                    currencyCondition
                                ));
                            } else if (addonAvailability === 'accompany_only') {
                                // Only guests get the addon
                                if (additionalGuest > 0) {
                                    const guestAddonPrice = addon.guest_amount > 0 ? addon.guest_amount : addon.amount;
                                    const guestAddonTotal = guestAddonPrice * additionalGuest;

                                    if (delegate == 2) {
                                        preTotalPrice += guestAddonTotal;
                                    } else {
                                        totalPrice += guestAddonTotal;
                                    }

                                    calculatedData.append(generatePriceTableRow(
                                        rowNumber++,
                                        `Add-on: ${addon.name} (Guests Only)`,
                                        additionalGuest,
                                        guestAddonPrice,
                                        guestAddonTotal,
                                        currencyCondition
                                    ));
                                }
                            } else {
                                // 'both' - Main attendee gets the addon
                                const mainAddonPrice = addon.amount;

                                if (delegate == 2) {
                                    preTotalPrice += mainAddonPrice;
                                } else {
                                    totalPrice += mainAddonPrice;
                                }

                                calculatedData.append(generatePriceTableRow(
                                    rowNumber++,
                                    `Add-on: ${addon.name}`,
                                    1,
                                    addon.amount,
                                    mainAddonPrice,
                                    currencyCondition
                                ));

                                // Guest addon pricing if there are guests AND include_guest is true
                                if (additionalGuest > 0 && addon.include_guest) {
                                    const guestAddonPrice = addon.guest_amount > 0 ? addon
                                        .guest_amount : addon.amount;
                                    const guestAddonTotal = guestAddonPrice * additionalGuest;

                                    if (delegate == 2) {
                                        preTotalPrice += guestAddonTotal;
                                    } else {
                                        totalPrice += guestAddonTotal;
                                    }

                                    calculatedData.append(generatePriceTableRow(
                                        rowNumber++,
                                        `${addon.name} - Additional Guests`,
                                        additionalGuest,
                                        guestAddonPrice,
                                        guestAddonTotal,
                                        currencyCondition
                                    ));
                                }
                            }
                        });
                    }

                    // Service charge for international payments
                    if (delegate == 2) {
                        const additionalCharge = preTotalPrice * 0.035;
                        totalPrice = preTotalPrice + additionalCharge;
                        calculatedData.append(generatePriceTableRow(
                            rowNumber++,
                            `Service Charge (3.5%)`,
                            '',
                            '',
                            additionalCharge,
                            currencyCondition
                        ));
                    }

                    // Calculate total workshop amount for tracking
                    let workshopAmount = totalWorkshopPrice;
                    if (additionalGuest > 0) {
                        workshopAmount += (additionalGuest * totalWorkshopGuestPrice);
                    }

                    // Final total row
                    calculatedData.append(generatePriceTableRow(
                        '',
                        '<strong>TOTAL AMOUNT</strong>',
                        `<strong>${totalAttendee}</strong>`,
                        '',
                        totalPrice,
                        currencyCondition,
                        true
                    ));

                    // Store calculated values
                    calculatedAmount = parseFloat(totalPrice).toFixed(2);
                    calculatedAmountUSD = parseFloat(totalPrice).toFixed(2); // Store original USD amount
                    isPriceCalculated = true;

                    // Update all amount fields
                    $(".amount").val(parseFloat(totalPrice).toFixed(2));

                    // Update workshop amount fields
                    $("input[name='workshop_amount']").val(workshopAmount);

                    // Update summary
                    $('#summaryAmount').text(currencyCondition + totalPrice.toFixed(2));

                    // Show price table with animation
                    $("#priceTable").fadeIn(500);

                    // Update step indicator
                    updateStepIndicator(2);

                    // Show payment section
                    $("#paymentSection").fadeIn(500);

                    // Show success message
                    const workshopText = selectedWorkshops.length > 0 ?
                        ` and ${selectedWorkshops.length} workshop${selectedWorkshops.length > 1 ? 's' : ''}` :
                        '';
                    const addOnText = selectedAddOns.length > 0 ?
                        ` with ${selectedAddOns.length} add-on${selectedAddOns.length > 1 ? 's' : ''}` :
                        '';
                    notyf.success(
                        `Price calculated successfully! Conference${workshopText}${addOnText} total: ${currencyCondition}${totalPrice.toFixed(2)}`
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
            $('#total_attendee').change(function() {
                updateRegistrationSummary();

                // Reset calculation if price was already calculated
                if (isPriceCalculated) {
                    resetPriceCalculation('attendee count');
                }
            });

            // Add-on checkbox change handler
            $(document).on('change', '.addon-checkbox', handleAddOnChange);

            // Function to update all hidden fields for multiple workshops
            function updateAllHiddenFields() {
                const registrantType = $('#registrant_type').val() || 1;
                const accompanyPerson = $('#total_attendee').val() || '0';

                // Create workshops data string for form submission
                const workshopsData = selectedWorkshops.map(workshop =>
                    `${workshop.id}:${workshop.main_price}:${workshop.guest_price}`
                ).join(',');

                // Create add-ons data string with main amount, guest amount, and guest inclusion flag
                const addOnsData = selectedAddOns.map(addon =>
                    `${addon.id}:${addon.amount}:${addon.guest_amount}:${addon.include_guest ? '1' : '0'}`
                ).join(',');

                // Update all registrant type fields
                $('input[name="registrant_type"]').val(registrantType);

                // Update all accompany person fields
                $('input[name="accompany_person"]').val(accompanyPerson);

                // Update workshops data in hidden fields
                $('input[name="selected_workshops"]').val(workshopsData);

                // Update add-ons data in hidden fields
                $('input[name="selected_addons"]').val(addOnsData);

                // Update conference base amount in all forms
                $('input[name="conference_base_amount"]').val(conferenceBaseAmount);

                // Calculate total workshop amount
                let totalWorkshopAmount = 0;
                selectedWorkshops.forEach(workshop => {
                    totalWorkshopAmount += workshop.main_price;
                    if (parseInt(accompanyPerson) > 0) {
                        totalWorkshopAmount += (parseInt(accompanyPerson) * workshop.guest_price);
                    }
                });

                $('input[name="workshop_amount"]').val(totalWorkshopAmount);
            }

            // Payment method selection handler
            $('input[name="paymentMode"]').change(function() {
                if (!isPriceCalculated) {
                    notyf.error('Please calculate the price first before selecting payment method.');
                    $(this).prop('checked', false);
                    return;
                }

                const selectedValue = $(this).val();

                if (selectedValue == 'bankTransfer' || selectedValue == 'staticQr') {
                    $('.paymentHeader').show();
                } else {
                    $('.paymentHeader').hide();
                }

                const checkCountry = '{{ auth()->user()->userDetail->country->country_name }}';
                const delegate = '{{ @$memberTypePrice->memberType->delegate }}';

                // Update visual selection
                $('.payment-method-card').removeClass('selected');
                $(this).closest('.payment-method-card').addClass('selected');

                // Update step indicator
                updateStepIndicator(3);

                $("#processingDiv").fadeIn();

                // Hide all processing divs
                $(".fonePayProcessingDiv, .dollarCardProcessingDiv, .mocoProcessingDiv, .esewaProcessingDiv, .khaltiProcessingDiv, .connectipsProcessingDiv, .bankTransferProcessingDiv, .staticQrProcessingDiv, .staticQrFormProcessingDiv")
                    .hide();

                // Update all hidden fields with current values
                updateAllHiddenFields();

                // Show selected payment method processing div
                if (selectedValue === 'staticQr') {
                    $('.staticQrProcessingDiv').fadeIn();
                    $('.staticQrFormProcessingDiv').fadeIn();
                } else {
                    $(`.${selectedValue}ProcessingDiv`).fadeIn();
                }

                // Enable appropriate submit buttons
                enablePaymentButton(selectedValue, delegate, checkCountry);
            });

            // Function to enable payment buttons based on method
            function enablePaymentButton(selectedValue, delegate, checkCountry) {
                console.log('Enabling payment button for:', selectedValue, 'Delegate:', delegate, 'Country:', checkCountry);
                
                // Disable all payment buttons first
                $("#submitFonePay, #submitEsewa, #submitKhalti, #submitMoco, #submitConnectIPS, #submitButtonInternationalPayment, #submitButtonBankTransfer, #submitButtonStaticQr")
                    .prop('disabled', true);

                // Determine if we need INR conversion
                const needsINRConversion = (delegate == 2 && checkCountry == 'India' && 
                                           (selectedValue == "fonePay" || selectedValue == "staticQr"));

                if (needsINRConversion) {
                    // Convert to INR for FonePay or Static QR
                    console.log('Converting to INR...');
                    convertUsdToInr(selectedValue);
                } else {
                    // Restore USD for other payment methods when delegate is international
                    if (delegate == 2 && isConvertedToINR) {
                        // Only restore if we previously converted to INR
                        console.log('Restoring USD from INR...');
                        calculatedAmount = calculatedAmountUSD;
                        const currencyCondition = '$ ';
                        
                        // Update all amount fields with USD
                        $(".amount").val(parseFloat(calculatedAmountUSD).toFixed(2));
                        
                        // Reset currency fields to USD
                        $("#fonePayCurrency").val('USD');
                        $("#staticQrCurrency").val('USD');
                        
                        // Restore USD in price table
                        $("#calculatedData tr:last-child td:last-child").html(
                            '<strong>' + currencyCondition + parseFloat(calculatedAmountUSD).toFixed(2) + '</strong>'
                        );
                        
                        // Show notification that we've reverted to USD
                        notyf.info('Amount displayed in USD: $' + parseFloat(calculatedAmountUSD).toFixed(2));
                        
                        // Reset the conversion flag
                        isConvertedToINR = false;
                    }
                    
                    // Enable appropriate payment button
                    console.log('Enabling button for:', selectedValue);
                    if (selectedValue == "fonePay") {
                        $("#submitFonePay").prop('disabled', false);
                    } else if (selectedValue == "dollarCard") {
                        $("#submitButtonInternationalPayment").prop('disabled', false);
                        console.log('International payment button enabled');
                    } else if (selectedValue == "moco") {
                        $("#submitMoco").prop('disabled', false);
                    } else if (selectedValue == "esewa") {
                        $("#submitEsewa").prop('disabled', false);
                    } else if (selectedValue == "khalti") {
                        $("#submitKhalti").prop('disabled', false);
                    } else if (selectedValue == "connectips") {
                        $("#submitConnectIPS").prop('disabled', false);
                    } else if (selectedValue == "bankTransfer") {
                        $("#submitButtonBankTransfer").prop('disabled', false);
                    } else if (selectedValue == "staticQr") {
                        $("#submitButtonStaticQr").prop('disabled', false);
                    }
                }
            }

            // Function to convert USD to INR for FonePay and Static QR
            function convertUsdToInr(paymentMode) {
                const currencyData = {
                    'usd': calculatedAmountUSD,
                    'paymentMode': paymentMode,
                    '_token': $('meta[name="csrf-token"]').attr('content')
                };

                $.post('{{ route('convertUsdToInr') }}', currencyData)
                    .done(function(response) {
                        if (response.type == 'success') {
                            // Update calculated amount to INR for display
                            calculatedAmount = response.amount;
                            
                            if (paymentMode === 'fonePay') {
                                // Store USD amount but mark currency as INR
                                $("#fonePayAmount").val(calculatedAmountUSD);
                                $("#fonePayCurrency").val('INR');
                                $("#submitFonePay").prop('disabled', false);
                            } else if (paymentMode === 'staticQr') {
                                // Store USD amount but mark currency as INR
                                $("#staticQrAmount").val(calculatedAmountUSD);
                                $("#staticQrCurrency").val('INR');
                                $("#submitButtonStaticQr").prop('disabled', false);
                            }

                            // Update the total in price table for INR display
                            $("#calculatedData tr:last-child td:last-child").html(
                                '<strong>INR ' + parseFloat(response.amount).toFixed(2) + '</strong>'
                            );
                            
                            // Set flag to track INR conversion
                            isConvertedToINR = true;
                            
                            notyf.success('Amount converted to INR: ₹' + parseFloat(response.amount).toFixed(2));
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
                    $('#summaryRegistrantType').text('Attendee');
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
                                        '{{ route('my-society.conference.submission.index', [$society, $conference]) }}';
                                }, 1500);
                            } else {
                                $("#openModal").modal('hide');
                                setRegistrantType('2');
                                $('#summaryRegistrantType').text('Speaker');
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

            $("#submitButtonStaticQr").click(function(e) {
                e.preventDefault();
                if (!isPriceCalculated) {
                    notyf.error('Please calculate the price first.');
                    return;
                }

                // Validate required fields
                const transactionId = $('#static_qr_transaction_id').val();
                if (!transactionId) {
                    notyf.error('Please enter the transaction ID.');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
                $("#staticQrForm").submit();
            });

            $("#submitFonePay, #submitEsewa, #submitKhalti, #submitConnectIPS").click(function(e) {
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
                    selected_workshops: $("#selected_workshops_moco").val(),
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

            // File upload preview for bank transfer and static QR
            $('#payment_voucher, #static_qr_payment_voucher').change(function() {
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

            // Initialize the form
            loadWorkshopPricing();
            updateRegistrationSummary();
            updateStepIndicator(1);

            // Smooth scrolling for better UX
            $('html').css('scroll-behavior', 'smooth');
        });

        // Payment status handling
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
