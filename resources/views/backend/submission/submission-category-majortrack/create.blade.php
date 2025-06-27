@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($submissionCategoryMajortrack) ? 'Edit' : 'Add' }} Submission Category/Major Track
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('submission.category-majortrack.index',[$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($submissionCategoryMajortrack) ? 'Edit' : 'Add' }} Submission Category/Major Track</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($submissionCategoryMajortrack) ? route('submission.category-majortrack.update', [$society, $conference, $submissionCategoryMajortrack->id]) : route('submission.category-majortrack.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($submissionCategoryMajortrack)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-12">
                            <label class="form-label" for="title">Category/Major Track Title <code>*</code></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                placeholder="Enter Title " name="title"
                                value="{{ !empty(old('title')) ? old('title') : @$submissionCategoryMajortrack->title }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Title.</div>
                            @error('title')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label for="major_areas">Category/Major Track Major Areas <code>*</code></label>
                            <textarea name="major_areas" class="form-control @error('major_areas') is-invalid @enderror" id="majorArea"
                                cols="30" rows="5" required>{{ isset($submissionCategoryMajortrack) ? $submissionCategoryMajortrack->major_areas : old('major_areas') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Major Track.</div>
                            @error('major_areas')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($submissionCategoryMajortrack) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
