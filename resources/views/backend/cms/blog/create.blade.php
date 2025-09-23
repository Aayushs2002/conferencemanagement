@extends('backend.layouts.main')
@section('title')
    {{ isset($blog) ? 'Edit' : 'Add' }} Blogs
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('blog.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($blog) ? 'Edit' : 'Add' }} Blogs</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($blog) ? route('blog.update', $blog->id) : route('blog.store') }}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($blog)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="title">Title <code>*</code></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                placeholder="Enter title" name="title"
                                value="{{ !empty(old('title')) ? old('title') : @$blog->title }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter title.</div>
                            @error('title')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="image">Image <code>*</code></label>
                            <input type="file" class="form-control" name="image" id="image"
                                value="{{ !empty(old('image')) ? old('image') : @$blog->image }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($blog) && $blog->image)
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/blog/image/' . $blog->image) }}" target="_blank"><img
                                                src="{{ asset('storage/blog/image/' . $blog->image) }}" class="img-fluid"
                                                alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="description" class="form-label">Description <code>*</code></label>
                            <textarea class="form-control ckeditor" name="description" id="description" cols="30" rows="5">{{ isset($blog) ? $blog->description : old('description') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter description.</div>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($blog) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
