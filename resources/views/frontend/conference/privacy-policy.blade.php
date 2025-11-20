@extends('frontend.conference.layouts.main')

@section('content')
    <section class="main-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm">
                        <div class=" p-5">
                            <h3 class="mb-4 text-center">Privacy Policy</h3>
                            <hr class="mb-4">
                            
                            @if($conference->conferenceSetting?->privacy_policy)
                                <div class="content-section">
                                    {!! $conference->conferenceSetting->privacy_policy !!}
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    Privacy Policy will be available soon.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .content-section {
            font-size: 15px;
            line-height: 1.8;
            color: #333;
        }
        .content-section h2 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        .content-section h3 {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: var(--primary-color);
        }
        .content-section ul, .content-section ol {
            margin-bottom: 1rem;
        }
        .content-section li {
            margin-bottom: 0.5rem;
        }
    </style>
@endsection
