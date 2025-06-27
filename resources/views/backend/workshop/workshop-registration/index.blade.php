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
                        <a href="{{ route('workshop.generatePass', ['workshop' => $workshop, 'registrant_type' => 1]) }}"
                            class="btn btn-success me-2" tabindex="0">
                            <span class="d-none d-sm-inline-block">Generate Pass</span>
                        </a>
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
                        <th scope="col">Verified Status</th>
                        <th scope="col">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registrations as $registration)
                        <tr>
                            @php
                                $userSociety = $registration->user->societies->first();
                                $memberType = $userSociety?->pivot?->memberType;
                            @endphp
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $registration->user->fullName($registration->user) }}</td>
                            <td>{{ $memberType->type ?? 'N/A' }}</td>

                            <td>{{ $registration->transaction_id }}</td>
                            <td>
                                @if ($registration->payment_type == 1)
                                    Fone-Pay
                                @elseif ($registration->payment_type == 2)
                                    Card Payment
                                @elseif (!empty($registration->payment_voucher) && $registration->payment_type == 3)
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
                                @if ($registration->verified_status == 1)
                                    <span class="badge bg-success">Verified</span>
                                @elseif ($registration->verified_status == 2)
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    @if ($registration->workshop->user_id == auth()->user()->id || auth()->user()->role == 2)
                                        <a href="#" class="verifyRegistrant" data-id="{{ $registration->id }}"
                                            data-toggle="modal" data-target="#openModal"><span
                                                class="badge bg-warning">Unverified</span></a>
                                    @else
                                        <span class="badge bg-warning">Unverified</span>
                                    @endif
                                @endif
                            </td>
                            <td>{{ !empty($registration->remarks) ? $registration->remarks : '-' }}</td>
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
    </div>
@endsection
