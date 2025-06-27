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

                        <!-- Signatures Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <span class="fw-bold">2</span>
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
                                                                        placeholder="Enter Job Title"
                                                                        value="{{ $signature['designation'] }}">
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
                    '<input type="text" name="designation[]" class="form-control form-control-sm" placeholder="Enter Job Title" required>' +
                    '</div>' +
                    '<small class="text-muted">Signature ' + (i + 1) + ' of ' + files.length + '</small>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            }
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
            let hasNewSignatures = $('#imagesMultiple')[0].files.length > 0;
            let hasExistingSignatures = $('.imgDelete').length > 0;

            if (!hasNewSignatures && !hasExistingSignatures) {
                e.preventDefault();
                alert('Please add at least one signature image.');
                $('#imagesMultiple').focus();
                return false;
            }

            // Check if names and designations are filled for new signatures
            if (hasNewSignatures) {
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
