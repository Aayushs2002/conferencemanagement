@extends('backend.layouts.main')
@section('title')
    {{ isset($page) ? 'Edit' : 'Add' }} Pages
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('page.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($page) ? 'Edit' : 'Add' }} Pages</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($page) ? route('page.update', $page->id) : route('page.store') }}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($page)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="title">Title <code>*</code></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                placeholder="Enter title" name="title"
                                value="{{ !empty(old('title')) ? old('title') : @$page->title }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter title.</div>
                            @error('title')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="image">Image <code>*</code></label>
                            <input type="file" class="form-control" name="image" id="image"
                                value="{{ !empty(old('image')) ? old('image') : @$page->image }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($page) && $page->image)
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/page/image/' . $page->image) }}" target="_blank"><img
                                                src="{{ asset('storage/page/image/' . $page->image) }}" class="img-fluid"
                                                alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="content" class="form-label">Content <code>*</code></label>
                            <textarea class="form-control ckeditor" name="content" id="content" cols="30" rows="5">{{ isset($page) ? $page->content : old('content') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter content.</div>
                            @error('content')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="meta_tag" class="form-label">Meta Tag <code>*</code></label>
                            <textarea class="form-control" name="meta_tag" id="meta_tag" cols="30" rows="6">{{ isset($page) ? $page->meta_tag : old('meta_tag') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Meta Tag.</div>
                            @error('meta_tag')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="meta_description" class="form-label">Meta Description <code>*</code></label>
                            <textarea class="form-control" name="meta_description" id="meta_description" cols="30" rows="6">{{ isset($page) ? $page->meta_description : old('meta_description') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Meta Description.</div>
                            @error('meta_description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($page) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
