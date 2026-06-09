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
                        @foreach ($registrantTypes as $rType)
                            <option {{ request()->registrant_type == $rType->id ? 'selected' : '' }} value="{{ $rType->id }}">
                                {{ $rType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="prefix" class="mb-2">Prefix</label>
                    <select name="prefix" id="prefix" class="form-control @error('prefix') is-invalid @enderror">
                        <option value="">-- Select Prefix --</option>
                        @foreach ($name_prefiexs as $name_prefiex)
                            <option {{ request()->prefix == $name_prefiex->id ? 'selected' : '' }}
                                value="{{ $name_prefiex->id }}">
                                {{ $name_prefiex->prefix }}
                            </option> 
                        @endforeach
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
                        <option {{ request()->payment_type == 4 ? 'selected' : '' }} value="4">
                            Khalti
                        </option>
                        <option {{ request()->payment_type == 5 ? 'selected' : '' }} value="5">
                            Card Payment
                        </option>
                        <option {{ request()->payment_type == 6 ? 'selected' : '' }} value="6">
                            Voucher Payment
                        </option>
                        <option {{ request()->payment_type == 7 ? 'selected' : '' }} value="7">
                            ConnectIPS
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
                <div class="col-md-3  form-group mb-3">
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
                <div class="col-md-2 form-group mb-3">
                    <label for="country_scope" class="mb-2">Nationality Scope</label>
                    <select name="country_scope" id="country_scope" class="form-control">
                        <option value="">-- Select Scope --</option>
                        <option value="national" {{ request('country_scope') === 'national' ? 'selected' : '' }}>National</option>
                        <option value="international" {{ request('country_scope') === 'international' ? 'selected' : '' }}>International</option>
                    </select>
                </div>
                <div class="col-md-3 form-group mb-3">
                    <label for="sort_by" class="mb-2">Sort By</label>
                    <select name="sort_by" id="sort_by" class="form-control @error('sort_by') is-invalid @enderror">
                        <option value="">-- Select Sort Order --</option>
                        <option {{ request()->sort_by == 'name_asc' ? 'selected' : '' }} value="name_asc">
                            Name (A-Z)
                        </option>
                        <option {{ request()->sort_by == 'name_desc' ? 'selected' : '' }} value="name_desc">
                            Name (Z-A)
                        </option>
                        <option {{ request()->sort_by == 'latest' ? 'selected' : '' }} value="latest">
                            Latest Registration
                        </option>
                        <option {{ request()->sort_by == 'oldest' ? 'selected' : '' }} value="oldest">
                            Oldest Registration
                        </option>
                        {{-- <option {{ request()->sort_by == 'amount_asc' ? 'selected' : '' }} value="amount_asc">
                            Amount (Low to High)
                        </option>
                        <option {{ request()->sort_by == 'amount_desc' ? 'selected' : '' }} value="amount_desc">
                            Amount (High to Low)
                        </option> --}}
                    </select>
                </div>
                <div class="row my-4">
                    <div class="col-12 text-end">
                        <a href="{{ route('conference.conference-registration.index', [$society, $conference]) }}"
                            class="btn btn-danger">Reset</a>
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Export'))
                            <button type="submit" id="ExportBtn" class="btn btn-success">
                                <i class="icon-base ti tabler-upload icon-xs me-sm-1"></i>
                                Export CSV</button>
                        @endif
                        <button type="submit" id="PassBtn" class="btn btn-warning" target="_blank">Pass</button>
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
                        <!-- Bulk Convert Button (Shows when registrations are selected) -->
                        <button type="button" id="bulkConvertSelectionBtn" class="btn btn-success" 
                                data-bs-toggle="modal" data-bs-target="#bulkConvertRegistrantTypeModal"
                                style="display: none;">
                            <i class="ti tabler-user-check me-2"></i>
                            <span>Convert Selected</span>
                            <span id="bulkConvertBtnCount" class="badge bg-light text-dark ms-2">0</span>
                        </button>
                        {{-- <div class="btn-group me-2">
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
                        </div> --}}
                        {{-- <a href="{{ route('conference.create') }}" class="btn btn-primary" tabindex="0">
                              <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                              <span class="d-none d-sm-inline-block">Add New</span>
                          </a> --}}
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-base ti tabler-user-plus icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Actions</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button type="button" id="importRegistrant" data-bs-toggle="modal" data-bs-target="#pricingModal"
                                        class="dropdown-item">
                                        <i class="ti tabler-file-import me-2"></i> Import Registrant
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#dummyPassModal">
                                        <i class="ti tabler-user-plus me-2"></i> Generate Dummy Pass
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('conference.conference-registration.bulkEmail', [$society, $conference]) }}">
                                        <i class="ti tabler-mail me-2"></i> Send Bulk Email
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('conference.conference-registration.allPaymentStatuses', [$society, $conference]) }}">
                                        <i class="ti tabler-credit-card me-2"></i> View All Payment Statuses
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form id="updateRegistrationIdsForm" 
                                          action="{{ route('conference.conference-registration.updateRegistrationIds', [$society, $conference]) }}" 
                                          method="POST" 
                                          style="display: inline;">
                                        @csrf 
                                        <button type="button" 
                                                class="dropdown-item"  
                                                onclick="confirmUpdateRegistrationIds()">
                                            <i class="ti tabler-refresh me-2"></i> Update Registration IDs
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#updateRegistrationIdsByTypeModal">
                                        <i class="ti tabler-adjustments me-2"></i> Update IDs By Registrant Type
                                    </a> 
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#updateRegistrationIdsByCountryModal">
                                        <i class="ti tabler-world me-2"></i> Update IDs By Country Scope
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#bulkConvertRegistrantTypeModal">
                                        <i class="ti tabler-user-check me-2"></i> Bulk Convert Registrant Type
                                    </a>
                                </li>
                            </ul>
                        </div> 

                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAllRegistrants" class="form-check-input" 
                                   title="Select/Deselect all registrations">
                        </th>
                        <th>#</th>
                        <th>Application Name</th>
                        <th>Membership Type</th>
                        {{-- <th>Email</th> --}}
                        <th>Payment Type/Voucher</th>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Registraton Type</th>
                        {{-- <th>Invitation Category</th> --}}
                        <th>No. of people</th>
                        <th>Is Verified?</th>
                        <th>Registration ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $inrConversionRate = null;
                        try {
                            $data = [
                                'page' => 1,
                                'per_page' => 10,
                                'from' => date('Y-m-d'),
                                'to' => date('Y-m-d'),
                            ];
                            $currencyExchange = \Illuminate\Support\Facades\Http::get('https://www.nrb.org.np/api/forex/v1/rates/', $data);
                            if ($currencyExchange->successful()) {
                                $USDRateSell = $currencyExchange->json()['data']['payload'][0]['rates'][1]['sell'] ?? null;
                                if (!empty($USDRateSell)) {
                                    $inrConversionRate = floatval($USDRateSell) / 1.6;
                                }
                            }
                        } catch (\Exception $e) {
                            $inrConversionRate = null;
                        }
                    @endphp
                    @foreach ($registrants as $registrant)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input registrant-checkbox" 
                                       value="{{ $registrant->id }}" data-registrant-type="{{ $registrant->registrant_type }}"
                                       title="Select this registrant">
                            </td>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{$registrant->user?->userDetail->namePrefix->prefix .' '. $registrant->user?->fullName($registrant->user) }}</td>
                            @php
                                $userSociety = $registrant->user?->societies->first();
                                $memberType = $userSociety?->pivot?->memberType;
                            @endphp
                            <td>{{ $memberType->type ?? 'N/A' }}</td>
                            {{-- <td>{{ $registrant->user->email }}</td> --}}
                            <td>
                                @if ($registrant->payment_type == 1)
                                    Fone-Pay
                                    @if (!empty($registrant->payment_voucher))
                                        <br><a href="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank" class="btn btn-sm btn-primary mt-1">
                                            <i class="icon-base ti tabler-eye icon-xs"></i> View
                                        </a>
                                    @endif
                                @elseif ($registrant->payment_type == 2)
                                    Moco Payment
                                    @if (!empty($registrant->payment_voucher))
                                        <br><a href="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank" class="btn btn-sm btn-primary mt-1">
                                            <i class="icon-base ti tabler-eye icon-xs"></i> View
                                        </a>
                                    @endif
                                @elseif ($registrant->payment_type == 3)
                                    Esewa
                                    @if (!empty($registrant->payment_voucher))
                                        <br><a href="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank" class="btn btn-sm btn-primary mt-1">
                                            <i class="icon-base ti tabler-eye icon-xs"></i> View
                                        </a>
                                    @endif
                                @elseif ($registrant->payment_type == 4)
                                    Khalti
                                    @if (!empty($registrant->payment_voucher))
                                        <br><a href="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank" class="btn btn-sm btn-primary mt-1">
                                            <i class="icon-base ti tabler-eye icon-xs"></i> View
                                        </a>
                                    @endif
                                @elseif ($registrant->payment_type == 5)
                                    Card Payment
                                    @if (!empty($registrant->payment_voucher))
                                        <br><a href="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank" class="btn btn-sm btn-primary mt-1">
                                            <i class="icon-base ti tabler-eye icon-xs"></i> View
                                        </a>
                                    @endif
                                @elseif (!empty($registrant->payment_voucher) && $registrant->payment_type == 6)
                                    {{-- @dd($registrant->payment_voucher) --}}
                                    @php
                                        $explodeFileName = explode('.', $registrant->payment_voucher);
                                    @endphp
                                    @if ($explodeFileName[1] == 'pdf')
                                        <a href="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank"><img src="{{ asset('default-image/pdf.png') }}"
                                                alt="voucher" height="50" width="40"></a>
                                    @else
                                        <a href="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                                alt="voucher" height="50" width="40"></a>
                                    @endif
                                @elseif($registrant->payment_type == 7)
                                    ConnectIPS
                                   @if (!empty($registrant->payment_voucher))
                                        <br><a href="{{ asset('storage/conference/payment-voucher/' . $registrant->payment_voucher) }}"
                                            target="_blank" class="btn btn-sm btn-primary mt-1">
                                            <i class="icon-base ti tabler-eye icon-xs"></i> View
                                        </a>
                                    @endif
                                @else
                                    Payment Voucher
                                @endif
                            </td>
                            <td>{{ $registrant->transaction_id ?? '-' }}</td>
                               <td>
                                @php
                                    $currencySymbol = '$';
                                    $displayAmount = (float) ($registrant->amount ?? 0);
                                    $showConversion = false;

                                    if ($registrant->payment_currency === 'INR') {
                                        $currencySymbol = 'INR';
                                        if (!empty($inrConversionRate) && !empty($registrant->amount)) {
                                            $displayAmount = ceil($inrConversionRate * floatval($registrant->amount));
                                            $showConversion = true;
                                        }
                                    } elseif (($registrant->user?->userDetail?->country_id ?? null) == 125) {
                                        $currencySymbol = 'Rs.';
                                    }
                                @endphp
                                {{ $currencySymbol }} {{ number_format($displayAmount, 2) }}
                                @if ($showConversion)
                                    <br><small class="text-muted">(USD ${{ number_format((float) $registrant->amount, 2) }})</small>
                                @endif
                            </td>
                            <td>
                                {{ $registrantTypes->firstWhere('id', $registrant->registrant_type)?->name ?? 'Unknown' }}
                                @if ($registrant->is_invited == 1)
                                    <span title="Invited"
                                        style="background-color: green; color: white; padding: 8px; height: 6px; width: 6px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;">
                                        I
                                    </span>
                                @endif
                            </td>
                            {{-- <td>{{ $registrant->invitationCategory?->name ?? '-' }}</td> --}}
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
                                        data-bs-toggle="modal" data-bs-target="#pricingModal"
                                        title="Verify Registrant"><span class="badge bg-warning">Unverified</span></a>
                                @endif
                            </td>
                         
                            <td>{{ $registrant->registration_id ?? 'N/A' }}</td>

                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Conference Registration'))
                                            <a class="dropdown-item" href="{{ route('conference.conference-registration.edit', [$society, $conference, $registrant->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif 
                                        <a class="dropdown-item viewData" data-id="{{ $registrant->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                class="icon-base ti tabler-eye me-1 "></i> View</a>
                                        @if($registrant->payment_type == 5)
                                            <a class="dropdown-item" href="{{ route('conference.conference-registration.showPaymentStatus', [$society, $conference, $registrant->id]) }}"><i
                                                    class="icon-base ti tabler-credit-card me-1 "></i> View Payment Status</a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Convert Registrant Type'))
                                            <a class="dropdown-item convertRegistrantType"
                                                data-id="{{ $registrant->id }}" data-bs-toggle="modal"
                                                data-bs-target="#pricingModal"><i
                                                    class="icon-base ti tabler-replace me-1 "></i> Convert Registrant
                                                Type</a>
                                        @endif
                                        <a class="dropdown-item" 
                                            href="{{ route('conference.conference-registration.generateIndividualPass', [$society, $conference, $registrant->id]) }}" target="_blank"><i
                                                class="icon-base ti tabler-ticket me-1"></i> Generate Pass</a>
                                        <a class="dropdown-item"
                                            href="{{ route('conference.conference-registration.downloadVoucher', [$society, $conference, $registrant->id]) }}"><i
                                                class="icon-base ti tabler-ticket me-1"></i> Downlaod Payment Voucher</a>
                                        <a class="dropdown-item"
                                            href="{{ route('conference.conference-registration.generateCertificate', [$society, $conference, $registrant->id]) }}"><i
                                                class="icon-base ti tabler-ticket me-1"></i>Generate Certificate</a>
                                        @if($registrant->user)
                                            <hr>
                                            <a class="dropdown-item"
                                                href="{{ route('conference.conference-registration.showIndividualEmail', [$society, $conference, $registrant->id]) }}"><i
                                                    class="icon-base ti tabler-mail me-1"></i> Send Email</a>
                                        @endif
                                        @if (is_super_admin())
                                            <hr>
                                            <form
                                                action="{{ route('conference.conference-registration.registrant.destroy', [$society, $conference, $registrant->id]) }}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                        class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                            </form>
                                        @endif
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

        <!-- Dummy Pass Generation Modal -->
        <div class="modal fade" id="dummyPassModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Dummy Pass</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('conference.conference-registration.generateDummyPass', [$society, $conference]) }}" method="POST" target="_blank">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="dummy_count" class="form-label">Number of Dummy Passes <code>*</code></label>
                                <input type="number" class="form-control" id="dummy_count" name="dummy_count" 
                                       min="1" max="100" value="1" required>
                                <small class="text-muted">Enter the number of dummy passes to generate (Max: 100)</small>
                            </div>
                            <div class="mb-3">
                                <label for="registrant_type" class="form-label">Registrant Type <code>*</code></label>
                                <select class="form-control" id="registrant_type" name="registrant_type" required>
                                    <option value="">-- Select Registrant Type --</option>
                                    @foreach ($registrantTypes as $rType)
                                        <option value="{{ $rType->id }}">{{ $rType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="dummy_country_scope" class="form-label">Country Scope <code>*</code></label>
                                <select class="form-control" id="dummy_country_scope" name="country_scope" required>
                                    <option value="">-- Select Scope --</option>
                                    <option value="national">National</option>
                                    <option value="international">International</option>
                                </select>
                                <small class="text-muted">Used to track dummy transaction ID as NAT-DUMMY-* or INT-DUMMY-*.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Generate Passes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Registration IDs By Type Modal -->
        <div class="modal fade" id="updateRegistrationIdsByTypeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Registration IDs By Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateRegistrationIdsByTypeForm" action="{{ route('conference.conference-registration.updateRegistrationIdsByType', [$society, $conference]) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="update_registrant_type" class="form-label">Registrant Type <code>*</code></label>
                                <select class="form-control" id="update_registrant_type" name="registrant_type" required>
                                    <option value="">-- Select Registrant Type --</option>
                                    @foreach ($registrantTypes as $rType)
                                        <option value="{{ $rType->id }}">{{ $rType->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Only the selected type will be re-numbered and updated.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="updateByTypeBtn" class="btn btn-primary" onclick="confirmUpdateRegistrationIdsByType()">
                                Update IDs
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Registration IDs By Country Scope Modal -->
        <div class="modal fade" id="updateRegistrationIdsByCountryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Registration IDs By Country Scope</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateRegistrationIdsByCountryForm" action="{{ route('conference.conference-registration.updateRegistrationIdsByCountryScope', [$society, $conference]) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="update_country_scope" class="form-label">Country Scope <code>*</code></label>
                                <select class="form-control" id="update_country_scope" name="country_scope" required>
                                    <option value="">-- Select Scope --</option>
                                    <option value="national">National</option>
                                    <option value="international">International</option>
                                </select>
                                <small class="text-muted">National = country_id 125, International = country_id other than 125.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="updateByCountryBtn" class="btn btn-primary" onclick="confirmUpdateRegistrationIdsByCountry()">
                                Update IDs
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bulk Convert Registrant Type Modal - Simplified for Selected Items -->
        <div class="modal fade" id="bulkConvertRegistrantTypeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Convert Registrant Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="bulkConvertRegistrantTypeForm" action="{{ route('conference.conference-registration.bulkUpdateRegistrantType', [$society, $conference]) }}" method="POST">
                        @csrf
                        <input type="hidden" id="selected_registrant_ids" name="selected_registrant_ids" value="">
                        <div class="modal-body">
                            <div class="alert alert-info" role="alert">
                                <i class="ti tabler-info-circle me-2"></i> 
                                <strong id="modalSelectedCount">0</strong> registrations selected for conversion.
                            </div>

                            <div class="mb-4" id="fromRegistrantTypeBlock">
                                <label for="from_registrant_type" class="form-label fw-bold">Convert FROM <code>*</code></label>
                                <select class="form-control form-control-lg" id="from_registrant_type" name="from_type">
                                    <option value="">-- Select Source Type --</option>
                                    <option value="1">Attendee</option>
                                    <option value="2">Speaker</option>
                                    <option value="3">Session Chair</option>
                                    <option value="4">Special Guest</option>
                                    <option value="5">Organizer</option>
                                    <option value="6">Faculty</option>
                                    <option value="7">Volunteer</option>
                                    <option value="8">Invitee</option>
                                </select>
                                <small class="text-muted">Only required when no registrations are selected.</small>
                            </div>

                            <div class="mb-4">
                                <label for="to_registrant_type" class="form-label fw-bold">Convert TO <code>*</code></label>
                                <select class="form-control form-control-lg" id="to_registrant_type" name="to_type" required>
                                    <option value="">-- Select Target Type --</option>
                                    <option value="1">Attendee</option>
                                    <option value="2">Speaker</option>
                                    <option value="3">Session Chair</option>
                                    <option value="4">Special Guest</option>
                                    <option value="5">Organizer</option>
                                    <option value="6">Faculty</option>
                                    <option value="7">Volunteer</option>
                                    <option value="8">Invitee</option>
                                </select>
                            </div>

                            <div class="alert alert-warning" role="alert">
                                <i class="ti tabler-alert-circle me-2"></i>
                                <strong>Warning:</strong> This will convert the selected registrations. This action cannot be undone.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="bulkConvertBtn" class="btn btn-success" onclick="confirmBulkConvertRegistrantTypeSimple()">
                                <i class="ti tabler-check me-2"></i> Convert Type
                            </button>
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
            // Handle Select All checkbox
            $(document).on('change', '#selectAllRegistrants', function() {
                const isChecked = $(this).is(':checked');
                $('.registrant-checkbox').prop('checked', isChecked);
                updateSelectedCount();
            });

            // Handle individual checkbox change
            $(document).on('change', '.registrant-checkbox', function() {
                updateSelectAllStatus();
                updateSelectedCount();
            });

            // Update Select All checkbox status
            function updateSelectAllStatus() {
                const totalCheckboxes = $('.registrant-checkbox').length;
                const checkedCheckboxes = $('.registrant-checkbox:checked').length;
                $('#selectAllRegistrants').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
            }

            // Update selected count display
            function updateSelectedCount() {
                const selectedCount = $('.registrant-checkbox:checked').length;
                
                // Update button visibility and count badge
                if (selectedCount > 0) {
                    $('#bulkConvertSelectionBtn').show();
                    $('#bulkConvertBtnCount').text(selectedCount);
                } else {
                    $('#bulkConvertSelectionBtn').hide();
                    $('#bulkConvertBtnCount').text(0);
                }
            }

            // Get selected registration IDs
            function getSelectedIds() {
                const ids = [];
                $('.registrant-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });
                return ids;
            }

            // Update the hidden field when modal is shown
            $('#bulkConvertRegistrantTypeModal').on('show.bs.modal', function() {
                const selectedIds = getSelectedIds();
                const selectedCount = selectedIds.length;
                $('#selected_registrant_ids').val(selectedIds.join(','));
                $('#modalSelectedCount').text(selectedCount);

                if (selectedCount > 0) {
                    $('#fromRegistrantTypeBlock').addClass('d-none');
                    $('#from_registrant_type').prop('required', false).val('');
                } else {
                    $('#fromRegistrantTypeBlock').removeClass('d-none');
                    $('#from_registrant_type').prop('required', true).val('');
                }
            });

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

            $(document).on("click", "#importRegistrant", function(e) { 
                e.preventDefault();
                var url =
                    '{{ route('conference.conference-registration.importExcel', [$society, $conference]) }}';
                var _token = '{{ csrf_token() }}';

                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                };

                $('#pricingModal .modal-dialog').removeClass('modal-lg');
                $('#pricingModal .modal-dialog').addClass('modal-md');
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

            $(document).on("click", ".verifyRegistrant", function(e) {
                e.preventDefault();
                var url =
                    '{{ route('conference.conference-registration.verifyForm', [$society, $conference]) }}';
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
                    // Exclude the sort_by select from the filter button requirement
                    if ($(this).attr('id') === 'sort_by') {
                        return true; // continue to next iteration
                    }
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
            
            // Allow sort_by to work independently without requiring filter
            $('#sort_by').on('change', function() {
                if ($(this).val()) {
                    $('#filterBtn').prop('disabled', false);
                } else {
                    toggleFilterButton();
                }
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
            $('#PassBtn').on('click', function(e) {
                e.preventDefault();
                form.attr('action',
                    '{{ route('conference.conference-registration.generatePass', [$society, $conference]) }}'
                );
                form.submit();
            });
        });

        // Function to confirm and submit registration ID update
        function confirmUpdateRegistrationIds() {
            if (confirm('This will update registration IDs for all registrants in this conference. This process may take a few minutes for large datasets. Do you want to continue?')) {
                const form = document.getElementById('updateRegistrationIdsForm');
                const button = form.querySelector('button');
                button.disabled = true;
                button.innerHTML = '<i class="ti tabler-loader me-2"></i> Updating...';
                form.submit();
            }
        }

        function confirmUpdateRegistrationIdsByType() {
            const form = document.getElementById('updateRegistrationIdsByTypeForm');
            const registrantType = document.getElementById('update_registrant_type').value;

            if (!registrantType) {
                alert('Please select a registrant type first.');
                return;
            }

            if (confirm('This will update registration IDs only for the selected registrant type. Do you want to continue?')) {
                const button = document.getElementById('updateByTypeBtn');
                button.disabled = true;
                button.innerHTML = '<i class="ti tabler-loader me-2"></i> Updating...';
                form.submit();
            }
        }

        function confirmUpdateRegistrationIdsByCountry() {
            const form = document.getElementById('updateRegistrationIdsByCountryForm');
            const countryScope = document.getElementById('update_country_scope').value;

            if (!countryScope) {
                alert('Please select a country scope first.');
                return;
            }

            if (confirm('This will update registration IDs only for the selected country scope and append dummy records at the end. Do you want to continue?')) {
                const button = document.getElementById('updateByCountryBtn');
                button.disabled = true;
                button.innerHTML = '<i class="ti tabler-loader me-2"></i> Updating...';
                form.submit();
            }
        }

        // Simple confirmation for bulk convert registrant type
        function confirmBulkConvertRegistrantTypeSimple() {
            const form = document.getElementById('bulkConvertRegistrantTypeForm');
            const fromType = document.getElementById('from_registrant_type').value;
            const toType = document.getElementById('to_registrant_type').value;
            const selectedCount = parseInt(document.getElementById('modalSelectedCount').textContent, 10) || 0;

            if (selectedCount === 0 && !fromType) {
                alert('Please select a source type.');
                return;
            }

            if (!toType) {
                alert('Please select a target type.');
                return;
            }

            if (fromType === toType) {
                alert('Source and target types cannot be the same.');
                return;
            }

            // Get type labels
            const typeLabels = {
                '1': 'Attendee',
                '2': 'Speaker',
                '3': 'Session Chair',
                '4': 'Special Guest',
                '5': 'Organizer',
                '6': 'Faculty',
                '7': 'Volunteer',
                '8': 'Invitee'
            };

            const fromLabel = selectedCount > 0 ? 'Selected Registrations' : typeLabels[fromType];
            const message = selectedCount > 0
                ? `Convert ${selectedCount} selected registrations to ${typeLabels[toType]}?\n\nContinue?`
                : `Convert registrations from ${fromLabel} to ${typeLabels[toType]}?\n\nContinue?`;

            if (confirm(message)) {
                const button = document.getElementById('bulkConvertBtn');
                button.disabled = true;
                button.innerHTML = '<i class="ti tabler-loader me-2"></i> Converting...';
                form.submit();
            }
        }
    </script>
@endsection
