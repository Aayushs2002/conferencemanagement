<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <!-- Pricing Plans -->
    <div class="rounded-top">
        <h4 class="text-center mb-4">View Data</h4>
        <div class="row closeModal">
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Presenter Name</p>
                <span>{{ $submission->presenter->fullName($submission->presenter) }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Presentation Type</p>
                <span>
                    @if ($submission->presentation_type == 1)
                        Poster
                    @elseif($submission->presentation_type == 2)
                        Oral(Abstract)
                    @else
                        Full Text
                    @endif
                </span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Topic</p>
                <span>{{ $submission->title }}</span>
            </div>
            @if (!empty($submission->keywords))
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Keywords</p>
                    <span>{{ $submission->keywords }}</span>
                </div>
            @endif
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Request Status</p>
                <span>
                    @if ($submission->request_status == 0)
                        <span class="badge bg-primary text-white">Pending</span>
                    @elseif ($submission->request_status == 1)
                        <span class="badge bg-success">Accepted</span>
                    @elseif ($submission->request_status == 3)
                        <span class="badge bg-danger">Rejected</span>
                    @elseif ($submission->request_status == 2)
                        <span class="badge bg-warning">Correction</span>
                    @endif
                </span>
            </div> 

        </div>

        <div class="closeModal">
            @if (!empty($submission->sections))
                {{-- Display sections if they exist --}}
                @foreach ($submission->sections as $index => $section)
                    <div class="mb-4">
                        <p class="text-primary mb-1"><i
                                class="i-Letter-Open text-16 mr-1"></i>{{ $section['name'] ?? 'Section ' . ($index + 1) }}
                        </p>
                        <p>{!! $section['content'] ?? '' !!}</p>
                    </div>
                @endforeach
            @elseif (!empty($submission->abstract_content))
                {{-- Display abstract content if no sections --}}
                <div>
                    <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Abstract Content</p>
                    <p>{!! $submission->abstract_content !!}</p>
                </div>
            @endif

            @if (!empty($submission->conflict_of_interest))
                <div class="mb-4">
                    <p class="text-primary mb-1"><i class="i-Information text-16 mr-1"></i>Conflict of Interest</p>
                    <p>{{ $submission->conflict_of_interest }}</p>
                </div>
            @endif

            @if (!empty($submission->source_of_funding))
                <div class="mb-4">
                    <p class="text-primary mb-1"><i class="i-Money-2 text-16 mr-1"></i>Source of Funding</p>
                    <p>{{ $submission->source_of_funding }}</p>
                </div>
            @endif

            @if (!empty($submission->image))
                <div class="mb-4">
                    <p class="text-primary mb-1"><i class="i-File-Picture text-16 mr-1"></i>Attachment</p>
                    <a href="{{ asset('storage/participant/submission/image/' . $submission->image) }}" target="_blank"
                        class="btn btn-sm btn-primary">
                        <i class="i-Download"></i> View/Download Attachment
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
<!--/ Pricing Plans -->
</div>
