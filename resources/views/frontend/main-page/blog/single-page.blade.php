@extends('frontend.main-page.layouts.main')
@section('content')
    <section class="banner d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
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
        <section class="blog-detail-section">
            <div class="container">
                <div class="row justify-content-between g-4">
                    <div class="col-lg-8">
                        <div class="main-blog-card">
                            <div class="position-relative mb-3">
                                <img src="{{ Storage::url('blog/image/' . $blog->image) }}" class="img-fluid "
                                    alt="Medical Conferences" style="border-radius: 40px;">
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                                <div class="blog-date text-muted">
                                    <i
                                        class="fa-regular fa-calendar me-2"></i>{{ \Carbon\Carbon::parse($blog->created_at)->format('Y/m/d') }}
                                </div>
                                <div class="blog-share">
                                    Share On:
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->full() }}"
                                        target="_blank" class="text-muted me-2"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#" class="text-muted me-2"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="text-muted"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>

                            <h2 class="section-title mb-4" style="font-size: 32px;">{{ $blog->title }}</h2>

                            <p class=" section-subtitle" style="text-align: justify;">
                                {!! $blog->description !!}</p>
                            </p>

                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="sticky-top" style="top: 100px;">
                            <h4 class="mb-4" style="color: black;">Other Relevant Blogs</h4>
                            @foreach ($relevantBlogs as $relevantBlog)
                                <a href="{{ route('blog.single-page', $relevantBlog->slug) }}">
                                    <div class="card mb-2 shadow-sm border-0">
                                        <img src="{{ Storage::url('blog/image/' . $relevantBlog->image) }}"
                                            class="card-img-top" alt="Streamlining Abstract">
                                        <div class="card-body p-3">
                                            <p class="text-muted small mb-1"> <i
                                                    class="fa-regular fa-calendar me-2"></i>{{ \Carbon\Carbon::parse($relevantBlog->created_at)->format('Y/m/d') }}
                                            </p>
                                            <h6 class="card-title fw-bold mb-2">{{ $relevantBlog->title }}</h6>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                            {{-- <div class="card mb-2 shadow-sm border-0">
                                <img src="assets/img/blog_3.png" class="card-img-top" alt=" Conferences">
                                <div class="card-body p-3">
                                    <p class="text-muted small mb-1"> <i class="fa-regular fa-calendar me-2"></i>2024/12/10
                                    </p>
                                    <h6 class="card-title fw-bold mb-2">Why Data-Driven Insights Matter in Conferences
                                    </h6>
                                </div>
                            </div> --}}

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <div class="td_height_60 td_height_lg_60"></div>
@endsection
