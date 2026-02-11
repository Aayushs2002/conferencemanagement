@extends('backend.layouts.conference.main')

@section('title')
    Scientific Session
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Scientific Session</h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="icon-base ti tabler-upload icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Export</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportTo('excel')">Export to Excel</a>
                                </li> 
                                <li><a class="dropdown-item" href="#" onclick="exportTo('pdf')">Export to PDF</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportTo('csv')">Export to CSV</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="window.print()">Print</a></li>
                            </ul>
                        </div>
                        {{-- <a href="{{ route('scheduleSession', [$society, $conference]) }}" class="btn btn-primary me-2"
                            tabindex="0">
                            <i class="icon-base ti tabler-clock icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Schdeule Session</span>
                        </a> --}}
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Scientific Session'))
                            <a href="{{ route('scientific-session.create', [$society, $conference]) }}"
                                class="btn btn-primary" tabindex="0">
                                <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- PDF Upload Section -->
            <div class="card-body border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-2">Scientific Session Schedule PDF</h6>
                        @if ($conference->scientific_session_pdf)
                            <div class="d-flex align-items-center">
                                <i class="icon-base ti tabler-file-text text-success me-2"></i>
                                <a href="{{ asset('storage/scientific-session/pdf/' . $conference->scientific_session_pdf) }}"
                                    target="_blank" class="me-3">
                                    <strong>{{ $conference->scientific_session_pdf }}</strong>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePdf()">
                                    <i class="icon-base ti tabler-trash icon-xs me-1"></i>Delete
                                </button>
                            </div>
                        @else
                            <p class="text-muted mb-0"><small>No PDF uploaded yet</small></p>
                        @endif
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPdfModal">
                            <i class="icon-base ti tabler-upload icon-xs me-1"></i>
                            {{ $conference->scientific_session_pdf ? 'Replace PDF' : 'Upload PDF' }}
                        </button>
                    </div>
                </div>
            </div>

            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Category Name</th>
                        <th>Title</th>
                        <th>Hall</th>
                        <th>Schedule Date</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($scientific_sessions as $scientific_session)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $scientific_session->category->category_name }}</td>
                            {{-- <td>{{ $scientific_session->topic }}</td> --}}
                            <td>
                                @if ($scientific_session->submission)
                                    {{ $scientific_session->submission->title }}
                                @else
                                    {{ $scientific_session->topic }}
                                @endif
                            </td>
                            <td>{{ $scientific_session->hall->hall_name }}</td>
                            <td>
                                @foreach ($dates as $date)
                                    @if ($scientific_session->day == $date)
                                        Day {{ $loop->iteration }}
                                    @endif
                                @endforeach
                            </td>
                            <td>{{ $scientific_session->start_time }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'View Poll'))
                                            <a class="dropdown-item"
                                                href="{{ route('poll.index', [$society, $conference, $scientific_session->id]) }}"><i
                                                    class="icon-base ti tabler-circle-letter-p me-1"></i> Poll</a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Scientific Session'))
                                            <a class="dropdown-item"
                                                href="{{ route('scientific-session.edit', [$society, $conference, $scientific_session->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        <hr>
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Delete Scientific Session'))
                                            <form
                                                action="{{ route('scientific-session.destroy', [$society, $conference, $scientific_session->id]) }}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                        class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                            </form>
                                        @endif
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>

        <!-- PDF Upload Modal -->
        <div class="modal fade" id="uploadPdfModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Scientific Session Schedule PDF</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="uploadPdfForm" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="pdf_file" class="form-label">Select PDF File</label>
                                <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf" required>
                                <small class="text-muted">Maximum file size: 10MB</small>
                            </div>
                            <div id="uploadProgress" class="progress d-none mb-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                     style="width: 0%"></div>
                            </div>
                            <div id="uploadError" class="alert alert-danger d-none"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="icon-base ti tabler-upload icon-xs me-1"></i>Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Upload PDF
            const uploadForm = document.getElementById('uploadPdfForm');
            if (uploadForm) {
                uploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const uploadBtn = document.getElementById('uploadBtn');
                    const uploadProgress = document.getElementById('uploadProgress');
                    const uploadError = document.getElementById('uploadError');
                    const progressBar = uploadProgress.querySelector('.progress-bar');
                    
                    // Reset error message
                    uploadError.classList.add('d-none');
                    
                    // Show progress bar
                    uploadProgress.classList.remove('d-none');
                    progressBar.style.width = '30%';
                    
                    // Disable upload button
                    uploadBtn.disabled = true;
                    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...';
                    
                    // Use XMLHttpRequest for better file upload handling
                    const xhr = new XMLHttpRequest();
                    
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            progressBar.style.width = percentComplete + '%';
                        }
                    });
                    
                    xhr.addEventListener('load', function() {
                        if (xhr.status === 200) {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                
                                if (data.success) {
                                    // Show success message using notyf
                                    if (typeof notyf !== 'undefined') {
                                        notyf.success(data.message);
                                    }
                                    
                                    // Close modal and reload page
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    uploadError.textContent = data.message || 'Upload failed';
                                    uploadError.classList.remove('d-none');
                                    uploadBtn.disabled = false;
                                    uploadBtn.innerHTML = '<i class="icon-base ti tabler-upload icon-xs me-1"></i>Upload';
                                }
                            } catch (error) {
                                uploadError.textContent = 'Error processing response';
                                uploadError.classList.remove('d-none');
                                uploadBtn.disabled = false;
                                uploadBtn.innerHTML = '<i class="icon-base ti tabler-upload icon-xs me-1"></i>Upload';
                            }
                        } else {
                            // Handle validation errors
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.errors && data.errors.pdf_file) {
                                    uploadError.textContent = data.errors.pdf_file[0];
                                } else if (data.message) {
                                    uploadError.textContent = data.message;
                                } else {
                                    uploadError.textContent = 'Upload failed with status: ' + xhr.status;
                                }
                            } catch (error) {
                                uploadError.textContent = 'Upload failed. Please try again.';
                            }
                            uploadError.classList.remove('d-none');
                            uploadBtn.disabled = false;
                            uploadBtn.innerHTML = '<i class="icon-base ti tabler-upload icon-xs me-1"></i>Upload';
                        }
                        
                        uploadProgress.classList.add('d-none');
                        progressBar.style.width = '0%';
                    });
                    
                    xhr.addEventListener('error', function() {
                        uploadError.textContent = 'Network error occurred while uploading the PDF.';
                        uploadError.classList.remove('d-none');
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = '<i class="icon-base ti tabler-upload icon-xs me-1"></i>Upload';
                        uploadProgress.classList.add('d-none');
                        progressBar.style.width = '0%';
                    });
                    
                    xhr.open('POST', '{{ route("scientific-session.upload-pdf", [$society, $conference]) }}');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.send(formData);
                });
            }

            // Delete PDF
            window.deletePdf = function() {
                if (!confirm('Are you sure you want to delete this PDF?')) {
                    return;
                }

                fetch('{{ route("scientific-session.delete-pdf", [$society, $conference]) }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof notyf !== 'undefined') {
                            notyf.success(data.message);
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('An error occurred while deleting the PDF.');
                });
            };
        });
    </script>
@endsection
