@extends('backend.layouts.society.main')
@section('title')
    Conference
@endsection
@section('content')
    <div class="row mb-12 g-6">
        <h2 class="text-center">Conference</h2>

        @foreach ($conferences as $conference)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    @if (!empty($conference->logo))
                        <div class="d-flex justify-content-center">
                            <img class="card-img-top" src="{{ asset('storage/conference/logo/' . $conference->logo) }}"
                                style="width: 20%" alt="Card image cap" />
                        </div>
                    @endif
                    <div class="card-body">
                        <h4 class="card-title">{{ $conference->conference_name }}
                        </h4>
                        <p class="card-text">
                            {{ $conference->conference_theme }}
                        <ul>
                            <li>Start Date: {{ $conference->start_date }}</li>
                            <li>End Date: {{ $conference->end_date }}</li>
                            <li>Early Bird Registration Deadline: {{ $conference->early_bird_registration_deadline }}</li>
                            <li>Regular Registration Deadline: {{ $conference->regular_registration_deadline }}</li>
                            <li>Start Time: {{ $conference->start_time }}</li>
                        </ul>
                        </p>
                        <a href="{{ checkRegistrations($conference) ? route('my-society.conference.index', [$society, $conference]) : route('my-society.conference.create', [$society, $conference]) }}"
                            class="btn btn-outline-primary">Go To
                            Conference</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
