@extends('backend.layouts.main')
@section('title')
    My Society
@endsection
@section('content')
    <div class="row mb-12 g-6">
        <h2 class="text-center">My Society</h2>
        @foreach ($joinedSocities as $society)
            <div class="col-md-6 col-lg-6">
                <div class="card h-100">
                    <div class="d-flex justify-content-center">
                        <img class="card-img-top" src="{{ asset('storage/society/logo/' . $society->logo) }}"
                            style="width: 20%" alt="Card image cap" />
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $society->users->where('type', 2)->value('f_name') }}
                            ({{ $society->abbreviation }})
                        </h5>
                        <p class="card-text">
                            {!! $society->description !!}
                        </p>
                        <a href="{{ route('my-society.conference', $society) }}" class="btn btn-outline-primary">Go To
                            Society</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
