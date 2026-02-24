<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class="mb-4" style="background: white;">
            <i class="ti tabler-credit-card me-2"></i>Payment Status Details
        </h4>
        
        <!-- Registrant Basic Info -->
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="ti tabler-user me-2"></i>Registrant Information
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <p class="text-primary mb-1"><i class="ti tabler-user text-14 me-1"></i>Name</p>
                        <span>{{ $registrant->user->fullName($registrant->user) }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="text-primary mb-1"><i class="ti tabler-mail text-14 me-1"></i>Email</p>
                        <span>{{ $registrant->user->email }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="text-primary mb-1"><i class="ti tabler-id text-14 me-1"></i>Registration ID</p>
                        <span>{{ $registrant->registration_id ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status Timeline -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="ti tabler-history me-2"></i>Payment History
                </h5>
                
                @if($paymentStatuses->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
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
                                        <td>
                                            <small>
                                                <i class="ti tabler-calendar me-1"></i>
                                                {{ $status->payment_initiated_at ? $status->payment_initiated_at->format('M d, Y') : $status->created_at->format('M d, Y') }}
                                                <br>
                                                <i class="ti tabler-clock me-1"></i>
                                                {{ $status->payment_initiated_at ? $status->payment_initiated_at->format('h:i A') : $status->created_at->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst($status->payment_method ?? 'N/A') }}
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
                                            <small class="font-monospace">{{ $status->transaction_id ?? 'Pending' }}</small>
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
                                                <i class="ti tabler-eye me-1"></i>Details
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="details-{{ $status->id }}">
                                        <td colspan="6" class="bg-light">
                                            <div class="p-3">
                                                <h6 class="mb-3">Payment Details</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-2">
                                                            <strong>Payment Initiated:</strong> 
                                                            {{ $status->payment_initiated_at ? $status->payment_initiated_at->format('M d, Y h:i A') : 'N/A' }}
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong>Payment Completed:</strong> 
                                                            {{ $status->payment_completed_at ? $status->payment_completed_at->format('M d, Y h:i A') : 'Pending' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        @if($status->error_message)
                                                            <div class="alert alert-danger py-2">
                                                                <strong>Error:</strong><br>
                                                                {{ $status->error_message }}
                                                            </div>
                                                        @endif
                                                        @if($status->payment_response)
                                                            <p class="mb-2">
                                                                <strong>Gateway Response:</strong>
                                                                <pre class="bg-white p-2 rounded" style="font-size: 11px; max-height: 150px; overflow-y: auto;">{{ $status->payment_response }}</pre>
                                                            </p>
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

                    <!-- Summary Card -->
                    <div class="alert alert-info mt-3">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Total Attempts:</strong> {{ $paymentStatuses->count() }}
                            </div>
                            <div class="col-md-4">
                                <strong>Completed:</strong> {{ $paymentStatuses->where('payment_status', 'completed')->count() }}
                            </div>
                            <div class="col-md-4">
                                <strong>Failed:</strong> {{ $paymentStatuses->where('payment_status', 'failed')->count() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="ti tabler-alert-circle me-2"></i>
                        No payment history found for this registrant.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
