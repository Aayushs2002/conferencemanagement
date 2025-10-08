@extends('frontend.conference.layouts.main')
@section('content')
    <div class="container py-4">
        <div class="mb-2">
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>
        <h2 class="section-title text-center">News & Notices</h2>
        <div class="row g-4 mt-4">
            @foreach ($notices as $notice)
                <div class="col-12 col-md-4">
                    <article class="card news-card h-100">
                        <img src="{{ Storage::url('notice/image/' . $notice->image) }}" alt="{{ $notice->title }}">
                        <div class="card-body">
                            <div>
                                <div class="name-title">{{ $notice->title }}</div>
                                <div class="date-text">{{ \Carbon\Carbon::parse($notice->date)->format('d F, Y') }}
                                </div>
                            </div>
                            <a href="#" class="default-btn ms-auto" target="_blank">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        {{-- <div class="row g-4 mt-4">
            <div class="col-12 col-md-4">
                <article class="card news-card h-100">
                    <img src="assets/img/NEWS&EVENT_2.jpg" alt="Prof. Dr. BalKrishna Bhattarai">
                    <div class="card-body">
                        <div>
                            <div class="name-title">Prof. Dr. BalKrishna Bhattarai</div>
                            <div class="date-text">04 April, 2025</div>
                        </div>
                        <a href="#" class="default-btn ms-auto" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="card news-card h-100">
                    <img src="assets/img/NEWS&EVENT_3.jpg" alt="DR Ayuko Igarashi">
                    <div class="card-body">
                        <div>
                            <div class="name-title">DR Ayuko Igarashi</div>
                            <div class="date-text">04 April, 2025</div>
                        </div>
                        <a href="#" class="default-btn ms-auto" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </article>
            </div>
            <div class="col-12 col-md-4">
                <article class="card news-card h-100">
                    <img src="assets/img/NEWS&EVENT_1.jpg" alt="Dr.Agnes NG Suah Bee">
                    <div class="card-body">
                        <div>
                            <div class="name-title">Dr.Agnes NG Suah Bee</div>
                            <div class="date-text">20 March, 2025</div>
                        </div>
                        <a href="#" class="default-btn ms-auto" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </article>
            </div>
        </div> --}}
    </div>
    <div class="my-4">
        {{ $notices->links('vendor.pagination.custom') }}
    </div>

    {{-- <nav aria-label="Pagination" class="my-4">
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
    </nav> --}}
    <!-- Image Popup Modal -->
    <div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2"
                    data-bs-dismiss="modal" aria-label="Close"></button>
                <img id="modalImage" src="" class="img-fluid rounded" alt="Popup Image">
            </div>
        </div>
    </div>

    <div class="td_height_80 td_height_lg_80"></div>
@endsection
