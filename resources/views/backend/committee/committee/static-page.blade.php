@extends('backend.layouts.conference.main')

@section('title')
    Committee Static Page
@endsection

@section('content')
    <div class="col-md">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <a href="{{ route('committee.index', [$society, $conference]) }}">
                        <i class="ti tabler-arrow-narrow-left"></i>
                    </a>
                    Committee Static Page Settings
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="ti tabler-info-circle me-2 mt-1" style="font-size: 20px;"></i>
                    <div>
                        <strong>About Committee Static Page:</strong>
                        <p class="mb-0">When enabled, the frontend committee page will display your custom HTML content instead of the dynamic committee member list. This is useful for creating a custom, formatted committee page with rich text, images, and custom layouts.</p>
                        <p class="mb-0 mt-2"><strong>Note:</strong> When disabled, the page will show the regular committee member list as usual.</p>
                    </div>
                </div>

                <form class="needs-validation"
                    action="{{ route('committee.static-page.update', [$society, $conference]) }}"
                    method="POST" novalidate>
                    @csrf

                    <div class="row">
                        <!-- Enable/Disable Static Page -->
                        <div class="col-12 mb-4">
                            <div class="card border shadow-none">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">
                                                <i class="ti tabler-toggle-left me-2 text-primary"></i>
                                                Enable Committee Static Page
                                            </h5>
                                            <p class="text-muted mb-0">
                                                Toggle to show static content instead of dynamic committee list on the frontend
                                            </p>
                                        </div>
                                        <div>
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="committee_static_page_enabled" value="0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="staticPageSwitch" name="committee_static_page_enabled"
                                                    value="1"
                                                    {{ $conferenceSetting?->committee_static_page_enabled ? 'checked' : '' }}
                                                    onchange="toggleEditor()">
                                                <label class="form-check-label" for="staticPageSwitch"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Static Page Content Editor -->
                        <div class="col-12 mb-4" id="editorSection" style="display: {{ $conferenceSetting?->committee_static_page_enabled ? 'block' : 'none' }}">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="ti tabler-edit me-2"></i>
                                        Committee Static Page Content
                                    </h5>
                                    <small class="text-muted">Create your custom committee page content using the rich text editor below</small>
                                </div>
                                <div class="card-body">
                                    <label class="form-label" for="committee_static_page_content">
                                        Page Content
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control ckeditor"
                                        name="committee_static_page_content"
                                        id="committee_static_page_content"
                                        rows="15"
                                        placeholder="Enter your committee page content here...">{{ $conferenceSetting?->committee_static_page_content ?? old('committee_static_page_content') }}</textarea>
                                    @error('committee_static_page_content')
                                        <p class="text-danger mt-2">{{ $message }}</p>
                                    @enderror
                                    <small class="text-muted mt-2 d-block">
                                        <i class="ti tabler-bulb me-1"></i>
                                        <strong>Tip:</strong> You can add formatted text, images, tables, lists, and more using the editor toolbar above.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="col-12" id="previewInfo" style="display: {{ $conferenceSetting?->committee_static_page_enabled ? 'none' : 'block' }}">
                            <div class="card border-warning">
                                <div class="card-body text-center py-5">
                                    <i class="ti tabler-eye-off text-warning" style="font-size: 48px;"></i>
                                    <h5 class="mt-3 mb-2">Static Page is Currently Disabled</h5>
                                    <p class="text-muted mb-0">Enable the toggle above to create a custom static committee page</p>
                                    <p class="text-muted">The frontend will continue to show the dynamic committee member list</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('committee.index', [$society, $conference]) }}" class="btn btn-secondary">
                                    <i class="ti tabler-arrow-left me-1"></i>
                                    Back to Committee List
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti tabler-device-floppy me-1"></i>
                                    Save Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleEditor() {
            const checkbox = document.getElementById('staticPageSwitch');
            const editorSection = document.getElementById('editorSection');
            const previewInfo = document.getElementById('previewInfo');

            if (checkbox.checked) {
                editorSection.style.display = 'block';
                previewInfo.style.display = 'none';
            } else {
                editorSection.style.display = 'none';
                previewInfo.style.display = 'block';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleEditor();
        });
    </script>
@endsection
