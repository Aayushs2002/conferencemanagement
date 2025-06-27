@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($committe_designation) ? 'Edit' : 'Add' }} Committee Designation
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('committe-designation.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($committe_designation) ? 'Edit' : 'Add' }} Committee Designation</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($committe_designation) ? route('committe-designation.update', [$society, $conference, $committe_designation->id]) : route('committe-designation.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($committe_designation)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="designation">Committee Designation <code>*</code></label>
                            <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                id="designation" placeholder="Enter Committee Designation" name="designation"
                                value="{{ !empty(old('designation')) ? old('designation') : @$committe_designation->designation }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Committee Designation.</div>
                            @error('designation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($committe_designation) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
