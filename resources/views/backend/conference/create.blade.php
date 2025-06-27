@extends('backend.layouts.society.main')

@section('title')
    {{ isset($conference) ? 'Edit' : 'Add' }}
    Conference
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('conference.index', $society) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($conference) ? 'Edit' : 'Add' }}
                Conference</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($conference) ? route('conference.update', [$society, $conference]) : route('conference.store', $society) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($conference)
                        @method('patch')
                    @endisset
                    <div class="row g-6">
                        <div class="col-12">
                            <h6>1. Conference Details</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>
                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="conference_name">Conference Name <code>*</code></label>
                            <input type="text" class="form-control @error('conference_name') is-invalid @enderror"
                                id="conference_name" placeholder="Enter Conference Name" name="conference_name"
                                value="{{ !empty(old('conference_name')) ? old('conference_name') : @$conference->conference_name }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter conference name.</div>
                            @error('conference_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="conference_theme">Conference Theme</label>
                            <input type="text" class="form-control @error('conference_theme') is-invalid @enderror"
                                id="conference_theme" placeholder="Enter Conference Theme" name="conference_theme"
                                value="{{ !empty(old('conference_theme')) ? old('conference_theme') : @$conference->conference_theme }}" />
                            {{-- <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter conference theme.</div> --}}
                            @error('conference_theme')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="start_date">Start Date <code>*</code></label>
                            <input type="text" class="form-control @error('start_date') is-invalid @enderror"
                                id="start_date" placeholder="Enter Start Date" name="start_date"
                                value="{{ !empty(old('start_date')) ? old('start_date') : @$conference->start_date }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter start date.</div>
                            @error('start_date')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="end_date">End Date <code>*</code></label>
                            <input type="text" class="form-control @error('end_date') is-invalid @enderror"
                                placeholder="YYYY-MM-DD" id="end_date" name="end_date"
                                value="{{ !empty(old('end_date')) ? old('end_date') : @$conference->end_date }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter End Date.</div>
                            @error('end_date')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="early_bird_registration_deadline">Early Bird Registration
                                Deadline <code>*</code></label>
                            <input type="text"
                                class="form-control @error('early_bird_registration_deadline') is-invalid @enderror"
                                placeholder="YYYY-MM-DD" id="early_bird_registration_deadline"
                                name="early_bird_registration_deadline"
                                value="{{ !empty(old('early_bird_registration_deadline')) ? old('early_bird_registration_deadline') : @$conference->early_bird_registration_deadline }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter Early Bird Registration Deadline.</div>
                            @error('early_bird_registration_deadline')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="regular_registration_deadline">Regular Registration
                                Deadline <code>*</code></label>
                            <input type="text"
                                class="form-control @error('regular_registration_deadline') is-invalid @enderror"
                                id="regular_registration_deadline" placeholder="Enter Regular Registration Deadline"
                                name="regular_registration_deadline"
                                value="{{ !empty(old('regular_registration_deadline')) ? old('regular_registration_deadline') : @$conference->regular_registration_deadline }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter start date.</div>
                            @error('regular_registration_deadline')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="bs-validation-abb">Start Time <code>*</code></label>
                            <input type="text" class="form-control @error('start_time') is-invalid @enderror"
                                id="flatpickr-time" placeholder="Enter Start Time" name="start_time"
                                value="{{ !empty(old('start_time')) ? old('start_time') : @$conference->start_time }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Start Time.</div>
                            @error('start_time')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-2">
                            <label for="primary_color" class="form-label">Conference Primary Color <code>*</code></label>
                            <input class="form-control" type="color"
                                value="{{ !empty(old('primary_color')) ? old('primary_color') : @$conference->primary_color }}"
                                id="primary_color" style="height: 38px" name="primary_color" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter a primary color</div>
                            @error('primary_color')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-2">
                            <label for="secendary_color" class="form-label">Conference Secondary Color
                                <code>*</code></label>
                            <input class="form-control" type="color"
                                value="{{ !empty(old('secendary_color')) ? old('secendary_color') : @$conference->secendary_color }}"
                                id="secendary_color" style="height: 38px" name="secendary_color" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter a secendary color</div>
                            @error('secendary_color')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="conference_logo">Conference Logo <code> (Only JPG/PNG) (Max:
                                    250
                                    KB)</code></label>
                            <input type="file" class="form-control" name="conference_logo" id="image"
                                value="{{ !empty(old('conference_logo')) ? old('conference_logo') : @$conference->conference_logo }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($conference))
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/conference/conference/logo/' . $conference->conference_logo) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/conference/conference/logo/' . $conference->conference_logo) }}"
                                                class="img-fluid" alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('conference_logo')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="description">Conference Description <code>*</code></label>
                            <textarea class="form-control ckeditor" id="description" name="conference_description" rows="5"
                                cols="30" required>{{ !empty(old('conference_description')) ? old('conference_description') : @$conference->conference_description }}</textarea>
                            @error('conference_description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12">
                            <h6>2. Venue Details</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="venue_name">Venue Name <code>*</code></label>
                            <input type="text" class="form-control @error('venue_name') is-invalid @enderror"
                                id="venue_name" placeholder="Enter Venue Name" name="venue_name"
                                value="{{ !empty(old('venue_name')) ? old('venue_name') : @$conference->ConferenceVenueDetail->venue_name }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Venue Name.</div>
                            @error('venue_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="venue_address">Venue Address <code>*</code></label>
                            <input type="text" class="form-control @error('venue_address') is-invalid @enderror"
                                id="venue_address" placeholder="Enter Venue Address" name="venue_address"
                                value="{{ !empty(old('venue_address')) ? old('venue_address') : @$conference->ConferenceVenueDetail->venue_address }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Venue Address.</div>
                            @error('venue_address')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="venue_contact_person_name">Venue Contact Person Name
                                <code>*</code></label>
                            <input type="text"
                                class="form-control @error('venue_contact_person_name') is-invalid @enderror"
                                id="venue_contact_person_name" placeholder="Enter Contact Person Name"
                                name="venue_contact_person_name"
                                value="{{ !empty(old('venue_contact_person_name')) ? old('venue_contact_person_name') : @$conference->ConferenceVenueDetail->venue_contact_person_name }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Venue Contact Person Name.</div>
                            @error('venue_contact_person_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="venue_phone_number">Venue Phone Number <code>*</code></label>
                            <input type="number" class="form-control @error('venue_phone_number') is-invalid @enderror"
                                id="venue_phone_number" placeholder="Enter Venue Phone Number" name="venue_phone_number"
                                value="{{ !empty(old('venue_phone_number')) ? old('venue_phone_number') : @$conference->ConferenceVenueDetail->venue_phone_number }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Venue Phone Name.</div>
                            @error('venue_phone_number')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="venue_email"> Venue Email <code>*</code></label>
                            <input type="venue_email" class="form-control @error('venue_email') is-invalid @enderror"
                                id="venue_email" placeholder="Enter Venue email" name="venue_email"
                                value="{{ !empty(old('venue_email')) ? old('venue_email') : @$conference->ConferenceVenueDetail->venue_email }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Venue Email.</div>
                            @error('venue_email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="google_map_link">Google Map Link <code>*</code></label>
                            <input type="text" class="form-control @error('google_map_link') is-invalid @enderror"
                                id="google_map_link" placeholder="Enter Google Map Link" name="google_map_link"
                                value="{{ !empty(old('google_map_link')) ? old('google_map_link') : @$conference->ConferenceVenueDetail->google_map_link }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Google Map Link.</div>
                            @error('google_map_link')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12">
                            <h6>3. Organizer Details</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="organizer_name">Organizer Name <code>*</code></label>
                            <input type="text" class="form-control @error('organizer_name') is-invalid @enderror"
                                id="organizer_name" placeholder="Enter Organizer Name" name="organizer_name"
                                value="{{ !empty(old('organizer_name')) ? old('organizer_name') : @$conference->ConferenceOrganizer->organizer_name }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Organizer Name.</div>
                            @error('organizer_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="organizer_contact_person">Contact Person <code>*</code></label>
                            <input type="text"
                                class="form-control @error('organizer_contact_person') is-invalid @enderror"
                                id="organizer_contact_person" placeholder="Enter Contact Person"
                                name="organizer_contact_person"
                                value="{{ !empty(old('organizer_contact_person')) ? old('organizer_contact_person') : @$conference->ConferenceOrganizer->organizer_contact_person }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Contact Person.</div>
                            @error('organizer_contact_person')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="organizer_phone_number">Organizer Phone Number
                                <code>*</code></label>
                            <input type="number"
                                class="form-control @error('organizer_phone_number') is-invalid @enderror"
                                id="organizer_phone_number" placeholder="Enter Organizer Phone Number"
                                name="organizer_phone_number"
                                value="{{ !empty(old('organizer_phone_number')) ? old('organizer_phone_number') : @$conference->ConferenceOrganizer->organizer_phone_number }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Organizer Phone Number.</div>
                            @error('organizer_phone_number')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="organizer_email">Organizer Email
                                <code>*</code></label>
                            <input type="email" class="form-control @error('organizer_email') is-invalid @enderror"
                                id="organizer_email" placeholder="Enter Organizer Email" name="organizer_email"
                                value="{{ !empty(old('organizer_email')) ? old('organizer_email') : @$conference->ConferenceOrganizer->organizer_email }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Organizer Email.</div>
                            @error('organizer_email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="organizer_logo">Organizer Logo <code> (Only JPG/PNG) (Max:
                                    250
                                    KB)</code></label>
                            <input type="file" class="form-control" name="organizer_logo" id="image2"
                                value="{{ !empty(old('organizer_logo')) ? old('organizer_logo') : @$conference->ConferenceOrganizer->organizer_logo }}" />
                            <div class="row" id="imgPreview2">
                                @if (isset($conference))
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/conference/organizer/logo/' . $conference->ConferenceOrganizer->organizer_logo) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/conference/organizer/logo/' . $conference->ConferenceOrganizer->organizer_logo) }}"
                                                class="img-fluid" alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('organizer_logo')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="description">Organizer Description</label>
                            <textarea class="form-control ckeditor" id="description" name="organizer_description" rows="5"
                                cols="30">{{ !empty(old('organizer_description')) ? old('organizer_description') : @$conference->ConferenceOrganizer->organizer_description }}</textarea>
                            @error('organizer_description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($conference) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            const startInput = document.querySelector('#start_date');
            const endInput = document.querySelector('#end_date');
            const earlyBirdInput = document.querySelector('#early_bird_registration_deadline');
            const regularDeadlineInput = document.querySelector('#regular_registration_deadline');

            // Disable initially
            endInput.disabled = true;
            earlyBirdInput.disabled = true;
            regularDeadlineInput.disabled = true;

            // Enable inputs if old values exist
            if (startInput.value && endInput.value && earlyBirdInput.value) {
                endInput.disabled = false;
                earlyBirdInput.disabled = false;
                regularDeadlineInput.disabled = false; //  explicitly enable it
            }

            const startDatePicker = flatpickr(startInput, {
                monthSelectorType: 'static',
                static: true,
                minDate: "today",
                defaultDate: startInput.value,
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        const nextDay = new Date(selectedDates[0]);
                        nextDay.setDate(nextDay.getDate() + 1);
                        endDatePicker.set('minDate', nextDay);
                        endInput.disabled = false;
                    }
                }
            });

            const endDatePicker = flatpickr(endInput, {
                monthSelectorType: 'static',
                static: true,
                minDate: "today",
                defaultDate: endInput.value,
                onChange: function() {
                    if (startInput.value && endInput.value) {
                        earlyBirdInput.disabled = false;
                        regularDeadlineInput.disabled = !!earlyBirdInput.value;
                    }
                }
            });

            const earlyBirdPicker = flatpickr(earlyBirdInput, {
                monthSelectorType: 'static',
                static: true,
                defaultDate: earlyBirdInput.value,
                minDate: startInput.value,
                maxDate: endInput.value,
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        const nextDay = new Date(selectedDates[0]);
                        nextDay.setDate(nextDay.getDate() + 1);
                        regularDeadlineInput.disabled = false;
                        regularDeadlinePicker.set('minDate', nextDay);
                        regularDeadlinePicker.set('maxDate', endInput.value);
                    }
                }
            });

            const regularDeadlinePicker = flatpickr(regularDeadlineInput, {
                monthSelectorType: 'static',
                static: true,
                defaultDate: regularDeadlineInput.value,
                minDate: earlyBirdInput.value,
                maxDate: endInput.value
            });
        });
    </script>
@endsection
