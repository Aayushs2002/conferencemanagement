<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <!-- Pricing Plans -->
    <div class="rounded-top">
        <h4 class="text-center mb-4">View Society Details</h4>
        <div class="row">
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Society Name</p>
                <span>{{ $society->users->value('f_name') }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Abbreviation</p>
                <span>{{ $society->abbreviation }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Email</p>
                <span>{{ $society->users->value('email') }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Society Phone Number</p>
                <span>{{ $society->phone }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Address</p>
                <span>{{ $society->address }}</span>
            </div>

            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Contact Person</p>
                <span>{{ $society->contact_person }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Contact Person Phone</p>
                <span>{{ $society->contact_person_phone }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Contact Person Email</p>
                <span>{{ $society->contact_person_email }}</span>
            </div>
        </div>
        @if (!empty($society->logo))
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Organizer Logo</p><span><img
                        src="{{ asset('storage/society/logo/' . $society->logo) }}" height="100" width="100"
                        alt="logo"></span>
            </div>
        @endif
        @if (!empty($society->description))
            <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Description</p>
            <p>{!! $society->description !!}</p>
        @endif
    </div>
</div>
<!--/ Pricing Plans -->
</div>
