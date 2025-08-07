<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h5 class=" mb-4 " style="background: white;">Login History<span class="text-danger">(User
                Name:
                {{ $user->fullName($user) }})</span></h5>
        <div class="card">

            <div class="card-datatable table-responsive pt-0">
               
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Country</th>
                            <th>Region</th>
                            <th>City</th>
                            <th>Browser</th>
                            <th>OS</th>
                            <th>Login</th>
                            <th>Logout</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $history)
                            <tr>
                                <td>{{ $history->ip_address }}</td>
                                <td>{{ $history->country }}</td>
                                <td>{{ $history->region }}</td>
                                <td>{{ $history->city }}</td>
                                <td>{{ $history->browser }}</td>
                                <td>{{ $history->os }}</td>
                                <td>{{ $history->logged_in_at }}</td>
                                <td>{{ $history->logged_out_at ?? '-' }}</td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
