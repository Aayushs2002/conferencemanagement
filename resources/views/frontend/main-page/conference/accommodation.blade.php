@extends('backend.layouts.conference.main')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Accommodation Details</h5>
                    </div>
                    <div class="card-body">
                        {{-- @dd($accommodation->arrival_date) --}}
                        <form action="{{ route('my-society.conference.accommodation.store', [$society, $conference]) }}"
                            method="POST">
                            @csrf

                            <div class="row g-4">
                                <!-- Flight Details -->
                                <div class="col-md-12">
                                    <h6 class="mb-3">Flight Details</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Flight Number</label>
                                            <input type="text" name="flight_number" class="form-control"
                                                value="{{ old('flight_number', $accommodation->flight_number ?? '') }}"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Arrival Date</label>
                                            <input type="date" name="arrival_date" class="form-control"
                                                value="{{ old('arrival_date', $accommodation?->arrival_date ? \Carbon\Carbon::parse($accommodation->arrival_date)->format('Y-m-d') : '') }}"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Arrival Time</label>
                                            <input type="time" name="arrival_time" class="form-control"
                                                value="{{ old('arrival_time', $accommodation?->arrival_time ? \Carbon\Carbon::parse($accommodation->arrival_time)->format('H:i') : '') }}"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Departure Date</label>
                                            <input type="date" name="departure_date" class="form-control"
                                                value="{{ old('departure_date', $accommodation?->departure_date ? \Carbon\Carbon::parse($accommodation->departure_date)->format('Y-m-d') : '') }}"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Departure Time</label>
                                            <input type="time" name="departure_time" class="form-control"
                                                value="{{ old('departure_time', $accommodation?->departure_time ? \Carbon\Carbon::parse($accommodation->departure_time)->format('H:i') : '') }}"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hotel Details -->
                                <div class="col-md-12">
                                    <h6 class="mb-3">Hotel Details</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Select Hotel</label>
                                            <select name="hotel_id" class="form-select" required>
                                                <option value="">Select a hotel</option>
                                                @foreach ($hotels as $hotel)
                                                    <option value="{{ $hotel->id }}"
                                                        {{ old('hotel_id', $accommodation->hotel_id ?? '') == $hotel->id ? 'selected' : '' }}>
                                                        {{ $hotel->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- <div class="col-md-6">
                                            <label class="form-label">Room Type</label>
                                            <select name="room_type" class="form-select" required>
                                                <option value="">Select room type</option>
                                                <option value="single" {{ old('room_type', $accommodation->room_type ?? '') == 'single' ? 'selected' : '' }}>Single</option>
                                                <option value="double" {{ old('room_type', $accommodation->room_type ?? '') == 'double' ? 'selected' : '' }}>Double</option>
                                                <option value="suite" {{ old('room_type', $accommodation->room_type ?? '') == 'suite' ? 'selected' : '' }}>Suite</option>
                                            </select>
                                        </div> --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Check-in Date</label>
                                            <input type="date" name="check_in_date" class="form-control"
                                                value="{{ old('check_in_date', $accommodation?->check_in_date ? \Carbon\Carbon::parse($accommodation->check_in_date)->format('Y-m-d') : '') }}"
                                                required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Check-out Date</label>
                                            <input type="date" name="check_out_date" class="form-control"
                                                value="{{ old('check_out_date', $accommodation?->check_out_date ? \Carbon\Carbon::parse($accommodation->check_out_date)->format('Y-m-d') : '') }}"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Additional Requirements -->
                                    {{-- <div class="col-md-12">
                                    <h6 class="mb-3">Additional Requirements</h6>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="form-check mb-3">
                                                <input type="checkbox" name="airport_pickup_required" class="form-check-input" 
                                                    id="airport_pickup" value="1" 
                                                    {{ old('airport_pickup_required', $accommodation->airport_pickup_required ?? '') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="airport_pickup">
                                                    I require airport pickup service
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Special Requirements</label>
                                            <textarea name="special_requirements" class="form-control" rows="3" 
                                                placeholder="Any special requirements or requests...">{{ old('special_requirements', $accommodation->special_requirements ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div> --}}
                                </div>

                                <div class="mt-4 text-end   ">
                                    <button type="submit" class="btn btn-primary">Save Accommodation Details</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Auto-populate check-in/out dates based on arrival/departure
        document.querySelector('input[name="arrival_date"]').addEventListener('change', function() {
            if (!document.querySelector('input[name="check_in_date"]').value) {
                document.querySelector('input[name="check_in_date"]').value = this.value;
            }
        });

        document.querySelector('input[name="departure_date"]').addEventListener('change', function() {
            if (!document.querySelector('input[name="check_out_date"]').value) {
                document.querySelector('input[name="check_out_date"]').value = this.value;
            }
        });
    </script>
@endsection
