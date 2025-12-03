<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        {{-- @dd($workshop) --}}
        <h4 class="text-center mb-4 " style="background: white;">View Workshop Detail <code>(Workshop:
                {{ $workshop->workshop_title }})</code></h4>
        <div class="row">

            <div class="col-12">
                <h6>1. Workshop Details</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Workshop Title</p>
                <span>{{ $workshop->workshop_title }}</span>
            </div>
            @if (!empty($workshop->workshop_type))
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Workshop Type</p>
                    <span>{{ $workshop->workshop_type == 1 ? 'Paid' : 'Unpaid' }}</span>
                </div>
            @endif

            @if (!empty($workshop->workshop_type == 1))
                @if ($workshop->registration_fee)
                    <div class="col-md-4 mb-4">
                        <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Registration Fee</p>
                        <span>{{ $workshop->registration_fee }}</span>
                    </div>
                @endif
                @if ($workshop->proposed_budget)
                    <div class="col-md-4 mb-4">
                        <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Proposed Budget</p>
                        <span>{{ $workshop->proposed_budget }}</span>
                    </div>
                @endif
            @endif
            {{-- @if (!empty($conference->conference_logo))
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Conference Logo</p><span><img
                            src="{{ asset('storage/conference/conference/logo/' . $conference->conference_logo) }}"
                            height="100" width="100" alt="conference logo"></span>
                </div>
            @endif --}}
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Date/Time</p>
                <span>{{ \Carbon\Carbon::parse($workshop->start_date)->format('d M, Y') }} -
                    {{ \Carbon\Carbon::parse($workshop->end_date)->format('d M, Y') }}
                    ({{ $workshop->start_time . '-' . $workshop->end_time }})</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i> Registration Deadline</p>
                <span>{{ \Carbon\Carbon::parse($workshop->registration_deadline)->format('d M, Y') }}</span>
            </div>

            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Contact Person Name </p>
                <span>{{ $workshop->contact_person_name }}</span>
            </div>

            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Contact Person Phone </p>
                <span>{{ $workshop->contact_person_phone }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Contact Person Email </p>
                <span>{{ $workshop->contact_person_email }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>No of Participants </p>
                <span>{{ $workshop->no_of_participants }}</span>
            </div>
            @if (!empty($workshop->workshop_description))
                <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Description</p>
                <p>{!! $workshop->workshop_description !!}</p>
            @endif
            @if (!empty($workshop->overview_of_organiztion))
                <div class="col-md-12 mb-4">
                    <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Overview of Organization</p>
                    <p>{!! $workshop->overview_of_organiztion !!}</p>
                </div>
            @endif
            @if (!empty($workshop->training_method_expected_outcome))
                <div class="col-md-12 mb-4">
                    <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Training Method & Expected
                        Outcome</p>
                    <p>{!! $workshop->training_method_expected_outcome !!}</p>
                </div>
            @endif
            @if (!empty($workshop->resource_requirement))
                <div class="col-md-12 mb-4"> 
                    <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Resource Requirement</p>
                    <p>{!! $workshop->resource_requirement !!}</p>
                </div>
            @endif
            <div class="col-12">
                <h6>2. Venue Details</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Venue Name</p>
                <span>{{ $workshop->WorkshopVenueDetail->venue_name }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Venue Address</p>
                <span>{{ $workshop->WorkshopVenueDetail->venue_address }}</span>
            </div>

            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Google Map Link</p>
                <span>{{ $workshop->WorkshopVenueDetail->google_map_link }}</span>
            </div>
            <div class="col-12">
                <h6>3. Chair Person Details</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Workshop Coordinator</p>
                <span>{{ $workshop->WorkshopChairPersonDetail->chairPerson->fullName($workshop->WorkshopChairPersonDetail->chairPerson) }}</span>
            </div>
            @if (!empty($workshop->WorkshopChairPersonDetail->short_cv))
                <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Short Cv</p>
                <p>{!! $workshop->WorkshopChairPersonDetail->short_cv !!}</p>
            @endif
        </div>

    </div>
</div>
</div>
