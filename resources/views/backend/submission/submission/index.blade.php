@extends('backend.layouts.conference.main')

@section('title')
    Submission
@endsection
@section('content')
    <div class="card border my-4 container">
        <h5 class="pt-3">Filter By:</h5>
        <form method="GET" action="{{ route('submission.viewSubmissions', [$society, $conference]) }}" id="filterForm">
            <div class="row">
                <div class="col-md-3 form-group mb-3">
                    <label for="article_type_id" class="mb-2">Presentation Category</label>
                    <select name="article_type_id" id="article_type_id"
                        class="form-control @error('article_type_id') is-invalid @enderror">
                        <option value="">-- Select Presentation Category --</option>
                        @foreach ($articleTypes as $articleType)
                            <option value="{{ $articleType->id }}"
                                {{ request()->article_type_id == $articleType->id ? 'selected' : '' }}>
                                {{ $articleType->name }}
                            </option>
                        @endforeach
                    </select>
                </div> 
                <div class="col-md-3 form-group mb-3">
                    <label for="presentation_type" class="mb-2">Presentation Type</label>
                    <select name="presentation_type" id="presentation_type"
                        class="form-control @error('presentation_type') is-invalid @enderror">
                        <option value="">-- Select Presentation Type --</option>
                        <option {{ request()->presentation_type == 2 ? 'selected' : '' }} value="2">
                            Oral
                        </option>
                        <option {{ request()->presentation_type == 1 ? 'selected' : '' }} value="1">
                            Poster
                        </option>
                    </select>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="request_status" class="mb-2">Request Status</label>
                    <select name="request_status" id="request_status"
                        class="form-control @error('request_status') is-invalid @enderror">
                        <option value="">-- Select Request Status --</option>
                        <option {{ request()->request_status === '0' ? 'selected' : '' }} value="0">
                            Pending
                        </option>
                        <option {{ request()->request_status == 1 ? 'selected' : '' }} value="1">
                            Accepted
                        </option>
                        <option {{ request()->request_status == 2 ? 'selected' : '' }} value="2">
                            Correction
                        </option>
                        <option {{ request()->request_status == 4 ? 'selected' : '' }} value="4">
                            Rejected
                        </option>
                    </select>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="submission_category_major_track_id" class="mb-2">Theme/Sub-theme</label>
                    <select name="submission_category_major_track_id" id="submission_category_major_track_id"
                        class="form-control @error('submission_category_major_track_id') is-invalid @enderror">
                        <option value="">-- Select Theme/Sub-theme --</option>
                        @foreach ($submissionTracks as $submissionTrack)
                            <option value="{{ $submissionTrack->id }}"
                                {{ request()->submission_category_major_track_id == $submissionTrack->id ? 'selected' : '' }}>
                                {{ $submissionTrack->title }}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="col-md-3 form-group mb-3">
                    <label for="from" class="mb-2">From</label>
                    <input type="date" value="{{ request('from') }}"
                        class="form-control @error('from') is-invalid @enderror" id="from" name="from" />
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="to" class="mb-2">To</label>
                    <input type="date" value="{{ request('to') }}"
                        class="form-control @error('to') is-invalid @enderror" id="to" name="to" />
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="designation" class="mb-2">Designation</label>
                    <select name="designation" id="designation" class="form-control @error('designation') is-invalid @enderror">
                        <option value="">-- Select Designation --</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->designation }}"
                                {{ request()->designation == $designation->designation ? 'selected' : '' }}>
                                {{ $designation->designation }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="expert_assigned" class="mb-2">Expert Assignment</label>
                    <select name="expert_assigned" id="expert_assigned" class="form-control @error('expert_assigned') is-invalid @enderror">
                        <option value="">-- All Submissions --</option>
                        <option value="assigned" {{ request()->expert_assigned == 'assigned' ? 'selected' : '' }}>
                            Assigned to Expert
                        </option>
                        <option value="not_assigned" {{ request()->expert_assigned == 'not_assigned' ? 'selected' : '' }}>
                            Not Assigned
                        </option>
                    </select>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="slide_uploaded" class="mb-2">Slide Upload</label>
                    <select name="slide_uploaded" id="slide_uploaded" class="form-control @error('slide_uploaded') is-invalid @enderror">
                        <option value="">-- All --</option>
                        <option value="uploaded" {{ request()->slide_uploaded == 'uploaded' ? 'selected' : '' }}>
                            Uploaded
                        </option>
                        <option value="not_uploaded" {{ request()->slide_uploaded == 'not_uploaded' ? 'selected' : '' }}>
                            Not Uploaded
                        </option>
                    </select>
                </div>

                <input type="hidden" name="color_filter" id="color_filter" value="{{ request()->color_filter }}">

                <div class="row my-4">
                    <div class="col-12 text-end">
                        <a href="{{ route('submission.index', [$society, $conference]) }}" class="btn btn-danger">Reset</a>
                        <button type="submit" id="ExportExcelBtn" class="btn btn-warning">Export Excel</button>
                        <button type="submit" id="ExportBtn" class="btn btn-success">Export Word</button>

                        <button type="submit" id="filterBtn" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card mb-6">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Presentation Submission
                    </h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <a href="{{ route('submission.viewSubmissions', [$society, $conference]) }}"
                            class="btn btn-info mr-2">
                            <i class="icon-base ti tabler-eye icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">View Abstract Book Format</span>
                        </a>
                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Expert Assign'))
                            <button type="button" class="btn btn-success" id="bulkAssignBtn" style="display: none;">
                                <i class="icon-base ti tabler-users icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Bulk Assign to Expert</span>
                                <span class="badge bg-white text-success ms-1" id="selectedCount">0</span>
                            </button>
                        @endif
                        {{-- @if (auth()->user()->hasConferencePermissionBlade($conference, 'Update Review Deadline')) --}}
                            <button type="button" class="btn btn-warning" id="bulkDeadlineBtn" style="display: none;">
                                <i class="icon-base ti tabler-calendar icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Bulk Update Deadline</span>
                                <span class="badge bg-white text-warning ms-1" id="selectedDeadlineCount">0</span>
                            </button>
                        {{-- @endif --}}
                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Send Mail'))
                            <a href="" class="btn btn-primary sendMail" data-bs-toggle="modal"
                                data-bs-target="#pricingModal" tabindex="0">
                                <i class="icon-base ti tabler-mail icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Send Mail</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="px-3 py-2">
                <small class="text-muted">
                    <strong>Color Legend (Click to filter):</strong>
                    <span class="badge bg-success color-filter-badge" data-color="green" style="cursor: pointer;">Green</span> = Multiple Poster submissions by same user |
                    <span class="badge bg-warning color-filter-badge" data-color="yellow" style="cursor: pointer;">Yellow</span> = Multiple Oral submissions by same user |
                    <span class="badge bg-danger color-filter-badge" data-color="red" style="cursor: pointer;">Red</span> = Multiple submissions with different presentation types by same user
                    @if(request()->color_filter)
                        <span class="ms-2">|</span>
                        <a href="{{ route('submission.index', [$society, $conference]) }}" class="badge bg-light text-dark border ms-2 mt-2" style="text-decoration: none; cursor: pointer;">
                            <i class="ti tabler-x" style="font-size: 10px;"></i> Clear Filter
                        </a>
                    @endif
                </small>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Expert Assign'))
                            <th>
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                        @endif
                        <th>#</th>
                        <th scope="col">Speaker Name</th>
                        {{-- <th>Presentation Category</th> --}}
                        {{-- <th>Theme/Sub-theme</th> --}}
                        <th>Topic</th>
                        <th>Presentation Type</th>
                        <th>Request Status</th>
                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Expert Assign'))
                            <th>Assign to Expert ?</th>
                        @endif
                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Score') &&
                                $submission_setting?->scoring_allowed == 1)
                            <th>Score</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($submissions as $submission)
                        <tr class="{{ $submission->row_color ?? '' }}">
                            @if (auth()->user()->hasConferencePermissionBlade($conference, 'Expert Assign'))
                                <td>
                                    <input type="checkbox" class="form-check-input submission-checkbox" 
                                           data-id="{{ $submission->id }}" 
                                           data-title="{{ $submission->title }}">
                                </td>
                            @endif
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $submission->presenter?->fullName($submission->presenter) }}</td>
{{-- 
                            <td> {{ $submission->articleType?->name ?? 'N/A' }}
                            </td>
                            <td >
                                {{ $submission->submissionCategoryMajorTrack->title }}
                            </td> --}}
                            <td class="viewData " data-id="{{ $submission->id }}" data-bs-toggle="modal" data-bs-target="#pricingModal" style="cursor: pointer;">
                                {{ \Illuminate\Support\Str::words($submission->title, 5, '...') }}
                            </td>

                            <td>
                                @if ($submission->presentation_type == 1)
                                    Poster
                                @elseif($submission->presentation_type == 2)
                                    Oral(Abstract)
                                @endif
                                <br>
                                @if ($submission->presentation_type_change === null)
                                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'Convert Presentation Type'))
                                        @if ($submission->presentation_type == 2)
                                            <a href="{{ route('submission.convertPresentationTypeRequest', [$society, $conference, $submission->id]) }}"
                                                class="btn btn-sm btn-primary convertPresentationTypeRequest mt-1">
                                                <span style="font-size: 10px;">
                                                    Convert
                                                    To
                                                    Poster
                                                </span>
                                            </a>
                                            {{-- @else --}}
                                            {{-- <a href="{{ route('submission.convertPresentationTypeRequest', [$society, $conference, $submission->id]) }}"
                                                class="btn btn-sm btn-primary convertPresentationTypeRequest mt-1"><span
                                                    style="font-size: 10px;">Convert
                                                    To
                                                    Oral</span></a> --}}
                                        @endif
                                    @endif
                                @endif
                                @if ($submission->presentation_type_change === 0)
                                    <p class="text-warning " style="font-size: 13px;">(Convert Request Send)</p>
                                @elseif($submission->presentation_type_change == 1)
                                    <p class="text-success" style="font-size: 12px;">Convert Request Accepted</p>
                                @elseif($submission->presentation_type_change == 2)
                                    <p class="text-danger" style="font-size: 12px;">Convert Request Rejected</p>
                                @endif
                            </td>
                            <td>
                                @if ($submission->request_status === 0)
                                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'Change Request Status'))
                                        <button class="btn btn-sm btn-primary sentToAuthot"
                                            data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal">Pending</button>
                                    @else
                                        <span class="badge bg-primary">Pending</span>
                                    @endif
                                @endif
                                @if ($submission->request_status === 1)
                                    <span class="badge bg-success">Accepted</span>
                                @endif
                                @if ($submission->request_status === 2)
                                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'Change Request Status'))
                                        <button class="btn btn-sm btn-warning sentToAuthot"
                                            data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal">Correction</button>
                                    @else
                                        <span class="badge bg-warning">Correction</span>
                                    @endif
                                @endif
                                @if ($submission->request_status === 3)
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            @if (auth()->user()->hasConferencePermissionBlade($conference, 'Expert Assign'))
                                <td>
                                    @if (empty($submission->expert_id))
                                        <button class="btn btn-sm btn-primary expertForward"
                                            data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal">Not Assigned</button>
                                    @else
                                        <button class="btn btn-sm btn-success expertForward"
                                            data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal">Assigned</button>
                                        <br>
                                        <small class="text-success">
                                            <i class="ti tabler-user-check"></i>
                                            {{ $submission->expert?->fullName($submission->expert) ?? 'Expert' }}
                                        </small>
                                    @endif
                                </td>
                            @endif
                            
                            @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Score') &&
                                    $submission_setting?->scoring_allowed == 1)
                                <td>
                                    @if ($submission->submissionRating)
                                        @php
                                            $totalScore = 0;
                                            // Check if section ratings exist
                                            if (
                                                !empty($submission->submissionRating->section_ratings) &&
                                                is_array($submission->submissionRating->section_ratings)
                                            ) {
                                                $totalScore =
                                                    collect($submission->submissionRating->section_ratings)->sum(
                                                        'rating',
                                                    ) +
                                                    ($submission->submissionRating->title_rating ?? 0) +
                                                    ($submission->submissionRating->grammar ?? 0) +
                                                    ($submission->submissionRating->overall_rating ?? 0);
                                            }
                                            // Check if overall rating exists
                                            elseif ($submission->submissionRating->overall_rating) {
                                                $totalScore = $submission->submissionRating->overall_rating;
                                            }
                                            // Default rating calculation
                                            else {
                                                $totalScore =
                                                    ($submission->submissionRating->introduction ?? 0) +
                                                    ($submission->submissionRating->method ?? 0) +
                                                    ($submission->submissionRating->result ?? 0) +
                                                    ($submission->submissionRating->conclusion ?? 0) +
                                                    ($submission->submissionRating->grammar ?? 0);
                                            }
                                        @endphp
                                        <a class="btn viewScore" data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal">
                                            {{ $totalScore }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            @endif
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Edit Submission'))
                                            <a class="dropdown-item"
                                                href="{{ route('submission.edit', [$society, $conference, $submission]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i>
                                                Edit</a>
                                        @endif
                                        <a class="dropdown-item viewData" data-id="{{ $submission->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                class="icon-base ti tabler-eye me-1"></i>
                                            View</a>

                                        @if (!empty($submission->slide_file))
                                            <a class="dropdown-item"
                                                href="{{ asset('storage/participant/submission/slides/' . $submission->slide_file) }}"
                                                target="_blank">
                                                <i class="icon-base ti tabler-presentation me-1"></i>
                                                View Slides
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('submission.downloadSlide', [$society, $conference, $submission]) }}">
                                                <i class="icon-base ti tabler-download me-1"></i>
                                                Download Slides
                                            </a>
                                        @endif

                                        @if (is_super_admin())
                                            <hr>
                                            <form
                                                action="{{ route('submission.submission.destroy', [$society, $conference, $submission]) }}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                        class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                            </form>
                                        @endif
                                    </div>
                                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Author'))
                                        @if ($submission->authors->isNotEmpty())
                                            <a href="{{ route('submission.author.index', [$society, $conference, $submission]) }}"
                                                class="btn btn-sm btn-success">Authors</a>
                                        @endif
                                    @endif
                                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Discussion'))
                                        @if ($submission->discussions->isNotEmpty())
                                            <span class="mt-2">
                                                <a href="{{ route('submission.viewDiscussion', [$society, $conference, $submission]) }}"
                                                    class="btn btn-sm btn-info mt-2">Discussion</a>
                                            </span>
                                        @endif
                                    @endif
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
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).off("click", ".viewData");
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                var url = '{{ route('submission.show', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    $('#openModal .modal-dialog').removeClass('custom-modal-width');
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

            $(document).off("click", ".expertForward");
            $(document).on("click", ".expertForward", function() {
                var url = '{{ route('submission.expertForwardForm', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };

                $.post(url, data, function(response) {
                    $('#pricingModal .modal-dialog').removeClass('custom-modal-width');
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });


            $(document).off("click", ".sentToAuthot");
            $(document).on("click", ".sentToAuthot", function() {
                var url = '{{ route('submission.sentToAuthorForm', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    $('#openModal .modal-dialog').removeClass('custom-modal-width');
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });


            $(document).off("click", ".convertPresentationTypeRequest");

            $(document).on("click", ".convertPresentationTypeRequest", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure to send convert presentation type request?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Convert!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = $(this).attr('href');
                    }
                })
            });

            $(document).off("click", ".viewScore");
            $(document).on("click", ".viewScore", function(e) {
                e.preventDefault();
                var url = '{{ route('submission.viewScore', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    $('#openModal .modal-dialog').removeClass('custom-modal-width');
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });


            $(document).on("click", ".sendMail", function(e) {
                e.preventDefault();
                var url = '{{ route('submission.sendMail', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    $('#openModal .modal-dialog').removeClass('custom-modal-width');
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });


            function toggleFilterButton() {
                let isAnyFilled = false;

                $('#filterForm select, #filterForm input[type="date"]').each(function() {
                    if ($(this).val() && $(this).val().trim() !== '') {
                        isAnyFilled = true;
                        return false;
                    }
                });

                $('#filterBtn').prop('disabled', !isAnyFilled);
            }

            toggleFilterButton();

            $('#filterForm select, #filterForm input[type="date"]').on('change input', function() {
                toggleFilterButton();
            });

            // Handle color legend badge clicks
            $('.color-filter-badge').on('click', function() {
                var color = $(this).data('color');
                $('#color_filter').val(color);
                form.attr('action', '{{ route('submission.index', [$society, $conference]) }}');
                form.submit();
            });


            var form = $('#filterForm');

            $('#ExportExcelBtn').on('click', function(e) {
                e.preventDefault();
                form.attr('action',
                    '{{ route('submission.export.excel', [$society, $conference]) }}'
                );
                form.submit();
            });

            $('#ExportBtn').on('click', function(e) {
                e.preventDefault();
                form.attr('action',
                    '{{ route('submission.export.word', [$society, $conference]) }}'
                );
                form.submit();
            });

            $('#filterBtn').on('click', function(e) {
                e.preventDefault();
                form.attr('action',
                    '{{ route('submission.index', [$society, $conference]) }}');
                form.submit();
            });

            // Bulk assignment functionality - Store selected submissions across all pages
            var selectedSubmissions = {};  // Store as object: {id: title}
            
            // Handle select all checkbox
            $(document).on('change', '#selectAll', function() {
                var isChecked = this.checked;
                $('.submission-checkbox:visible').each(function() {
                    var id = $(this).data('id');
                    var title = $(this).data('title');
                    
                    $(this).prop('checked', isChecked);
                    
                    if (isChecked) {
                        selectedSubmissions[id] = title;
                    } else {
                        delete selectedSubmissions[id];
                    }
                });
                updateBulkAssignButton();
            });

            // Handle individual checkbox changes - use event delegation for DataTables
            $(document).on('change', '.submission-checkbox', function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                
                if (this.checked) {
                    selectedSubmissions[id] = title;
                } else {
                    delete selectedSubmissions[id];
                    $('#selectAll').prop('checked', false);
                }
                
                updateBulkAssignButton();
                updateSelectAllState();
            });

            // Restore checkbox states when DataTable redraws (pagination, search, etc.)
            $('.datatables-basic').on('draw.dt', function() {
                $('.submission-checkbox').each(function() {
                    var id = $(this).data('id');
                    if (selectedSubmissions.hasOwnProperty(id)) {
                        $(this).prop('checked', true);
                    }
                });
                updateSelectAllState();
            });

            function updateSelectAllState() {
                var visibleCheckboxes = $('.submission-checkbox:visible');
                var visibleCheckedCount = 0;
                
                visibleCheckboxes.each(function() {
                    if ($(this).is(':checked')) {
                        visibleCheckedCount++;
                    }
                });
                
                if (visibleCheckboxes.length > 0 && visibleCheckedCount === visibleCheckboxes.length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
            }

            function updateBulkAssignButton() {
                var selectedCount = Object.keys(selectedSubmissions).length;
                $('#selectedCount').text(selectedCount);
                $('#selectedDeadlineCount').text(selectedCount);
                
                if (selectedCount > 0) {
                    $('#bulkAssignBtn').fadeIn();
                    $('#bulkDeadlineBtn').fadeIn();
                } else {
                    $('#bulkAssignBtn').fadeOut();
                    $('#bulkDeadlineBtn').fadeOut();
                }
            }

            $('#bulkAssignBtn').on('click', function() {
                var selectedIds = Object.keys(selectedSubmissions);
                var selectedTitles = Object.values(selectedSubmissions);
                
                if (selectedIds.length === 0) {
                    notyf.error('Please select at least one submission');
                    return;
                }

                var url = '{{ route('submission.bulkExpertForwardForm', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                
                var data = {
                    _token: _token,
                    ids: selectedIds,
                    titles: selectedTitles
                };
                
                $.post(url, data, function(response) {
                    $('#pricingModal .modal-dialog').removeClass('modal-lg').addClass('modal-xl');
                    setTimeout(function() {
                        $('#modalContent').html(response);
                        $('#pricingModal').modal('show');
                    }, 500);
                });
            });

            $('#bulkDeadlineBtn').on('click', function() {
                var selectedIds = Object.keys(selectedSubmissions);
                var selectedTitles = Object.values(selectedSubmissions);
                
                if (selectedIds.length === 0) {
                    notyf.error('Please select at least one submission');
                    return;
                }

                var url = '{{ route('submission.bulkUpdateDeadlineForm', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                
                var data = {
                    _token: _token,
                    ids: selectedIds,
                    titles: selectedTitles
                };
                
                $.post(url, data, function(response) {
                    $('#pricingModal .modal-dialog').removeClass('modal-lg').addClass('modal-xl');
                    setTimeout(function() {
                        $('#modalContent').html(response);
                        $('#pricingModal').modal('show');
                    }, 500);
                });
            });
        });
    </script>
@endsection
