@extends('backend.layouts.main')
@section('title')
    {{ isset($institution) ? 'Edit' : 'Add' }} Institution
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('institution.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($institution) ? 'Edit' : 'Add' }} Institution</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($institution) ? route('institution.update', $institution->id) : route('institution.store') }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($institution)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="name">Institution Name<code>*</code></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                placeholder="Enter Institution Name" name="name"
                                value="{{ !empty(old('name')) ? old('name') : @$institution->name }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Institution Name.</div>
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($institution) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
