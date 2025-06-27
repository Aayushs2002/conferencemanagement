@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($notice) ? 'Edit' : 'Add' }} News/Notice
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('notice.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($notice) ? 'Edit' : 'Add' }} News/Notice</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($notice) ? route('notice.update', [$society, $conference, $notice->id]) : route('notice.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($notice)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="title">Title <code>*</code></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                placeholder="Enter title" name="title"
                                value="{{ !empty(old('title')) ? old('title') : @$notice->title }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter title.</div>
                            @error('title')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="date">Published Date <code>*</code></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                                placeholder="Enter date" name="date"
                                value="{{ !empty(old('date')) ? old('date') : @$notice->date }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter date.</div>
                            @error('date')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="image">Cover Image</label>
                            <input type="file" class="form-control" name="image" id="image"
                                value="{{ !empty(old('image')) ? old('image') : @$notice->image }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($notice) && $notice->image)
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/notice/image/' . $notice->image) }}" target="_blank"><img
                                                src="{{ asset('storage/notice/image/' . $notice->image) }}"
                                                class="img-fluid" alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="attachment" class="form-label">Attachment <code>(Only: JPG/PDF/MS-Word & Max File
                                    Size:
                                    5MB)</code></label>
                            <input type="file" class="form-control @error('attachment') is-invalid @enderror"
                                name="attachment" id="image2" />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter attachment.</div>
                            @error('attachment')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div class="row" id="imgPreview2">
                                @if (isset($notice) && $notice->attachment)
                                    @php
                                        $extension = explode('.', $notice->attachment);
                                    @endphp
                                    @if ($extension[1] == 'pdf')
                                        <img src="{{ asset('default-image/pdf.png') }}" alt="attachment"
                                            style="height: 60px; width: 70px">
                                    @else
                                        <div class="col-3 mt-2">
                                            <img src="{{ asset('storage/notice/attachment/' . $notice->attachment) }}"
                                                class="img-fluid">
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="description" class="form-label">Description </label>
                            <textarea class="form-control ckeditor" name="description" id="description" cols="30" rows="5">{{ isset($notice) ? $notice->description : old('description') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter description.</div>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($notice) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
