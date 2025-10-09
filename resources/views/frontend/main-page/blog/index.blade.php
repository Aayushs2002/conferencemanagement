@extends('frontend.main-page.layouts.main')
@section('content')
    <section class="banner d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Blogs</li>
                        </ol>
                    </nav>
                    <h1 class="banner-title">MedConAlert: Sharing Ideas,</br> Inspiring Change</h1>
                    <p class="banner-sub">
                        Explore insights, updates, and stories on smarter conference management. Learn how MedConAlert
                        empowers organizers, speakers, and participants through innovation and collaboration.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="main-section">
        <section class="about-section ">
            <div class="container">
                <div class="row justify-content-between">

                    <div class="col-lg-3">
                        <h2 class="section-title">MedConAlert
                            Blogs</h2>
                    </div>
                    <div class="col-lg-7">
                        <p class="section-subtitle mb-4">
                            At MedConAlert Blogs, we share insights, updates, and stories that matter to conference
                            organizers, sponsors, speakers, and attendees. From best practices in event planning to
                            innovations in technology, our blogs highlight strategies that enhance collaboration,
                            streamline management, and elevate the overall conference experience.</p>
                        <p class="section-subtitle">
                            Whether it’s simplifying registrations, fostering interactive participation, or exploring
                            data-driven analytics, our content is designed to inspire smarter, smoother, and more
                            impactful events — empowering you to deliver knowledge that lasts.</p>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <div class="td_height_60 td_height_lg_60"></div>
    <section class="blogs-section ">
        <div class="container">
            <h2 class="section-title text-center mb-5">
                Insights
            </h2>
            <div class="row g-4 mb-4">
                @foreach ($blogs as $blog)
                    <div class="col-md-4">
                        <a href="{{ route('blog.single-page', $blog->slug) }}" class="blog-link text-decoration-none">
                            <div class="blog-cards">
                                <img src="{{ Storage::url('blog/image/' . $blog->image) }}" alt="{{ $blog->title }}"
                                    class="img-fluid blog-img">
                                <div class="blog-date d-flex align-items-center mb-2">
                                    <i class="fa-regular fa-calendar me-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($blog->created_at)->format('Y/m/d') }}</span>
                                </div>
                                <h5 class="blog-title mb-3">{{ $blog->title }}</h5>
                                <p class="blog-description">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($blog->description), 160, '...') }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
                {{-- <div class="col-md-4">
                    <div class="blog-card">
                        <img src="assets/img/Blog_2.png" alt="Blog 2" class="img-fluid blog-img">
                        <div class="blog-date d-flex align-items-center mb-2">
                            <i class="fa-regular fa-calendar me-2"></i>
                            <span>2025/01/01</span>
                        </div>
                        <h5 class="blog-title mb-3">Streamlining Abstract Submissions with Technology</h5>
                        <p class="blog-description">Learn how MedConAlert’s digital tools simplify abstract
                            submissions, enable blind peer reviews...</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="blog-card">
                        <img src="assets/img/blog_3.png" alt="Blog 3" class="img-fluid blog-img">
                        <div class="blog-date d-flex align-items-center mb-2">
                            <i class="fa-regular fa-calendar me-2"></i>
                            <span>2025/01/01</span>
                        </div>
                        <h5 class="blog-title mb-3">Why Data-Driven Insights Matter in Conferences</h5>
                        <p class="blog-description">Explore how real-time analytics help organizers optimize resources,
                            boost engagement, and meas...</p>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="row g-4">
                <div class="col-md-4">
                    <div class="blog-card">
                        <img src="assets/img/blog_4.png" alt="Blog 1" class="img-fluid blog-img">
                        <div class="blog-date d-flex align-items-center mb-2">
                            <i class="fa-regular fa-calendar me-2"></i>
                            <span>2025/01/01</span>
                        </div>
                        <h5 class="blog-title mb-3">The Future of Medical Conferences: Going Hybrid</h5>
                        <p class="blog-description">Discover how hybrid conference models combine physical presence
                            with virtual participation, expand...</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="blog-card">
                        <img src="assets/img/blog_1.png" alt="Blog 2" class="img-fluid blog-img">
                        <div class="blog-date d-flex align-items-center mb-2">
                            <i class="fa-regular fa-calendar me-2"></i>
                            <span>2025/01/01</span>
                        </div>
                        <h5 class="blog-title mb-3">Streamlining Abstract Submissions with Technology</h5>
                        <p class="blog-description">Learn how MedConAlert’s digital tools simplify abstract
                            submissions, enable blind peer reviews...</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="blog-card">
                        <img src="assets/img/Blog_2.png" alt="Blog 3" class="img-fluid blog-img">
                        <div class="blog-date d-flex align-items-center mb-2">
                            <i class="fa-regular fa-calendar me-2"></i>
                            <span>2025/01/01</span>
                        </div>
                        <h5 class="blog-title mb-3">Why Data-Driven Insights Matter in Conferences</h5>
                        <p class="blog-description">Explore how real-time analytics help organizers optimize resources,
                            boost engagement, and meas...</p>
                    </div>
                </div>
                <nav aria-label="Pagination" class="my-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Previous">
                                <i class="fa-solid fa-angle-left"></i>
                            </a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#">01</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">02</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <i class="fa-solid fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>

            </div> --}}
        </div>
    </section>
    <div class="td_height_60 td_height_lg_60"></div>
@endsection
