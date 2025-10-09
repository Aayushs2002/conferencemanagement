@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($official_message) ? 'Edit' : 'Add' }} News/Notice
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('official-message.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($official_message) ? 'Edit' : 'Add' }} News/Notice</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($official_message) ? route('official-message.update', [$society, $conference, $official_message->id]) : route('official-message.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($official_message)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="title">Full Name <code>*</code></label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                id="full_name" placeholder="Enter full_name" name="full_name"
                                value="{{ !empty(old('full_name')) ? old('full_name') : @$official_message->full_name }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Full Name.</div>
                            @error('full_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="designation">Designation <code>*</code></label>
                            <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                id="designation" placeholder="Enter designation" name="designation"
                                value="{{ !empty(old('designation')) ? old('designation') : @$official_message->designation }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter designation.</div>
                            @error('designation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="image">Image</label>
                            <input type="file" class="form-control" name="image" id="image"
                                value="{{ !empty(old('image')) ? old('image') : @$official_message->image }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($official_message) && $official_message->image)
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/offical-message/image/' . $official_message->image) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/offical-message/image/' . $official_message->image) }}"
                                                class="img-fluid" alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($official_message) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
