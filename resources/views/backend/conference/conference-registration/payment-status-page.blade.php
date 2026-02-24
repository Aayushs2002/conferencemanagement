@extends('backend.layouts.conference.main')

@section('title')
    Payment Status - {{ $registrant->user->fullName($registrant->user) }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}" class="btn btn-secondary">
                <i class="ti tabler-arrow-left me-1"></i> Back to Registrants
            </a>
        </div>

        <!-- Registrant Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="ti tabler-user me-2"></i>Registrant Information
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <p class="text-muted mb-1"><strong>Name:</strong></p>
                        <p class="mb-0">{{ $registrant->user->userDetail->namePrefix->prefix ?? '' }} {{ $registrant->user->fullName($registrant->user) }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="text-muted mb-1"><strong>Email:</strong></p>
                        <p class="mb-0">{{ $registrant->user->email }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="text-muted mb-1"><strong>Registration ID:</strong></p>
                        <p class="mb-0">{{ $registrant->registration_id ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="text-muted mb-1"><strong>Registrant Type:</strong></p>
                        <p class="mb-0">
                            @if ($registrant->registrant_type == 1)
                                Attendee
                            @elseif ($registrant->registrant_type == 2)
                                Speaker
                            @elseif ($registrant->registrant_type == 3)
                                Session Chair
                            @elseif ($registrant->registrant_type == 4)
                                Special Guest
                            @elseif ($registrant->registrant_type == 5)
                                Organizer
                            @elseif ($registrant->registrant_type == 6)
                                Faculty
                            @elseif ($registrant->registrant_type == 7)
                                Volunteer
                            @endif
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <p class="text-muted mb-1"><strong>Phone:</strong></p>
                        <p class="mb-0">{{ $registrant->user->userDetail->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="text-muted mb-1"><strong>Country:</strong></p>
                        <p class="mb-0">{{ $registrant->user->userDetail->country->country_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="text-muted mb-1"><strong>Institution:</strong></p>
                        <p class="mb-0">{{ $registrant->user->userDetail->institution->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <p class="text-muted mb-1"><strong>Registration Date:</strong></p>
                        <p class="mb-0">{{ $registrant->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status History Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="ti tabler-history me-2"></i>Payment Status History
                </h4>
                @if($paymentStatuses->isNotEmpty())
                    <div>
                        <span class="badge bg-label-primary me-2">
                            <i class="ti tabler-list me-1"></i>{{ $paymentStatuses->count() }} Attempt(s)
                        </span>
                        <span class="badge bg-label-success me-2">
                            <i class="ti tabler-check me-1"></i>{{ $paymentStatuses->where('payment_status', 'completed')->count() }} Completed
                        </span>
                        <span class="badge bg-label-danger">
                            <i class="ti tabler-x me-1"></i>{{ $paymentStatuses->where('payment_status', 'failed')->count() }} Failed
                        </span>
                    </div>
                @endif
            </div>
            <div class="card-body">
                @if($paymentStatuses->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date/Time</th>
                                    <th>Payment Method</th>
                                    <th>Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentStatuses as $status)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div>
                                                <i class="ti tabler-calendar me-1"></i>
                                                <strong>{{ $status->payment_initiated_at ? $status->payment_initiated_at->format('M d, Y') : $status->created_at->format('M d, Y') }}</strong>
                                            </div>
                                            <small class="text-muted">
                                                <i class="ti tabler-clock me-1"></i>
                                                {{ $status->payment_initiated_at ? $status->payment_initiated_at->format('h:i A') : $status->created_at->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="ti tabler-credit-card me-1"></i>{{ ucfirst($status->payment_method ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($status->amount)
                                                <strong>{{ $status->currency ?? '' }} {{ number_format($status->amount, 2) }}</strong>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($status->transaction_id)
                                                <code class="bg-light p-1 rounded">{{ $status->transaction_id }}</code>
                                            @else
                                                <span class="text-muted">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $status->statusBadge }}">
                                                {{ ucfirst($status->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#details-{{ $status->id }}"
                                                    aria-expanded="false">
                                                <i class="ti tabler-eye me-1"></i>View Details
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="details-{{ $status->id }}">
                                        <td colspan="7" class="bg-light">
                                            <div class="p-4">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="mb-3">
                                                            <i class="ti tabler-info-circle me-2"></i>Payment Timeline
                                                        </h6>
                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td style="width: 150px;"><strong>Payment Initiated:</strong></td>
                                                                <td>{{ $status->payment_initiated_at ? $status->payment_initiated_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Payment Completed:</strong></td>
                                                                <td>{{ $status->payment_completed_at ? $status->payment_completed_at->format('M d, Y h:i A') : 'Pending' }}</td>
                                                            </tr>
                                                            @if($status->payment_completed_at && $status->payment_initiated_at)
                                                                <tr>
                                                                    <td><strong>Processing Time:</strong></td>
                                                                    <td>{{ $status->payment_initiated_at->diffForHumans($status->payment_completed_at, true) }}</td>
                                                                </tr>
                                                            @endif
                                                        </table>
                                                    </div>
                                                    <div class="col-md-6">
                                                        @if($status->error_message)
                                                            <div class="alert alert-danger mb-3">
                                                                <h6 class="alert-heading mb-2">
                                                                    <i class="ti tabler-alert-triangle me-2"></i>Error Details
                                                                </h6>
                                                                <p class="mb-0">{{ $status->error_message }}</p>
                                                            </div>
                                                        @endif
                                                        
                                                        @if($status->payment_response)
                                                            <h6 class="mb-3">
                                                                <i class="ti tabler-code me-2"></i>Gateway Response
                                                            </h6>
                                                            <div class="bg-white border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                                                <pre class="mb-0" style="font-size: 11px; white-space: pre-wrap;">{{ $status->payment_response }}</pre>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
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
                        <h5 class="mt-3 text-muted">No Payment History Found</h5>
                        <p class="text-muted">There are no payment records for this registrant yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Add smooth scrolling to collapse elements
            $('[data-bs-toggle="collapse"]').on('click', function() {
                const $this = $(this);
                const target = $this.attr('data-bs-target');
                
                setTimeout(function() {
                    if ($(target).hasClass('show')) {
                        $('html, body').animate({
                            scrollTop: $this.offset().top - 100
                        }, 300);
                    }
                }, 350);
            });
        });
    </script>
@endsection
