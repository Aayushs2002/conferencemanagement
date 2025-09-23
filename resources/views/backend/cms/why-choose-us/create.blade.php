@extends('backend.layouts.main')
@section('title')
    {{ isset($whyChoose) ? 'Edit' : 'Add' }} Why Choose Us
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('why-choose-us.index') }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($whyChoose) ? 'Edit' : 'Add' }} Why Choose Us</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($whyChoose) ? route('why-choose-us.update', $whyChoose->id) : route('why-choose-us.store') }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($whyChoose)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="title">Title <code>*</code></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                placeholder="Enter title" name="title"
                                value="{{ !empty(old('title')) ? old('title') : @$whyChoose->title }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter title.</div>
                            @error('title')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="image">Image <code>*</code></label>
                            <input type="file" class="form-control" name="image" id="image"
                                value="{{ !empty(old('image')) ? old('image') : @$whyChoose->image }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($whyChoose) && $whyChoose->image)
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/whyChooseUs/image/' . $whyChoose->image) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/whyChooseUs/image/' . $whyChoose->image) }}"
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
                            <textarea class="form-control ckeditor" name="description" id="description" cols="30" rows="5">{{ isset($whyChoose) ? $whyChoose->description : old('description') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter description.</div>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($whyChoose) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
