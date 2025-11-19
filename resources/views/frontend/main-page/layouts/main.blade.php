<!DOCTYPE html>
<html class="no-js" lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $segment = request()->segment(1);
        $meta = getMetas(Request::segment(1), Request::segment(2));
    @endphp 
    <!-- Favicon Icon -->
    <link rel="icon" href="{{asset('frontend/assets/img/MEDCON-Favicon.png')}}" type="image/x-icon">
    <!-- Site Title -->
    <title>{{ $meta->title ?? 'Medcon Alert' }}</title>
    
    <!-- Basic Meta Tags -->
    <meta name="description" content="{{ Str::limit(strip_tags($meta->description), 160) }}">
    
    <!-- Open Graph Meta Tags (Facebook, LinkedIn) -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $meta->title ?? 'Medcon Alert' }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($meta->description), 200) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($meta->image)
        <meta property="og:image" content="{{ $meta->image }}">
        <meta property="og:image:secure_url" content="{{ $meta->image }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $meta->title ?? 'Medcon Alert' }}">
    @endif
    <meta property="og:site_name" content="Medcon Alert">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta->title ?? 'Medcon Alert' }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($meta->description), 200) }}">
    @if($meta->image)
        <meta name="twitter:image" content="{{ $meta->image }}">
        <meta name="twitter:image:alt" content="{{ $meta->title ?? 'Medcon Alert' }}">
    @endif
    
    <!-- Canonical URL -->
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/slick.min.css') }}"> 
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/odometer.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    @include('frontend.main-page.layouts.navbar')
    @yield('content')
    @include('frontend.main-page.layouts.footer')

    <!-- End Scroll Up Button -->
    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.slick.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/odometer.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>
    <!-- BLOGS -->
    <script>
        const cards = document.querySelectorAll('.blog-card');
        let current = 0;

        function animateCards() {
            cards.forEach(card => {
                card.classList.remove('col-6');
                card.classList.add('col-3');
            });

            cards[current].classList.remove('col-3');
            cards[current].classList.add('col-6');

            current = (current + 1) % cards.length;
        }

        animateCards();
        setInterval(animateCards, 3000);
    </script>

    <!-- image slider -->



</body>

</html>
