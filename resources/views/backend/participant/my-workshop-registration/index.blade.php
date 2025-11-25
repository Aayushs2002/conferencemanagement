@extends('backend.layouts.conference.main')

@section('title')
    Workshop Registrants - {{ $workshop->workshop_title }}
@endsection

@section('content')
    <div class="card mb-6">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">
                        Workshop Registrants
                        <br><small class="text-muted">Workshop: {{ $workshop->workshop_title }}</small>
                    </h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <a href="{{ route('my-society.conference.my-workshop.index', [$society, $conference]) }}"
                            class="btn btn-secondary" tabindex="0">
                            <i class="icon-base ti tabler-arrow-left icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Back to Workshops</span>
                        </a>
                    </div>
                </div>
            </div>
            
            @if($registrations->isEmpty())
                <div class="text-center py-5">
                    <i class="icon-base ti tabler-user-off mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h5 class="text-muted">No registrations yet</h5>
                    <p class="text-muted">Registrations will appear here when users register for this workshop.</p>
                </div>
            @else
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Registrant Name</th>
                            <th>Member Type</th>
                            <th>Transaction ID</th>
                            <th>Payment Type</th>
                            <th>Meal Type</th>
                            <th>Verified Status</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registrations as $registration)
                            <tr>
                                @php 
                                    $userSociety = $registration->user->societies->first();
                                    $memberType = $userSociety?->pivot?->memberType;
                                @endphp
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $registration->user->fullName($registration->user) }}</td>
                                <td>{{ $memberType->type ?? 'N/A' }}</td>
                                <td>{{ $registration->transaction_id }}</td>
                                <td>
                                    @if ($registration->payment_type == 1)
                                        Fone-Pay
                                    @elseif ($registration->payment_type == 2)
                                        Moco
                                    @elseif ($registration->payment_type == 3)
                                        Esewa
                                    @elseif ($registration->payment_type == 4)
                                        Khalti
                                    @elseif ($registration->payment_type == 5)
                                        Card Payment
                                    @elseif ($registration->payment_type == 6)
                                        Bank Transfer
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if ($registration->meal_type == 1)
                                        <span class="badge bg-success">Veg</span>
                                    @elseif ($registration->meal_type == 2)
                                        <span class="badge bg-warning">Non-Veg</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if ($registration->verified_status == 1)
                                        <span class="badge bg-success">Verified</span>
                                    @elseif ($registration->verified_status == 2)
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <a href="#" class="verifyRegistrant" data-id="{{ $registration->id }}"
                                            data-bs-toggle="modal" data-bs-target="#verifyModal">
                                            <span class="badge bg-warning">Pending</span>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $registration->remarks ?? '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item viewData" data-id="{{ $registration->id }}"
                                                data-bs-toggle="modal" data-bs-target="#viewModal">
                                                <i class="icon-base ti tabler-eye me-1"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        
        <!-- View Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="viewModalContent">
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Verify Modal -->
        <div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="verifyModalContent">
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // View registration details
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                var url = '{{ route('my-society.conference.my-workshop.registration.view', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                $('#viewModalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    $('#viewModalContent').html(response);
                });
            });
            
            // Verify registrant
            $(document).on("click", ".verifyRegistrant", function(e) {
                e.preventDefault();
                var url = '{{ route('my-society.conference.my-workshop.registration.verifyForm', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                $('#verifyModalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    $('#verifyModalContent').html(response);
                });
            });
        });
    </script>
@endsection
