<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class="text-center mb-4 " style="background: white;">View Conference Detail</h4>
        <div class="row">
            <div class="col-12">
                <h6>1. Conference Details</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Conference Name</p>
                <span>{{ $conference->conference_name }}</span>
            </div>
            @if (!empty($conference->conference_theme))
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Conference Theme</p>
                    <span>{{ $conference->conference_theme }}</span>
                </div>
            @endif
            @if (!empty($conference->conference_logo))
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Conference Logo</p><span><img
                            src="{{ asset('storage/conference/conference/logo/' . $conference->conference_logo) }}"
                            height="100" width="100" alt="conference logo"></span>
                </div>
            @endif
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Date/Time</p>
                <span>{{ \Carbon\Carbon::parse($conference->start_date)->format('d M, Y') }} -
                    {{ \Carbon\Carbon::parse($conference->end_date)->format('d M, Y') }}
                    ({{ $conference->start_time }})</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Early Bird Registration Deadline</p>
                <span>{{ \Carbon\Carbon::parse($conference->early_bird_registration_deadline)->format('d M, Y') }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Regular Registration Deadline</p>
                <span>{{ \Carbon\Carbon::parse($conference->regular_registration_deadline)->format('d M, Y') }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Conference Primary Color</p>
                <span data-toggle="tooltip" data-placement="top" title="{{ $conference->primary_color }}"
                    style="display: inline-block; width: 20px; height: 20px; background-color: {{ $conference->primary_color }}; border: 1px solid #ccc; border-radius: 4px;"></span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Conference Secondary Color</p>
                <span data-toggle="tooltip" data-placement="top" title="{{ $conference->secendary_color }}"
                    style="display: inline-block; width: 20px; height: 20px; background-color: {{ $conference->secendary_color }}; border: 1px solid #ccc; border-radius: 4px;"></span>
            </div>
            @if (!empty($conference->conference_description))
                <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Description</p>
                <p>{!! $conference->conference_description !!}</p>
            @endif
            <div class="col-12">
                <h6>2. Venue Details</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Venue Name</p>
                <span>{{ $conference->ConferenceVenueDetail->venue_name }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Venue Address</p>
                <span>{{ $conference->ConferenceVenueDetail->venue_address }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Venue Contact</p>
                <span>{{ $conference->ConferenceVenueDetail->venue_contact_person_name }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Venue Phone Number</p>
                <span>{{ $conference->ConferenceVenueDetail->venue_phone_number }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Venue Email</p>
                <span>{{ $conference->ConferenceVenueDetail->venue_email }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Google Map Link</p>
                <span>{{ $conference->ConferenceVenueDetail->google_map_link }}</span>
            </div>
            <div class="col-12">
                <h6>3. Organizer Details</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Organizer Name</p>
                <span>{{ $conference->ConferenceOrganizer->organizer_name }}</span>
            </div>

            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Organizer Contact Person</p>
                <span>{{ $conference->ConferenceOrganizer->organizer_contact_person }}</span>
            </div>
            @if (!empty($conference->ConferenceOrganizer->organizer_email))
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Organizer Email</p>
                    <span>{{ $conference->ConferenceOrganizer->organizer_email }}</span>
                </div>
            @endif
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Organizer Phone</p>
                <span>{{ $conference->ConferenceOrganizer->organizer_phone_number }}</span>
            </div>
            @if (!empty($conference->ConferenceOrganizer->organizer_logo))
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Organizer Logo</p><span><img
                            src="{{ asset('storage/conference/organizer/logo/' . $conference->ConferenceOrganizer->organizer_logo) }}"
                            height="100" width="100" alt="organizer logo"></span>
                </div>
            @endif
        </div>
        @if (!empty($conference->ConferenceOrganizer->organizer_description))
            <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Description</p>
            <p>{!! $conference->ConferenceOrganizer->organizer_description !!}</p>
        @endif
    </div>
</div>
</div>
