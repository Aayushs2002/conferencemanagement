{{-- <x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

<x-guest-layout>

    <!-- Logo -->
    <div class="app-brand justify-content-center mb-6">
        {{-- <a href="" class="app-brand-link">
            <a href="#" class="app-brand-link">

                <img src="{{ asset('frontend/assets/img/MEDCON-LOGO.png') }}" style="height: 60px;">
            </a>
        </a> --}}
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
        {{-- <div class="app-
        <div class="text-center" class="app-brand-link">
            <img src="{{ asset('frontend/assets/img/MEDCON-LOGO-blue.png') }}" alt="Medcon Alert">
        </div> --}}
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
    <h4 class="mb-1">Forgot Password? 🔒</h4>
    <p class="mb-6">Enter your email and we'll send you instructions to reset your password</p>
    <form id="formAuthentication" class="mb-6" action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="mb-6 form-control-validation">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email"
                autofocus />
            @error('email')
                <p class="text-danger">{{ $message }}</p>
            @enderror
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
                        <a href="{{ route('conference.terms-conditions', $nextConference->slug) }}" class="tc-hover"
                            target="blank">
                            terms
                            & conditions</a> | <a href="{{ route('conference.privacy-policy', $nextConference->slug) }}"
                            target="blank" class="tc-hover">Privacy Policies</a>
                    @else
                        <a href="" class="tc-hover" target="blank">
                            terms
                            & conditions</a> | <a href="" target="blank" class="tc-hover">Privacy Policies</a>
                    @endif
                </label>
            </div>
        </div>
        <button class="btn btn-primary d-grid w-100">Send Reset Link</button>
    </form>
    <div class="text-center">
        <a href="{{ route('login') }}" class="d-flex justify-content-center">
            <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl me-1_5"></i>
            Back to login
        </a>
    </div>


    <!-- /Forgot Password -->
</x-guest-layout>
