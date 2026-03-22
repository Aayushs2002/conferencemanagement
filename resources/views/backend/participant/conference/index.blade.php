@extends('backend.layouts.society.main')
@section('title')
    Conference
@endsection
@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="ti tabler-calendar-event text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h1 class="display-5 fw-bold text-dark mb-2">Conferences</h1>
                    <p class="text-muted mb-0">Discover and manage your conferences</p>
                </div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm settings-card">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-primary bg-opacity-10 rounded p-2 mt-1">
                                    <i class="ti-tablersettings text-primary" style="font-size: 1.4rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold">Account Settings</h5>
                                    <p class="text-muted mb-2">Manage profile details and student/resident verification documents in one place.</p>
                                    @if($userSociety && $userSociety->documents_uploaded_at)
                                        <small class="text-muted d-inline-flex align-items-center">
                                            <i class="ti-tablerclock me-1"></i>
                                            Documents last updated: {{ \Carbon\Carbon::parse($userSociety->documents_uploaded_at)->format('M d, Y') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary rounded-pill px-4"
                                data-bs-toggle="modal" data-bs-target="#accountSettingsModal">
                                <i class="ti-tableradjustments-horizontal me-1"></i> Open Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Modal -->
        <div class="modal fade" id="accountSettingsModal" tabindex="-1" aria-labelledby="accountSettingsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="accountSettingsModalLabel">Conference Account Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2">
                        <ul class="nav nav-pills settings-nav mb-4" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="documents-tab" data-bs-toggle="pill"
                                    data-bs-target="#documents-pane" type="button" role="tab"
                                    aria-controls="documents-pane" aria-selected="true">
                                    <i class="ti-tablerfile-certificate me-1"></i> Verification Documents
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#profile-pane" type="button" role="tab"
                                    aria-controls="profile-pane" aria-selected="false">
                                    <i class="ti-tableruser-edit me-1"></i> Profile
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="documents-pane" role="tabpanel" aria-labelledby="documents-tab" tabindex="0">
                                @if($userSociety && $userSociety->memberType && $userSociety->memberType->requires_student_verification == 1)
                                    <div class="alert alert-info d-flex align-items-start" role="alert">
                                        <i class="ti-tablerinfo-circle me-2 mt-1"></i>
                                        <div>
                                            <strong>Verification is required</strong>
                                            <div class="small">Please upload your ID card and/or approval letter for {{ $society->users->where('type', 2)->value('f_name') }} membership.</div>
                                        </div>
                                    </div>

                                    <form action="{{ route('mySociety.updateDocuments', $society->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">
                                                    <i class="ti-tablerid-badge me-1 text-primary"></i> ID Card
                                                </label>

                                                @if($userSociety->id_card_document)
                                                    <div class="mb-2">
                                                        <a href="{{ asset('storage/society/student-verification/' . $userSociety->id_card_document) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="ti-tablereye me-1"></i> View Uploaded ID Card
                                                        </a>
                                                    </div>
                                                @endif

                                                <input type="file" class="form-control @error('id_card_document') is-invalid @enderror"
                                                    name="id_card_document" accept=".jpg,.jpeg,.png,.pdf">
                                                @error('id_card_document')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">JPG, PNG, or PDF - Max 5MB</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">
                                                    <i class="ti-tablerfile-certificate me-1 text-primary"></i> Approval Letter (HoD/Principal)
                                                </label>

                                                @if($userSociety->official_letter_document)
                                                    <div class="mb-2">
                                                        <a href="{{ asset('storage/society/student-verification/' . $userSociety->official_letter_document) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="ti-tablereye me-1"></i> View Uploaded Letter
                                                        </a>
                                                    </div>
                                                @endif

                                                <input type="file" class="form-control @error('official_letter_document') is-invalid @enderror"
                                                    name="official_letter_document" accept=".jpg,.jpeg,.png,.pdf">
                                                @error('official_letter_document')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">JPG, PNG, or PDF - Max 5MB</small>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                                <i class="ti-tablerupload me-1"></i> Save Documents
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-center py-4">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 64px; height: 64px;">
                                            <i class="ti-tablercircle-check text-success" style="font-size: 1.8rem;"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">Verification documents are not required</h6>
                                        <p class="text-muted mb-0">Your current member type does not require ID card or approval letter upload.</p>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="profile-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                <div class="card border-0 bg-light-subtle">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-2">Update Your Profile</h6>
                                        <p class="text-muted mb-3">Need to change your institution, designation, department, address, photo, or council number? Open profile settings and update your details.</p>
                                        <a href="{{ route('security.index.society',$society) }}" class="btn btn-outline-primary rounded-pill px-4">
                                            <i class="ti-tableruser-edit me-1"></i> Open Profile Settings
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conference Tabs -->
        <div class="row mb-4"> 
            <div class="col-12">
                <ul class="nav nav-pills justify-content-center gap-2" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4 py-2 rounded-3 d-flex align-items-center gap-2" id="active-tab"
                            data-bs-toggle="tab" data-bs-target="#active-conferences" type="button" role="tab"
                            aria-controls="active-conferences" aria-selected="true">
                            <i class="ti tabler-calendar-check"></i>
                            <span>Active</span>
                            <span
                                class="badge bg-primary bg-opacity-25 text-primary rounded-pill">{{ $activeConferences->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 py-2 rounded-3 d-flex align-items-center gap-2" id="archived-tab"
                            data-bs-toggle="tab" data-bs-target="#archived-conferences" type="button" role="tab"
                            aria-controls="archived-conferences" aria-selected="false">
                            <i class="ti tabler-archive"></i>
                            <span>Archived</span>
                            <span
                                class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill">{{ $archivedConferences->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Active Conferences Tab -->
            <div class="tab-pane fade show active" id="active-conferences" role="tabpanel" aria-labelledby="active-tab">
                @if ($activeConferences->isEmpty())
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-5">
                                    <div class="mb-3">
                                        <i class="ti tabler-calendar-x text-muted"
                                            style="font-size: 4rem; opacity: 0.3;"></i>
                                    </div>
                                    <h4 class="text-muted mb-2">No Active Conferences</h4>
                                    <p class="text-muted mb-0">There are currently no active conferences available.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($activeConferences as $conference)
                            @php
                                $now = \Carbon\Carbon::now();
                                $startDate = \Carbon\Carbon::parse($conference->start_date);
                                $endDate = \Carbon\Carbon::parse($conference->end_date);

                                if ($now->lt($startDate)) {
                                    $status = ['Upcoming', 'primary', 'bi-hourglass-split'];
                                } elseif ($now->between($startDate, $endDate)) {
                                    $status = ['Ongoing', 'success', 'bi-activity'];
                                } else {
                                    $status = ['Completed', 'secondary', 'bi-check-circle'];
                                }
                            @endphp

                            <div class="col-lg-4 col-md-6">
                                <div class="card border-0 shadow-sm h-100 hover-lift transition-all">
                                    <!-- Status Badge -->
                                    <div class="position-absolute top-0 end-0 m-3 z-3">
                                        <span class="badge bg-{{ $status[1] }} px-3 py-2 rounded-pill">
                                            <i class="bi {{ $status[2] }} me-1"></i>{{ $status[0] }}
                                        </span>
                                    </div>

                                    <!-- Conference Logo -->
                                    <div class="card-header bg-white border-0 text-center py-4">
                                        @if (!empty($conference->conference_logo))
                                            <img src="{{ asset('storage/conference/conference/logo/' . $conference->conference_logo) }}"
                                                alt="{{ $conference->conference_name }}"
                                                class="rounded-circle border border-3 border-primary shadow-sm"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 80px; height: 80px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="text-primary" style="font-size: 2rem;"
                                                    fill="currentColor" class="bi bi-mortarboard-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z" />
                                                    <path
                                                        d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z" />
                                                </svg>
                                                {{-- <i class="ti tabler-m text-primary" ></i> --}}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Conference Details -->
                                    <div class="card-body px-4">
                                        <div class="text-center mb-3">
                                            <h5 class="card-title fw-bold text-dark mb-2">{{ $conference->conference_name }}
                                            </h5>
                                            <p class="text-muted small mb-0">{{ $conference->conference_theme }}</p>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-success bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-calendar-check text-success"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">Start Date</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-danger bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-calendar-x text-danger"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">End Date</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-warning bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-clock text-warning"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">Start Time</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->start_time)->format('g:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-warning bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-clock-filled text-warning"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">End Time</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->end_time)->format('g:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Registration Deadlines -->
                                        <div class="bg-light rounded-3 p-3 mt-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="ti tabler-bell text-warning me-2"></i>
                                                <h6 class="mb-0 fw-semibold small">Registration Deadlines</h6>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-success rounded-pill px-2 py-1 small">Early
                                                    Bird</span>
                                                <small
                                                    class="text-muted fw-semibold">{{ \Carbon\Carbon::parse($conference->early_bird_registration_deadline)->format('M d, Y') }}</small>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary rounded-pill px-2 py-1 small">Regular</span>
                                                <small
                                                    class="text-muted fw-semibold">{{ \Carbon\Carbon::parse($conference->regular_registration_deadline)->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer bg-white border-0 p-3">
                                        <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                            class="btn btn-primary w-100 rounded-pill py-2">
                                            <i class="ti tabler-arrow-right-circle me-2"></i>
                                            Go To Conference
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Archived Conferences Tab -->
            <div class="tab-pane fade" id="archived-conferences" role="tabpanel" aria-labelledby="archived-tab">
                @if ($archivedConferences->isEmpty())
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-5">
                                    <div class="mb-3">
                                        <i class="ti tabler-archive text-muted"
                                            style="font-size: 4rem; opacity: 0.3;"></i>
                                    </div>
                                    <h4 class="text-muted mb-2">No Archived Conferences</h4>
                                    <p class="text-muted mb-0">There are no archived conferences at this time.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($archivedConferences as $conference)
                            <div class="col-lg-4 col-md-6">
                                <div class="card border-0 shadow-sm h-100 hover-lift transition-all"
                                    style="opacity: 0.9;">
                                    <!-- Archived Badge -->
                                    <div class="position-absolute top-0 end-0 m-3 z-3">
                                        <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                            <i class="ti tabler-archive me-1"></i>Archived
                                        </span>
                                    </div>

                                    <!-- Conference Logo -->
                                    <div class="card-header bg-white border-0 text-center py-4">
                                        @if (!empty($conference->conference_logo))
                                            <img src="{{ asset('storage/conference/conference/logo/' . $conference->conference_logo) }}"
                                                alt="{{ $conference->conference_name }}"
                                                class="rounded-circle border border-3 border-secondary shadow-sm"
                                                style="width: 80px; height: 80px; object-fit: cover; filter: grayscale(30%);">
                                        @else
                                            <div class="bg-secondary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 80px; height: 80px;">
                                                  <svg xmlns="http://www.w3.org/2000/svg" class="text-secondary" style="font-size: 2rem;"
                                                    fill="currentColor" class="bi bi-mortarboard-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z" />
                                                    <path
                                                        d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z" />
                                                </svg>
                                                {{-- <i class="ti tabler-mortarboard-fill text-secondary"
                                                    style="font-size: 2rem;"></i> --}}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Conference Details -->
                                    <div class="card-body px-4">
                                        <div class="text-center mb-3">
                                            <h5 class="card-title fw-bold text-dark mb-2">
                                                {{ $conference->conference_name }}</h5>
                                            <p class="text-muted small mb-2">{{ $conference->conference_theme }}</p>
                                            <div class="d-flex align-items-center justify-content-center gap-1 text-muted">
                                                <i class="ti tabler-clock small"></i>
                                                <small>Archived on
                                                    {{ $conference->archived_at ? $conference->archived_at->format('M d, Y') : 'N/A' }}</small>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-secondary bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-calendar-check text-secondary"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">Start Date</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="bg-secondary bg-opacity-10 rounded p-2 flex-shrink-0">
                                                        <i class="ti tabler-calendar-x text-secondary"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <small class="text-muted d-block mb-1">End Date</small>
                                                        <div class="fw-semibold small text-truncate">
                                                            {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="card-footer bg-white border-0 p-3">
                                        <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                            class="btn btn-outline-secondary w-100 rounded-pill py-2">
                                            <i class="ti tabler-eye me-2"></i>
                                            View Conference
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let shouldAutoOpenSettings = false;

            @if ($errors->has('id_card_document') || $errors->has('official_letter_document'))
                shouldAutoOpenSettings = true;
            @elseif (
                $userSociety &&
                $userSociety->memberType &&
                $userSociety->memberType->requires_student_verification == 1 &&
                empty($userSociety->id_card_document) &&
                empty($userSociety->official_letter_document)
            )
                shouldAutoOpenSettings = true;
            @endif

            if (shouldAutoOpenSettings) {
                const settingsModalElement = document.getElementById('accountSettingsModal');
                if (settingsModalElement) {
                    const settingsModal = new bootstrap.Modal(settingsModalElement);
                    settingsModal.show();

                    const documentsTab = document.getElementById('documents-tab');
                    if (documentsTab) {
                        bootstrap.Tab.getOrCreateInstance(documentsTab).show();
                    }
                }
            }
        });
    </script>

    <style>
        .settings-card {
            background: linear-gradient(130deg, #ffffff 0%, #f6f9ff 100%);
        }

        .settings-nav .nav-link {
            border-radius: 999px;
            padding: 0.5rem 1rem;
            color: #6c757d;
            font-weight: 600;
        }

        .settings-nav .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
        }

        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link {
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-pills .nav-link:not(.active):hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .nav-pills .nav-link.active {
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .card {
            overflow: hidden;
        }

        .min-w-0 {
            min-width: 0;
        }
    </style>
@endsection
