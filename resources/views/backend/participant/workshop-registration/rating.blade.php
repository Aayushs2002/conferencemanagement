<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h5 class="text-center mb-4" style="background: white;">Rate workshop:
            <code>{{ $registrant->workshop->workshop_title }}</code>
        </h5>
        <div class="row">
            <div class="col-md-12 form-group mb-3">
                <label class="d-block text-center mb-3">Your Rating <code>*</code></label>
                <div class="rating-stars text-center mb-3">
                    <span class="star" data-rating="1">★</span>
                    <span class="star" data-rating="2">★</span>
                    <span class="star" data-rating="3">★</span>
                    <span class="star" data-rating="4">★</span>
                    <span class="star" data-rating="5">★</span>
                </div>
                <p class="text-danger text-center" id="rating_error"></p>
                <input type="hidden" id="rating_value" value="{{ $existingRating->rating ?? '' }}">

                <div id="comment_section" style="{{ isset($existingRating) ? '' : 'display: none;' }}">
                    <label for="rating_comment">Comment (Optional)</label>
                    <textarea name="rating_comment" id="rating_comment" class="form-control" rows="4"
                        placeholder="Share your experience with this workshop...">{{ $existingRating->comment ?? '' }}</textarea>
                    <p class="text-danger" id="comment_error"></p>
                </div>

                <div class=" mt-4 text-end">
                    <button type="submit" data-id="{{ $registrant->id }}" class="btn btn-primary" id="submitButton"
                        {{ isset($existingRating) ? '' : 'disabled' }}>
                        {{ isset($existingRating) ? 'Update Rating' : 'Submit Rating' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rating-stars {
        font-size: 3rem;
        cursor: pointer;
        letter-spacing: 10px;
    }

    .rating-stars .star {
        color: #ddd;
        transition: all 0.2s ease;
        display: inline-block;
        cursor: pointer;
        user-select: none;
    }

    .rating-stars .star:hover,
    .rating-stars .star.hover {
        color: #ffc107;
        transform: scale(1.1);
    }

    .rating-stars .star.selected {
        color: #ffc107;
    }
</style>

<script>
    $(document).ready(function() {
        let selectedRating = {{ $existingRating->rating ?? 0 }};

        // Initialize with existing rating
        if (selectedRating > 0) {
            updateStars(selectedRating);
        }

        // Star click interaction
        $('.star').on('click', function() {
            selectedRating = $(this).data('rating');
            $('#rating_value').val(selectedRating);
            $('#rating_error').text('');

            // Update star display
            updateStars(selectedRating);

            // Show comment section and enable submit button
            $('#comment_section').slideDown(300);
            $('#submitButton').prop('disabled', false);

            // Update button text if it was previously disabled
            if ($('#submitButton').text().trim() === 'Submit Rating') {
                $('#submitButton').text('Submit Rating');
            }
        });

        // Hover effect
        $('.star').on('mouseenter', function() {
            let hoverRating = $(this).data('rating');
            updateStars(hoverRating, true);
        });

        $('.rating-stars').on('mouseleave', function() {
            updateStars(selectedRating);
        });

        function updateStars(rating, isHover = false) {
            $('.star').each(function() {
                let starRating = $(this).data('rating');
                $(this).removeClass('selected hover');

                if (starRating <= rating) {
                    if (isHover) {
                        $(this).addClass('hover');
                    } else {
                        $(this).addClass('selected');
                    }
                }
            });
        }

        // Submit rating
        $('#submitButton').click(function(e) {
            e.preventDefault();

            $('#rating_error').text('');
            $('#comment_error').text('');

            let rating = $('#rating_value').val();
            let comment = $('#rating_comment').val();
            let registrantId = $(this).data('id');
            let submitButton = $('#submitButton');

            if (!rating) {
                $('#rating_error').text('Please select a rating');
                return;
            }

            submitButton.prop('disabled', true).html(
                'Submitting... <span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: "{{ route('my-society.conference.workshop.submitRating', [$society, $conference]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    rating: rating,
                    comment: comment,
                    workshop_registration_id: registrantId
                },
                success: function(response) {
                    notyf.success(response.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.rating) {
                            $('#rating_error').text(errors.rating[0]);
                        }
                        if (errors.comment) {
                            $('#comment_error').text(errors.comment[0]);
                        }
                    } else {
                        notyf.error(xhr.responseJSON?.message || 'An error occurred');
                    }
                    submitButton.prop('disabled', false).html(
                        '{{ isset($existingRating) ? 'Update Rating' : 'Submit Rating' }}'
                    );
                }
            });
        });
    });
</script>
