<div class="modal-header">
    <h5 class="modal-title">Create Accommodation for {{ $user->f_name }} {{ $user->l_name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="alert alert-info">
        <strong>Participant Information:</strong><br>
        <strong>Name:</strong> {{ $user->f_name }} {{ $user->l_name }}<br>
        <strong>Country:</strong> {{ $user->userDetail->country->country_name ?? 'N/A' }}<br>
        <strong>Type:</strong> <span class="badge bg-primary">Invited Participant</span>
    </div>

    <form id="accommodationForm">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="hotel_id" class="form-label">Hotel <span class="text-danger">*</span></label>
                <select class="form-select" id="hotel_id" name="hotel_id" required>
                    <option value="">Select Hotel</option>
                    @foreach ($hotels as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="flight_number" class="form-label">Arrival Flight Number</label>
                <input type="text" class="form-control" id="flight_number" name="flight_number"
                    placeholder="e.g., AA123">
            </div>
            {{-- <div class="col-md-6 mb-3">
                <label for="airport_pickup_required" class="form-label">Airport Pickup Required</label>
                <select class="form-select" id="airport_pickup_required" name="airport_pickup_required">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div> --}}
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="arrival_date" class="form-label">Arrival Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="arrival_date" name="arrival_date" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="arrival_time" class="form-label">Arrival Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control" id="arrival_time" name="arrival_time" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="departure_date" class="form-label">Departure Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="departure_date" name="departure_date" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="departure_time" class="form-label">Departure Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control" id="departure_time" name="departure_time" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="check_in_date" class="form-label">Check-in Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="check_in_date" name="check_in_date" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="check_out_date" class="form-label">Check-out Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="check_out_date" name="check_out_date" required>
            </div>
        </div>

        <div class="row">


            {{-- <div class="col-md-6 mb-3">
                <label for="departure_flight_number" class="form-label">Departure Flight Number</label>
                <input type="text" class="form-control" id="departure_flight_number" name="departure_flight_number" placeholder="e.g., AA456">
            </div> --}}
        </div>

        {{-- <div class="mb-3">
            <label for="special_requirements" class="form-label">Special Requirements</label>
            <textarea class="form-control" id="special_requirements" name="special_requirements" rows="3" placeholder="Any special dietary requirements, accessibility needs, etc."></textarea>
        </div>

        <div class="mb-3">
            <label for="remarks" class="form-label">Admin Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Internal notes or comments"></textarea>
        </div> --}}
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-primary" id="saveAccommodation">
        <i class="ti-tablerdevice-floppy"></i> Save Accommodation
    </button>
</div>

<script>
    $(document).ready(function() {
        // Set minimum dates
        const today = new Date().toISOString().split('T')[0];
        $('#arrival_date').attr('min', today);

        $('#arrival_date').on('change', function() {
            const arrivalDate = this.value;
            $('#departure_date').attr('min', arrivalDate);
            $('#check_in_date').attr('min', arrivalDate);

            // Reset check-in date if it's before arrival date
            if ($('#check_in_date').val() && $('#check_in_date').val() < arrivalDate) {
                $('#check_in_date').val(arrivalDate);
            }
        });

        $('#departure_date').on('change', function() {
            const departureDate = this.value;
            $('#check_out_date').attr('max', departureDate);

            // Reset check-out date if it's after departure date
            if ($('#check_out_date').val() && $('#check_out_date').val() > departureDate) {
                $('#check_out_date').val(departureDate);
            }
        });

        $('#check_in_date').on('change', function() {
            const checkInDate = this.value;
            $('#check_out_date').attr('min', checkInDate);

            // Reset check-out date if it's before check-in date
            if ($('#check_out_date').val() && $('#check_out_date').val() < checkInDate) {
                $('#check_out_date').val(checkInDate);
            }
        });

        $('#saveAccommodation').on('click', function() {
            const form = $('#accommodationForm')[0];
            const formData = new FormData(form);

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="ti-tablerloader fa-spin"></i> Saving...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('conference.accommodation.storeForInvited', [$society, $conference]) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#accommodationModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    let message = 'An error occurred while saving accommodation details.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });

                    btn.prop('disabled', false).html(
                        '<i class="ti-tablerdevice-floppy"></i> Save Accommodation');
                }
            });
        });
    });
</script>
