@extends('backend.layouts.conference.main')

@section('title')
    Submission
@endsection
@section('content')
    <div class="card border my-4 container">
        <h5 class="pt-3">Filter By:</h5>
        <form method="GET" action="{{ route('submission.index', [$society, $conference]) }}" id="filterForm">
            <div class="row">
                <div class="col-md-3 form-group mb-3">
                    <label for="article_type" class="mb-2">Article Type</label>
                    <select name="article_type" id="article_type"
                        class="form-control @error('article_type') is-invalid @enderror">
                        <option value="">-- Select Article Type --</option>
                        <option {{ request()->article_type == 1 ? 'selected' : '' }} value="1">
                            Orginal
                        </option>
                        <option {{ request()->article_type == 2 ? 'selected' : '' }} value="2">
                            Review
                        </option>
                    </select>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="presentation_type" class="mb-2">Presnetation Type</label>
                    <select name="presentation_type" id="presentation_type"
                        class="form-control @error('presentation_type') is-invalid @enderror">
                        <option value="">-- Select Presentation Type --</option>
                        <option {{ request()->presentation_type == 2 ? 'selected' : '' }} value="2">
                            Oral
                        </option>
                        <option {{ request()->presentation_type === 1 ? 'selected' : '' }} value="1">
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
                    <label for="submission_category_major_track_id" class="mb-2">Submission Track/Category</label>
                    <select name="submission_category_major_track_id" id="submission_category_major_track_id"
                        class="form-control @error('submission_category_major_track_id') is-invalid @enderror">
                        <option value="">-- Select Submission Track/Category --</option>
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

                <div class="row my-4">
                    <div class="col-12 text-end">
                        <a href="{{ route('submission.index', [$society, $conference]) }}" class="btn btn-danger">Reset</a>
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
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th scope="col">Speaker Name</th>
                        <th>Article Type</th>
                        <th>Major Track/Category</th>
                        <th>Topic</th>
                        <th>Presentation Type</th>
                        <th>Request Status</th>
                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Expert Assign'))
                            <th>Assign to Expert ?</th>
                        @endif
                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Score'))
                            <th>Score</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($submissions as $submission)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $submission->presenter->fullName($submission->presenter) }}</td>

                            <td> {{ $submission->article_type == 1 ? 'Original' : 'Review' }}
                            </td>
                            <td>
                                {{ $submission->submissionCategoryMajorTrack->title }}
                            </td>
                            <td>
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
                                        @else
                                            <a href="{{ route('submission.convertPresentationTypeRequest', [$society, $conference, $submission->id]) }}"
                                                class="btn btn-sm btn-primary convertPresentationTypeRequest mt-1"><span
                                                    style="font-size: 10px;">Convert
                                                    To
                                                    Oral</span></a>
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
                                        <button class="btn btn-sm btn-primary sentToAuthot" data-id="{{ $submission->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal">Pending</button>
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
                                    @endif
                                </td>
                            @endif
                            @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Score'))
                                <td>
                                    @if ($submission->submissionRating?->introduction)
                                        <a class="btn viewScore" data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal">
                                            {{ $submission->submissionRating->introduction + $submission->submissionRating->method + $submission->submissionRating->result + $submission->submissionRating->conclusion + $submission->submissionRating->grammar }}
                                        </a>
                                    @elseif ($submission->submissionRating?->overallrating)
                                        <a class="btn viewScore" data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal">
                                            {{ $submission->submissionRating->overallrating }}
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
                                            <a class="dropdown-item" href=""><i
                                                    class="icon-base ti tabler-pencil me-1"></i>
                                                Edit</a>
                                        @endif
                                        <a class="dropdown-item viewData" data-id="{{ $submission->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                class="icon-base ti tabler-eye me-1"></i>
                                            View</a>
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


            var form = $('#filterForm');

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
        });
    </script>
@endsection
