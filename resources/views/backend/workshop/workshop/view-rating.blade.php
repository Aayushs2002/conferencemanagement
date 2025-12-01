<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class="text-center mb-4" style="background: white;">
            Workshop Ratings: <code>{{ $workshop->workshop_title }}</code>
        </h4>

        {{-- Rating Overview --}}
        <div class="rating-overview mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Average Rating</h6>
                            <div class="average-rating-display">
                                <span class="avg-number">{{ number_format($averageRating, 1) }}</span>
                                <div class="stars-display mt-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= floor($averageRating))
                                            <span class="star filled">★</span>
                                        @elseif($i - 0.5 <= $averageRating)
                                            <span class="star half-filled">★</span>
                                        @else
                                            <span class="star">★</span>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">Total Ratings</h6>
                            <h2 class="mb-0 text-primary">{{ $totalRatings }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">With Comments</h6>
                            <h2 class="mb-0 text-success">{{ $ratingsWithComments }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rating Distribution --}}
        <div class="rating-distribution mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Rating Distribution</h6>
                </div>
                <div class="card-body pt-4">
                    @foreach ([5, 4, 3, 2, 1] as $star)
                        @php
                            $count = $ratingDistribution[$star] ?? 0;
                            $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
                        @endphp
                        <div class="rating-bar-item mb-3">
                            <div class="d-flex align-items-center">
                                <span class="star-label me-2" style="min-width: 60px;">{{ $star }} <span
                                        class="star-small">★</span></span>
                                <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="count-label" style="min-width: 40px;">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Individual Ratings --}}
        <div class="individual-ratings">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Individual Ratings</h6>
                    <div class="filter-buttons btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="filter" id="filter-all" checked>
                        <label class="btn btn-outline-primary" for="filter-all" data-filter="all">All</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-comments">
                        <label class="btn btn-outline-primary" for="filter-comments" data-filter="comments">With
                            Comments</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-5star">
                        <label class="btn btn-outline-primary" for="filter-5star" data-filter="5">5★</label>

                        <input type="radio" class="btn-check" name="filter" id="filter-low">
                        <label class="btn btn-outline-primary" for="filter-low" data-filter="low">Low (1-2★)</label>
                    </div>
                </div>
                <div class="card-body pt-4" style="max-height: 400px; overflow-y: auto;">
                    @forelse($ratings as $rating)
                        <div class="rating-item mb-3 pb-3 border-bottom" data-rating="{{ $rating->rating }}"
                            data-has-comment="{{ $rating->comment ? 'yes' : 'no' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>{{ $rating->user->fullName($rating->user) }}</strong>
                                    <div class="rating-stars-small">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span
                                                class="star-small {{ $i <= $rating->rating ? 'filled' : '' }}">★</span>
                                        @endfor
                                        <span class="text-muted ms-2">({{ $rating->rating }}/5)</span>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $rating->created_at->diffForHumans() }}</small>
                            </div>
                            @if ($rating->comment)
                                <div class="rating-comment p-2 bg-light rounded">
                                    <small class="text-muted d-block mb-1"><i class="fas fa-comment"></i>
                                        Comment:</small>
                                    <p class="mb-0">{{ $rating->comment }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-star-half-alt fa-3x mb-3 opacity-25"></i>
                            <p>No ratings yet for this workshop.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Rating Overview */
    .average-rating-display .avg-number {
        font-size: 3rem;
        font-weight: bold;
        color: #ffc107;
    }

    .stars-display .star {
        font-size: 1.5rem;
        color: #ddd;
        margin: 0 2px;
    }

    .stars-display .star.filled {
        color: #ffc107;
    }

    .stars-display .star.half-filled {
        background: linear-gradient(90deg, #ffc107 50%, #ddd 50%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Rating Distribution */
    .rating-bar-item .star-small {
        color: #ffc107;
        font-size: 1rem;
    }

    .rating-bar-item .star-label {
        font-weight: 600;
    }

    .rating-bar-item .count-label {
        text-align: right;
        font-weight: 600;
        color: #666;
    }

    /* Individual Ratings */
    .rating-stars-small .star-small {
        color: #ddd;
        font-size: 1rem;
    }

    .rating-stars-small .star-small.filled {
        color: #ffc107;
    }

    .rating-comment {
        border-left: 3px solid #ffc107;
    }

    .rating-item {
        transition: all 0.3s ease;
    }

    .rating-item.hidden {
        display: none;
    }

    .card {
        border-radius: 8px;
    }

    .card-header {
        border-bottom: 2px solid #dee2e6;
    }
</style>

<script>
    $(document).ready(function() {
        // Filter functionality
        $('.filter-buttons label').on('click', function() {
            const filter = $(this).data('filter');

            $('.rating-item').each(function() {
                const rating = parseInt($(this).data('rating'));
                const hasComment = $(this).data('has-comment');
                let show = false;

                switch (filter) {
                    case 'all':
                        show = true;
                        break;
                    case 'comments':
                        show = hasComment === 'yes';
                        break;
                    case '5':
                        show = rating === 5;
                        break;
                    case 'low':
                        show = rating <= 2;
                        break;
                }

                if (show) {
                    $(this).removeClass('hidden').fadeIn(300);
                } else {
                    $(this).addClass('hidden').fadeOut(300);
                }
            });
        });
    });
</script>
