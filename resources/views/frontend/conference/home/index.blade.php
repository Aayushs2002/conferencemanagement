@extends('frontend.conference.layouts.main')
@section('content')
    <section class="container">
        <div class="row g-4 text-center stats-dashboard">
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card p-4 rounded-4 shadow-sm">
                    <h2 class="stat-number" data-target="{{ $stats['speakers'] }}">0</h2>
                    <p class="stat-label mb-0">Speakers</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card p-4 rounded-4 shadow-sm">
                    <h2 class="stat-number" data-target="{{ $stats['national_participants'] }}">0</h2>
                    <p class="stat-label mb-0">National Participants</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card p-4 rounded-4 shadow-sm">
                    <h2 class="stat-number" data-target="{{ $stats['international_participants'] }}">0</h2>
                    <p class="stat-label mb-0">International Participants</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card p-4 rounded-4 shadow-sm">
                    <h2 class="stat-number" data-target="{{ $stats['total_participants'] }}">0</h2>
                    <p class="stat-label mb-0">Total Participants</p>
                </div>
            </div>
        </div>
    </section>
    <div class="td_height_40 td_height_lg_40"></div>
    <section class="container">
        <div class="mb-2">
            {{-- @dd($conference->society) --}}
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>
        <div class="row g-5">
            <aside class="col-lg-3 order-1 order-lg-2">
                <div class="sidebar-sticky" style="position:sticky; top:20px;">
                    <div class="nav flex-column nav-pills mb-4" id="safogTabs" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active d-flex align-items-center" id="tab-overview" data-bs-toggle="pill"
                            data-bs-target="#overview" type="button" role="tab">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            <span>Overview</span>
                        </button>
                        <button class="nav-link d-flex align-items-center" id="tab-abstract" data-bs-toggle="pill"
                            data-bs-target="#abstract" type="button" role="tab">
                            <i class="fa-regular fa-file-lines me-2"></i>
                            <span>Abstract Submission</span>
                        </button>
                        <button class="nav-link d-flex align-items-center" id="tab-travel" data-bs-toggle="pill"
                            data-bs-target="#travel" type="button" role="tab">
                            <i class="fa-regular fa-building me-2"></i>
                            <span>Travel & Accommodation</span>
                        </button>
                        <button class="nav-link d-flex align-items-center" id="tab-sponsors" data-bs-toggle="pill"
                            data-bs-target="#sponsors" type="button" role="tab">
                            <i class="fa-regular fa-handshake me-2"></i>
                            <span>Sponsors</span>
                        </button>
                        <button class="nav-link d-flex align-items-center" id="tab-downloads" data-bs-toggle="pill"
                            data-bs-target="#downloads" type="button" role="tab">
                            <i class="fa-solid fa-download me-2"></i>
                            <span>Downloads</span>
                        </button>
                        <button class="nav-link d-flex align-items-center" id="tab-contact" data-bs-toggle="pill"
                            data-bs-target="#contact" type="button" role="tab">
                            <i class="fa-regular fa-message me-2"></i>
                            <span>Contact Information</span>
                        </button>
                    </div>

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
                                <div class=""><img style="height: 100px !important; width: 110px !important;"
                                        src="{{ asset('frontend/assets/img/unnamed.png') }}" alt="Fone Pay" class="">
                                </div>
                            @endif
                        </div>
                        @if ($conference->society->internationalPaymentSetting)
                            <h6 class="mb-2 fw-600">For International Delegates</h6>
                            <div class="d-flex justify-content-between align-items-center mb-3 payment-logos">
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_1.png') }}"
                                        alt="Visa" class="logo-img">
                                </div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_2.png') }}"
                                        alt="Mastercard" class="logo-img"></div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_3.png') }}"
                                        alt="PayPal" class="logo-img">
                                </div>
                                <div class="logo-item"><img
                                        src="{{ asset('frontend/assets/img/international_delegate_4.png') }}"
                                        alt="Amex" class="logo-img">
                                </div>
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

            <main class="col-lg-9 order-2 order-lg-1">
                <div class="tab-content" id="safogTabsContent">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel"
                        style="background-color: #F1F4FC; padding: 40px; border-radius: 20px;">
                        <h2 class="section-title">Welcome to {{ $conference->conference_name }}</h2>
                        <p class="mt-4" style="color: black; text-align: justify;">{!! $conference->conference_description !!}</p>

                        <p class="span-text mt-5">About Conference</p>
                        <h2 class="section-title">Official Message</h2>
                        <div class="row mt-3 align-items-center">
                            @foreach ($conference->officialMessages  as $offical_message)
                                <div class="col-md-4">
                                    <div class="prof-card p-3 rounded-3 h-100 d-flex flex-column">
                                        <img src="{{ Storage::url('offical-message/image/' . $offical_message->image) }}"
                                            alt="{{ $offical_message->full_name }}" class="profile-img mb-3">
                                        <div class="w-100 d-flex align-items-center">
                                            <h6 class="card-title mb-0">{{ $offical_message->full_name }}</h6>
                                            <a href="#" class="default-btn ms-auto" data-bs-toggle="modal"
                                                data-bs-target="#officialMessageModal" {{-- data-name="{{ $offical_message->full_name }}" --}}
                                                {{-- data-designation="{{ $offical_message->designation }}" --}} data-message="{!! $offical_message->message !!}">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            </a>
                                        </div>
                                        {{-- <small class="text-muted">Organizing Chair, SAFOG 2025<br>President, NESOG</small> --}}
                                        <small class="text-muted">{{ $offical_message->designation }}</small>
                                    </div>
                                </div>
                            @endforeach
                            {{-- <div class="col-md-4">
                                <div class="prof-card p-3 rounded-3 h-100 d-flex flex-column">
                                    <img src="assets/img/Prof. Shyam Desai.png" alt="Prof. Shyam Desai"
                                        class="profile-img mb-3">
                                    <div class="w-100 d-flex align-items-center">
                                        <h6 class="card-title mb-0">Prof. Shyam Desai</h6>
                                        <a href="#" class="default-btn ms-auto" target="_blank">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </div>
                                    <small class="text-muted">Organizing Chair, SAFOG 2025<br>President, NESOG</small>
                                </div>
                            </div> --}}
                        </div>
                    </div>


                    <div class="tab-pane fade" id="abstract" role="tabpanel"
                        style="background-color: #F1F4FC; padding: 40px; border-radius: 20px;">
                        <h2 class="section-title">Abstract Submission Guidelines</h2>

                        {!! $submissionSetting?->abstract_guidelines !!}
                    </div>


                    <div class="tab-pane fade" id="travel" role="tabpanel"
                        style="background-color: #F1F4FC; padding: 40px; border-radius: 20px;">
                        <h2 class="section-title">Accommodation Partner</h2>
                        <p class="span-text mt-4">Accommodation</p>
                        {{-- <p>To receive the conference discount, please use the booking code: SAFOGCON 2025 when making your
                            reservation.</p>
                        <p>Note: Discounts will not be applied to online bookings made through international or third-party
                            apps/websites.</p> --}}

                        <div class="row g-4">
                            @foreach ($hotels as $hotel)
                                <div class="col-md-4">
                                    <div class="accom-card p-3 rounded-4 shadow-sm d-flex flex-column">
                                        <img src="{{ Storage::url('hotel/cover-image/' . $hotel->cover_image) }}"
                                            alt="{{ $hotel->name }}" class="accom-img mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="card-title">{{ $hotel->name }}</h6>
                                                <small class="text-muted">{{ $hotel->address }}</small>
                                            </div>
                                            <a href="AccomadationDetail.html" class="default-btn">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="tab-pane fade" id="sponsors" role="tabpanel"
                        style="background-color: #F1F4FC; padding: 40px; border-radius: 20px;">
                        <h2 class="section-title">Sponsors</h2>
                        <p class="span-text mt-4">Our Financial Partners</p>
                        <p>Experience premium comfort during your stay at our partner hotel, specially selected for <span style="text-transform: uppercase;">
                            {{ $conference->society->abbreviation }}
                        </span> 
                            conference
                            participants.</p>

                        @foreach ($sponsorCategories as $sponsorCategory)
                            <h4 class="sponsor-type mb-3 mt-5">{{ $sponsorCategory->category_name }}</h4>
                            @foreach ($sponsorCategory->sponsors as $sponsor)
                                <div class="row g-4 mb-2">
                                    <div class="col-lg-4 col-md-6">
                                        <div class="sponsor-card p-3 text-center rounded-4 shadow-sm">
                                            <div class="sponsor-logo mb-2">
                                                <img src="{{ Storage::url('sponsor/logo/' . $sponsor->logo) }}"
                                                    alt="Yetichem" class="logo-img">
                                            </div>
                                            <h6 class="sponsor-name mb-0">{{ $sponsor->name }}</h6>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>

                    <div class="tab-pane fade" id="downloads" role="tabpanel"
                        style="background-color: #F1F4FC; padding: 40px; border-radius: 20px;">
                        <h2 class="section-title">Downloads</h2>
                        <p class="span-text mt-4">Conference Materials</p>
                        <div class="download-section mt-5">
                            @foreach ($downloads as $download)
                                @php
                                    // Extract file extension
                                    $extension = strtolower(pathinfo($download->file, PATHINFO_EXTENSION));

                                    // Determine icon and file type label
                                    switch ($extension) {
                                        case 'pdf':
                                            $icon = 'fa-file-pdf';
                                            $type = 'PDF';
                                            break;
                                        case 'doc':
                                        case 'docx':
                                            $icon = 'fa-file-word';
                                            $type = 'Word Document';
                                            break;
                                        case 'jpg':
                                        case 'jpeg':
                                        case 'png':
                                            $icon = 'fa-file-image';
                                            $type = 'Image';
                                            break;
                                        default:
                                            $icon = 'fa-file';
                                            $type = strtoupper($extension);
                                            break;
                                    }
                                @endphp

                                <div class="download-item mb-2">
                                    <span class="download-title">{{ $download->title ?? 'Conference File' }}
                                        ({{ $type }})
                                    </span>
                                    <a href="{{ Storage::url('download/file/' . $download->file) }}" download
                                        class="download-btn">
                                        <i class="fa-solid {{ $icon }}"></i> Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>


                    <div class="tab-pane fade" id="contact" role="tabpanel"
                        style="background-color: #F1F4FC; padding: 40px; border-radius: 20px;">
                        <h2 class="section-title">Contact Information</h2>
                        <p class="span-text mt-4">Contact Our Event Manager</p>
                        <div class="contact-section">
                            <p class="contact-manager mb-3 mt-5">
                                {{ $conference->ConferenceOrganizer->organizer_contact_person }}</p>
                            <p class="contact-info mb-2">
                                <i class="fa-solid fa-phone me-2"></i>
                                <strong>Phone:</strong> {{ $conference->ConferenceOrganizer->organizer_phone_number }}
                                <span class="text-muted">(Available on WhatsApp and Viber)</span>
                            </p>
                            <p class="contact-info mb-0">
                                <i class="fa-solid fa-envelope me-2"></i>
                                <strong>Email:</strong> <a
                                    href="mailto:{{ $conference->ConferenceOrganizer->organizer_email }}">{{ $conference->ConferenceOrganizer->organizer_email }}</a>
                            </p>
                        </div>
                    </div>
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
                                    alt="Mastercard" class="logo-img">
                            </div>
                            <div class="logo-item"><img
                                    src="{{ asset('frontend/assets/img/international_delegate_3.png') }}" alt="PayPal"
                                    class="logo-img">
                            </div>
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


                <section class="mt-5">
                    <h3 class="section-title">Registration Fee Structure</h3>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Description</th>
                                    <th scope="col">Early Bird (till
                                        {{ \Carbon\Carbon::parse($conference->early_bird_registration_deadline)->format('M j') }})
                                    </th>
                                    <th scope="col">Regular (till
                                        {{ \Carbon\Carbon::parse($conference->regular_registration_deadline)->format('M j') }})
                                    </th>
                                    <th scope="col">Spot Registration</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @dd($memberTypes) --}}
                                @foreach ($memberTypes as $memberType)
                                    @if ($memberType->early_bird_amount || $memberType->regular_amount || $memberType->on_site_amount)
                                        <tr>
                                            <td>{{ $memberType->type }}
                                                {{ $memberType->delegate == 1 ? '(Nepal)' : '(International)' }}</td>
                                            <td>{{ $memberType->early_bird_amount ?? 'N/A' }}
                                                {{ $memberType->delegate == 1 ? '(NRs)' : '(USD)' }}</td>
                                            <td>{{ $memberType->regular_amount ?? 'N/A' }}
                                                {{ $memberType->delegate == 1 ? '(NRs)' : '(USD)' }}</td>
                                            <td>{{ $memberType->on_site_amount ?? 'N/A' }}
                                                {{ $memberType->delegate == 1 ? '(NRs)' : '(USD)' }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                {{-- <tr>
                                    <td>International Participants</td>
                                    <td>150 USD</td>
                                    <td>170 USD</td>
                                    <td>190 USD</td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>
                    <p class="span-text mt-3">Payment Instructions</p>
                    @if ($conference->conferenceSetting?->payment_instruction)
                        {!! $conference->conferenceSetting->payment_instruction !!}
                    @else
                        <ul class="payment-list">
                            <li>Local payments in NPR through Nepali payment gateways.</li>
                            <li>International payment in USD through International gateways.</li>
                            <li>Early registration recommended for all participants till:
                                {{ \Carbon\Carbon::parse($conference->early_bird_registration_deadline)->format('F j, Y') }}.
                            </li>
                            <li>Payments will be accessible as soon as registration starts.</li>
                        </ul>
                    @endif

                    <h3 class="section-title mb-4 mt-5">Frequently Asked Questions</h3>
                    <div class="accordion" id="faqAccordion">
                        @foreach ($faqs as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faqHeading{{ $index }}">
                                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-controls="faqCollapse{{ $index }}">
                                        {{ $faq->question }}
                                    </button>
                                </h2>
                                <div id="faqCollapse{{ $index }}"
                                    class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                    aria-labelledby="faqHeading{{ $index }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </section>
            </main>
        </div>
    </section>
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fa-solid fa-bars me-2"></i> Conference Menu
    </button>

    <div class="mobile-sidebar" id="mobileSidebar">
        <button class="close-sidebar" id="closeSidebar">
            <i class="fa-solid fa-times"></i>
        </button>

        <div class="nav flex-column nav-pills mb-4" id="mobileSafogTabs" role="tablist" aria-orientation="vertical">
            <button class="nav-link active d-flex align-items-center mobile-tab-link" data-bs-target="#overview"
                type="button">
                <i class="fa-solid fa-circle-info me-2"></i>
                <span>Overview</span>
            </button>
            <button class="nav-link d-flex align-items-center mobile-tab-link" data-bs-target="#abstract" type="button">
                <i class="fa-regular fa-file-lines me-2"></i>
                <span>Abstract Submission</span>
            </button>
            <button class="nav-link d-flex align-items-center mobile-tab-link" data-bs-target="#travel" type="button">
                <i class="fa-regular fa-building me-2"></i>
                <span>Travel & Accommodation</span>
            </button>
            <button class="nav-link d-flex align-items-center mobile-tab-link" data-bs-target="#sponsors" type="button">
                <i class="fa-regular fa-handshake me-2"></i>
                <span>Sponsors</span>
            </button>
            <button class="nav-link d-flex align-items-center mobile-tab-link" data-bs-target="#downloads"
                type="button">
                <i class="fa-solid fa-download me-2"></i>
                <span>Downloads</span>
            </button>
            <button class="nav-link d-flex align-items-center mobile-tab-link" data-bs-target="#contact" type="button">
                <i class="fa-regular fa-message me-2"></i>
                <span>Contact Information</span>
            </button>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="td_height_60 td_height_lg_60"></div>

    <!-- Official Message Modal -->
    <div class="modal fade" id="officialMessageModal" tabindex="-1" aria-labelledby="officialMessageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="officialMessageModalLabel">Official Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- <div class="text-center mb-3">
                        <img id="modalImage" src="" alt="" class="img-fluid rounded-circle"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    </div> --}}
                    {{-- <h5 id="modalName" class="text-center mb-2"></h5> --}}
                    {{-- <p id="modalDesignation" class="text-center text-muted mb-4"></p> --}}
                    <div id="modalMessage" class="text-justify"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Official Message Modal Handler
        document.addEventListener('DOMContentLoaded', function() {
            const officialMessageModal = document.getElementById('officialMessageModal');
            if (officialMessageModal) {
                officialMessageModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    // const name = button.getAttribute('data-name');
                    // const designation = button.getAttribute('data-designation');
                    const message = button.getAttribute('data-message');
                    console.log(message, 'sas');
                    // const image = button.getAttribute('data-image');

                    // Update modal content
                    // document.getElementById('modalName').textContent = name;
                    // document.getElementById('modalDesignation').textContent = designation;
                    document.getElementById('modalMessage').innerHTML = message;
                    // document.getElementById('modalImage').src = image;
                    // document.getElementById('modalImage').alt = name;
                });
            }
        });
    </script>
@endsection
