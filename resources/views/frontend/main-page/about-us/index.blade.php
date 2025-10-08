@extends('frontend.main-page.layouts.main')
@section('content')
    <section class="banner d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">About Us</li>
                        </ol>
                    </nav>
                    <h1 class="banner-title">MedConAlert: Connecting<br>Medical Minds</h1>
                    <p class="banner-sub">
                        MedConAlert empowers seamless conference experiences, fostering collaboration,
                        innovation, and knowledge exchange for global medical professionals and researchers.
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
                            Overview</h2>
                    </div>
                    <div class="col-lg-7">
                        <p class="section-subtitle mb-4">
                            MedConAlert is a cutting-edge platform designed to transform medical conference management,
                            delivering seamless, technology-driven solutions for organizers, attendees, speakers, and
                            sponsors.
                            Our mission is to empower the global medical community by streamlining every aspect of
                            conference
                            planning and participation, fostering collaboration, innovation, and knowledge exchange.
                        </p>
                        <p class="section-subtitle">
                            Our platform serves diverse user groups — attendees, speakers, organizers, and exhibitors —
                            with intuitive flows like registration, abstract submission, and program management.
                            Inspired by
                            modern design principles, MedConAlert ensures accessibility through a responsive,
                            mobile-friendly
                            interface with offline schedule access and sticky navigation.
                        </p>
                    </div>

                </div>
            </div>
        </section>
    </section>
    <div class="td_height_80 td_height_lg_80"></div>
    <section class="partner-section ">
        <div class="container">
            <h2 class="section-title text-center text-white mb-5" style="font-size: 20px;">
                We are honored to work with:
            </h2>
            <div class="row g-4 text-center justify-content-center ">
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="partner-card ">
                        <img src="assets/img/image 1.png" alt="Nepal Health Research Council" class="img-fluid mb-3">
                        <h6 class="partner-name text-white ">Nepal Health Research Council</h6>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="partner-card ">
                        <img src="assets/img/NMA-removebg-preview 1.png" alt="Nepal Medical Association"
                            class="img-fluid mb-3">
                        <h6 class="partner-name text-white ">Nepal Medical Association (NMA)</h6>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="partner-card ">
                        <img src="assets/img/image 4.png" alt="Annapurna Neurological Institute" class="img-fluid mb-3">
                        <h6 class="partner-name text-white ">Annapurna Neurological Institute & Allied Sciences (ANIAS)
                        </h6>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="partner-card">
                        <img src="assets/img/nesog-logo.png" alt="NESOG" class="img-fluid mb-3">
                        <h6 class="partner-name text-white ">Nepal Society of Obstetricians and Gynaecologists (NESOG)
                        </h6>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="partner-card">
                        <img src="assets/img/image 2.png" alt="SAN" class="img-fluid mb-3">
                        <h6 class="partner-name text-white ">Society of Anaesthesiologists of Nepal (SAN)</h6>
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection
