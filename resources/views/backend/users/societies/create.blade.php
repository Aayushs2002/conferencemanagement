@extends('backend.layouts.main')
@section('title')
    {{ isset($society) ? 'Edit' : 'Add' }} society
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('society.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($society) ? 'Edit' : 'Add' }}
                Society</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($society) ? route('society.update', $society) : route('society.store') }}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($society)
                        @method('patch')
                    @endisset 
                    <div class="row">
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="society-name">Society Name <code>*</code></label>
                            <input type="text" class="form-control @error('society_name') is-invalid @enderror"
                                id="society-name" placeholder="Enter Society Name" name="society_name"
                                value="{{ old('society_name') ?? (@$society?->users->where('type', 2)->value('f_name') ?? '') }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter society name.</div>
                            @error('society_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="bs-validation-abb">Abbreviation <code>*</code></label>
                            <input type="text" class="form-control @error('abbreviation') is-invalid @enderror"
                                id="bs-validation-abb" placeholder="Enter Society Abbreviation" name="abbreviation"
                                value="{{ !empty(old('abbreviation')) ? old('abbreviation') : @$society->abbreviation }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please society abbreviation.</div>
                            @error('abbreviation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="bs-validation-email">Society Email <code>*</code></label>
                            <input type="email" id="bs-validation-email"
                                value="{{ old('email') ?? (@$society?->users->where('type', 2)->value('email') ?? '') }}"
                                name="email" class="form-control" placeholder="Enter Email" aria-label="john.doe"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter a valid email</div>
                            @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="phone">Society Phone Number <code>*</code></label>
                            <input type="number" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                placeholder="Enter Society Phone Number" name="phone"
                                value="{{ !empty(old('phone')) ? old('phone') : @$society->phone }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please society phone number.</div>
                            @error('phone')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="address">Address <code>*</code></label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                                placeholder="Enter Society Address" name="address"
                                value="{{ !empty(old('address')) ? old('address') : @$society->address }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please society address.</div>
                            @error('address')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="contact_person">Contact Person <code>*</code></label>
                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                id="contact_person" placeholder="Enter Society Contact Person" name="contact_person"
                                value="{{ !empty(old('contact_person')) ? old('contact_person') : @$society->contact_person }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please contact person.</div>
                            @error('contact_person')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="contact_person_phone">Contact Person Phone <code>*</code></label>
                            <input type="number" class="form-control @error('contact_person_phone') is-invalid @enderror"
                                id="contact_person_phone" placeholder="Enter Contact Person Phone Number"
                                name="contact_person_phone"
                                value="{{ !empty(old('contact_person_phone')) ? old('contact_person_phone') : @$society->contact_person_phone }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please society contact person phone number.</div>
                            @error('contact_person_phone')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="contact_person_email">Contact Person Email <code>*</code></label>
                            <input type="email" id="contact_person_email"
                                value="{{ !empty(old('contact_person_email')) ? old('contact_person_email') : @$society->contact_person_email }}"
                                name="contact_person_email" class="form-control" placeholder="Enter Contact Person Email"
                                aria-label="john.doe" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter a valid email</div>
                            @error('contact_person_email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="logo">Logo <code>* (Only JPG/PNG) (Max:
                                    250
                                    KB)</code></label>
                            <input type="file" class="form-control" name="logo" id="image"
                                value="{{ !empty(old('logo')) ? old('logo') : @$society->logo }}"{{ isset($society) ? '' : 'required' }} />
                            <div class="row" id="imgPreview">
                                @if (isset($society))
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/society/logo/' . $society->logo) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/society/logo/' . $society->logo) }}"
                                                class="img-fluid" alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('logo')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control ckeditor" id="description" name="description" rows="5" cols="30">{{ !empty(old('description')) ? old('description') : @$society->description }}</textarea>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($society) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
