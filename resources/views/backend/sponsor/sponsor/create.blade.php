@extends('backend.layouts.conference.main')
@section('title')
    {{ isset($sponsor) ? 'Edit' : 'Add' }} Sponsor
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('sponsor.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($sponsor) ? 'Edit' : 'Add' }} Sponsor</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($sponsor) ? route('sponsor.update', [$society, $conference, $sponsor->id]) : route('sponsor.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($sponsor)
                        @method('patch')
                    @endisset
                    <div class="row">
                        <div class="col-md-3 form-group mb-3">
                            <label for="sponsor_category_id">Category <code>*</code></label>
                            <select name="sponsor_category_id"
                                class="form-control @error('sponsor_category_id') is-invalid @enderror" required>
                                <option value="" hidden>-- Select Category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('sponsor_category_id', @$sponsor->sponsor_category_id) == $category->id)>
                                        {{ $category->category_name }}</option>
                                @endforeach
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Select Category.</div>
                            @error('sponsor_category_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="name">Name <code>*</code></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                placeholder="Enter Sponsor Category" name="name"
                                value="{{ !empty(old('name')) ? old('name') : @$sponsor->name }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Sponsor Name.</div>
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="amount">Amount <code>*</code></label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror" name="amount"
                                id="amount" value="{{ isset($sponsor) ? $sponsor->amount : old('amount') }}"
                                placeholder="Enter amount" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Amount.</div>
                            @error('amount')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="logo">Logo </label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" name="logo"
                                id="image" />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Logo.</div>
                            @error('logo')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div class="row" id="imgPreview">
                                @if (isset($sponsor))
                                    <div class="col-3 mt-2">
                                        <img src="{{ asset('storage/sponsor/logo/' . $sponsor->logo) }}" alt="logo"
                                            class="img-fluid">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="flyers_ads">Flyers/Ads </label>
                            <input type="file" class="form-control @error('flyers_ads') is-invalid @enderror"
                                name="flyers_ads" id="image2" />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Flyers/Ads.</div>
                            @error('flyers_ads')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div class="row" id="imgPreview2">
                                @if (isset($sponsor))
                                    @php
                                        $extension = explode('.', $sponsor->flyers_ads);
                                    @endphp
                                    @if ($extension[1] == 'pdf')
                                        <img src="{{ asset('default-image/pdf.png') }}" alt="flyers_ads"
                                            style="height: 60px; width: 70px">
                                    @else
                                        <div class="col-3 mt-2">
                                            <img src="{{ asset('storage/sponsor/ads/' . $sponsor->flyers_ads) }}"
                                                class="img-fluid">
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="address">Address </label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" name="address"
                                id="address" value="{{ isset($sponsor) ? $sponsor->address : old('address') }}"
                                placeholder="Enter address" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Address.</div>
                            @error('address')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="contact_person">Contact Person </label>
                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                name="contact_person" id="contact_person"
                                value="{{ isset($sponsor) ? $sponsor->contact_person : old('contact_person') }}"
                                placeholder="Enter contact person" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Contact Person.</div>
                            @error('contact_person')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="email">Email </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                id="email" value="{{ isset($sponsor) ? $sponsor->email : old('email') }}"
                                placeholder="Enter email" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Email.</div>
                            @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="phone">Phone <code>*</code></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                name="phone" id="phone"
                                value="{{ isset($sponsor) ? $sponsor->phone : old('phone') }}" placeholder="Enter phone"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Phone Number.</div>
                            @error('phone')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-3 form-group mb-3">
                            <label for="lunch_access">Lunch Access <code>*</code></label>
                            <select name="lunch_access" class="form-control @error('lunch_access') is-invalid @enderror"
                                required>
                                <option value="" hidden>-- Select Lunch Access --</option>
                                <option value="1" @selected(old('lunch_access', @$sponsor->lunch_access) == 1)>Yes</option>
                                <option value="0" @selected(old('lunch_access', @$sponsor->lunch_access) === 0)>No</option>
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Select Lunch Access.</div>
                            @error('lunch_access')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="dinner_access">Dinner Access <code>*</code></label>
                            <select name="dinner_access"
                                class="form-control @error('dinner_access') is-invalid @enderror" required>
                                <option value="" hidden>-- Select Lunch Access --</option>
                                <option value="1" @selected(old('dinner_access', @$sponsor->dinner_access) == 1)>Yes</option>
                                <option value="0" @selected(old('dinner_access', @$sponsor->dinner_access) === 0)>No</option>
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Select Dinner Access.</div>
                            @error('dinner_access')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="description">Description </label>
                            <textarea class="form-control ckeditor" name="description" id="description" cols="30" rows="5">{{ isset($sponsor) ? $sponsor->description : old('description') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Description.</div>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 text-end">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($sponsor) ? 'Update' : 'Submit' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
