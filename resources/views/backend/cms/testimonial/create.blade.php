@extends('backend.layouts.main')
@section('title')
    {{ isset($testimonial) ? 'Edit' : 'Add' }} Testimonial
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('testimonial.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($testimonial) ? 'Edit' : 'Add' }} Testimonial</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($testimonial) ? route('testimonial.update', $testimonial->id) : route('testimonial.store') }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($testimonial)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="name">Name <code>*</code></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                placeholder="Enter name" name="name"
                                value="{{ !empty(old('name')) ? old('name') : @$testimonial->name }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter name.</div>
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="organization_name">Organization Name <code>*</code></label>
                            <input type="text" class="form-control @error('organization_name') is-invalid @enderror"
                                id="organization_name" placeholder="Enter Organization Name" name="organization_name"
                                value="{{ !empty(old('organization_name')) ? old('organization_name') : @$testimonial->organization_name }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Organization Name.</div>
                            @error('organization_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="designation">Designation <code>*</code></label>
                            <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                id="designation" placeholder="Enter Designation" name="designation"
                                value="{{ !empty(old('designation')) ? old('designation') : @$testimonial->designation }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Designation.</div>
                            @error('designation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="rating">Rating <code>*</code></label>
                            <div class="rating-container">
                                <div class="star-rating" id="starRating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="star" data-rating="{{ $i }}">★</span>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating"
                                    value="{{ !empty(old('rating')) ? old('rating') : @$testimonial->rating }}" required />
                                <div class="rating-text mt-2">
                                    <small class="text-muted" id="ratingText">Click on stars to rate</small> 
                                </div>
                            </div>
                            @error('rating')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="image">Image <code>*</code></label>
                            <input type="file" class="form-control" name="image" id="image"
                                value="{{ !empty(old('image')) ? old('image') : @$testimonial->image }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($testimonial) && $testimonial->image)
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/testimonial/image/' . $testimonial->image) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/testimonial/image/' . $testimonial->image) }}"
                                                class="img-fluid" alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="description" class="form-label">Description <code>*</code></label>
                            <textarea class="form-control ckeditor" name="description" id="description" cols="30" rows="5">{{ isset($testimonial) ? $testimonial->description : old('description') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter description.</div>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($testimonial) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .star-rating {
            display: inline-flex;
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
        }

        .star {
            transition: color 0.2s ease-in-out;
            margin-right: 0.25rem;
        }

        .star:hover,
        .star.active {
            color: #ffc107;
        }

        .star.hover {
            color: #ffc107;
        }

        .rating-container {
            margin-top: 0.5rem;
        }

        .rating-text {
            font-size: 0.875rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const ratingInput = document.getElementById('rating');
            const ratingText = document.getElementById('ratingText');

            const ratingTexts = {
                1: "Poor - 1 star",
                2: "Fair - 2 stars",
                3: "Good - 3 stars",
                4: "Very Good - 4 stars",
                5: "Excellent - 5 stars"
            };

            // Set initial rating if editing
            const currentRating = ratingInput.value;
            if (currentRating) {
                setRating(parseInt(currentRating));
            }

            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    setRating(rating);
                    ratingInput.value = rating;
                    ratingText.textContent = ratingTexts[rating];
                });

                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    highlightStars(rating);
                });

                star.addEventListener('mouseleave', function() {
                    const currentRating = parseInt(ratingInput.value) || 0;
                    highlightStars(currentRating);
                });
            });

            function setRating(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }

            function highlightStars(rating) {
                stars.forEach((star, index) => {
                    star.classList.remove('hover');
                    if (index < rating) {
                        star.classList.add('hover');
                    }
                });
            }
        });
    </script>
@endsection
