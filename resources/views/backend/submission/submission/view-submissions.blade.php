@extends('backend.layouts.conference.main')

@section('title')
    View Submissions
@endsection

@section('content')
    <style>
        .submission-container {
            font-family: 'Times New Roman', Times, serif;
        }
        .submission-container .abstract-content,
        .submission-container .section-content {
            text-align: justify;
            text-justify: inter-word;
        }
    </style>
    
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

                <div class="row my-4">
                    <div class="col-12 text-end">
                        <a href="{{ route('submission.viewSubmissions', [$society, $conference]) }}"
                            class="btn btn-danger">Reset</a>
                        <button type="submit" id="filterBtn" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="container submission-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Submissions ({{ $submissions->count() }})</h2>
            <button class="btn btn-success" id="copy-all-btn">
                <i class="ti tabler-copy me-1"></i>Copy All
            </button>
        </div>

        @forelse ($submissions as $submission)
            <div class="card my-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex gap-2 mb-2">
                                <label class="fw-bold">Article Type:</label>
                                <h6 class="mb-0">{{ $submission->articleType?->name ?? 'N/A' }}</h6>
                            </div>
                            <div class="d-flex gap-2">
                                <label class="fw-bold">Submission Track:</label>
                                <h6 class="mb-0">{{ $submission->submissionCategoryMajorTrack?->title ?? 'N/A' }}</h6>
                            </div>
                        </div>
                        <button class="btn btn-primary copy-btn" data-target="submission-{{ $submission->id }}">
                            <i class="ti tabler-copy me-1"></i>Copy
                        </button>
                    </div>

                    <div id="submission-{{ $submission->id }}">
                        <p style="font-size: 25px; font-weight: 700;" class="mb-3">
                            {{ $submission->presentation_type == 1 ? 'Poster Submission' : 'Oral Submission' }}
                        </p>
                        <h4 style="font-size: 25px; font-weight: 700;" class="mb-3">{{ $submission->title }}</h4>

                        @php
                            $names = '';
                            $affiliationList = [];
                            $superscripts = [
                                '¹',
                                '²',
                                '³',
                                '⁴',
                                '⁵',
                                '⁶',
                                '⁷',
                                '⁸',
                                '⁹',
                                '¹⁰',
                                '¹¹',
                                '¹²',
                                '¹³',
                                '¹⁴',
                                '¹⁵',
                                '¹⁶',
                                '¹⁷',
                                '¹⁸',
                                '¹⁹',
                                '²⁰',
                            ];

                            $groupedAuthors = $submission->authors->groupBy([
                                'designation',
                                'institution',
                                'institution_address',
                            ]);
                            $duplicatedData = [];
                            $nonDuplicatedData = [];
                            $i = 1;

                            foreach ($groupedAuthors as $designationGroup) {
                                foreach ($designationGroup as $institutionGroup) {
                                    foreach ($institutionGroup as $addressGroup) {
                                        foreach ($addressGroup as $record) {
                                            $data = [
                                                'designation' =>
                                                    $record->designation == 'null' ?? '' ? '' : $record->designation,
                                                'institution' => $record->institution ?? '',
                                                'institution_address' => $record->institution_address ?? '',
                                                'countValue' => $superscripts[$i - 1] ?? $i,
                                            ];

                                            if ($addressGroup->count() > 1) {
                                                $duplicatedData[$record->name][] = $data;
                                            } else {
                                                $nonDuplicatedData[$record->name] = $data;
                                            }
                                        }
                                        $i++;
                                    }
                                }
                            }

                            $uniqueValues = [];
                            foreach ($duplicatedData as $key => $value) {
                                $names .= $key . ' ' . $value[0]['countValue'] . ', ';
                                if (!in_array($value[0]['countValue'], $uniqueValues)) {
                                    $affiliationList[] = "<strong>{$value[0]['countValue']}</strong> {$value[0]['designation']}, {$value[0]['institution']}, {$value[0]['institution_address']}";
                                    $uniqueValues[] = $value[0]['countValue'];
                                }
                            }

                            foreach ($nonDuplicatedData as $key => $value) {
                                $names .= $key . ' ' . $value['countValue'] . ', ';
                                $affiliationList[] = "<strong>{$value['countValue']}</strong> {$value['designation']}, {$value['institution']}, {$value['institution_address']}";
                            }

                            $names = rtrim($names, ', ');
                        @endphp

                        <p class="mb-2">{!! $names !!}</p>
                        <p class="mb-4">{!! implode('<br>', $affiliationList) !!}</p>

                        <h4 style="font-size: 20px; font-weight: 800" class="mb-3">Correspondence</h4>
                        @if ($submission->authors->where('main_author', 1)->first())
                            @php $mainAuthor = $submission->authors->where('main_author', 1)->first(); @endphp
                            <p class="mb-1"><strong>{{ $mainAuthor->name }}</strong></p>
                            <p class="mb-1">{{ $mainAuthor->designation }}</p>
                            <p class="mb-1">{{ $mainAuthor->institution }}</p>
                            <p class="mb-1">{{ $mainAuthor->institution_address }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $mainAuthor->email }}</p>
                            <p class="mb-4"><strong>Phone:</strong> {{ $mainAuthor->phone }}</p>
                        @endif

                        {{-- Check if submission has sections or abstract --}}
                        @if (!empty($submission->sections) && is_array($submission->sections))
                            {{-- Display sections --}}
                            @foreach ($submission->sections as $index => $section)
                                <h4 style="font-size: 20px; font-weight: 800" class="mb-3">
                                    {{ $section['name'] ?? 'Section ' . ($index + 1) }}
                                </h4>
                                <div class="mb-4 section-content">{!! $section['content'] ?? 'No content' !!}</div>
                            @endforeach
                        @else
                            {{-- Display abstract content --}}
                            <h4 style="font-size: 20px; font-weight: 800" class="mb-3">Abstract</h4>
                            <div class="mb-4 abstract-content">{!! $submission->abstract_content !!}</div>
                        @endif

                        <p class="mb-0">
                            <strong style="font-size: 16px; font-weight: 800">Keywords:</strong>
                            {{ $submission->keywords }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ti tabler-folder-off" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-3">No submissions found. Try adjusting your filters.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Individual copy buttons
            document.querySelectorAll('.copy-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let targetId = this.getAttribute('data-target');
                    let content = document.getElementById(targetId).innerHTML;

                    copyToClipboard(content);

                    // Change button text temporarily
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="ti tabler-check me-1"></i>Copied!';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');

                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-primary');
                    }, 1500);
                });
            });

            // Copy all button
            document.getElementById('copy-all-btn').addEventListener('click', function() {
                let allContent = '';
                document.querySelectorAll('[id^="submission-"]').forEach(submission => {
                    allContent += submission.innerHTML + '\n\n<hr>\n\n';
                });

                copyToClipboard(allContent);

                // Change button text temporarily
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="ti tabler-check me-1"></i>All Copied!';
                this.classList.remove('btn-success');
                this.classList.add('btn-primary');

                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');
                }, 1500);
            });

            function copyToClipboard(content) {
                let tempDiv = document.createElement('div');
                tempDiv.innerHTML = content;
                tempDiv.style.position = 'absolute';
                tempDiv.style.left = '-9999px';

                // Remove background colors and classes from all elements
                const allElements = tempDiv.querySelectorAll('*');
                allElements.forEach(element => {
                    element.style.backgroundColor = '';
                    element.style.background = '';
                    element.classList.remove('card', 'card-body', 'table-success', 'table-warning',
                        'table-danger', 'bg-success', 'bg-warning', 'bg-danger', 'bg-primary',
                        'bg-info', 'bg-secondary');
                });

                document.body.appendChild(tempDiv);

                let range = document.createRange();
                range.selectNodeContents(tempDiv);
                let selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);

                try {
                    document.execCommand('copy');
                } catch (err) {
                    console.error('Failed to copy:', err);
                }

                document.body.removeChild(tempDiv);
                selection.removeAllRanges();
            }

            // Filter form validation
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
        });
    </script>
@endsection
