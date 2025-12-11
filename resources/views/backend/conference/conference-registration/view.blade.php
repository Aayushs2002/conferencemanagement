<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class=" mb-4 " style="background: white;">View Data</h4>
        <div class="row">
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Applicant Name</p>
                <span>{{ $registrant->user->fullName($registrant->user) }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Email</p>
                <span>{{ $registrant->user->email }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Phone</p>
                <span>{{ $registrant->user->userDetail->phone }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Institute Name</p>
                <span>{{ $registrant->user->userDetail->institution?->name }}</span> 
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Institute Address</p>
                <span>{{ $registrant->user->userDetail->institute_address }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Department</p>
                <span>{{ $registrant->user->userDetail->department?->name }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Country</p>
                <span>{{ !empty($registrant->user->userDetail->country_id) ? '(' . $registrant->user->userDetail->country->country_name . ')' : '' }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Membership Type</p>
                @php
                    $userSociety = $registrant->user->societies->first();
                    $memberType = $userSociety?->pivot?->memberType;
                @endphp
                <span>{{ $memberType->type }}</span>
            </div>
            <div class="col-md-12 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Addons</p>
                @if($registrant->addons->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Addon Name</th>
                                    <th>Main Attendee Amount</th>
                                    <th>Guest Amount</th>
                                    <th>Include for Guests</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registrant->addons as $addon)
                                    @php
                                        $guestCount = $registrant->accompanyPersons->where('status', 1)->count();
                                        $mainAmount = $addon->amount;
                                        $guestAmount = $addon->ConferenceAddon->guest_amount ?? $addon->amount;
                                        $totalAmount = $mainAmount;
                                        
                                        if($addon->include_for_guests && $guestCount > 0) {
                                            $totalAmount += ($guestAmount * $guestCount);
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $addon->ConferenceAddon->addon_name }}</td>
                                        <td>{{ $mainAmount }}</td>
                                        <td>
                                            @if($addon->include_for_guests && $guestCount > 0)
                                                {{ $guestAmount }} × {{ $guestCount }} = {{ $guestAmount * $guestCount }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($addon->include_for_guests)
                                                <span class="badge badge-success">Yes</span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $totalAmount }}</strong></td>
                                    </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td colspan="4" class="text-end"><strong>Total Addon Cost:</strong></td>
                                    <td>
                                        <strong>
                                            @php
                                                $grandTotal = 0;
                                                foreach ($registrant->addons as $addon) {
                                                    $guestCount = $registrant->accompanyPersons->where('status', 1)->count();
                                                    $mainAmount = $addon->amount;
                                                    $guestAmount = $addon->ConferenceAddon->guest_amount ?? $addon->amount;
                                                    $grandTotal += $mainAmount;
                                                    if($addon->include_for_guests && $guestCount > 0) {
                                                        $grandTotal += ($guestAmount * $guestCount);
                                                    }
                                                }
                                                echo $grandTotal;
                                            @endphp
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <span class="text-muted">No addons selected</span>
                @endif
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Total Amount</p>
                <span>{{ $registrant->amount }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Council Number</p>
                <span>{{ $registrant->user->userDetail->council_number }}</span>
            </div>
            @if (!empty($registrant->user->userDetail->image))
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="i-ID-2 text-14 mr-1"></i>Image</p><span><img
                            src="{{ asset('storage/profile/image/' . $registrant->user->userDetail->image) }}"
                            alt="image" height="70"></span>
                </div>
            @endif
            @if (!empty($registrant->remarks))
                <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Remarks</p>
                <p>{!! $registrant->remarks !!}</p>
            @endif
        </div>
        @if (!empty($registrant->short_cv))
            <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Short Cv</p>
            <p>{!! $registrant->short_cv !!}</p>
        @endif
        @if ($registrant->accompanyPersons->where('status', 1)->isNotEmpty())
            <div>
                <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Accompany Persons</p>
                <p>
                <ol>
                    @foreach ($registrant->accompanyPersons->where('status', 1) as $accompanyPerson)
                        <li>{{ $accompanyPerson->person_name }}</li>
                    @endforeach
                </ol>
                </p>
            </div>
        @endif
    </div>
</div>
