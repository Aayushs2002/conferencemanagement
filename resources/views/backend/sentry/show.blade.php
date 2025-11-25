@extends('backend.layouts.main')

@section('content')
    <style>
        .issue-detail-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin: 15px 0;
        }

        .stacktrace-frame {
            background: #f8f9fa;
            border-left: 3px solid #667eea;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .stacktrace-frame:hover {
            background: #e9ecef;
        }

        .context-line {
            padding: 2px 10px;
            margin: 2px 0;
            border-radius: 3px;
        }

        .context-line.highlight {
            background: #fff3cd;
            border-left: 3px solid #ffc107;
            font-weight: bold;
        }

        .event-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .event-item:hover {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .tag-badge {
            background: #f1f3f5;
            padding: 5px 12px;
            border-radius: 15px;
            margin: 3px;
            display: inline-block;
            font-size: 12px;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('sentry.index') }}">Sentry Monitoring</a></li>
                <li class="breadcrumb-item active">Issue Details</li>
            </ol>
        </nav>

        @if (empty($issue))
            <div class="alert alert-danger">
                Unable to load issue details. Please check your Sentry configuration.
            </div>
            <a href="{{ route('sentry.index') }}" class="btn btn-primary">
                <i class="ti tabler-arrow-left"></i> Back to Issues
            </a>
        @else
            <!-- Issue Header -->
            <div class="issue-detail-card">
                <div class="row align-items-center mb-4">
                    <div class="col-md-8">
                        <h3 class="mb-2">
                            @if ($issue['level'] == 'error')
                                <i class="ti tabler-circle-x text-danger fs-2"></i>
                            @elseif($issue['level'] == 'warning')
                                <i class="ti tabler-alert-triangle text-warning fs-2"></i>
                            @else
                                <i class="ti tabler-info-circle text-info fs-2"></i>
                            @endif
                            {{ $issue['title'] ?? 'Unknown Error' }}
                        </h3>
                        <p class="text-muted mb-0">{{ $issue['culprit'] ?? 'No culprit information' }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('sentry.index') }}" class="btn btn-outline-primary">
                            <i class="ti tabler-arrow-left"></i> Back to Issues
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <small class="text-muted d-block">Status</small>
                            <span
                                class="badge bg-{{ $issue['status'] == 'resolved' ? 'success' : ($issue['status'] == 'ignored' ? 'secondary' : 'danger') }}">
                                {{ ucfirst($issue['status'] ?? 'unresolved') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <small class="text-muted d-block">Level</small>
                            <strong>{{ ucfirst($issue['level'] ?? 'error') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <small class="text-muted d-block">First Seen</small>
                            <strong>{{ isset($issue['firstSeen']) ? \Carbon\Carbon::parse($issue['firstSeen'])->format('M d, Y H:i') : 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <small class="text-muted d-block">Last Seen</small>
                            <strong>{{ isset($issue['lastSeen']) ? \Carbon\Carbon::parse($issue['lastSeen'])->diffForHumans() : 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">Total Events</small>
                            <h4 class="mb-0">{{ $issue['count'] ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">Users Affected</small>
                            <h4 class="mb-0">{{ $issue['userCount'] ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">Platform</small>
                            <h4 class="mb-0">{{ $issue['platform'] ?? 'php' }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            @if (isset($issue['metadata']) && isset($issue['metadata']['value']))
                <div class="issue-detail-card">
                    <h5 class="mb-3"><i class="ti tabler-message-circle"></i> Error Message</h5>
                    <div class="alert alert-danger">
                        <strong>{{ $issue['metadata']['type'] ?? 'Error' }}:</strong>
                        {{ $issue['metadata']['value'] }}
                    </div>
                </div>
            @endif

            <!-- Tags -->
            @if (isset($issue['tags']) && is_array($issue['tags']) && count($issue['tags']) > 0)
                <div class="issue-detail-card">
                    <h5 class="mb-3"><i class="ti tabler-tag"></i> Tags</h5>
                    <div>
                        @foreach ($issue['tags'] as $tag)
                            <span class="tag-badge">
                                <strong>{{ $tag['key'] ?? 'key' }}:</strong> {{ $tag['value'] ?? 'N/A' }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Events -->
            <div class="issue-detail-card">
                <h5 class="mb-3"><i class="ti tabler-activity"></i> Recent Events ({{ count($events) }})</h5>
                @if (count($events) > 0)
                    @foreach ($events as $event)
                        <div class="event-item">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1">{{ $event['title'] ?? $event['message'] ?? 'Event' }}</h6>
                                    <small class="text-muted">
                                        Event ID: {{ $event['eventID'] ?? $event['id'] }}
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <small class="text-muted">
                                        {{ isset($event['dateCreated']) ? \Carbon\Carbon::parse($event['dateCreated'])->diffForHumans() : 'N/A' }}
                                    </small>
                                </div>
                            </div>

                            @if (isset($event['context']))
                                <div class="mt-2">
                                    <small class="text-muted">Context:</small>
                                    <div class="code-block">
                                        {{ json_encode($event['context'], JSON_PRETTY_PRINT) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center py-3">No recent events available</p>
                @endif
            </div>

            <!-- Stacktrace -->
            @if (isset($issue['entries']) && is_array($issue['entries']) && count($issue['entries']) > 0)
                @foreach ($issue['entries'] as $entry)
                    @if (isset($entry['type']) && $entry['type'] == 'exception' && isset($entry['data']['values']) && is_array($entry['data']['values']))
                        <div class="issue-detail-card">
                            <h5 class="mb-3"><i class="ti tabler-stack"></i> Stack Trace</h5>
                            @foreach ($entry['data']['values'] as $exception)
                                <div class="mb-4">
                                    <h6 class="text-danger">{{ $exception['type'] ?? 'Exception' }}: {{ $exception['value'] ?? '' }}</h6>
                                    @if (isset($exception['stacktrace']['frames']) && is_array($exception['stacktrace']['frames']))
                                        @foreach (array_reverse($exception['stacktrace']['frames']) as $frame)
                                            <div class="stacktrace-frame">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $frame['filename'] ?? 'Unknown file' }}</strong>
                                                        @if (isset($frame['lineNo']))
                                                            <span class="text-muted">Line {{ $frame['lineNo'] }}</span>
                                                        @endif
                                                    </div>
                                                    <span class="badge bg-info">{{ $frame['function'] ?? 'N/A' }}</span>
                                                </div>
                                                @if (isset($frame['context']) && is_array($frame['context']))
                                                    <div class="code-block mt-2">
                                                        @foreach ($frame['context'] as $lineNum => $lineCode)
                                                            <div class="context-line {{ isset($frame['lineNo']) && $lineNum == $frame['lineNo'] ? 'highlight' : '' }}">
                                                                <span class="text-muted">{{ $lineNum }}:</span>
                                                                {{ $lineCode }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            @endif

            <!-- Actions -->
            <div class="issue-detail-card">
                <h5 class="mb-3"><i class="ti tabler-settings"></i> Actions</h5>
                <div class="btn-group" role="group">
                    @if ($issue['status'] != 'resolved')
                        <button type="button" class="btn btn-success resolve-issue"
                            data-issue-id="{{ $issue['id'] }}">
                            <i class="ti tabler-check"></i> Mark as Resolved
                        </button>
                    @endif
                    @if ($issue['status'] != 'ignored')
                        <button type="button" class="btn btn-secondary ignore-issue" data-issue-id="{{ $issue['id'] }}">
                            <i class="ti tabler-eye-off"></i> Ignore Issue
                        </button>
                    @endif
                    @if (isset($issue['permalink']))
                        <a href="{{ $issue['permalink'] }}" target="_blank" class="btn btn-primary">
                            <i class="ti tabler-external-link"></i> View in Sentry
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Resolve issue
            $('.resolve-issue').click(function() {
                const issueId = $(this).data('issue-id');
                const $btn = $(this);

                if (confirm('Are you sure you want to mark this issue as resolved?')) {
                    $btn.prop('disabled', true).html('<i class="ti tabler-loader"></i> Resolving...');

                    $.ajax({
                        url: `/sentry/issues/${issueId}/resolve`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                notyf.success(response.message);
                                setTimeout(() => window.location.href = '{{ route('sentry.index') }}',
                                    1500);
                            } else {
                                notyf.error(response.message);
                                $btn.prop('disabled', false).html(
                                    '<i class="ti tabler-check"></i> Mark as Resolved');
                            }
                        },
                        error: function() {
                            notyf.error('Failed to resolve issue');
                            $btn.prop('disabled', false).html(
                                '<i class="ti tabler-check"></i> Mark as Resolved');
                        }
                    });
                }
            });

            // Ignore issue
            $('.ignore-issue').click(function() {
                const issueId = $(this).data('issue-id');
                const $btn = $(this);

                if (confirm('Are you sure you want to ignore this issue?')) {
                    $btn.prop('disabled', true).html('<i class="ti tabler-loader"></i> Ignoring...');

                    $.ajax({
                        url: `/sentry/issues/${issueId}/ignore`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                notyf.success(response.message);
                                setTimeout(() => window.location.href = '{{ route('sentry.index') }}',
                                    1500);
                            } else {
                                notyf.error(response.message);
                                $btn.prop('disabled', false).html(
                                    '<i class="ti tabler-eye-off"></i> Ignore Issue');
                            }
                        },
                        error: function() {
                            notyf.error('Failed to ignore issue');
                            $btn.prop('disabled', false).html(
                                '<i class="ti tabler-eye-off"></i> Ignore Issue');
                        }
                    });
                }
            });
        });
    </script>
@endsection
