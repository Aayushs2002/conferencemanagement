@extends('backend.layouts.conference.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('my-society.conference.submission.index', [$society, $conference]) }}" 
                   class="btn btn-icon btn-outline-secondary rounded-circle">
                    <i class="icon-base ti tabler-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="icon-base ti tabler-presentation me-2"></i>Presentation Type Change Request
                    </h4>
                    <p class="text-muted mb-0">Review and respond to the presentation type change request</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <!-- Alert Section -->
                <div class="card-body p-4 border-bottom" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 193, 7, 0.02) 100%);">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 50px; height: 50px;">
                            <i class="icon-base ti tabler-alert-circle text-warning fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-warning mb-2">Action Needed</h5>
                            <p class="text-muted mb-0">The organizers have requested to change your presentation format. Please review the details below and respond within the given timeframe.</p>
                        </div>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="card-body p-4">
                    <!-- Conference Information -->
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-info bg-opacity-10 rounded p-2 flex-shrink-0">
                                        <i class="icon-base ti tabler-building text-info"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Conference</p>
                                        <p class="fw-semibold mb-0">{{ $conference->conference_name }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-primary bg-opacity-10 rounded p-2 flex-shrink-0">
                                        <i class="icon-base ti tabler-calendar-event text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="text-muted small mb-1">Dates</p>
                                        <p class="fw-semibold mb-0">
                                            {{ \Carbon\Carbon::parse($conference->start_date)->format('M d') }} - 
                                            {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Details -->
                    <hr class="my-4">

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="icon-base ti tabler-file-text me-2"></i>Submission Details
                        </h5>
                        
                        <div class="bg-light rounded-3 p-4">
                            <div class="mb-4">
                                <label class="form-label text-muted small mb-2">Submission Title</label>
                                <h6 class="fw-bold text-dark mb-0">{{ $submission->title }}</h6>
                            </div>

                            @if ($submission->abstract_content)
                            <div class="mb-4">
                                <label class="form-label text-muted small mb-2">Abstract</label>
                                <div class="text-dark small" style="max-height: 200px; overflow-y: auto;">
                                    {!! $submission->abstract_content !!}
                                </div>
                            </div>
                            @elseif ($submission->sections)
                            <div class="mb-4">
                                <label class="form-label text-muted small mb-2">Abstract Sections</label>
                                @php
                                    $sections = is_array($submission->sections) ? $submission->sections : json_decode($submission->sections, true);
                                @endphp
                                <div class="text-dark small" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($sections as $section)
                                        @if (!empty($section['content']))
                                        <div class="mb-3">
                                            <strong class="text-primary">{{ $section['name'] }}:</strong>
                                            <p class="mb-0 mt-2">{!! $section['content'] !!}</p>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-2">Keywords</label>
                                    <p class="fw-semibold mb-0">{{ $submission->keywords ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-2">Category</label>
                                    <p class="fw-semibold mb-0">
                                        {{ $submission->submissionCategoryMajorTrack?->category_major_track_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Presentation Type Change -->
                    <hr class="my-4">

                    <div class="mb-4">
                        <h5 class="fw-bold mb-4">
                            <i class="icon-base ti tabler-arrows-exchange text-primary me-2"></i>Presentation Format Change
                        </h5>

                        <div class="row g-4">
                            <!-- Current Presentation Type -->
                            <div class="col-md-5">
                                <div class="card border-0 h-100" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(220, 53, 69, 0.02) 100%); border: 2px solid rgba(220, 53, 69, 0.2);">
                                    <div class="card-body text-center p-4">
                                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                             style="width: 70px; height: 70px;">
                                            <i class="icon-base ti tabler-presentation text-danger" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="text-muted small fw-semibold mb-2 text-uppercase">Current Format</h6>
                                        <h3 class="fw-bold text-danger mb-0">
                                            {{ $submission->presentation_type == 1 ? 'Poster' : 'Oral' }}
                                        </h3>
                                        {{-- <p class="text-muted small mt-2 mb-0">{{ $submission->presentation_type == 1 ? 'Visual presentation board format' : 'Live presentation format' }}</p> --}}
                                    </div>
                                </div>
                            </div>

                            <!-- Arrow Icon -->
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <div class="text-center w-100">
                                    <i class="icon-base ti tabler-arrow-right text-muted" style="font-size: 1.5rem;"></i>
                                    <p class="text-muted small mt-2">Change to</p>
                                </div>
                            </div>

                            <!-- Requested Presentation Type -->
                            <div class="col-md-5">
                                <div class="card border-0 h-100" style="background: linear-gradient(135deg, rgba(40, 167, 69, 0.05) 0%, rgba(40, 167, 69, 0.02) 100%); border: 2px solid rgba(40, 167, 69, 0.2);">
                                    <div class="card-body text-center p-4">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                             style="width: 70px; height: 70px;">
                                            <i class="icon-base ti tabler-checkup-list text-success" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="text-muted small fw-semibold mb-2 text-uppercase">Requested Format</h6>
                                        <h3 class="fw-bold text-success mb-0">
                                            {{ $submission->presentation_type == 1 ? 'Oral' : 'Poster' }}
                                        </h3>
                                        {{-- <p class="text-muted small mt-2 mb-0">{{ $submission->presentation_type == 1 ? 'Live presentation format' : 'Visual presentation board format' }}</p> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Guidelines -->
                    <hr class="my-4">

                    <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-4">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <i class="icon-base ti tabler-info-circle text-info fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-info mb-2">Important Information</h6>
                                <ul class="list-unstyled text-muted small mb-0">
                                    <li class="mb-2">
                                        <i class="icon-base ti tabler-circle-check text-info me-2"></i>
                                        By accepting this change, your presentation format will be updated in the system
                                    </li>
                                    <li class="mb-2">
                                        <i class="icon-base ti tabler-circle-check text-info me-2"></i>
                                        You will receive the relevant presentation guidelines after acceptance
                                    </li>
                                    <li class="mb-2">
                                        <i class="icon-base ti tabler-circle-check text-info me-2"></i>
                                        If you reject, your current format will remain unchanged
                                    </li>
                                    <li>
                                        <i class="icon-base ti tabler-circle-check text-info me-2"></i>
                                        Please respond within 24 hours to ensure timely conference planning
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Author Information -->
                    @if ($submission->presenter)
                    <hr class="my-4">

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="icon-base ti tabler-user-check me-2"></i>Presenter Information
                        </h6>
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                                 style="width: 50px; height: 50px;">
                                <i class="icon-base ti tabler-user text-primary fs-4"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-1">{{ $submission->presenter->fullName($submission->presenter) }}</p>
                                <p class="text-muted small mb-1">Email: {{ $submission->presenter->email }}</p>
                                @if ($submission->presenter->phone)
                                <p class="text-muted small mb-0">Phone: {{ $submission->presenter->phone }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Response Section -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="icon-base ti tabler-help text-primary me-2"></i>Your Response
                    </h5>

                    <!-- Response Form -->
                    <form id="responseForm">
                        @csrf
                        
                        <!-- Accept Option -->
                        <div class="mb-3">
                            <div class="form-check card border-0 p-3 cursor-pointer hover-shadow transition-all" 
                                 style="transition: all 0.3s ease;">
                                <input class="form-check-input" type="radio" name="confirmation" 
                                       value="yes" id="confirmYes" required>
                                <label class="form-check-label w-100 mb-0 cursor-pointer" for="confirmYes">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold text-dark mb-1">Accept Change</h6>
                                            <p class="text-muted small mb-0">
                                                I agree to present as {{ $submission->presentation_type == 1 ? 'Oral' : 'Poster' }}
                                            </p>
                                        </div>
                                        <div class="text-success mt-1">
                                            <i class="icon-base ti tabler-circle-check-filled fs-5"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Reject Option -->
                        <div class="mb-4">
                            <div class="form-check card border-0 p-3 cursor-pointer hover-shadow transition-all" 
                                 style="transition: all 0.3s ease;">
                                <input class="form-check-input" type="radio" name="confirmation" 
                                       value="no" id="confirmNo">
                                <label class="form-check-label w-100 mb-0 cursor-pointer" for="confirmNo">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold text-dark mb-1">Decline Change</h6>
                                            <p class="text-muted small mb-0">
                                                I prefer to keep my current format or not participate
                                            </p>
                                        </div>
                                        <div class="text-danger mt-1">
                                            <i class="icon-base ti tabler-circle-x-filled fs-5"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-semibold">
                                <i class="icon-base ti tabler-send me-2"></i>Submit Response
                            </button>
                            <a href="{{ route('my-society.conference.submission.index', [$society, $conference]) }}" 
                               class="btn btn-outline-secondary btn-lg rounded-pill fw-semibold">
                                <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Submissions
                            </a>
                        </div>
                    </form>

                    <!-- Timeline Widget -->
                    <hr class="my-4">

                    <div>
                        <h6 class="fw-bold mb-3">Timeline</h6>
                        <div class="timeline">
                            <div class="timeline-item d-flex gap-3 mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="icon-base ti tabler-mail-opened text-success"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="fw-semibold small mb-0">Request Sent</p>
                                    <p class="text-muted small mb-0">{{ now()->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="timeline-item d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="icon-base ti tabler-hourglass-high text-warning"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="fw-semibold small mb-0">Response Deadline</p>
                                    <p class="text-muted small mb-0">Within 24 hours</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for form handling -->
<script>
document.getElementById('responseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const confirmation = document.querySelector('input[name="confirmation"]:checked').value;
    
    if (!confirmation) {
        alert('Please select an option');
        return;
    }
    
    // Create a form to submit with the confirmation parameter
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("my-society.conference.submission.convertPresentationType", [$society, $conference, $submission->id]) }}';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'confirmation';
    input.value = confirmation;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
});

// Style the radio button cards on interaction
document.querySelectorAll('.form-check').forEach(checkbox => {
    const input = checkbox.querySelector('input[type="radio"]');
    
    input.addEventListener('change', function() {
        document.querySelectorAll('.form-check').forEach(c => {
            c.style.borderColor = '';
            c.style.backgroundColor = '';
        });
        
        if (this.checked) {
            if (this.value === 'yes') {
                checkbox.style.borderColor = '#28a745';
                checkbox.style.backgroundColor = 'rgba(40, 167, 69, 0.05)';
                checkbox.style.border = '2px solid #28a745';
            } else {
                checkbox.style.borderColor = '#dc3545';
                checkbox.style.backgroundColor = 'rgba(220, 53, 69, 0.05)';
                checkbox.style.border = '2px solid #dc3545';
            }
        }
    });
});
</script>

<style>
.form-check {
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-check:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
}

.form-check-input {
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
}

.timeline-item {
    position: relative;
    padding-left: 10px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 19px;
    top: 40px;
    height: 30px;
    width: 2px;
    background: #e9ecef;
}
</style>
@endsection
