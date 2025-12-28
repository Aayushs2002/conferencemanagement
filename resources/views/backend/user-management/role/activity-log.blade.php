<div class="modal-header">
    <h5 class="modal-title" id="pricingModalLabel">User Registered Under - {{ $user->fullName($user) }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    @if ($activityLogs->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        {{-- <th>User Agent</th> --}}
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activityLogs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address ?? 'N/A' }}</td>
                            {{-- <td>{{ Str::limit($log->user_agent ?? 'N/A', 50) }}</td> --}}
                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">
            <i class="icon-base ti tabler-info-circle me-2"></i>
            No User Found Registered Under {{ $user->fullName($user) }}.
        </div>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
