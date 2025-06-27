@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($committee) ? 'Edit' : 'Add' }} Committee
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('committee.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($committee) ? 'Edit' : 'Add' }} Committee</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($committee) ? route('committee.update', [$society, $conference, $committee->id]) : route('committee.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($committee)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="committee_name">Committee Name <code>*</code></label>
                            <input type="text" class="form-control @error('committee_name') is-invalid @enderror"
                                id="committee_name" placeholder="Enter Committee Name" name="committee_name"
                                value="{{ !empty(old('committee_name')) ? old('committee_name') : @$committee->committee_name }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Committee Name.</div>
                            @error('committee_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="focal_person">Focal Person Name <code>*</code></label>
                            <input type="text" class="form-control @error('focal_person') is-invalid @enderror"
                                id="focal_person" placeholder="Enter Focal Person Name" name="focal_person"
                                value="{{ !empty(old('focal_person')) ? old('focal_person') : @$committee->focal_person }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Focal Person Name.</div>
                            @error('focal_person')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="email">Email <code>*</code></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                placeholder="Enter Email" name="email"
                                value="{{ !empty(old('email')) ? old('email') : @$committee->email }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Email.</div>
                            @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="phone">Phone <code>*</code></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                placeholder="Enter phone" name="phone"
                                value="{{ !empty(old('phone')) ? old('phone') : @$committee->phone }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter phone.</div>
                            @error('phone')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="description">Description </label>
                            <textarea class="form-control ckeditor" name="description" id="description" cols="30" rows="5"
                                placeholder="Enter description">{{ isset($committee) ? $committee->description : old('description') }}</textarea>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class=" text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($committee) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
