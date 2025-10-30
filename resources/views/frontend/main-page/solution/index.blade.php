@extends('frontend.main-page.layouts.main')
@section('content')
    <section class="banner d-flex align-items-center">
        <span class="nth-circle-1"></span>
        <span class="nth-circle-2"></span>
        <span class="nth-circle-3"></span>
        <span class="nth-circle-4"></span>
        <span class="nth-circle-5"></span>
        <div class="container">

            <div class="row">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="i{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Solution</li>
                        </ol>
                    </nav>
                    <h1 class="banner-title">MedConAlert: Powering <br>Seamless Conferences</h1>
                    <p class="banner-sub">
                        MedConAlert delivers innovative, data-driven solutions to streamline medical conferences,
                        enhancing collaboration, efficiency, and real-time engagement for all users.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="main-section">
        <section class="about-section ">
            <div class="container">
                <div class="row justify-content-between">

                    <div class="col-lg-3">
                        <h2 class="section-title">MedConAlert
                            Solutions</h2>
                    </div>
                    <div class="col-lg-7">
                        <p class="section-subtitle mb-4">
                            MedConAlert delivers innovative, data-driven solutions to streamline medical conferences,
                            enhancing collaboration, efficiency, and real-time engagement for all users.
                        </p>
                        <p class="section-subtitle">
                            Our platform transforms the way medical conferences are managed, providing a comprehensive,
                            user-centric system tailored for organizers, attendees, speakers, and sponsors. By
                            leveraging cutting-edge technology, we ensure seamless event execution and impactful
                            knowledge exchange.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <div class="td_height_60 td_height_lg_60"></div>
    <section class="features-section mb-5">
        <div class="container">
            <div class="row justify-content-between mb-5">
                @foreach ($features as $feature)
                    <div class="col-lg-6 mb-5">
                        <div class="feature-card text-center p-4 rounded-4">
                            <div class="d-flex align-items-start mb-3">
                                <span class="feature-number me-3">{{ $loop->iteration }}.</span>
                                <div class="text-start">
                                    <h4 class="feature-title mb-3">{{ $feature->title }}</h4>
                                    <ul class="feature-list">
                                        {{-- <li>Payment gateway integration (Visa, MasterCard, AmEx, local methods).</li>
                                        <li>QR-based registration confirmation.</li>
                                        <li>Support for early-bird, spot registration, and group discounts.</li> --}}
                                        {!! $feature->description !!}
                                    </ul>
                                </div>
                            </div>
                            <div class="feature-images mt-3">
                                <img src="{{ Storage::url('feature/image/' . $feature->image) }}" alt="Online Registration"
                                    class="img-fluid">
                            </div>
                        </div>
                    </div>
                @endforeach


                {{-- <div class="col-lg-6 mb-5">
                    <div class="feature-card text-center p-4 rounded-4">
                        <div class="d-flex align-items-start mb-3">
                            <span class="feature-number me-3">2.</span>
                            <div class="text-start">
                                <h4 class="feature-title mb-3">Abstract Submission Management</h4>
                                <ul class="feature-list">
                                    <li>Auto-formatting with journal-style templates.</li>
                                    <li>Reviewer routing & blind peer review system.</li>
                                    <li>Dashboard for authors (submission status, revisions, acceptance).</li>
                                </ul>
                            </div>
                        </div>
                        <div class="feature-images mt-3">
                            <img src="assets/img/abstract.png" alt="Abstract Management" class="img-fluid">
                        </div>
                    </div>
                </div> --}}
            </div>

            {{-- <div class="row justify-content-between mb-5">
                <div class="col-lg-6 mb-5">
                    <div class="feature-card text-center p-4 rounded-4">
                        <div class="d-flex align-items-start mb-3">
                            <span class="feature-number me-3">3.</span>
                            <div class="text-start">
                                <h4 class="feature-title mb-3">Workshop Management</h4>
                                <ul class="feature-list">
                                    <li>Online pre/post-conference workshop registration.</li>
                                    <li>QR code-based entry/check-in system.</li>
                                    <li>MCQ Assessments + Auto-Scoring.</li>
                                    <li>Digital ID cards with RFID/QR integration.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="feature-images mt-3">
                            <img src="assets/img/attendance 1.png" alt="Workshop Management" class="img-fluid">
                        </div>
                    </div>
                </div>


                <div class="col-lg-6 mb-5">
                    <div class="feature-card text-center p-4 rounded-4">
                        <div class="d-flex align-items-start mb-3">
                            <span class="feature-number me-3">4.</span>
                            <div class="text-start">
                                <h4 class="feature-title mb-3">Scientific Session Management</h4>
                                <ul class="feature-list">
                                    <li>Dynamic agenda builder (with real-time updates).</li>
                                    <li>Speaker profiles with photos, bios, and sessions.</li>
                                    <li>Live Q&A & interactive polling.</li>
                                    <li>Live Q&A & interactive polling.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="feature-images mt-3">
                            <img src="assets/img/scientific session.png" alt="scientific Management" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-between mb-5">
                <div class="col-lg-6 mb-5">
                    <div class="feature-card text-center p-4 rounded-4">
                        <div class="d-flex align-items-start mb-3">
                            <span class="feature-number me-3">5.</span>
                            <div class="text-start">
                                <h4 class="feature-title mb-3">Analytics & Reporting</h4>
                                <ul class="feature-list">
                                    <li>Real-time dashboard (registrations, payments,
                                        attendance, feedback).</li>
                                    <li>Post-conference analytics (abstract stats,).</li>
                                    <li>engagement, revenue.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="feature-images mt-3">
                            <img src="assets/img/analytics and reporting 1.png" alt="Analytics & Reporting"
                                class="img-fluid">
                        </div>
                    </div>
                </div>


                <div class="col-lg-6 mb-5">
                    <div class="feature-card text-center p-4 rounded-4">
                        <div class="d-flex align-items-start mb-3">
                            <span class="feature-number me-3">6.</span>
                            <div class="text-start">
                                <h4 class="feature-title mb-3">Exhibition & Sponsor Management</h4>
                                <ul class="feature-list">
                                    <li>Virtual exhibitor booths.</li>
                                    <li>Sponsor banners and clickable ads on the platform.</li>
                                    <li>Lead retrieval system for exhibitors (scan attendee QR badges).</li>

                                </ul>
                            </div>
                        </div>
                        <div class="feature-images mt-3">
                            <img src="assets/img/Exibition.png" alt="Exhibition & Sponsor Management" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </section>
@endsection
