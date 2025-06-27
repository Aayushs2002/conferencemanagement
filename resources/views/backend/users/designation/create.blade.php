@extends('backend.layouts.main')
@section('title')
    {{ isset($designation) ? 'Edit' : 'Add' }} Designation
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('designation.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($designation) ? 'Edit' : 'Add' }} Designation</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($designation) ? route('designation.update', $designation->id) : route('designation.store') }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($designation)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="designation">Designation <code>*</code></label>
                            <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                id="designation" placeholder="Enter Designation " name="designation"
                                value="{{ !empty(old('designation')) ? old('designation') : @$designation->designation }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Designation.</div>
                            @error('designation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($designation) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
