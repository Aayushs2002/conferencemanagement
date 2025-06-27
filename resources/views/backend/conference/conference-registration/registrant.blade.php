@extends('backend.layouts.conference.main')

@section('title')
    Registrant
@endsection
@section('content')
    <div class=" card border my-4 container">
        <h5 class="pt-3">Filter By:</h5>
        <form method="GET" action="{{ route('conference.conference-registration.index', [$society, $conference]) }}"
            id="filterForm">
            <div class="row">
                <div class="col-md-3 form-group mb-3">
                    <label for="registrant_type" class="mb-2">Registration Type</label>
                    <select name="registrant_type" id="registrant_type"
                        class="form-control @error('registrant_type') is-invalid @enderror">
                        <option value="">-- Select Registrant Type --</option>
                        <option {{ request()->registrant_type == 1 ? 'selected' : '' }} value="1">
                            Attendee 
                        </option>
                        <option {{ request()->registrant_type == 2 ? 'selected' : '' }} value="2">
                            Speaker
                        </option>
                        <option {{ request()->registrant_type == 3 ? 'selected' : '' }} value="3">
                            Session Chair
                        </option>
                        <option {{ request()->registrant_type == 4 ? 'selected' : '' }} value="4">
                            Special Guest
                        </option>
                    </select>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="is_invited" class="mb-2">Invited</label>
                    <select name="is_invited" id="is_invited"
                        class="form-control @error('is_invited') is-invalid @enderror">
                        <option value="">-- Select Invited Type --</option>
                        <option {{ request()->is_invited == 1 ? 'selected' : '' }} value="1">
                            Yes
                        </option>
                        <option {{ request()->is_invited === 0 ? 'selected' : '' }} value="0">
                            No
                        </option>
                    </select>
                </div>
                <div class="col-md-2 form-group mb-3">
                    <label for="payment_type" class="mb-2">Payment Type</label>
                    <select name="payment_type" id="payment_type"
                        class="form-control @error('payment_type') is-invalid @enderror">
                        <option value="">-- Select Payment Type --</option>
                        <option {{ request()->payment_type == 1 ? 'selected' : '' }} value="1">
                            Fone Pay
                        </option>
                        <option {{ request()->payment_type == 2 ? 'selected' : '' }} value="2">
                            Moco
                        </option>
                        <option {{ request()->payment_type == 3 ? 'selected' : '' }} value="3">
                            Esewa
                        </option>
                        <option {{ request()->payment_type == 4 ? 'selected' : '' }} value="3">
                            Khalti
                        </option>
                        <option {{ request()->payment_type == 5 ? 'selected' : '' }} value="3">
                            Card Payment
                        </option>
                        <option {{ request()->payment_type == 6 ? 'selected' : '' }} value="3">
                            Voucher Payment
                        </option>
                    </select>
                </div>
                <div class="col-md-2 form-group mb-3">
                    <label for="from" class="mb-2">From</label>
                    <input type="date" value="{{ request('from') }}"
                        class="form-control @error('from') is-invalid @enderror" id="from" name="from" />
                </div>
                <div class="col-md-2 form-group mb-3">
                    <label for="to" class="mb-2">To</label>
                    <input type="date" value="{{ request('to') }}"
                        class="form-control @error('to') is-invalid @enderror" id="to" name="to" />
                </div>
                <div class="col-md-3 mt-2  form-group mb-3">
                    <label for="country_id" class="mb-2">Country <code>*</code></label>
                    <select class="form-control select2" name="country_id" id="country_id">
                        <option value="">-- Select Country --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" @selected(request('country_id') == $country->id)>
                                {{ $country->country_name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="row my-4">
                    <div class="col-12 text-end">
                        <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}"
                            class="btn btn-danger">Reset</a>
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Export'))
                            <button type="submit" id="ExportBtn" class="btn btn-success">Export</button>
                        @endif
                        <button type="submit" id="filterBtn" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Conference Registrant</h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="icon-base ti tabler-upload icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Export</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportTo('excel')">Export to
                                        Excel</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="exportTo('pdf')">Export to PDF</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="exportTo('csv')">Export to CSV</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="window.print()">Print</a></li>
                            </ul>
                        </div>
                        {{-- <a href="{{ route('conference.create') }}" class="btn btn-primary" tabindex="0">
                              <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                              <span class="d-none d-sm-inline-block">Add New</span>
                          </a> --}}
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Application Name</th>
                        <th>Membership Type</th>
                        {{-- <th>Email</th> --}}
                        <th>Payment Type/Voucher</th>
                        <th>Transaction ID</th>
                        <th>Registraton Type</th>
                        <th>No. of people</th>
                        <th>Is Verified?</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registrants as $registrant)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $registrant->user->fullName($registrant->user) }}</td>
                            @php
                                $userSociety = $registrant->user->societies->first();
                                $memberType = $userSociety?->pivot?->memberType;
                            @endphp
                            <td>{{ $memberType->type ?? 'N/A' }}</td>
                            {{-- <td>{{ $registrant->user->email }}</td> --}}
                            <td>

                                @if ($registrant->payment_type == 1)
                                    Fone-Pay
                                @elseif ($registrant->payment_type == 2)
                                    Moco Payment
                                @elseif ($registrant->payment_type == 3)
                                    Esewa
                                @elseif ($registrant->payment_type == 4)
                                    Khalti
                                @elseif ($registrant->payment_type == 5)
                                    Card Payment
                                @elseif (!empty($registrant->payment_voucher) && $registrant->payment_type == 3)
                                    {{-- @dd($registrant->payment_voucher) --}}
                                    @php
                                        $explodeFileName = explode('.', $registrant->payment_voucher);
                                    @endphp
                                    @if ($explodeFileName[1] == 'pdf')
                                        <a href="{{ asset('storage/conference/registration/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank"><img src="{{ asset('default-image/pdf.png') }}"
                                                alt="voucher" height="50" width="40"></a>
                                    @else
                                        <a href="{{ asset('storage/conference/registration/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/conference/registration/payment-voucher/' . $registrant->payment_voucher) }}"
                                                alt="voucher" height="50" width="40"></a>
                                    @endif
                                @else
                                    Payment Voucher
                                @endif
                            </td>
                            <td>{{ $registrant->transaction_id ?? '-' }}</td>
                            <td>
                                @if ($registrant->registrant_type == 1)
                                    Attendee
                                @elseif ($registrant->registrant_type == 2)
                                    Speaker
                                @elseif ($registrant->registrant_type == 3)
                                    Session Chair
                                @elseif ($registrant->registrant_type == 4)
                                    Special Guest
                                @endif
                                @if ($registrant->is_invited == 1)
                                    <span title="Invited"
                                        style="background-color: green; color: white; padding: 8px; height: 6px; width: 6px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">
                                        I
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">{{ $registrant->total_attendee }}<br>
                                @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add People'))
                                    <button class="btn btn-sm btn-primary addPerson mt-1" data-id="{{ $registrant->id }}"
                                        data-bs-toggle="modal" data-bs-target="#pricingModal" type="submit">
                                        <span style="font-size: 9px;">
                                            Add Person
                                        </span>
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if ($registrant->verified_status == 1)
                                    <span class="badge bg-success">Verified</span>
                                @elseif ($registrant->verified_status == 2)
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <a href="" class="verifyRegistrant" data-id="{{ $registrant->id }}"
                                        data-toggle="modal" data-target="#openModal" title="Verify Registrant"><span
                                            class="badge bg-warning">Unverified</span></a>
                                @endif
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Conference Registration'))
                                            <a class="dropdown-item" href=""><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        <a class="dropdown-item viewData" data-id="{{ $registrant->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                class="icon-base ti tabler-eye me-1 "></i> View</a>
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Convert Registrant Type'))
                                            <a class="dropdown-item convertRegistrantType"
                                                data-id="{{ $registrant->id }}" data-bs-toggle="modal"
                                                data-bs-target="#pricingModal"><i
                                                    class="icon-base ti tabler-replace me-1 "></i> Convert Registrant
                                                Type</a>
                                        @endif
                                        <a class="dropdown-item"
                                            href="{{ route('conference.conference-registration.generateIndividualPass', [$society, $conference, $registrant->id]) }}"><i
                                                class="icon-base ti tabler-ticket me-1"></i> Generate Pass</a>
                                    </div>

                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on("click", ".addPerson", function(e) {
                e.preventDefault();
                var url =
                    '{{ route('conference.conference-registration.addPerson', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
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
                $('#pricingModal .modal-dialog').removeClass('modal-lg');
                $('#pricingModal .modal-dialog').addClass('modal-md');
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

            $(document).on("click", ".convertRegistrantType", function(e) {
                e.preventDefault();
                var url =
                    '{{ route('conference.conference-registration.convertRegistrantType', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
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

                $('#pricingModal .modal-dialog').removeClass('modal-lg');
                $('#pricingModal .modal-dialog').addClass('modal-md');

                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                var url =
                    '{{ route('conference.conference-registration.show', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
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

                $('#pricingModal .modal-dialog').removeClass('modal-md');
                $('#pricingModal .modal-dialog').addClass('modal-lg');

                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

            function toggleFilterButton() {
                let isAnyFilled = false;

                $('#filterForm select, #filterForm input[type="date"]').each(function() {
                    if ($(this).val() && $(this).val().trim() !== '') {
                        isAnyFilled = true;
                        return false;
                    }
                });

                $('#filterBtn').prop('disabled', !isAnyFilled);
            }

            toggleFilterButton();

            $('#filterForm select, #filterForm input[type="date"]').on('change input', function() {
                toggleFilterButton();
            });
            var form = $('#filterForm');

            $('#ExportBtn').on('click', function(e) {
                e.preventDefault();
                form.attr('action',
                    '{{ route('conference.conference-registration.excelExport', [$society, $conference]) }}'
                );
                form.submit();
            });

            $('#filterBtn').on('click', function(e) {
                e.preventDefault();
                form.attr('action',
                    '{{ route('conference.conference-registration.index', [$society, $conference]) }}');
                form.submit();
            });
        });
    </script>
@endsection
