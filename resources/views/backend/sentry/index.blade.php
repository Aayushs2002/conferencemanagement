@extends('backend.layouts.main')

@section('content')
    <style>
        .sentry-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .issue-card {
            background: white;
            border-left: 4px solid #dc3545;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .issue-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transform: translateX(5px);
        }

        .issue-card.resolved {
            border-left-color: #28a745;
            opacity: 0.7;
        }

        .issue-card.ignored {
            border-left-color: #6c757d;
            opacity: 0.6;
        }

        .badge-custom {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .filter-tabs {
            background: white;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-tabs .nav-link {
            border-radius: 8px;
            padding: 10px 20px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .filter-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .event-timeline {
            position: relative;
            padding-left: 30px;
        }

        .event-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            border: 2px solid white;
            box-shadow: 0 0 0 2px #667eea;
        }

        .code-snippet {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }

        .severity-high {
            color: #dc3545;
        }

        .severity-medium {
            color: #ffc107;
        }

        .severity-low {
            color: #28a745;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state img {
            max-width: 300px;
            opacity: 0.6;
            margin-bottom: 20px;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Dashboard Header -->
        <div class="sentry-dashboard">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2 text-white">
                        <i class="ti tabler-bug"></i> Sentry Error Monitoring
                    </h2>
                    <p class="mb-0">Monitor and track application errors in real-time</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('sentry.clear-cache') }}" class="btn btn-light">
                        <i class="ti tabler-refresh"></i> Refresh Data
                    </a>
                </div>
            </div>
        </div>

        @if (isset($error))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> {{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="card">
                <div class="card-body text-center py-5">
                    <h5>Setup Instructions</h5>
                    <p class="text-muted">Please add the following to your <code>.env</code> file:</p>
                    <div class="code-snippet text-start mx-auto" style="max-width: 600px;">
                        SENTRY_AUTH_TOKEN=your_auth_token_here<br>
                        SENTRY_ORG_SLUG=your_organization_slug<br>
                        SENTRY_PROJECT_SLUG=your_project_slug
                    </div>
                    <p class="text-muted mt-3">
                        <small>Get your auth token from: <a href="https://sentry.io/settings/account/api/auth-tokens/"
                                target="_blank">Sentry API Tokens</a></small>
                    </p>
                </div>
            </div>
        @else
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="ti tabler-alert-triangle text-danger fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 text-danger">{{ count($issues) }}</h3>
                                <p class="text-muted mb-0">Total Issues</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="ti tabler-clock text-warning fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 text-warning">
                                    {{ collect($issues)->where('status', 'unresolved')->count() }}
                                </h3>
                                <p class="text-muted mb-0">Unresolved</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="ti tabler-check text-success fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 text-success">
                                    {{ collect($issues)->where('status', 'resolved')->count() }}
                                </h3>
                                <p class="text-muted mb-0">Resolved</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="ti tabler-activity text-info fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 text-info">{{ $stats['name'] ?? 'N/A' }}</h3>
                                <p class="text-muted mb-0">Project</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ $filter == 'unresolved' ? 'active' : '' }}"
                            href="{{ route('sentry.index', ['filter' => 'unresolved']) }}">
                            <i class="ti tabler-alert-circle"></i> Unresolved
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filter == 'resolved' ? 'active' : '' }}"
                            href="{{ route('sentry.index', ['filter' => 'resolved']) }}">
                            <i class="ti tabler-circle-check"></i> Resolved
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filter == 'ignored' ? 'active' : '' }}"
                            href="{{ route('sentry.index', ['filter' => 'ignored']) }}">
                            <i class="ti tabler-eye-off"></i> Ignored
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filter == 'all' ? 'active' : '' }}"
                            href="{{ route('sentry.index', ['filter' => 'all']) }}">
                            <i class="ti tabler-list"></i> All Issues
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Issues List -->
            <div class="row">
                <div class="col-12">
                    @if (count($issues) > 0)
                        @foreach ($issues as $issue)
                            <div class="issue-card {{ $issue['status'] ?? '' }}">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-start mb-2">
                                            <div class="me-3">
                                                @if ($issue['level'] == 'error')
                                                    <i class="ti tabler-circle-x severity-high fs-3"></i>
                                                @elseif($issue['level'] == 'warning')
                                                    <i class="ti tabler-alert-triangle severity-medium fs-3"></i>
                                                @else
                                                    <i class="ti tabler-info-circle severity-low fs-3"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">
                                                    <a href="{{ route('sentry.show', $issue['id']) }}"
                                                        class="text-dark text-decoration-none">
                                                        {{ $issue['title'] ?? 'Unknown Error' }}
                                                    </a>
                                                </h5>
                                                <p class="text-muted mb-2 small">
                                                    {{ $issue['culprit'] ?? 'No culprit information' }}
                                                </p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <span class="badge bg-{{ $issue['status'] == 'resolved' ? 'success' : ($issue['status'] == 'ignored' ? 'secondary' : 'danger') }}">
                                                        {{ ucfirst($issue['status'] ?? 'unresolved') }}
                                                    </span>
                                                    <span class="badge bg-info">{{ ucfirst($issue['level'] ?? 'error') }}</span>
                                                    @if (isset($issue['metadata']['type']))
                                                        <span class="badge bg-warning">{{ $issue['metadata']['type'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="mb-2">
                                            <span class="text-muted small">
                                                <i class="ti tabler-users"></i>
                                                {{ $issue['userCount'] ?? 0 }} users affected
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-muted small">
                                                <i class="ti tabler-clock"></i>
                                                {{ isset($issue['lastSeen']) ? \Carbon\Carbon::parse($issue['lastSeen'])->diffForHumans() : 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('sentry.show', $issue['id']) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="ti tabler-eye"></i> View
                                            </a>
                                            @if ($issue['status'] != 'resolved')
                                                <button type="button" class="btn btn-sm btn-success resolve-issue"
                                                    data-issue-id="{{ $issue['id'] }}">
                                                    <i class="ti tabler-check"></i> Resolve
                                                </button>
                                            @endif
                                            @if ($issue['status'] != 'ignored')
                                                <button type="button" class="btn btn-sm btn-secondary ignore-issue"
                                                    data-issue-id="{{ $issue['id'] }}">
                                                    <i class="ti tabler-eye-off"></i> Ignore
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="mb-4">
                                <i class="ti tabler-circle-check text-success" style="font-size: 100px;"></i>
                            </div>
                            <h4 class="mb-2">No Issues Found</h4>
                            <p class="text-muted">
                                @if ($filter == 'unresolved')
                                    Great! There are no unresolved issues at the moment.
                                @elseif($filter == 'resolved')
                                    No resolved issues to display.
                                @elseif($filter == 'ignored')
                                    No ignored issues to display.
                                @else
                                    No issues have been reported yet.
                                @endif
                            </p>
                        </div>
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
                const $card = $btn.closest('.issue-card');

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
                                $card.addClass('resolved');
                                $btn.remove();
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                notyf.error(response.message);
                                $btn.prop('disabled', false).html(
                                    '<i class="ti tabler-check"></i> Resolve');
                            }
                        },
                        error: function() {
                            notyf.error('Failed to resolve issue');
                            $btn.prop('disabled', false).html('<i class="ti tabler-check"></i> Resolve');
                        }
                    });
                }
            });

            // Ignore issue
            $('.ignore-issue').click(function() {
                const issueId = $(this).data('issue-id');
                const $btn = $(this);
                const $card = $btn.closest('.issue-card');

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
                                $card.addClass('ignored');
                                $btn.remove();
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                notyf.error(response.message);
                                $btn.prop('disabled', false).html(
                                    '<i class="ti tabler-eye-off"></i> Ignore');
                            }
                        },
                        error: function() {
                            notyf.error('Failed to ignore issue');
                            $btn.prop('disabled', false).html('<i class="ti tabler-eye-off"></i> Ignore');
                        }
                    });
                }
            });
        });
    </script>
@endsection
