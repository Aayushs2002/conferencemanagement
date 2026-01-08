@extends('frontend.conference.layouts.main')
@section('title')
    {{ $conference->society->sub_domain_name }} | {{ $workshop->workshop_title }}
@endsection
@section('content')
    <div class="container mt-5">

        <div class="mb-2">
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-9">
                <div class="main-workshop" style="background-color: #F1F4FC; border-radius: 20px; padding: 40px;">
                    @php
                        use Carbon\Carbon;

                        $start = Carbon::parse($workshop->start_date);
                        $end = Carbon::parse($workshop->end_date);

                        if ($start->format('F Y') === $end->format('F Y')) {
                            $formattedDate = $start->format('jS') . ' – ' . $end->format('jS F, Y');
                        } else {
                            $formattedDate = $start->format('jS F, Y') . ' – ' . $end->format('jS F, Y');
                        }
                    @endphp
                    <h2 class="section-title">{{ $workshop->workshop_title }}</h2>
                    <div class="event-info mt-3">
                        <div class="info-item d-flex align-items-center">
                            <i class="fa-regular fa-calendar me-2"></i>
                            <span>Date: {{ $formattedDate }}</span>
                        </div>
                        <div class="info-item d-flex align-items-center">
                            <i class="fa-solid fa-location-dot me-2"></i>
                            <span>Venue:
                                {{ $workshop->WorkshopVenueDetail->venue_name . ', ' . $workshop->WorkshopVenueDetail->venue_address }}</span>
                        </div>
                    </div>
                    <p class="subtitle mt-4">Aims & Scope of Workshop</p>
                    {!! $workshop->workshop_description !!}
                    {{-- <p class="span-text mt-4">Overview</p>
                    <p>This one-day course on Fetal Medicine will combine evidence-based didactic lectures in the
                        morning with interactive case discussions and simulation-based training in the afternoon. The
                        workshop aims to enhance knowledge on screening, diagnosis, and management of common fetal
                        conditions while emphasizing a multidisciplinary approach between obstetricians, radiologists,
                        neonatologists, and anesthesiologists. Practical sessions will focus on ultrasound techniques,
                        fetal surveillance, and management of complicated scenarios.</p>
                    <p>This workshop builds on successful fetal medicine updates conducted nationally and will be
                        tailored to current clinical needs with modifications based on participant feedback.</p>
                    <p>The Core Faculty will include specialists from Paropakar Maternity and Women’s Hospital,
                        Tribhuvan University Teaching Hospital, Kathmandu Medical College, and Bir Hospital.</p>
                    <p class="mb-3"><span style="color: black; font-weight: bold;"> Designer & Coordinator:</span>
                        Dr.
                        Menuka Shrestha, Department of Fetal Medicine, Paropakar Maternity and Women’s Hospital.</p>

                    <p class="span-text mt-5">Overview</p>
                    <ul class="default-disc mt-4">
                        <li>Understand the principles of prenatal screening and diagnosis.</li>
                        <li>Identify structural and chromosomal anomalies through fetal imaging.</li>
                        <li>Review current recommendations for managing high-risk pregnancies (IUGR, twin pregnancies,
                            fetal growth restriction).</li>
                        <li>Discuss intrauterine interventions and perinatal management strategies.</li>
                        <li>
                            Enhance skills in interpreting obstetric ultrasounds and fetal Doppler studies.
                        </li>
                        <li>Improve multidisciplinary communication and decision-making for complex fetal conditions.
                        </li>
                        <li>Practice team-based approaches to counseling parents regarding fetal anomalies.</li>
                    </ul>
                    <p class="span-text mt-5">Target Participants</p>
                    <p><span style="font-weight: 600;">20–24</span> participants, including:</p>
                    <ul class="default-disc">
                        <li>OB-GYN residents</li>
                        <li>Radiology residents</li>
                        <li>Junior faculty in Obstetrics & Gynecology</li>
                        <li>Neonatology fellows</li>
                    </ul>
                    <p class="span-text mt-5">Faculties</p>
                    <p><span style="font-weight: 600;">8–10 </span> national and international experts in fetal
                        medicine, maternal-fetal medicine, obstetric imaging, and neonatology.</p>
                    <p class="subtitle mt-4">Program Schedule</p>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Time</th>
                                    <th scope="col">Topic</th>
                                    <th scope="col">Speaker</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>08:30–09:00 AM</td>
                                    <td>Registration and Breakfast</td>
                                    <td>13,000 NRs</td>
                                </tr>
                                <tr>
                                    <td>09:00–09:20 AM</td>
                                    <td>Introduction and Opening Ceremony</td>
                                    <td>Dr. Suman Basnet</td>
                                </tr>
                                <tr>
                                    <td>09:20–09:40 AM</td>
                                    <td>Pre-test</td>
                                    <td>170 USD</td>
                                </tr>
                                <tr>
                                    <td>09:40–10:10 AM</td>
                                    <td>Basics of Prenatal Screening & Diagnosis</td>
                                    <td>Dr. Anupama Shrestha</td>
                                </tr>
                            </tbody>
                        </table>
                    </div> --}}

                </div>
                <p class="span-text mt-5">Instructors</p>
                <div class="row g-4 ">
                    @foreach ($workshop->registrations as $registration)
                        <div class="col-md-4">
                            <div class="prof-card p-3 d-flex flex-column h-100 ">
                                <img src="{{ Storage::url('profile/image/' . $registration->user->userDetail->image) }}"
                                    alt="{{ $registration->user->fullName($registration->user) }}" class="profile-img mb-3">
                                <h6 class="card-title mb-1">{{ $registration->user->userDetail->namePrefix->prefix }}
                                    {{ $registration->user->fullName($registration->user) }}
                                </h6>
                                <small
                                    class="card-subtitle">{{ $registration->user->userDetail->designation?->designation }}</small>
                            </div>
                        </div>
                    @endforeach
                    {{-- <div class="col-md-4">
                        <div class="prof-card p-3 d-flex flex-column h-100 ">
                            <img src="assets/img/user.jpg" alt="Prof. Narendra Malhotra" class="profile-img mb-3">
                            <h6 class="card-title mb-1">Prof. Narendra Malhotra</h6>
                            <small class="card-subtitle">Organizing Chair, SAFOG 2025
                                President, NESOG</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="prof-card p-3 d-flex flex-column h-100 ">
                            <img src="assets/img/user.jpg" alt="Prof. Rubina Sohail" class="profile-img mb-3">
                            <h6 class="card-title mb-1">Prof. Rubina Sohail</h6>
                            <small class="card-subtitle">Organizing Chair, SAFOG 2025
                                President, NESOG</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="prof-card p-3 d-flex flex-column h-100 ">
                            <img src="assets/img/user.jpg" alt="Prof. Rubina Sohail" class="profile-img mb-3">
                            <h6 class="card-title mb-1">Prof. Firoza Begum</h6>
                            <small class="card-subtitle">Organizing Chair, SAFOG 2025
                                President, NESOG</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="prof-card p-3 d-flex flex-column h-100 ">
                            <img src="assets/img/user.jpg" alt="Prof. Rubina Sohail" class="profile-img mb-3">
                            <h6 class="card-title mb-1">Prof. UDP Ratnasiri</h6>
                            <small class="card-subtitle">Organizing Chair, SAFOG 2025
                                President, NESOG</small>
                        </div>
                    </div> --}}

                </div>
            </div>
            <div class="col-lg-3">
                <div class="sticky-top" style="top: 100px;">
                    @foreach ($relevantWorkshops as $workshop)
                        <div class="other-workshop mb-4 {{ !$workshop->image ? 'logo-fallback-sidebar' : '' }}"
                            style="background-color: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <img src="{{ $workshop->image
                                ? Storage::url('workshop/workshop/image/' . $workshop->image)
                                : Storage::url('society/logo/' . $conference->society->logo) }}"
                                class="img-fluid {{ !$workshop->image ? 'logo-img-sidebar' : '' }}" alt="{{ $workshop->title }}">
                            <div class="p-2">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    <small>
                                        {{ \Carbon\Carbon::parse($workshop->start_time)->format('g:i A') }} -
                                        {{ \Carbon\Carbon::parse($workshop->end_time)->format('g:i A') }}
                                    </small>
                                </div>
                                <p class="mb-0 fw-bold">{{ $workshop->workshop_title }}</p>
                            </div>
                        </div>
                    @endforeach


                    {{-- <div class="other-workshop mb-4"
                        style="background-color: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <img src="assets/img/infertility.jpg" class="img-fluid" alt="Infertility">
                        <div class="p-2">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fa-regular fa-calendar me-1"></i>
                                <small>10:00 AM - 12:00 PM</small>
                            </div>
                            <p class="mb-0 fw-bold">Infertility</p>
                        </div>
                    </div> --}}
                </div>
            </div>

        </div>
    </div>

    <div class="td_height_80 td_height_lg_80"></div>

    <style>
        .logo-fallback-sidebar {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa !important;
            padding: 15px;
        }

        .logo-img-sidebar {
            object-fit: contain !important;
            max-height: 150px;
            width: auto !important;
            height: auto !important;
        }
    </style>
@endsection
