<div class="row g-4 mb-5">
    @forelse ($conferences as $conference)
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('conference.name', $conference->slug) }}" class="text-decoration-none conference-link">
                <div class="conference-card p-4 h-100">
                    <div class="d-flex gap-2 mb-3">
                        <img src="{{ Storage::url('society/logo/' . $conference->society?->logo) }}" class="logo-img"
                            alt="{{ $conference->conference_name }}" loading="lazy">
                    </div> 
                    <h5 class="card-title">{{ $conference->conference_name }}</h5>
                    <p class="text-muted small mb-3">
                        {{ $conference->conference_theme }}
                    </p>

                    <div class="countdown d-flex mb-3 justify-content-center countdown-box"
                        data-start="{{ $conference->start_date }}" data-end="{{ $conference->end_date }}">
                        <div class="time-box"><span class="days">00</span><br><span>Days</span></div>
                        <span class="sep">:</span>
                        <div class="time-box"><span class="hours">00</span><br><span>Hrs</span></div>
                        <span class="sep">:</span>
                        <div class="time-box"><span class="minutes">00</span><br><span>Mins</span></div>
                        <span class="sep">:</span>
                        <div class="time-box"><span class="seconds">00</span><br><span>Secs</span></div>
                    </div>

                    <div class="mb-3">
                        @if ($conference->tags)
                            @foreach (explode(',', $conference->tags) as $tag)
                                <span class="badge rounded-pill bg-primary me-1">{{ trim($tag) }}</span>
                            @endforeach
                        @endif
                    </div>

                    <p class="small mb-1">
                        <i class="fa-regular fa-calendar-days me-1"></i>
                        {{ \Carbon\Carbon::parse($conference->start_date)->format('F jS, Y') }} -
                        {{ \Carbon\Carbon::parse($conference->end_date)->format('F jS, Y') }}
                    </p>

                    @if ($conference->ConferenceVenueDetail)
                        <p class="small text-muted mb-0">
                            <i class="fa-solid fa-location-dot me-1"></i>
                            {{ $conference->ConferenceVenueDetail->venue_name . ', ' . $conference->ConferenceVenueDetail->venue_address }}
                        </p>
                    @endif
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fa-solid fa-search fa-3x text-muted"></i>
                </div>
                <h4 class="text-muted">No conferences found</h4>
                <p class="text-muted">Try adjusting your search criteria or clear filters to see all conferences.</p>
            </div>
        </div>
    @endforelse
</div>
