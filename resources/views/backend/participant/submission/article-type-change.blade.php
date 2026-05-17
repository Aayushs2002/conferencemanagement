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
                        <i class="icon-base ti tabler-category me-2"></i>Presentation Category Change Request
                    </h4>
                    <p class="text-muted mb-0">Review and respond to the presentation category change recommendation</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <!-- Alert Banner -->
                <div class="card-body p-4 border-bottom"
                     style="background: linear-gradient(135deg, rgba(115,103,240,0.07) 0%, rgba(115,103,240,0.02) 100%);">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 50px; height: 50px;">
                            <i class="icon-base ti tabler-bell text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-primary mb-1">Action Needed</h5>
                            <p class="text-muted mb-0">
                                The organizing committee has recommended changing the
                                <strong>Presentation Category</strong> of your submission.
                                Please review the details below and respond at your earliest convenience.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Conference Info -->
                    <div class="row g-3 mb-4">
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
                                <div class="bg-success bg-opacity-10 rounded p-2 flex-shrink-0">
                                    <i class="icon-base ti tabler-calendar-event text-success"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-1">Conference Dates</p>
                                    <p class="fw-semibold mb-0">
                                        {{ \Carbon\Carbon::parse($conference->start_date)->format('M d') }} –
                                        {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Submission Details -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">
                            <i class="icon-base ti tabler-file-text me-2"></i>Submission Details
                        </h5>

                        <div class="bg-light rounded-3 p-4">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Submission Title</label>
                                <h6 class="fw-bold text-dark mb-0">{{ $submission->title }}</h6>
                            </div>

                            @if ($submission->abstract_content)
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Abstract</label>
                                <div class="text-dark small" style="max-height: 200px; overflow-y: auto;">
                                    {!! $submission->abstract_content !!}
                                </div>
                            </div>
                            @elseif ($submission->sections)
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Abstract Sections</label>
                                @php
                                    $sections = is_array($submission->sections) ? $submission->sections : json_decode($submission->sections, true);
                                @endphp
                                <div class="text-dark small" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($sections as $section)
                                        @if (!empty($section['content']))
                                        <div class="mb-2">
                                            <strong class="text-primary">{{ $section['name'] }}:</strong>
                                            <p class="mb-0 mt-1">{!! $section['content'] !!}</p>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Keywords</label>
                                    <p class="fw-semibold mb-0 small">{{ $submission->keywords ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Theme / Sub-theme</label>
                                    <p class="fw-semibold mb-0 small">
                                        {{ $submission->submissionCategoryMajorTrack?->category_major_track_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Category Change Section -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-4">
                            <i class="icon-base ti tabler-arrows-exchange text-primary me-2"></i>
                            Recommended Category Change
                        </h5>

                        <div class="row g-4 align-items-center">
                            <!-- Current Category -->
                            <div class="col-md-5">
                                <div class="card border-0 h-100 text-center p-3"
                                     style="background: linear-gradient(135deg, rgba(255,193,7,0.08) 0%, rgba(255,193,7,0.03) 100%);
                                            border: 2px solid rgba(255,193,7,0.3) !important; border-radius: 12px;">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center
                                                justify-content-center mx-auto mb-3"
                                         style="width: 60px; height: 60px;">
                                        <i class="icon-base ti tabler-tag text-warning" style="font-size: 1.6rem;"></i>
                                    </div>
                                    <p class="text-muted small fw-semibold mb-2 text-uppercase letter-spacing-1">Current Category</p>
                                    <h5 class="fw-bold text-warning mb-0">
                                        {{ $submission->articleType?->name ?? 'N/A' }}
                                    </h5>
                                </div>
                            </div>

                            <!-- Arrow -->
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <i class="icon-base ti tabler-arrow-right text-muted" style="font-size: 1.8rem;"></i>
                                    <p class="text-muted small mt-1 mb-0">Change to</p>
                                </div>
                            </div>

                            <!-- Recommended Category -->
                            <div class="col-md-5">
                                <div class="card border-0 h-100 text-center p-3"
                                     style="background: linear-gradient(135deg, rgba(40,167,69,0.08) 0%, rgba(40,167,69,0.03) 100%);
                                            border: 2px solid rgba(40,167,69,0.3) !important; border-radius: 12px;">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center
                                                justify-content-center mx-auto mb-3"
                                         style="width: 60px; height: 60px;">
                                        <i class="icon-base ti tabler-tag-starred text-success" style="font-size: 1.6rem;"></i>
                                    </div>
                                    <p class="text-muted small fw-semibold mb-2 text-uppercase letter-spacing-1">Recommended Category</p>
                                    <h5 class="fw-bold text-success mb-0">
                                        {{ $submission->requestedArticleType?->name ?? 'N/A' }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Important Notes -->
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-4">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <i class="icon-base ti tabler-info-circle text-info fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-info mb-2">Important Information</h6>
                                <ul class="list-unstyled text-muted small mb-0">
                                    <li class="mb-1">
                                        <i class="icon-base ti tabler-circle-check text-info me-2"></i>
                                        Accepting will update your submission category in the system immediately.
                                    </li>
                                    <li class="mb-1">
                                        <i class="icon-base ti tabler-circle-check text-info me-2"></i>
                                        Declining will keep your current category unchanged.
                                    </li>
                                    <li>
                                        <i class="icon-base ti tabler-circle-check text-info me-2"></i>
                                        Please respond promptly to assist conference planning.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Presenter Info -->
                    @if ($submission->presenter)
                    <hr class="my-4">
                    <div>
                        <h6 class="fw-bold mb-3">
                            <i class="icon-base ti tabler-user-check me-2"></i>Presenter Information
                        </h6>
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 46px; height: 46px;">
                                <i class="icon-base ti tabler-user text-primary fs-5"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-1">{{ $submission->presenter->fullName($submission->presenter) }}</p>
                                <p class="text-muted small mb-0">{{ $submission->presenter->email }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Response Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="icon-base ti tabler-help text-primary me-2"></i>Your Response
                    </h5>

                    <form id="articleTypeResponseForm">
                        @csrf

                        <!-- Accept -->
                        <div class="mb-3">
                            <label class="d-block cursor-pointer" for="confirmYes">
                                <div class="card border p-3 response-card" id="cardYes"
                                     style="cursor: pointer; transition: all 0.2s ease;">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1 flex-shrink-0" type="radio"
                                               name="confirmation" value="yes" id="confirmYes" required>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold text-dark mb-1">Accept Change</h6>
                                            <p class="text-muted small mb-0">
                                                Change my category to
                                                <strong class="text-success">{{ $submission->requestedArticleType?->name ?? 'the recommended category' }}</strong>
                                            </p>
                                        </div>
                                        <i class="icon-base ti tabler-circle-check-filled text-success fs-5"></i>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Decline -->
                        <div class="mb-4">
                            <label class="d-block cursor-pointer" for="confirmNo">
                                <div class="card border p-3 response-card" id="cardNo"
                                     style="cursor: pointer; transition: all 0.2s ease;">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1 flex-shrink-0" type="radio"
                                               name="confirmation" value="no" id="confirmNo">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold text-dark mb-1">Decline Change</h6>
                                            <p class="text-muted small mb-0">
                                                Keep my current category:
                                                <strong class="text-warning">{{ $submission->articleType?->name ?? 'current category' }}</strong>
                                            </p>
                                        </div>
                                        <i class="icon-base ti tabler-circle-x-filled text-danger fs-5"></i>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-semibold"
                                    id="submitResponseBtn" disabled>
                                <i class="icon-base ti tabler-send me-2"></i>Submit Response
                            </button>
                            <a href="{{ route('my-society.conference.submission.index', [$society, $conference]) }}"
                               class="btn btn-outline-secondary btn-lg rounded-pill fw-semibold">
                                <i class="icon-base ti tabler-arrow-left me-2"></i>Back to Submissions
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- Timeline -->
                    <div>
                        <h6 class="fw-bold mb-3">Status Timeline</h6>
                        <div class="d-flex gap-3 mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center
                                        justify-content-center flex-shrink-0"
                                 style="width: 38px; height: 38px;">
                                <i class="icon-base ti tabler-mail-opened text-success"></i>
                            </div>
                            <div>
                                <p class="fw-semibold small mb-0">Request Sent</p>
                                <p class="text-muted small mb-0">Organizers sent a recommendation</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center
                                        justify-content-center flex-shrink-0"
                                 style="width: 38px; height: 38px;">
                                <i class="icon-base ti tabler-hourglass-high text-warning"></i>
                            </div>
                            <div>
                                <p class="fw-semibold small mb-0">Awaiting Your Response</p>
                                <p class="text-muted small mb-0">Please respond promptly</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var radios = document.querySelectorAll('input[name="confirmation"]');
    var submitBtn = document.getElementById('submitResponseBtn');

    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            submitBtn.disabled = false;

            // Highlight selected card
            document.getElementById('cardYes').classList.remove('border-success', 'border-danger', 'bg-success', 'bg-opacity-10', 'bg-danger');
            document.getElementById('cardNo').classList.remove('border-success', 'border-danger', 'bg-success', 'bg-opacity-10', 'bg-danger');

            if (this.value === 'yes') {
                document.getElementById('cardYes').style.borderColor = '#198754';
                document.getElementById('cardYes').style.background = 'rgba(40,167,69,0.05)';
                document.getElementById('cardNo').style.borderColor = '';
                document.getElementById('cardNo').style.background = '';
            } else {
                document.getElementById('cardNo').style.borderColor = '#dc3545';
                document.getElementById('cardNo').style.background = 'rgba(220,53,69,0.05)';
                document.getElementById('cardYes').style.borderColor = '';
                document.getElementById('cardYes').style.background = '';
            }
        });
    });

    document.getElementById('articleTypeResponseForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var selected = document.querySelector('input[name="confirmation"]:checked');
        if (!selected) {
            if (typeof notyf !== 'undefined') {
                notyf.error('Please select a response option.');
            } else {
                alert('Please select a response option.');
            }
            return;
        }

        var label = selected.value === 'yes' ? 'accept' : 'decline';
        var confirmMsg = selected.value === 'yes'
            ? 'Are you sure you want to accept the category change to "{{ addslashes($submission->requestedArticleType?->name ?? "the recommended category") }}"?'
            : 'Are you sure you want to decline this category change recommendation?';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Confirm Response',
                text: confirmMsg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, ' + label.charAt(0).toUpperCase() + label.slice(1),
                cancelButtonText: 'Cancel',
                confirmButtonColor: selected.value === 'yes' ? '#198754' : '#dc3545',
            }).then(function (result) {
                if (result.isConfirmed) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Submitting...';
                    window.location.href = '{{ route('my-society.conference.submission.convertArticleType', [$society, $conference, $submission->id]) }}' + '?confirmation=' + selected.value;
                }
            });
        } else {
            if (confirm(confirmMsg)) {
                window.location.href = '{{ route('my-society.conference.submission.convertArticleType', [$society, $conference, $submission->id]) }}' + '?confirmation=' + selected.value;
            }
        }
    });
});
</script>
@endsection
