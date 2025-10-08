@extends('frontend.main-page.layouts.main')
@section('content')
    <section class="banner d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Clients</li>
                        </ol>
                    </nav>
                    <h1 class="banner-title">MedConAlert: Connect<br> with Us</h1>
                    <p class="banner-sub">
                        MedConAlert empowers seamless conference experiences, fostering collaboration, innovation, and
                        knowledge exchange for global medical professionals and researchers. </p>
                </div>
            </div>
        </div>
    </section>
    <section class="main-section">
        <div class="contact-container container">
            <h2 class="form-title">Plan Your Event with MedConAlert</h2>
            <p class="section-subtitle text-center">
                Share your event details and let us help you manage registrations, programs, and participants
                seamlessly.
            </p>

            <div class="divider">
                <span class="dot">●</span><span class="line"></span><span class="dot">●</span>
            </div>
            <div class="first-step">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" class="form-control" id="fullName" placeholder="Full Name">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Email">
                </div>
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="text" class="form-control" id="contact" placeholder="Contact Number">
                </div>
                <div class="text-center">
                    <button class="btn btn-next" id="nextBtn">Next</button>
                </div>
            </div>


            <div class="second-step">
                <div class="form-group">
                    <label for="conferenceType">Type of Conference (Medical, Scientific, Workshop, Hybrid,
                        etc.)</label>
                    <input type="text" class="form-control" id="conferenceType" placeholder="Type of Conference">
                </div>
                <div class="date-group row">
                    <div class="form-group col-12 col-md-6">
                        <label for="startDate">Start Date</label>
                        <input type="date" class="form-control" id="startDate">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="endDate">End Date</label>
                        <input type="date" class="form-control" id="endDate">
                    </div>
                </div>
                <div class="form-group">
                    <label for="nationalParticipants">Number of National Participants</label>
                    <input type="number" class="form-control" id="nationalParticipants">
                </div>
                <div class="form-group">
                    <label for="internationalParticipants">Number of International Participants</label>
                    <input type="number" class="form-control" id="internationalParticipants">
                </div>
                <div class="form-group">
                    <label for="query">Any Query?</label>
                    <textarea class="form-control" rows="4" id="query"></textarea>
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-submit">Submit</button>
                </div>
            </div>
        </div>
    </section>
    <script>
        const nextBtn = document.getElementById("nextBtn");
        const firstStep = document.querySelector(".first-step");
        const secondStep = document.querySelector(".second-step");

        nextBtn.addEventListener("click", () => {
            firstStep.style.display = "none";

            secondStep.style.display = "block";
        });
    </script>
@endsection
