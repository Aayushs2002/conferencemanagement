@extends('backend.layouts.main')
@section('title')
    {{ isset($feature) ? 'Edit' : 'Add' }}Features
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('feature.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($feature) ? 'Edit' : 'Add' }} Features</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($feature) ? route('feature.update', $feature->id) : route('feature.store') }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($feature)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="title">Title <code>*</code></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                placeholder="Enter title" name="title"
                                value="{{ !empty(old('title')) ? old('title') : @$feature->title }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter title.</div>
                            @error('title')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="image">Image <code>*</code></label>
                            <input type="file" class="form-control" name="image" id="image"
                                value="{{ !empty(old('image')) ? old('image') : @$feature->image }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($feature) && $feature->image)
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/feature/image/' . $feature->image) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/feature/image/' . $feature->image) }}"
                                                class="img-fluid" alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="description" class="form-label">Description <code>*</code></label>
                            <textarea class="form-control ckeditor" name="description" id="description" cols="30" rows="5">{{ isset($feature) ? $feature->description : old('description') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter description.</div>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($feature) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
