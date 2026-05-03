@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($conference_certificate) ? 'Edit' : 'Add' }} Conference Certificate Setting
@endsection

@section('content')

    <div class="row">
        <div class="col-12 col-xl-12">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center ">
                        <a href="{{ route('conference-certificate.index', [$society, $conference]) }}"
                            class="btn btn-outline-light btn-sm me-3 ">
                            <i class="ti tabler-arrow-narrow-left me-1"></i>Back
                        </a>
                        <div> 
                            <h4 class="mb-0 fw-bold text-white">
                                <i class="ti tabler-certificate me-2"></i>
                                {{ isset($conference_certificate) ? 'Edit' : 'Create' }} Certificate Settings
                            </h4>
                            <small class="opacity-75">Configure your conference certificate design and
                                signatures</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form class="needs-validation"
                        action="{{ isset($conference_certificate) ? route('conference-certificate.update', [$society, $conference, $conference_certificate->id]) : route('conference-certificate.store', [$society, $conference]) }}"
                        method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        @isset($conference_certificate)
                            @method('patch')
                        @endisset

                        <!-- Background Image Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <span class="fw-bold">1</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 text-primary fw-bold">Certificate Background</h5>
                                        <p class="text-muted mb-0 small">Upload the background image for your
                                            certificate</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-lg-6">
                                <div class="border rounded-3 p-4 bg-light">
                                    <label class="form-label fw-semibold mb-3" for="background_image">
                                        <i class="ti tabler-photo me-2"></i>Background Image 
                                    </label>

                                    <div class="mb-3">
                                        <input type="file"
                                            class="form-control form-control-lg @error('background_image') is-invalid @enderror"
                                            name="background_image" id="background_image" accept="image/*" />
                                        <div class="form-text">
                                            <i class="ti tabler-info-circle me-1"></i>
                                            Recommended: JPG, PNG format. Max size: 5MB
                                        </div>
                                        @error('background_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Current Background Preview -->
                                    @if (isset($conference_certificate) && $conference_certificate->background_image)
                                        <div class="text-center">
                                            <p class="text-muted mb-2 small">Current Background:</p>
                                            <div class="border rounded-3 p-2 bg-white d-inline-block">
                                                <a href="{{ asset('storage/conference/conference/certificate/background/' . $conference_certificate->background_image) }}"
                                                    target="_blank" class="text-decoration-none">
                                                    <img src="{{ asset('storage/conference/conference/certificate/background/' . $conference_certificate->background_image) }}"
                                                        class="img-fluid rounded" alt="Current Background"
                                                        style="max-height: 200px;">
                                                    <div class="mt-2">
                                                        <small class="text-primary">
                                                            <i class="ti tabler-external-link me-1"></i>View Full Size
                                                        </small>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="h-100 d-flex align-items-center justify-content-center">
                                    <div id="backgroundPreview" class="text-center">
                                        @if (isset($conference_certificate) && $conference_certificate->background_image)
                                            <div class="border rounded-3 p-2 bg-white">
                                                <img src="{{ asset('storage/conference/conference/certificate/background/' . $conference_certificate->background_image) }}"
                                                    class="img-fluid rounded" alt="Background Preview"
                                                    style="max-height: 250px; max-width: 100%;">
                                            </div>
                                        @else
                                            <div class="text-muted">
                                                <i class="ti tabler-file-certificate"
                                                    style="font-size: 4rem; opacity: 0.3;"></i>
                                                <p class="mt-3 mb-0">Background preview will appear here</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom CSS Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <span class="fw-bold">2</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 text-warning fw-bold">Certificate Custom CSS</h5>
                                        <p class="text-muted mb-0 small">Add custom CSS code for certificate layout styling</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-12">
                                <div class="border rounded-3 p-4 bg-light">
                                    <label class="form-label fw-semibold mb-2" for="custom_css">
                                        <i class="ti tabler-code me-2"></i>Custom CSS Code
                                    </label>
                                    <textarea class="form-control font-monospace @error('custom_css') is-invalid @enderror" name="custom_css"
                                        id="custom_css" rows="10"
                                        placeholder="/* Example */&#10;.invoice-box {&#10;    color: #1f2937;&#10;}&#10;&#10;h3 {&#10;    font-size: 90px;&#10;}">{{ old('custom_css', $conference_certificate->custom_css ?? '') }}</textarea>
                                    <div class="form-text">
                                        <i class="ti tabler-info-circle me-1"></i>
                                        This CSS is injected only in the certificate generate page.
                                    </div>
                                    @error('custom_css')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Signatures Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <span class="fw-bold">3</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 text-success fw-bold">Certificate Signatures</h5>
                                        <p class="text-muted mb-0 small">Add signature images with names and
                                            designations</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="border rounded-3 p-4 bg-light">
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold d-block" for="include_title">
                                            <i class="ti tabler-heading me-2"></i>Show Certificate Title
                                        </label>
                                        <input type="hidden" name="include_title" value="0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="include_title" name="include_title" value="1"
                                                {{ old('include_title', isset($conference_certificate) ? ($conference_certificate->include_title ?? 1) : 1) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="include_title">
                                                Show "Conference Abbreviation" heading in generated certificates
                                            </label>
                                        </div>
                                        <div class="form-text">
                                            Disable this if you want to hide the main certificate title block.
                                        </div>
                                        @error('include_title')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold d-block" for="show_presentation_type">
                                            <i class="ti tabler-presentation me-2"></i>Show Presentation Type for Speaker
                                        </label>
                                        <input type="hidden" name="show_presentation_type" value="0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="show_presentation_type" name="show_presentation_type" value="1"
                                                {{ old('show_presentation_type', isset($conference_certificate) ? ($conference_certificate->show_presentation_type ?? 0) : 0) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_presentation_type">
                                                Show submission presentation type (e.g. "Oral-Original", "Poster-Review") for speakers who have a submission
                                            </label>
                                        </div>
                                        <div class="form-text">
                                            When enabled, the registrant type on the certificate will reflect the submission's presentation type and article type (e.g. "Oral-Original"). Applies only to registrants with a linked submission.
                                        </div>
                                        @error('show_presentation_type')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold d-block" for="include_signature">
                                            <i class="ti tabler-signature me-2"></i>Include Signatures In Certificate
                                        </label>
                                        <input type="hidden" name="include_signature" value="0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="include_signature" name="include_signature" value="1"
                                                {{ old('include_signature', isset($conference_certificate) ? ($conference_certificate->include_signature ?? 1) : 1) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="include_signature">
                                                Show signature block in generated certificates
                                            </label>
                                        </div>
                                        <div class="form-text">
                                            Disable this if you want certificate without signatures.
                                        </div>
                                        @error('include_signature')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <label for="signature" class="form-label fw-semibold mb-3">
                                        <i class="ti tabler-writing-sign me-2"></i>Upload Signature Images
                                    </label>

                                    <!-- Instructions Alert -->
                                    <div class="alert alert-info d-flex align-items-start mb-4" role="alert">
                                        <i class="ti tabler-info-circle me-2 mt-1"></i>
                                        <div>
                                            <h6 class="alert-heading mb-2">How to add multiple signatures:</h6>
                                            <ol class="mb-0 ps-3">
                                                <li>Click "Choose Files" button below</li>
                                                <li>Hold <kbd>Ctrl</kbd> (Windows) or <kbd>Cmd</kbd> (Mac) key</li>
                                                <li>Click on each signature image you want to select</li>
                                                <li>Click "Open" to upload all selected images</li>
                                                <li>Add names and designations for each signature</li>
                                            </ol>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <!-- Custom File Upload Button -->
                                        <div class="d-grid">
                                            <label for="imagesMultiple"
                                                class="btn btn-outline-primary btn-lg position-relative overflow-hidden">
                                                <i class="ti tabler-cloud-upload me-2"></i>
                                                <span id="fileButtonText">Choose Signature Files</span>
                                                <input type="file"
                                                    class="position-absolute top-0 start-0 w-100 h-100 opacity-0 @error('signatures') is-invalid @enderror"
                                                    name="signatures[]" id="imagesMultiple" multiple
                                                    accept="image/jpeg,image/png" style="cursor: pointer;" />
                                            </label>
                                        </div>

                                        <div class="form-text text-center mt-2">
                                            <i class="ti tabler-info-circle me-1"></i>
                                            <strong>Maximum 5 images</strong> • Only JPG/PNG formats • Max 5MB each
                                        </div>

                                        @error('signatures')
                                            <div class="text-danger text-center mt-2">
                                                <i class="ti tabler-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        @if ($errors->get('signatures.*'))
                                            <div class="text-danger text-center mt-2">
                                                <i class="ti tabler-alert-circle me-1"></i>
                                                Images must be JPG or PNG format.
                                            </div>
                                        @endif
                                        @error('signature_order')
                                            <div class="text-danger text-center mt-2">
                                                <i class="ti tabler-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        @error('signature_order_old')
                                            <div class="text-danger text-center mt-2">
                                                <i class="ti tabler-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- File Selection Status -->
                                    <div id="fileStatus" class="alert alert-success d-none mb-3" role="alert">
                                        <i class="ti tabler-check-circle me-2"></i>
                                        <span id="fileCount">0</span> signature(s) selected
                                    </div>

                                    <!-- New Images Preview -->
                                    <div id="imagesPreview" class="row"></div>

                                    <!-- Existing Signatures -->
                                    @if (isset($conference_certificate) && !empty($conference_certificate->signature))
                                        <div class="mt-4">
                                            <h6 class="text-muted mb-3">
                                                <i class="ti tabler-photo me-2"></i>Current Signatures
                                            </h6>
                                            <div class="row">
                                                @foreach ($conference_certificate->signature as $signature)
                                                    <div class="col-md-6 col-lg-4 mb-4">
                                                        <div class="card border h-100">
                                                            <div class="card-body text-center p-3">
                                                                <div class="mb-3">
                                                                    <img src="{{ asset('storage/conference/conference/certificate/signature/' . $signature['fileName']) }}"
                                                                        alt="Signature" class="img-fluid rounded border"
                                                                        style="max-height: 100px; background: white;">
                                                                </div>

                                                                <div class="mb-2">
                                                                    <label
                                                                        class="form-label small text-muted">Name:</label>
                                                                    <input type="text" name="name_old[]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Enter Full Name"
                                                                        value="{{ $signature['name'] }}">
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label
                                                                        class="form-label small text-muted">Designation:</label>
                                                                    <input type="text" name="designation_old[]"
                                                                        class="form-control form-control-sm"
                                                                        placeholder="Enter Designation"
                                                                        value="{{ $signature['designation'] }}">
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label small text-muted">Display Order:</label>
                                                                    <input type="number" name="signature_order_old[]"
                                                                        class="form-control form-control-sm"
                                                                        min="1"
                                                                        value="{{ $signature['order'] ?? $loop->iteration }}">
                                                                </div>

                                                                {{-- <a href="{{ route('hotel.image.delete', [$hotel->id, $signature['fileName']]) }}" --}}
                                                                <a href="{{ route('conference-certificate.signature.remove', [$conference_certificate->id, $signature['fileName']]) }}"
                                                                    class="btn btn-outline-danger btn-sm imgDelete"
                                                                    onclick="return confirm('Are you sure you want to remove this signature?')">
                                                                    <i class="ti tabler-trash me-1"></i>Remove
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Certificate Label Settings Section -->
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <span class="fw-bold">4</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 text-info fw-bold">Certificate Label Settings</h5>
                                        <p class="text-muted mb-0 small">Customise the "Participating as ..." label per registration type and per committee</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-12">
                                <div class="border rounded-3 p-4 bg-light">

                                    {{-- Registration Type Labels --}}
                                    <h6 class="fw-bold mb-3"><i class="ti tabler-tag me-2"></i>Registration Type Labels</h6>
                                    <p class="text-muted small mb-3">
                                        Set a custom label for each registration type. Leave blank to use the default (e.g. "Delegate", "Speaker").
                                    </p>
                                    @php
                                        $regTypeDefaults = [
                                            1 => 'Delegate (Attendee)',
                                            2 => 'Speaker/Presenter',
                                            3 => 'Session Chair',
                                            4 => 'Special Guest',
                                            5 => 'Organizer',
                                            6 => 'Faculty',
                                            7 => 'Volunteer',
                                            8 => 'Invitee',
                                        ];
                                    @endphp
                                    <div class="table-responsive mb-4">
                                        <table class="table table-bordered table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:30%">Registration Type</th>
                                                    <th>"Participating as" Text <small class="text-muted fw-normal">(leave blank for default)</small></th>
                                                    <th>Custom Label <small class="text-muted fw-normal">(leave blank for default)</small></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($regTypeDefaults as $typeId => $defaultLabel)
                                                    <tr>
                                                        <td class="fw-semibold">{{ $defaultLabel }}</td>
                                                        <td>
                                                            <input type="text"
                                                                name="cert_reg_participating_text[{{ $typeId }}]"
                                                                class="form-control form-control-sm"
                                                                placeholder="Participating as"
                                                                value="{{ old('cert_reg_participating_text.' . $typeId, isset($certRegistrantTags[$typeId]) ? $certRegistrantTags[$typeId]->participating_text : '') }}">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                name="cert_reg_tag[{{ $typeId }}]"
                                                                class="form-control form-control-sm"
                                                                placeholder="e.g. {{ $defaultLabel }}"
                                                                value="{{ old('cert_reg_tag.' . $typeId, isset($certRegistrantTags[$typeId]) ? $certRegistrantTags[$typeId]->name_tag : '') }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr class="my-4">

                                    {{-- Committee Label Overrides --}}
                                    <h6 class="fw-bold mb-3"><i class="ti tabler-users me-2"></i>Committee Label Overrides</h6>
                                    <p class="text-muted small mb-3">
                                        Set a label for committee members. This takes priority over registration type labels.
                                    </p>

                                    <div id="certCommitteeTagsWrapper">
                                        @if (isset($certCommitteeTags) && $certCommitteeTags->count())
                                            @foreach ($certCommitteeTags as $ct)
                                                <div class="row g-2 mb-3 cert-committee-row align-items-end">
                                                    <div class="col-md-4">
                                                        <label class="form-label small text-muted">Committee</label>
                                                        <select name="cert_comm_committee_id[]" class="form-select form-select-sm">
                                                            <option value="">-- Select Committee --</option>
                                                            @foreach ($committees as $committee)
                                                                <option value="{{ $committee->id }}"
                                                                    {{ $ct->committee_id == $committee->id ? 'selected' : '' }}>
                                                                    {{ $committee->committee_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small text-muted">Designation <span class="text-muted">(optional)</span></label>
                                                        <select name="cert_comm_designation_id[]" class="form-select form-select-sm">
                                                            <option value="">-- Any Designation --</option>
                                                            @foreach ($committeeDesignations as $des)
                                                                <option value="{{ $des->id }}"
                                                                    {{ $ct->designation_id == $des->id ? 'selected' : '' }}>
                                                                    {{ $des->designation }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label small text-muted">"Participating as" Text</label>
                                                        <input type="text" name="cert_comm_participating_text[]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Participating as"
                                                            value="{{ $ct->participating_text }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small text-muted">Label on Certificate</label>
                                                        <input type="text" name="cert_comm_name_tag[]"
                                                            class="form-control form-control-sm"
                                                            placeholder="e.g. Scientific Committee Member"
                                                            value="{{ $ct->name_tag }}">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-cert-committee-row w-100">
                                                            <i class="ti tabler-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <button type="button" id="addCertCommitteeRow" class="btn btn-outline-secondary btn-sm mt-1">
                                        <i class="ti tabler-plus me-1"></i>Add Committee Override
                                    </button>

                                    {{-- Hidden template row --}}
                                    <template id="certCommitteeRowTemplate">
                                        <div class="row g-2 mb-3 cert-committee-row align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Committee</label>
                                                <select name="cert_comm_committee_id[]" class="form-select form-select-sm">
                                                    <option value="">-- Select Committee --</option>
                                                    @foreach ($committees as $committee)
                                                        <option value="{{ $committee->id }}">{{ $committee->committee_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small text-muted">Designation <span class="text-muted">(optional)</span></label>
                                                <select name="cert_comm_designation_id[]" class="form-select form-select-sm">
                                                    <option value="">-- Any Designation --</option>
                                                    @foreach ($committeeDesignations as $des)
                                                        <option value="{{ $des->id }}">{{ $des->designation }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-muted">"Participating as" Text</label>
                                                <input type="text" name="cert_comm_participating_text[]"
                                                    class="form-control form-control-sm"
                                                    placeholder="Participating as">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small text-muted">Label on Certificate</label>
                                                <input type="text" name="cert_comm_name_tag[]"
                                                    class="form-control form-control-sm"
                                                    placeholder="e.g. Scientific Committee Member">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-cert-committee-row w-100">
                                                    <i class="ti tabler-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="border-top pt-4 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="ti tabler-device-floppy me-2"></i>
                                        {{ isset($conference_certificate) ? 'Update Certificate' : 'Create Certificate' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Background image preview
        $("#background_image").change(function(e) {
            const file = e.target.files[0];
            const preview = $("#backgroundPreview");

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.html(
                        '<div class="border rounded-3 p-2 bg-white">' +
                        '<img src="' + e.target.result +
                        '" class="img-fluid rounded" alt="Background Preview" style="max-height: 250px; max-width: 100%;">' +
                        '</div>'
                    );
                };
                reader.readAsDataURL(file);
            } else {
                preview.html(
                    '<div class="text-muted">' +
                    '<i class="ti tabler-file-certificate" style="font-size: 4rem; opacity: 0.3;"></i>' +
                    '<p class="mt-3 mb-0">Background preview will appear here</p>' +
                    '</div>'
                );
            }
        });

        // Multiple signature images handling
        const existingSignatureCount = {{ isset($conference_certificate) && !empty($conference_certificate->signature) ? count($conference_certificate->signature) : 0 }};

        $("#imagesMultiple").change(function(e) {
            e.preventDefault();
            let files = e.target.files;
            const fileCount = files.length;

            // Update button text and show status
            if (fileCount > 0) {
                $("#fileButtonText").html('<i class="ti tabler-check me-2"></i>' + fileCount + ' file(s) selected');
                $("#fileStatus").removeClass('d-none');
                $("#fileCount").text(fileCount);

                // Validate file count
                if (fileCount > 5) {
                    alert('Maximum 5 files allowed. Please select only 5 files.');
                    $(this).val('');
                    $("#fileButtonText").text('Choose Signature Files');
                    $("#fileStatus").addClass('d-none');
                    $("#imagesPreview").html('');
                    return;
                }
            } else {
                $("#fileButtonText").text('Choose Signature Files');
                $("#fileStatus").addClass('d-none');
            }

            $("#imagesPreview").html('');

            if (files.length > 0) {
                // Add preview header
                $("#imagesPreview").before(
                    '<div class="mt-4 mb-3" id="previewHeader"><h6 class="text-success mb-0"><i class="ti tabler-eye me-2"></i>Preview New Signatures</h6><small class="text-muted">Add names and designations for each signature below</small></div>'
                );

                // Remove existing preview header if present
                $("#previewHeader").remove();
                $("#imagesPreview").before(
                    '<div class="mt-4 mb-3" id="previewHeader"><h6 class="text-success mb-0"><i class="ti tabler-eye me-2"></i>Preview New Signatures</h6><small class="text-muted">Add names and designations for each signature below</small></div>'
                );
            }

            for (let i = 0; i < files.length; i++) {
                let file = files[i];

                // Validate file type
                if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
                    alert('File "' + file.name + '" is not a valid image. Only JPG and PNG files are allowed.');
                    continue;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File "' + file.name + '" is too large. Maximum file size is 5MB.');
                    continue;
                }

                $("#imagesPreview").append(
                    '<div class="col-md-6 col-lg-4 mb-4">' +
                    '<div class="card border h-100">' +
                    '<div class="card-body text-center p-3">' +
                    '<div class="mb-3">' +
                    '<img src="' + URL.createObjectURL(file) +
                    '" class="img-fluid rounded border" style="max-height: 100px; background: white;"/>' +
                    '</div>' +
                    '<div class="mb-2">' +
                    '<label class="form-label small text-muted">Name:</label>' +
                    '<input type="text" name="name[]" class="form-control form-control-sm" placeholder="Enter Full Name" required>' +
                    '</div>' +
                    '<div class="mb-2">' +
                    '<label class="form-label small text-muted">Designation:</label>' +
                    '<input type="text" name="designation[]" class="form-control form-control-sm" placeholder="Enter Designation" required>' +
                    '</div>' +
                    '<div class="mb-2">' +
                    '<label class="form-label small text-muted">Display Order:</label>' +
                    '<input type="number" name="signature_order[]" class="form-control form-control-sm" min="1" value="' + (existingSignatureCount + i + 1) + '" required>' +
                    '</div>' +
                    '<small class="text-muted">Signature ' + (i + 1) + ' of ' + files.length + '</small>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            }
        });

        // Committee override rows add/remove
        $('#addCertCommitteeRow').on('click', function () {
            const template = document.getElementById('certCommitteeRowTemplate');
            const clone = template.content.cloneNode(true);
            document.getElementById('certCommitteeTagsWrapper').appendChild(clone);
        });

        $(document).on('click', '.remove-cert-committee-row', function () {
            $(this).closest('.cert-committee-row').remove();
        });

        // File input styling feedback
        $('input[type="file"]').on('change', function() {
            const $this = $(this);
            if (this.files && this.files.length > 0) {
                $this.closest('.form-group, .mb-3').find('.form-text').addClass('text-success');
            }
        });

        // Form validation enhancement
        $('form').on('submit', function(e) {
            let includeSignature = $('#include_signature').is(':checked');
            let hasNewSignatures = $('#imagesMultiple')[0].files.length > 0;
            let hasExistingSignatures = $('.imgDelete').length > 0;

            if (includeSignature && !hasNewSignatures && !hasExistingSignatures) {
                e.preventDefault();
                alert('Please add at least one signature image.');
                $('#imagesMultiple').focus();
                return false;
            }

            // Check if names and designations are filled for new signatures
            if (includeSignature && hasNewSignatures) {
                let allFilled = true;
                $('#imagesPreview input[required]').each(function() {
                    if ($(this).val().trim() === '') {
                        allFilled = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!allFilled) {
                    e.preventDefault();
                    alert('Please fill in names and designations for all signature images.');
                    return false;
                }
            }
        });
    </script>
@endsection
