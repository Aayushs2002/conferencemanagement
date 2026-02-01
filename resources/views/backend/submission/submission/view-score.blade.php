<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Submission Score <span class="text-danger">(Topic:
                {{ $submission->title }})</span></h5>
        <hr class="py-4">

        <div class="row closeModal">
            @if (
                !empty($articleTypeSections) &&
                    is_array($articleTypeSections) &&
                    !empty($submission->submissionRating?->section_ratings))
                {{-- Section-based ratings --}}
                <div class="col-12 mb-3">
                    <h6 class="text-primary"><i class="ti tabler-report-analytics me-2"></i>Section-Based Scores</h6>
                </div>

                {{-- Title Rating --}}
                @if ($submission->submissionRating->title_rating !== null)
                    <div class="col-md-4 mb-4">
                        <p class="text-primary mb-1"><i class="ti tabler-heading text-16 me-1"></i>Title Rating</p>
                        <span class="badge bg-label-warning">{{ $submission->submissionRating->title_rating }}</span>
                    </div>
                @endif

                @foreach ($submission->submissionRating->section_ratings as $index => $sectionRating)
                    <div class="col-md-4 mb-4">
                        <p class="text-primary mb-1"><i
                                class="ti tabler-file-text text-16 me-1"></i>{{ $sectionRating['name'] ?? 'Section ' . ($index + 1) }}
                        </p>
                        <span class="badge bg-label-primary">{{ $sectionRating['rating'] ?? 'N/A' }}</span>
                    </div>
                @endforeach

                {{-- <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="ti tabler-language text-16 me-1"></i>Grammar/Languages</p>
                    <span class="badge bg-label-primary">{{ $submission->submissionRating->grammar ?? 'N/A' }}</span>
                </div> --}}
                @if ($submission->submissionRating->overall_rating)
                    <div class="col-md-4 mb-4">
                        <p class="text-primary mb-1"><i class="ti tabler-star text-16 me-1"></i>Overall Rating</p>
                        <span class="badge bg-label-primary">{{ $submission->submissionRating->overall_rating }}</span>
                    </div>
                @endif

                @php
                    $totalSectionScore =
                        collect($submission->submissionRating->section_ratings)->sum('rating') +
                        ($submission->submissionRating->title_rating ?? 0) +
                        ($submission->submissionRating->grammar ?? 0) +
                        ($submission->submissionRating->overall_rating ?? 0);
                @endphp

                <div class="col-12 mt-3">
                    <hr>
                    <h6 class="text-success">Total Score: <strong>{{ $totalSectionScore }}</strong></h6>
                </div>
            @elseif($submission->submissionRating?->overall_rating)
                {{-- Overall rating (when structure checkbox was used) --}}
                <div class="col-md-12 mb-4">
                    <p class="text-primary mb-1"><i class="ti tabler-star text-16 me-1"></i>Overall Rating</p>
                    <span class="badge bg-label-success fs-5">{{ $submission->submissionRating->overall_rating }}</span>
                </div>
            @else
                {{-- Default ratings (Introduction, Method, Result, Conclusion, Grammar) --}}
                <div class="col-12 mb-3">
                    <h6 class="text-primary"><i class="ti tabler-report-analytics me-2"></i>Detailed Scores</h6>
                </div>

                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="ti tabler-file-info text-16 me-1"></i>Introduction/Background
                    </p>
                    <span
                        class="badge bg-label-primary">{{ $submission->submissionRating->introduction ?? 'N/A' }}</span>
                </div>
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="ti tabler-test-pipe text-16 me-1"></i>Methods</p>
                    <span class="badge bg-label-primary">{{ $submission->submissionRating->method ?? 'N/A' }}</span>
                </div>
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="ti tabler-chart-bar text-16 me-1"></i>Result/Findings</p>
                    <span class="badge bg-label-primary">{{ $submission->submissionRating->result ?? 'N/A' }}</span>
                </div>
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="ti tabler-check text-16 me-1"></i>Conclusion</p>
                    <span class="badge bg-label-primary">{{ $submission->submissionRating->conclusion ?? 'N/A' }}</span>
                </div>
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="ti tabler-language text-16 me-1"></i>Grammar/Languages</p>
                    <span class="badge bg-label-primary">{{ $submission->submissionRating->grammar ?? 'N/A' }}</span>
                </div>

                @if ($submission->submissionRating)
                    @php
                        $totalScore =
                            ($submission->submissionRating->introduction ?? 0) +
                            ($submission->submissionRating->method ?? 0) +
                            ($submission->submissionRating->result ?? 0) +
                            ($submission->submissionRating->conclusion ?? 0) +
                            ($submission->submissionRating->grammar ?? 0);
                    @endphp

                    <div class="col-12 mt-3">
                        <hr>
                        <h6 class="text-success">Total Score: <strong>{{ $totalScore }}</strong></h6>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
