@extends('backend.layouts.conference.main')

@section('title')
    All Payment Statuses
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="ti tabler-credit-card me-2"></i>Payment Status Overview
                </h4>
                <p class="text-muted mb-0">Track card and MoCo payment transactions for {{ $conference->conference_name }}</p>
            </div>
            <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}" class="btn btn-secondary">
                <i class="ti tabler-arrow-left me-1"></i> Back to Registrants
            </a>
        </div>

        <!-- Statistics Cards -->
        @if($paymentStatuses->isNotEmpty())
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti tabler-list ti-md"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $paymentStatuses->count() }}</h5>
                                    <small>Total Attempts</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="ti tabler-check ti-md"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $paymentStatuses->where('payment_status', 'completed')->count() }}</h5>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-danger">
                                        <i class="ti tabler-x ti-md"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $paymentStatuses->where('payment_status', 'failed')->count() }}</h5>
                                    <small>Failed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="ti tabler-clock ti-md"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $paymentStatuses->whereIn('payment_status', ['pending', 'processing'])->count() }}</h5>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="ti tabler-qrcode ti-md"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $paymentStatuses->where('payment_method', 'moco')->count() }}</h5>
                                    <small>MoCo</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-dark">
                                        <i class="ti tabler-x ti-md"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $paymentStatuses->where('payment_status', 'cancelled')->count() }}</h5>
                                    <small>Cancelled</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Payment Transactions</h5>
                <div class="d-flex gap-2 flex-wrap justify-content-end">
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search by name, email, transaction ID..." style="width: 300px;">
                    <select id="statusFilter" class="form-select form-select-sm" style="width: 150px;">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="processing">Processing</option>
                    </select>
                    <select id="methodFilter" class="form-select form-select-sm" style="width: 150px;">
                        <option value="">All Methods</option>
                        <option value="card">Card</option>
                        <option value="moco">MoCo</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                @if($paymentStatuses->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover" id="paymentStatusTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User Details</th>
                                    <th>Date/Time</th>
                                    <th>Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentStatuses as $status)
                                    <tr data-status="{{ $status->payment_status }}" data-method="{{ $status->payment_method ?? 'card' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $status->user->userDetail->namePrefix->prefix ?? '' }} {{ $status->user->fullName($status->user) }}</strong>
                                            </div>
                                            <small class="text-muted">
                                                <i class="ti tabler-mail ti-xs me-1"></i>{{ $status->user->email }}
                                            </small>
                                            @if($status->user->userDetail->country)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="ti tabler-map-pin ti-xs me-1"></i>{{ $status->user->userDetail->country->country_name }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <i class="ti tabler-calendar ti-xs me-1"></i>
                                                {{ $status->payment_initiated_at ? $status->payment_initiated_at->format('M d, Y') : $status->created_at->format('M d, Y') }}
                                            </div>
                                            <small class="text-muted">
                                                <i class="ti tabler-clock ti-xs me-1"></i>
                                                {{ $status->payment_initiated_at ? $status->payment_initiated_at->format('h:i A') : $status->created_at->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($status->amount)
                                                <strong>{{ $status->currency ?? 'USD' }} {{ number_format($status->amount, 2) }}</strong>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($status->transaction_id)
                                                <code class="bg-light p-1 rounded">{{ Str::limit($status->transaction_id, 20) }}</code>
                                            @else
                                                <span class="text-muted">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $status->payment_method === 'moco' ? 'info' : 'primary' }}">
                                                <i class="ti {{ $status->payment_method === 'moco' ? 'tabler-qrcode' : 'tabler-credit-card' }} me-1"></i>{{ ucfirst($status->payment_method ?? 'Card') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $status->statusBadge }}">
                                                {{ ucfirst($status->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-primary" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#details-{{ $status->id }}"
                                                    aria-expanded="false"
                                                    title="View Details">
                                                <i class="ti tabler-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="details-{{ $status->id }}">
                                        <td colspan="8" class="bg-light">
                                            <div class="p-3">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <h6 class="mb-3">
                                                            <i class="ti tabler-info-circle me-2"></i>Payment Timeline
                                                        </h6>
                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td style="width: 140px;"><strong>Initiated:</strong></td>
                                                                <td>{{ $status->payment_initiated_at ? $status->payment_initiated_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Completed:</strong></td>
                                                                <td>{{ $status->payment_completed_at ? $status->payment_completed_at->format('M d, Y h:i A') : 'Pending' }}</td>
                                                            </tr>
                                                            @if($status->payment_completed_at && $status->payment_initiated_at)
                                                                <tr>
                                                                    <td><strong>Duration:</strong></td>
                                                                    <td>{{ $status->payment_initiated_at->diffForHumans($status->payment_completed_at, true) }}</td>
                                                                </tr>
                                                            @endif
                                                        </table>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <h6 class="mb-3">
                                                            <i class="ti tabler-user me-2"></i>User Information
                                                        </h6>
                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td style="width: 100px;"><strong>Phone:</strong></td>
                                                                <td>{{ $status->user->userDetail->phone ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Institution:</strong></td>
                                                                <td>{{ $status->user->userDetail->institution->name ?? 'N/A' }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-4">
                                                        @if($status->error_message)
                                                            <div class="alert alert-danger mb-0">
                                                                <h6 class="alert-heading mb-2">
                                                                    <i class="ti tabler-alert-triangle me-2"></i>Error
                                                                </h6>
                                                                <p class="mb-0 small">{{ $status->error_message }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($status->payment_response)
                                                    <div class="mt-3">
                                                        <h6 class="mb-2">
                                                            <i class="ti tabler-code me-2"></i>Gateway Response
                                                        </h6>
                                                        <div class="bg-white border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                                            <pre class="mb-0" style="font-size: 10px; white-space: pre-wrap;">{{ $status->payment_response }}</pre>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ti tabler-file-off" style="font-size: 64px; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">No Payment Records Found</h5>
                        <p class="text-muted">There are no payment status records for this conference yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Search functionality
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                filterTable();
            });

            // Status filter
            $('#statusFilter').on('change', function() {
                filterTable();
            });

            $('#methodFilter').on('change', function() {
                filterTable();
            });

            function filterTable() {
                var searchValue = $('#searchInput').val().toLowerCase();
                var statusValue = $('#statusFilter').val().toLowerCase();
                var methodValue = $('#methodFilter').val().toLowerCase();

                $('#paymentStatusTable tbody tr:not(.collapse)').filter(function() {
                    var text = $(this).text().toLowerCase();
                    var status = $(this).data('status');
                    var method = $(this).data('method');
                    
                    var matchesSearch = searchValue === '' || text.indexOf(searchValue) > -1;
                    var matchesStatus = statusValue === '' || status === statusValue;
                    var matchesMethod = methodValue === '' || method === methodValue;
                    
                    $(this).toggle(matchesSearch && matchesStatus && matchesMethod);
                });
            }

            // Smooth scroll on expand
            $('[data-bs-toggle="collapse"]').on('click', function() {
                const $this = $(this);
                const target = $this.attr('data-bs-target');
                
                setTimeout(function() {
                    if ($(target).hasClass('show')) {
                        $('html, body').animate({
                            scrollTop: $this.closest('tr').offset().top - 100
                        }, 300);
                    }
                }, 350);
            });
        });
    </script>
@endsection
