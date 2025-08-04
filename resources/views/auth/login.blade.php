{{-- <x-guest-layout>

    <x-auth-session-status class="mb-4" :status="session('status')" />


    <div class="app-brand justify-content-center mb-6">
        <a href="#" class="app-brand-link">
            @if ($society)
                <img src="{{ asset('storage/society/logo/' . $society->logo) }}" height="65">
            @else
                <img src="{{ asset('default-image/NESOG.png') }}" style="height: 60px;">
            @endif
        </a>
    </div>

    <p class="mb-6">Please sign-in to your account and start the registration</p>

    <form id="formAuthentication" class="mb-4" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-6 form-control-validation">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email"
                autofocus />
            @error('email')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6 form-password-toggle form-control-validation">
            <label class="form-label" for="password">Password</label>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
            </div>
            @error('password')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        <div class="my-8">
            <div class="d-flex justify-content-between">
                <div class="form-check mb-0 ms-2">
                    <input class="form-check-input" type="checkbox" id="remember-me" />
                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                </div>
                <a href="{{ route('password.request') }}">
                    <p class="mb-0">Forgot Password?</p>
                </a>
            </div>
        </div>
        <div class="mb-6">
            <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
        </div>
    </form>

    <p class="text-center">
        <span>Not Signed Up Yet?</span>
        <a href="{{ route('register') }}">
            <span>Create an account</span>
        </a>
    </p>
</x-guest-layout> --}}

<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="formAuthentication" class="mb-4" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="mb-6 form-control-validation">
                    <label for="email" class="form-label">Email/Username</label>
                    <input type="text" class="form-control" id="email" name="email"
                        placeholder="Enter your email" autofocus />
                    @error('email')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6 form-password-toggle form-control-validation">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password" class="form-control" name="password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password" />
                        <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('password')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="my-8">
                    <div class="d-flex justify-content-between">
                        <div class="form-check mb-0 ms-2">
                            <input class="form-check-input" type="checkbox" id="remember-me" />
                            <label class="form-check-label" for="remember-me"> Remember Me </label>
                        </div>
                        <a href="{{ route('password.request') }}">
                            <p class="mb-0">Forgot Password?</p>
                        </a>
                    </div>
                </div>
                <div class="mb-6">
                    <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                </div>
    </form>

    <p class="text-center">
        <span>Not Signed Up Yet?</span>
        <a href="{{ route('register') }}">
            <span>Create an account</span>
        </a>
    </p>
    </div>
    <div class="col-md-6">
        <div class="login-box"
            style="background:#f0e9fe;  text-align:center; height:auto; padding:40px 20px 50px; color:#000;">
            <a href="#" class="app-brand-link">

                <img src="{{ asset('default-image/NESOG.png') }}" style="height:50px;">
            </a> <br />
            <i>"Advancing Women's Health in South Asia; Quality Innovation, and
                Sustainability"</i><br /><br /><br /><br />
            <p><strong>Time & Venue </strong><br />
                7th-9th November, 2025<br />

                Park Village Resort, Budhanilkantha, Kathmandu<br />
            </p>

            <!-- Guidelines Link -->
            <div class="mt-3">
                <a href="#" onclick="openGuidelines()" class="btn btn-outline-primary btn-sm">
                    📋 View Registration Guidelines
                </a>
            </div>
        </div>
    </div>
    </div>

    <script>
        function openGuidelines() {
            // Option 1: Open in a new window/tab
            window.open("{{ asset('backend/guideline/Steps_for_Registering_in_SAFOGCON.pdf') }}", '_blank');

            // Option 2: If you prefer to show the modal instead, uncomment below and comment above
            // var guidelineModal = new bootstrap.Modal(document.getElementById('guidelineModal'));
            // guidelineModal.show();
        }
    </script>
</x-guest-layout>
