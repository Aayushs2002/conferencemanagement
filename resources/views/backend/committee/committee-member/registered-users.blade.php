<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class="text-center mb-4 " style="background: white;">User Registered Under {{$committee_member->user->fullName($committee_member->user)}}</h4>
        <table class="datatables-basic table">
            <thead>
                <tr>
                    {{-- <th>User</th> --}}
                    <th>Action</th>
                    <th>Description</th>
                    {{-- <th>IP Address</th> --}}
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activity_logs as $log)
                    <tr>
                        {{-- <td>{{ $log->user->fullName($log->user) }}</td> --}}
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->description }}</td>
                        {{-- <td>{{ $log->ip_address }}</td> --}}
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
