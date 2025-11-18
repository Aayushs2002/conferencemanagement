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
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Clients</li>
                        </ol>
                    </nav>
                    <h1 class="banner-title">MedConAlert: Elevating Your<br>Conference Experience</h1>
                    <p class="banner-sub">
                        MedConAlert offers smart, data-driven tools that simplify conference management, improve
                        collaboration, and ensure smooth, real-time interactions for everyone involved. </p>
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
                            Clients</h2>
                    </div>
                    <div class="col-lg-7">
                        <p class="section-subtitle mb-4">
                            At MedConAlert, we empower conference organizers, sponsors, speakers, and attendees with an
                            all-in-one, technology-driven platform. Our solutions streamline event planning, enhance
                            collaboration, and deliver real-time insights — ensuring every conference runs smoothly,
                            efficiently, and with maximum impact. </p>
                        <p class="section-subtitle">
                            From simplified registration to interactive sessions and data-rich analytics, we make every
                            step seamless so you can focus on what truly matters: delivering exceptional experiences and
                            meaningful knowledge exchange. </p>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <div class="td_height_80 td_height_lg_80"></div>
    <section class="Experts-section">
        <div class="container">
            <h2 class="section-title text-center mb-5" style="line-height: 1.5;">
                Chosen by Experts,<br>
                Backed by Results
            </h2>
            <div class="row g-4  align-items-center text-center">
                @foreach ($societies as $society)
                    @php
                        $justifyClass = 'justify-content-center';
                        if ($loop->first) {
                            $justifyClass = 'justify-content-start';
                        }
                        if ($loop->last) {
                            $justifyClass = 'justify-content-end';
                        }
                    @endphp

                    <a href="{{ route('our-client.detail', $society->slug) }}"
                        class="col-6 col-sm-4 col-md-3 col-lg-2 d-flex {{ $justifyClass }} text-decoration-none">
                        <div class="Experts-card text-center w-100">
                            <img src="{{ Storage::url('society/logo/' . $society->logo) }}" alt="{{ $society->name }}"
                                class="img-fluid mb-3">
                            <h6 class="Experts-name">{{ $society->users->value('f_name') }}</h6>
                        </div>
                    </a>
                @endforeach
            </div>


        </div>
    </section>

    <div class="td_height_60 td_height_lg_60"></div>
@endsection
