<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class="text-center mb-4 " style="background: white;">View Data</h4>
        <div class="row">
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Full Name</p>
                <span>{{ $contact->full_name }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Email</p>
                <span>{{ $contact->email }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Contact Number</p>
                <span>{{ $contact->contact_number }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Conference Type</p>
                <span>{{ $contact->conference_type }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Start Date</p>
                <span>{{ $contact->start_date }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>End Date</p>
                <span>{{ $contact->end_date }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>National Participant</p>
                <span>{{ $contact->no_of_national_participant }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>International Participant</p>
                <span>{{ $contact->no_of_international_participant }}</span>
            </div>
            {{-- <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Date</p>
                <span>{{ \Carbon\Carbon::parse($contact->date)->format('d M, Y') }}
                    {{ !empty($contact->end_date) ? ' - ' . \Carbon\Carbon::parse($contact->end_date)->format('d M, Y') : '' }}
                    </span>
            </div> --}}
        </div>
        @if (!empty($contact->query))
            <p class="text-primary mb-1"><i class="i-Letter-Open text-16 mr-1"></i>Query</p>
            <p>{!! $contact->query !!}</p>
        @endif
    </div>
</div>
