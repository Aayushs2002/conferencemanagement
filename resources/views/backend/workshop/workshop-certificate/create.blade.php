@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($workshop_certificate) ? 'Edit' : 'Add' }} Workshop Certificate Setting
@endsection

@section('content')

    <div class="row">
        <div class="col-12 col-xl-12">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center ">
                        <a href="{{ route('workshop-certificate.index', [$society, $conference, $workshop]) }}"
                            class="btn btn-outline-light btn-sm me-3 ">
                            <i class="ti tabler-arrow-narrow-left me-1"></i>Back
                        </a>
                        <div> 
                            <h4 class="mb-0 fw-bold text-white">
                                <i class="ti tabler-certificate me-2"></i>
                                {{ isset($workshop_certificate) ? 'Edit' : 'Create' }} Certificate Settings
                            </h4>
                            <small class="opacity-75">Configure your workshop certificate design</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form class="needs-validation"
                        action="{{ isset($workshop_certificate) ? route('workshop-certificate.update', [$society, $conference, $workshop, $workshop_certificate->id]) : route('workshop-certificate.store', [$society, $conference, $workshop]) }}"
                        method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        @isset($workshop_certificate)
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
                                        <i class="ti tabler-photo me-2"></i>Background Image <code>*</code>
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
                                    @if (isset($workshop_certificate) && $workshop_certificate->background_image)
                                        <div class="text-center">
                                            <p class="text-muted mb-2 small">Current Background:</p>
                                            <div class="border rounded-3 p-2 bg-white d-inline-block">
                                                <a href="{{ asset('storage/workshop/certificate/background/' . $workshop_certificate->background_image) }}"
                                                    target="_blank" class="text-decoration-none">
                                                    <img src="{{ asset('storage/workshop/certificate/background/' . $workshop_certificate->background_image) }}"
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
                                        @if (isset($workshop_certificate) && $workshop_certificate->background_image)
                                            <div class="border rounded-3 p-2 bg-white">
                                                <img src="{{ asset('storage/workshop/certificate/background/' . $workshop_certificate->background_image) }}"
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

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="border-top pt-4 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="ti tabler-device-floppy me-2"></i>
                                        {{ isset($workshop_certificate) ? 'Update Certificate' : 'Create Certificate' }}
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
    </script>
@endsection
