<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="{{ route('conference.name', $conference->slug) }}">
            <img src="{{ Storage::url('society/logo/' . $conference->society->logo) }}" alt="NESOG Logo"
                style="max-height: 50px;">
        </a>

        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('conference.name', $conference->slug) }}">Home</a>
                </li>
                <li class="nav-item dropdown custom-dropdown">
                    <a class="nav-link" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        About Conference
                        <i class="fa-solid fa-angle-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                        <li><a class="dropdown-item"
                                href="{{ route('conference.committe', $conference->slug) }}">Committe</a></li>
                        <li><a class="dropdown-item"
                                href="{{ route('conference.speaker', $conference->slug) }}">Speakers</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                        href="{{ route('conference.scientific-session', $conference->slug) }}">Scientific
                        Sessions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('conference.workshop', $conference->slug) }}">Workshops</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('conference.news-and-notice', $conference->slug) }}">News &
                        Notices</a>
                </li>
            </ul>
            <div class="btn-container ms-lg-3">
                <a href="{{ route('login') }}">
                    <button class="btn btn-primary ">Login</button>
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary">
                    Register
                </a>

            </div>
        </div>
    </div>
</nav>
