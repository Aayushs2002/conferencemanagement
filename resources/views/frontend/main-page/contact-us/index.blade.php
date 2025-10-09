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

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="divider">
                <span class="dot">●</span><span class="line"></span><span class="dot">●</span>
            </div>

            <form action="{{ route('contact-us.store') }}" method="POST" id="contactForm">
                @csrf
                <div class="first-step">
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                               id="fullName" name="full_name" 
                               placeholder="Full Name" 
                               value="{{ old('full_name') }}">
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" 
                               placeholder="Email" 
                               value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact Number</label>
                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                               id="contact" name="contact_number" 
                               placeholder="Contact Number" 
                               value="{{ old('contact_number') }}">
                        @error('contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-next" id="nextBtn">Next</button>
                    </div>
                </div>


                <div class="second-step">
                    <div class="form-group">
                        <label for="conferenceType">Type of Conference (Medical, Scientific, Workshop, Hybrid,
                            etc.)</label>
                        <input type="text" class="form-control @error('conference_type') is-invalid @enderror" 
                               id="conferenceType" name="conference_type" 
                               placeholder="Type of Conference" 
                               value="{{ old('conference_type') }}">
                        @error('conference_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="date-group row">
                        <div class="form-group col-12 col-md-6">
                            <label for="startDate">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="startDate" name="start_date" 
                                   value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="endDate">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="endDate" name="end_date" 
                                   value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nationalParticipants">Number of National Participants</label>
                        <input type="number" class="form-control @error('no_of_national_participant') is-invalid @enderror" 
                               id="nationalParticipants" name="no_of_national_participant" 
                               value="{{ old('no_of_national_participant') }}">
                        @error('no_of_national_participant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="internationalParticipants">Number of International Participants</label>
                        <input type="number" class="form-control @error('no_of_international_participant') is-invalid @enderror" 
                               id="internationalParticipants" name="no_of_international_participant" 
                               value="{{ old('no_of_international_participant') }}">
                        @error('no_of_international_participant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="query">Any Query?</label>
                        <textarea class="form-control @error('query') is-invalid @enderror" 
                                  rows="4" id="query" name="query">{{ old('query') }}</textarea>
                        @error('query')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-secondary me-2" id="backBtn">Back</button>
                        <button type="submit" class="btn btn-submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <script>
        const nextBtn = document.getElementById("nextBtn");
        const backBtn = document.getElementById("backBtn");
        const firstStep = document.querySelector(".first-step");
        const secondStep = document.querySelector(".second-step");
        const contactContainer = document.querySelector(".contact-container");

        // Function to show error message
        function showError(message) {
            // Remove any existing error alerts
            const existingAlert = document.querySelector('.validation-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show validation-alert';
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            // Insert alert at the top of the form
            const divider = document.querySelector('.divider');
            divider.parentNode.insertBefore(alertDiv, divider);

            // Scroll to the alert
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (alertDiv && alertDiv.parentNode) {
                    const bsAlert = new bootstrap.Alert(alertDiv);
                    bsAlert.close();
                }
            }, 5000);
        }

        // Check if there are errors in second step fields
        const hasSecondStepErrors = document.querySelectorAll('.second-step .is-invalid').length > 0;

        // If validation errors exist in second step, show second step by default
        if (hasSecondStepErrors || "{{ old('conference_type') }}" !== "") {
            firstStep.style.display = "none";
            secondStep.style.display = "block";
        }

        nextBtn.addEventListener("click", () => {
            // Basic validation for first step
            const fullName = document.getElementById('fullName').value.trim();
            const email = document.getElementById('email').value.trim();
            const contact = document.getElementById('contact').value.trim();

            if (!fullName) {
                showError('Please enter your full name to continue.');
                document.getElementById('fullName').focus();
                return;
            }

            if (!email) {
                showError('Please enter your email address to continue.');
                document.getElementById('email').focus();
                return;
            }

            // Basic email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                showError('Please enter a valid email address.');
                document.getElementById('email').focus();
                return;
            }

            if (!contact) {
                showError('Please enter your contact number to continue.');
                document.getElementById('contact').focus();
                return;
            }

            // Remove any validation alerts when moving to next step
            const existingAlert = document.querySelector('.validation-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            firstStep.style.display = "none";
            secondStep.style.display = "block";

            // Scroll to top of second step
            secondStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        backBtn.addEventListener("click", () => {
            // Remove any validation alerts
            const existingAlert = document.querySelector('.validation-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            secondStep.style.display = "none";
            firstStep.style.display = "block";

            // Scroll to top of first step
            firstStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        // Auto-hide server-side alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert:not(.validation-alert)');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endsection