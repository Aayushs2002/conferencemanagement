@extends('frontend.conference.layouts.main')
@section('content')
    <div class="container py-5">
        <div class="mb-2">
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>
        <div class="row mb-3">
            <div class="col text-center">
                <h2 class="section-title text-center">Sign up Now - {{ $conference->conference_name }}</h2>
                <p class="section-subtitle text-center">
                    Register today to secure your spot at the {{ $conference->conference_name }} and be part of insightful
                    sessions,
                    <br> workshops, and networking opportunities.
                </p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7">
                <div class="register-card">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-card">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Please select your Gender</option>
                                <option value="1" @selected(old('gender') == 1)>Male</option>
                                <option value="2" @selected(old('gender') == 2)>Female</option>
                                <option value="3" @selected(old('gender') == 3)>Other</option>
                            </select>
                        </div>
                        @error('gender')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <div class="form-card">
                            <label for="title" class="form-label">Select Title</label>
                            <select class="form-select" id="title">
                                <option value="">Please select your title...</option>
                                @foreach ($name_prefiexs as $name_prefix)
                                    <option value="{{ $name_prefix->id }}" @selected(old('name_prefix_id') == $name_prefix->id)>
                                        {{ $name_prefix->prefix }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('name_prefix_id')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <div class="form-card">
                            <label for="f_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('f_name') is-invalid @enderror" id="f_name"
                                name="f_name"
                                value="{{ !empty(old('f_name')) ? old('f_name') : session()->get('f_name') }}"
                                placeholder="Enter your First name...">
                        </div>
                        @error('f_name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <div class="form-card">
                            <label for="m_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('m_name') is-invalid @enderror" id="m_name"
                                name="m_name"
                                value="{{ !empty(old('m_name')) ? old('m_name') : session()->get('m_name') }}"
                                placeholder="Enter your Middle name...">
                        </div>
                        @error('m_name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <div class="form-card">
                            <label for="l_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('l_name') is-invalid @enderror" id="l_name"
                                name="l_name"
                                value="{{ !empty(old('l_name')) ? old('l_name') : session()->get('l_name') }}"
                                placeholder="Enter your Last name...">
                        </div>
                        @error('l_name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <div class="form-card">
                            <label for="email" class="form-label @error('email') is-invalid @enderror">Email</label>
                            <input type="email" class="form-control"
                                value="{{ !empty(old('email')) ? old('email') : session()->get('email') }}" name="email"
                                id="email" placeholder="Enter your email...">
                        </div>
                        @error('email')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror

                        <div class="form-card">
                            <label for="phone" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ !empty(old('phone')) ? old('phone') : session()->get('phone') }}"
                                placeholder="Enter your phone number...">
                        </div>
                        <div class="form-card">
                            <label for="country_id" class="form-label">Select Country</label>
                            <select class="form-select @error('country_id') is-invalid @enderror" name="country_id"
                                id="country_id">
                                <option value="">Please select a country...</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                                        {{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('country_id')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    
                                <div class=" form-card ">
                                    <label for="password" class="form-label">Password<code>* (Must be atleat 6
                                            characters)</code></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" placeholder="Enter your password..." name="password">
                                        <span class="input-group-text toggle-password" style="cursor:pointer;">
                                            <i class="fa-solid fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('password')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                                <div class=" form-card">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            id="password_confirmation" name="password_confirmation"
                                            placeholder="Rewrite your password...">
                                        <span class="input-group-text toggle-password" style="cursor:pointer;">
                                            <i class="fa-solid fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('password_confirmation')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="td_height_80 td_height_lg_80"></div>
@endsection
