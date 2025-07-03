{{-- <x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="f_name" :value="__('First Name')" />
            <x-text-input id="f_name" class="block mt-1 w-full" type="text" name="f_name" :value="old('f_name')" required
                autofocus autocomplete="f_name" />
            <x-input-error :messages="$errors->get('f_name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="m_name" :value="__('Middle Name')" />
            <x-text-input id="m_name" class="block mt-1 w-full" type="text" name="m_name" :value="old('m_name')"
                 autofocus autocomplete="m_name" />
            <x-input-error :messages="$errors->get('m_name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="l_name" :value="__('Last Name')" />
            <x-text-input id="l_name" class="block mt-1 w-full" type="text" name="l_name" :value="old('l_name')"
                required autofocus autocomplete="l_name" />
            <x-input-error :messages="$errors->get('l_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

{{-- <!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign Up | Nfn</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/fonts/iconify-icons.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/node-waves/node-waves.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/pickr/pickr-themes.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/demo.css') }}" />



    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />


    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('backend/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/fonts/flag-icons.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('backend/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('backend/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />

    <link rel="stylesheet"
        href="{{ asset('backend/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/notyf/notyf.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/animate-css/animate.css') }}" />

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/pages/cards-advance.css') }}" />


    <script src="{{ asset('backend/assets/vendor/js/helpers.js') }}"></script>
  

    <script src="{{ asset('backend/assets/js/config.js') }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="auth-layout-wrap"
        style="background-image: url({{ asset('backend') }}/dist-assets/images/conference-background.jpg)">
        <div class="auth-content">
            <div class="card o-hidden">
                <div class="row">
                    <div class="col-md-12">
                        <div class="p-4">
                            <div class="auth-logo text-center mb-4">
                                <h4><b><u>Conference Management System</u></b></h4>
                                <h5><b><u>Sign Up</u></b></h5>
                            </div>
                            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
                                id="signUpForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="gender">Select Gender <code>*</code></label><br>
                                            <input type="radio" @if (old('gender') == 1) checked @endif
                                                id="male" name="gender" value="1">
                                            <label for="male" class="mr-3">Male</label>
                                            <input type="radio" @if (old('gender') == 2) checked @endif
                                                id="female" name="gender" value="2">
                                            <label for="female" class="mr-3">Female</label>
                                            <input type="radio" @if (old('gender') == '3') checked @endif
                                                id="other" name="gender" value="3">
                                            <label for="other">Other</label>
                                            @error('gender')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name_prefix_id">Name Prefix <code>*</code></label>
                                            <select
                                                class="form-control form-control-rounded @error('name_prefix_id') is-invalid @enderror"
                                                name="name_prefix_id" id="name_prefix_id">
                                                <option value="" hidden>-- Select name Prefix --</option>
                                                @foreach ($name_prefiexs as $name_prefix)
                                                    <option value="{{ $name_prefix->id }}"
                                                        @selected(old('name_prefix_id') == $name_prefix->id)>
                                                        {{ $name_prefix->prefix }}</option>
                                                @endforeach
                                            </select>
                                            @error('name_prefix_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="f_name">First Name <code>*</code></label>
                                            <input type="text"
                                                class="form-control form-control-rounded @error('f_name') is-invalid @enderror"
                                                id="f_name" name="f_name"
                                                value="{{ !empty(old('f_name')) ? old('f_name') : session()->get('f_name') }}"
                                                required autocomplete="f_name" autofocus>
                                            @error('f_name')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="m_name">Middle Name </label>
                                            <input type="text"
                                                class="form-control form-control-rounded @error('m_name') is-invalid @enderror"
                                                id="m_name" name="m_name"
                                                value="{{ !empty(old('m_name')) ? old('m_name') : session()->get('m_name') }}"
                                                autocomplete="m_name" autofocus>
                                            @error('m_name')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="l_name">Last Name <code>*</code></label>
                                            <input type="text"
                                                class="form-control form-control-rounded @error('l_name') is-invalid @enderror"
                                                id="l_name" name="l_name"
                                                value="{{ !empty(old('l_name')) ? old('l_name') : session()->get('l_name') }}"
                                                required autocomplete="l_name" autofocus>
                                            @error('l_name')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email <code>*</code></label>
                                            <input type="email"
                                                class="form-control form-control-rounded @error('email') is-invalid @enderror"
                                                id="email" name="email"
                                                value="{{ !empty(old('email')) ? old('email') : session()->get('email') }}"
                                                required autocomplete="email" autofocus>
                                            @error('email')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone <code>*</code></label>
                                            <input type="text"
                                                class="form-control form-control-rounded @error('phone') is-invalid @enderror"
                                                id="phone" name="phone"
                                                value="{{ !empty(old('phone')) ? old('phone') : session()->get('phone') }}"
                                                required autocomplete="phone" autofocus>
                                            @error('phone')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="country_id">Country <code>*</code></label>
                                            <select
                                                class="form-control form-control-rounded @error('country_id') is-invalid @enderror"
                                                name="country_id" id="country_id">
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
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password <code>* (Must be atleat 8
                                                    characters)</code></label>
                                            <input
                                                class="form-control form-control-rounded @error('password') is-invalid @enderror"
                                                id="password" type="password" name="password" required
                                                autocomplete="current-password">
                                            @error('password')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm Password
                                                <code>*</code></label>
                                            <input
                                                class="form-control form-control-rounded @error('password_confirmation') is-invalid @enderror"
                                                id="password_confirmation" type="password"
                                                name="password_confirmation" required autocomplete="current-password">
                                            @error('password_confirmation')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-rounded btn-primary btn-block mt-2" type="submit"
                                    id="submitButton">Sign Up</button>
                                <div class="text-center">
                                    <a class="btn btn-link" href="{{ route('login') }}">
                                        {{ __('Already Signed Up? Go to login') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('backend/assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/hammer/hammer.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/i18n/i18n.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/js/menu.js') }}"></script>

   
    <script src="{{ asset('backend/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/notyf/notyf.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

    <script src="{{ asset('backend/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}">
    </script>
    <script src="{{ asset('backend/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>

 
    <script src="{{ asset('backend/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
  
    <script src="{{ asset('backend/assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>


    <script src="{{ asset('backend/assets/js/main.js') }}"></script>

   
    <script src="{{ asset('backend/assets/js/dashboards-analytics.js') }}"></script>
    <script src="{{ asset('backend/assets/js/tables-datatables-basic.js') }}"></script>
    <script src="{{ asset('backend/assets/js/form-validation.js') }}"></script>
    <script src="{{ asset('backend/assets/js/extended-ui-sweetalert2.js') }}"></script>
    <script src="{{ asset('backend/assets/js/forms-pickers.js') }}"></script>


    <script src="{{ asset('backend/assets/js/ui-toasts.js') }}"></script>


    <script src=" {{ asset('backend/plugin-links/ckeditor/ckeditor.js') }}"></script>

    @if (Session::has('status'))
        <script>
            toastr.success("{!! Session::get('status') !!}");
        </script>
    @endif
    @if (Session::has('delete'))
        <script>
            toastr.error("{!! Session::get('delete') !!}");
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $("#submitButton").click(function(e) {
                e.preventDefault();
                $(this).attr('disabled', true);
                $("#signUpForm").submit();
            });
        });
    </script>
</body> --}}
<x-guest-layout>
    <!-- Logo -->
    <div class="app-brand justify-content-center mb-6">
        <a href="#" class="app-brand-link">

            <img src="{{ asset('default-image/omway.png') }}" style="height: 60px;">
        </a>
    </div>
    <!-- /Logo -->
      <h4 class="mb-1">Welcome to Conflyze! 👋</h4>
    <p class="mb-6">Please sign-up to your account and start the registration</p>

    <form id="formAuthentication" class="mb-6" method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-6 form-control-validation">
            <label for="gender">Select Gender <code>*</code></label><br>
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
                name="f_name" value="{{ !empty(old('f_name')) ? old('f_name') : session()->get('f_name') }}" required
                autocomplete="f_name" autofocus>
            @error('f_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6 form-control-validation">
            <label for="m_name">Middle Name </label>
            <input type="text" class="form-control form-control-rounded @error('m_name') is-invalid @enderror"
                id="m_name" name="m_name"
                value="{{ !empty(old('m_name')) ? old('m_name') : session()->get('m_name') }}" autocomplete="m_name"
                autofocus>
            @error('m_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6 form-control-validation">
            <label for="l_name">Last Name <code>*</code></label>
            <input type="text" class="form-control @error('l_name') is-invalid @enderror" id="l_name"
                name="l_name" value="{{ !empty(old('l_name')) ? old('l_name') : session()->get('l_name') }}" required
                autocomplete="l_name" autofocus>
            @error('l_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6 form-control-validation">
            <label for="email">Email <code>*</code></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                name="email" value="{{ !empty(old('email')) ? old('email') : session()->get('email') }}" required
                autocomplete="email" autofocus>
            @error('email')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6 form-control-validation">
            <label for="phone">Phone <code>*</code></label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                name="phone" value="{{ !empty(old('phone')) ? old('phone') : session()->get('phone') }}" required
                autocomplete="phone" autofocus>
            @error('phone')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6 form-control-validation">
            <label for="country_id">Country <code>*</code></label>
            <select class="form-control @error('country_id') is-invalid @enderror" name="country_id" id="country_id">
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
        <div class="mb-6 form-password-toggle form-control-validation">
            <label for="password">Password <code>* (Must be atleat 6
                    characters)</code></label>
            <div class="input-group input-group-merge">

                <input class="form-control @error('password') is-invalid @enderror" id="password" type="password"
                    name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password">
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
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
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
            </div>
            @error('password_confirmation')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="my-8 form-control-validation">
            <div class="form-check mb-0 ms-2">
                <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                <label class="form-check-label" for="terms-conditions">
                    I agree to
                    <a href="javascript:void(0);">privacy policy & terms</a>
                </label>
            </div>
        </div>
        <button class="btn btn-primary d-grid w-100">Sign up</button>
    </form>

    <p class="text-center">
        <span>Already have an account?</span>
        <a href="{{route('login')}}">
            <span>Sign in instead</span>
        </a>
    </p>

</x-guest-layout>
