<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('frontend/assets/img/MEDCON-LOGO.png') }}" alt="MedCon Logo" style="max-height: 17px;">
        </a>

        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about-us') }}">About Uss</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('solution') }}">Solutions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('our-client') }}">Our Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('blog') }}">Blogs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact-us') }}">Contact</a>
                </li>
            </ul>
            <div class="btn-container ms-lg-3">
                <button class="btn btn-primary ">Request a Quote</button>
                <a href="{{route('login')}}" class="btn btn-outline-primary">
                    Login
                </a>
            </div>
        </div>
    </div>
</nav>
