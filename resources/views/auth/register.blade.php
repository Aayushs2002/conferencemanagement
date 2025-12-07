<x-guest-layout>
    <!-- Logo -->
    @php
        $nextConference = null;
        if (!empty($society) && $society->conferences) {
            $upcoming = $society->conferences
                ->filter(function ($c) {
                    return \Carbon\Carbon::parse($c->start_date)->isToday() ||
                        \Carbon\Carbon::parse($c->start_date)->isFuture();
                })
                ->sortBy('start_date');

            $nextConference = $upcoming->first();

            if (!$nextConference) {
                $nextConference = $society->conferences->sortByDesc('start_date')->first();
            }
        }
    @endphp
    <div class="app-brand justify-content-center mb-6">
        @if (!empty($nextConference))
            {{-- <a href="#" class="app-brand-link">

                <img src="{{ asset('default-image/NESOG.png') }}" style="height: 60px;">
            </a> --}}
            @if (!empty($nextConference->conference_logo))
                <a href="#" class="app-brand-link">
                    <img src="{{ asset('storage/conference/conference/logo/' . $nextConference->conference_logo) }}"
                        style="height:50px;">
                </a>
            @else
                <div class="text-center">
                    <h3>{{ $nextConference->conference_name ?? 'Upcoming Conference' }}</h3>
                </div>
            @endif
        @else
            <h3>Medcon Alert</h3>

        @endif
    </div>
    <!-- /Logo -->
    <p class="mb-6">Please sign-up to your account and start the conference registration</p>

    <form id="formAuthentication" class="mb-6" method="POST" action="{{ route('register') }}">
        @csrf
        <div style="display:none;">
            <label for="website">Website</label>
            <input type="text" name="website" id="website" autocomplete="off">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-6 form-control-validation">
                    <label for="gender" style="padding-bottom:18px;">Select Gender <code>*</code></label><br />
                    <input type="radio" @if (old('gender') == 1) checked @endif id="male" name="gender"
                        value="1">
                    <label for="male" class="mr-3">Male</label>
                    <input type="radio" @if (old('gender') == 2) checked @endif id="female" name="gender"
                        value="2">
                    <label for="female" class="mr-3">Female</label>
                    <input type="radio" @if (old('gender') == '3') checked @endif id="other" name="gender"
                        value="3">
                    <label for="other">Other</label>
                    @error('gender')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6 form-control-validation">
                    <label for="name_prefix_id">Name Prefix <code>*</code></label>
                    <select class="form-control @error('name_prefix_id') is-invalid @enderror" name="name_prefix_id"
                        id="name_prefix_id">
                        <option value="" hidden>-- Select name Prefix --</option>
                        @foreach ($name_prefiexs as $name_prefix)
                            <option value="{{ $name_prefix->id }}" @selected(old('name_prefix_id') == $name_prefix->id)>
                                {{ $name_prefix->prefix }}</option>
                        @endforeach
                    </select>
                    @error('name_prefix_id')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6 form-control-validation">
                    <label for="f_name">First Name <code>*</code></label>
                    <input type="text" class="form-control @error('f_name') is-invalid @enderror" id="f_name"
                        name="f_name" value="{{ !empty(old('f_name')) ? old('f_name') : session()->get('f_name') }}"
                        required autocomplete="f_name" autofocus>
                    @error('f_name')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6 form-control-validation">
                    <label for="m_name">Middle Name </label>
                    <input type="text"
                        class="form-control form-control-rounded @error('m_name') is-invalid @enderror" id="m_name"
                        name="m_name" value="{{ !empty(old('m_name')) ? old('m_name') : session()->get('m_name') }}"
                        autocomplete="m_name" autofocus>
                    @error('m_name')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6 form-control-validation">
                    <label for="l_name">Last Name <code>*</code></label>
                    <input type="text" class="form-control @error('l_name') is-invalid @enderror" id="l_name"
                        name="l_name" value="{{ !empty(old('l_name')) ? old('l_name') : session()->get('l_name') }}"
                        required autocomplete="l_name" autofocus>
                    @error('l_name')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">

                <div class="mb-6 form-control-validation">
                    <label for="email">Email <code>*</code></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ !empty(old('email')) ? old('email') : session()->get('email') }}"
                        required autocomplete="email" autofocus>
                    @error('email')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6 form-control-validation">
                    <label for="phone">Phone <code>*</code></label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                        name="phone" value="{{ !empty(old('phone')) ? old('phone') : session()->get('phone') }}"
                        required autocomplete="phone" autofocus>
                    @error('phone')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6 form-control-validation">
                    <label for="country_id">Country <code>*</code></label>
                    <select class="form-control @error('country_id') is-invalid @enderror" name="country_id"
                        id="country_id">
                        <option value="" hidden>-- Select Country --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                                {{ $country->country_name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                @if ($society)
                    <div class="mb-6 form-control-validation">
                        <label for="member_type_id">Participant Type <code>*</code></label>
                        <select name="member_type_id"
                            class="form-control form-control @error('member_type_id') is-invalid @enderror member_type_id"
                            id="member_type_id" required>
                            <option value="" hidden>-- Select Participant Type --</option>
                        </select>
                        @error('member_type_id')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
                <div class="mb-6 form-password-toggle form-control-validation">
                    <label for="password">Password <code>* (Must be atleat 6
                            characters)</code></label>
                    <div class="input-group input-group-merge">

                        <input class="form-control @error('password') is-invalid @enderror" id="password"
                            type="password" name="password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password">
                        <span class="input-group-text cursor-pointer"><i
                                class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('password')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6 form-password-toggle form-control-validation">
                    <label for="password_confirmation">Confirm Password
                        <code>*</code></label>
                    <div class="input-group input-group-merge">
                        <input class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation" type="password" name="password_confirmation"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password_confirmation">
                        <span class="input-group-text cursor-pointer"><i
                                class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('password_confirmation')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <style>
                .tc-hover {
                    text-decoration: underline !important;
                    color: purple !important;
                }

                .tc-hover:hover {
                    color: blue !important;
                }
            </style>
            <div class="my-8 form-control-validation" style="margin-block-start:0rem !important;">
                <div class="form-check mb-0 ms-2">
                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                    <label class="form-check-label" for="terms-conditions">
                        I agree to
                        @if ($nextConference)
                            <a href="{{ route('conference.terms-conditions', $nextConference->slug) }}"
                                class="tc-hover" target="blank">
                                terms
                                & conditions</a> | <a
                                href="{{ route('conference.privacy-policy', $nextConference->slug) }}" target="blank"
                                class="tc-hover">Privacy Policies</a>
                        @else
                            <a href="" class="tc-hover" target="blank">
                                terms
                                & conditions</a> | <a href="" target="blank" class="tc-hover">Privacy
                                Policies</a>
                        @endif
                    </label>
                </div>
            </div>
            <button class="btn btn-primary d-grid" style="float:left; width:100px;">Sign up</button>
        </div>
    </form>

    <p class="text-left">
        <span>Already have an account?</span>
        <a href="{{ route('login') }}">
            <span>Sign in instead</span>
        </a>
    </p>
</x-guest-layout>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#country_id').on('change', function() {
            var country_id = $(this).val();
            var memberTypeId = '{{ old('member_type_id') }}';
            var society = '{{ $society->id ?? '' }}';
            if (!country_id) return;
            $.ajax({
                type: 'GET',
                url: '{{ route('getMemberTypes') }}',
                data: {
                    country_id: country_id,
                    society: society
                },
                success: function(response) {
                    $('#member_type_id')
                        .empty()
                        .append(
                        '<option value="" hidden>-- Select Member Type --</option>');

                    if (response.type === 'success' && response.data.length > 0) {
                        let optionsHtml = '';

                        $.each(response.data, function(index, item) {
                            let selected = (item.id == memberTypeId) ? 'selected' :
                                '';
                            optionsHtml +=
                                `<option value="${item.id}" ${selected}>${item.type}</option>`;
                        });

                        $('#member_type_id').append(optionsHtml);
                    } else {
                        $('#member_type_id').append(
                            '<option disabled>No Member Types Found</option>');
                    }
                },
                error: function(xhr) {
                    console.log('AJAX Error:', xhr);
                }
            });
        });
        $("#country_id").trigger('change');
    });
</script>
