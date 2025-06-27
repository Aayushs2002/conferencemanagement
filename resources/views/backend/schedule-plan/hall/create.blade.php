@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($hall) ? 'Edit' : 'Add' }} Hall
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('hall.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($hall) ? 'Edit' : 'Add' }} Hall</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($hall) ? route('hall.update', [$society, $conference, $hall->id]) : route('hall.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($hall)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="hall_name">Hall Name <code>*</code></label>
                            <input type="text" class="form-control @error('hall_name') is-invalid @enderror"
                                id="hall_name" placeholder="Enter Hall Name" name="hall_name"
                                value="{{ !empty(old('hall_name')) ? old('hall_name') : @$hall->hall_name }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Hall Name.</div>
                            @error('hall_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($hall) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
