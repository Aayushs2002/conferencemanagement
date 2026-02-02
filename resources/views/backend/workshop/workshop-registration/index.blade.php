@extends('backend.layouts.conference.main')

@section('title')
    Registered Attendees
@endsection
@section('content')
    <div class="card mb-6">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Registered Attendees (Workshop:
                        {{ $workshop->workshop_title }})
                    </h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <button type="button" class="btn btn-secondary dropdown-toggle me-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti tabler-sort-ascending me-1"></i>
                            <span class="d-none d-sm-inline-block">Sort by Name</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request('sort') == 'name_asc' ? 'active' : '' }}" href="{{ route('workshop.workshop-registration.index', [$society, $conference, $workshop, 'sort' => 'name_asc']) }}">
                                    <i class="ti tabler-sort-ascending me-2"></i> Name (A-Z)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('sort') == 'name_desc' ? 'active' : '' }}" href="{{ route('workshop.workshop-registration.index', [$society, $conference, $workshop, 'sort' => 'name_desc']) }}">
                                    <i class="ti tabler-sort-descending me-2"></i> Name (Z-A)
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ !request('sort') ? 'active' : '' }}" href="{{ route('workshop.workshop-registration.index', [$society, $conference, $workshop]) }}">
                                    <i class="ti tabler-refresh me-2"></i> Default (Recent First)
                                </a>
                            </li>
                        </ul>
                        <button type="button" class="btn btn-success dropdown-toggle me-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-none d-sm-inline-block">Generate Pass</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('workshop.generatePass', ['workshop' => $workshop, 'registrant_type' => 1]) }}">
                                    <i class="ti tabler-users me-2"></i> Generate for Registered Users
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#dummyPassModal">
                                    <i class="ti tabler-user-plus me-2"></i> Generate Dummy Pass
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div> 
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th scope="col">Registrant Name</th>
                        <th scope="col">Member Type</th>
                        <th scope="col">Transaction ID</th>
                        <th scope="col">Payment Type/Payment Voucher</th>
                        <th scope="col">Meal Type</th>
                        <th scope="col">Verified Status</th>
                        <th scope="col">Remarks</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registrations as $registration)
                        <tr>
                            @php 
                                $userSociety = $registration->user?->societies->first();
                                $memberType = $userSociety?->pivot?->memberType;
                            @endphp
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $registration->user?->fullName($registration->user) }}</td>
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
                                @elseif (!empty($registration->payment_voucher) && $registration->payment_type == 6)
                                    @php
                                        $extension = explode('.', $registration->payment_voucher);
                                    @endphp
                                    @if ($extension[1] == 'pdf')
                                        <a href="{{ asset('storage/workshop/payment-voucher/' . $registration->payment_voucher) }}"
                                            target="_blank"><img src="{{ asset('default-image/pdf.png') }}" height="60"
                                                alt="voucher"></a>
                                    @else
                                        <a href="{{ asset('storage/workshop/payment-voucher/' . $registration->payment_voucher) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/workshop/payment-voucher/' . $registration->payment_voucher) }}"
                                                height="60" alt="voucher"></a>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($registration->meal_type == 1)
                                    Veg
                                @elseif ($registration->meal_type == 2)
                                    Non-Veg
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
                                        data-bs-toggle="modal" data-bs-target="#pricingModal"><span
                                            class="badge bg-warning">Unverified</span></a>
                                @endif
                            </td>
                            <td>{{ !empty($registration->remarks) ? $registration->remarks : '-' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Workshop'))
                                            <a class="dropdown-item"
                                                href="{{ route('workshop.workshop-registration.edit', [$society, $conference, $workshop, $registration->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        <a class="dropdown-item viewData" data-id="{{ $registration->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                class="icon-base ti tabler-eye me-1 "></i> View</a>
                                        <a class="dropdown-item"
                                            href="{{ route('workshop.workshop-registration.downloadVoucher', [$society, $conference, $registration->id]) }}"><i
                                                class="icon-base ti tabler-ticket me-1"></i> Downlaod Payment Voucher</a>
                                        {{-- @if ($registration->is_dummy == 1) --}}
                                            <hr>
                                            <form action="{{ route('workshop.workshop-registration.destroy', [$society, $conference, $workshop, $registration->id]) }}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                        class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                            </form>
                                        {{-- @endif --}}
                                    </div>

                                </div>
                            </td>
                            {{-- <td><a href="{{ route('workshop-registration.generateCertificate', $registration->id) }}" class="btn btn-info btn-sm mt-1" target="_blank"><i class="nav-icon i-File"></i> Generate</a></td> --}}
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>

        <!-- Dummy Pass Generation Modal -->
        <div class="modal fade" id="dummyPassModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Dummy Pass</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('workshop.generateDummyPass', ['workshop' => $workshop]) }}" method="POST" target="_blank">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="dummy_count" class="form-label">Number of Dummy Passes <code>*</code></label>
                                <input type="number" class="form-control" id="dummy_count" name="dummy_count" 
                                       min="1" max="100" value="1" required>
                                <small class="text-muted">Enter the number of dummy passes to generate (Max: 100)</small>
                            </div>
                            <input type="hidden" name="registrant_type" value="1">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Generate Passes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on("click", ".verifyRegistrant", function(e) {
                e.preventDefault();
                $(".modal-dialog").removeClass('custom-modal-width');
                var url =
                    '{{ route('workshop.workshop-registration.verifyForm', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    $('#modalContent').html(response);
                });
            });
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                $(".modal-dialog").removeClass('custom-modal-width');
                var url =
                    '{{ route('workshop.workshop-registration.view', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    $('#modalContent').html(response);
                });
            });
        });
    </script>
@endsection
