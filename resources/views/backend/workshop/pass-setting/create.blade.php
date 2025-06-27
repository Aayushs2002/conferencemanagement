@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($workshop_pass_setting) ? 'Edit' : 'Add' }} Pass Setting
@endsection

@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header">
                <a href="{{ route('workshop-pass-settings.index', [$society, $conference]) }}">
                    <i class="ti tabler-arrow-narrow-left"></i>
                </a>
                {{ isset($workshop_pass_setting) ? 'Edit' : 'Add' }} Pass Setting
            </h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($workshop_pass_setting) ? route('workshop-pass-settings.update', [$society, $conference, $workshop_pass_setting->id]) : route('workshop-pass-settings.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @isset($workshop_pass_setting)
                        @method('patch')
                    @endisset

                    <div class="row g-6">


                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="image">Pass Image</label>
                            <input type="file" class="form-control" name="image" id="image"
                                value="{{ old('image', @$workshop_pass_setting->image) }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($workshop_pass_setting) && $workshop_pass_setting->image)
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/workshop/pass/' . $workshop_pass_setting->image) }}"
                                            target="_blank">
                                            <img src="{{ asset('storage/workshop/pass/' . $workshop_pass_setting->image) }}"
                                                class="img-fluid" alt="image">
                                        </a>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row mt-6">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($workshop_pass_setting) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
