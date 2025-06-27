@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($workshop) ? 'Edit' : 'Add' }}
    Workshop
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('workshop.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($workshop) ? 'Edit' : 'Add' }}
                Workshop</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($workshop) ? route('workshop.update', [$society, $conference, $workshop->id]) : route('workshop.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($workshop)
                        @method('patch') 
                    @endisset
                    <div class="row g-6">
                        <div class="col-12">
                            <h6>1. Workshop Details</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>
                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="workshop_title">Workshop title <code>*</code></label>
                            <input type="text" class="form-control @error('workshop_title') is-invalid @enderror"
                                id="workshop_title" placeholder="Enter workshop Name" name="workshop_title"
                                value="{{ !empty(old('workshop_title')) ? old('workshop_title') : @$workshop->workshop_title }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter workshop tite.</div>
                            @error('workshop_title')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label for="workshop_type" class="form-label">Wokshop Type <code>*</code></label>
                            <select class="form-select" name="workshop_type" id="workshop_type" required>
                                <option value="" hidden>-- Select Workshop Type --</option>
                                <option value="1"
                                    @if (isset($workshop)) {{ $workshop->workshop_type == '1' ? 'selected' : '' }} @else @selected(old('workshop_type') == '1') @endif>
                                    Paid</option>
                                <option value="2"
                                    @if (isset($workshop)) {{ $workshop->workshop_type == '2' ? 'selected' : '' }} @else @selected(old('workshop_type') == '2') @endif>
                                    Free</option>
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please select Workshop Type.</div>
                            @error('workshop_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="start_date">Start Date <code>*</code></label>
                            <input type="text" class="form-control @error('start_date') is-invalid @enderror"
                                id="start_date" placeholder="Enter Start Date" name="start_date"
                                value="{{ !empty(old('start_date')) ? old('start_date') : @$workshop->start_date }}"
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
                                value="{{ !empty(old('end_date')) ? old('end_date') : @$workshop->end_date }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter End Date.</div>
                            @error('end_date')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="registration_deadline">Registration
                                Deadline <code>*</code></label>
                            <input type="text" class="form-control @error('registration_deadline') is-invalid @enderror"
                                id="registration_deadline" placeholder="Enter Regular Registration Deadline"
                                name="registration_deadline"
                                value="{{ !empty(old('registration_deadline')) ? old('registration_deadline') : @$workshop->registration_deadline }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter registration deadline.</div>
                            @error('registration_deadline')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="bs-validation-abb">Start Time <code>*</code></label>
                            <input type="text" class="form-control @error('start_time') is-invalid @enderror"
                                id="start-time" placeholder="Enter Start Time" name="start_time"
                                value="{{ !empty(old('start_time')) ? old('start_time') : @$workshop->start_time }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Start Time.</div>
                            @error('start_time')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="bs-validation-abb">End Time <code>*</code></label>
                            <input type="text" class="form-control @error('end_time') is-invalid @enderror"
                                id="end-time" placeholder="Enter Start Time" name="end_time"
                                value="{{ !empty(old('end_time')) ? old('end_time') : @$workshop->end_time }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please End Time.</div>
                            @error('end_time')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="bs-validation-abb">Contact Person Name <code>*</code></label>
                            <input type="text" class="form-control @error('contact_person_name') is-invalid @enderror"
                                id="contact_person_name" placeholder="Enter Contact Person Name"
                                name="contact_person_name"
                                value="{{ !empty(old('contact_person_name')) ? old('contact_person_name') : @$workshop->contact_person_name }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Contact Person Name.</div>
                            @error('contact_person_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="bs-validation-abb">Contact Person Phone <code>*</code></label>
                            <input type="text" class="form-control @error('contact_person_phone') is-invalid @enderror"
                                id="contact_person_phone" placeholder="Enter Contact Person Phone"
                                name="contact_person_phone"
                                value="{{ !empty(old('contact_person_phone')) ? old('contact_person_phone') : @$workshop->contact_person_phone }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Contact Person Phone.</div>
                            @error('contact_person_phone')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="bs-validation-abb">Contact Person email <code>*</code></label>
                            <input type="email"
                                class="form-control @error('contact_person_email') is-invalid @enderror"
                                id="contact_person_email" placeholder="Enter Contact Person email"
                                name="contact_person_email"
                                value="{{ !empty(old('contact_person_email')) ? old('contact_person_email') : @$workshop->contact_person_email }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Contact Person email.</div>
                            @error('contact_person_email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="no_of_participants" class="form-label">No. Of Participants <code>*</code></label>
                            <input type="text"
                                class="form-control @error('no_of_participants') is-invalid @enderror integerValue"
                                name="no_of_participants" id="no_of_participants" placeholder="Enter No. Of Participant"
                                value="{{ isset($workshop) ? $workshop->no_of_participants : old('no_of_participants') }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter No. of Participant.</div>
                            @error('no_of_participants')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="description">Workshop Description <code>*</code></label>
                            <textarea class="form-control ckeditor" id="workshop_description" name="workshop_description" rows="5"
                                cols="30">{{ !empty(old('workshop_description')) ? old('workshop_description') : @$workshop->workshop_description }}</textarea>
                            @error('workshop_description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12">
                            <h6>2. Workshop Venue Details</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="venue_name">Venue Name <code>*</code></label>
                            <input type="text" class="form-control @error('venue_name') is-invalid @enderror"
                                id="venue_name" placeholder="Enter Venue Name" name="venue_name"
                                value="{{ !empty(old('venue_name')) ? old('venue_name') : @$workshop->WorkshopVenueDetail->venue_name }}"
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
                                value="{{ !empty(old('venue_address')) ? old('venue_address') : @$workshop->WorkshopVenueDetail->venue_address }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Venue Address.</div>
                            @error('venue_address')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="google_map_link">Google Map Links <code>*</code></label>
                            <input type="text" class="form-control @error('google_map_link') is-invalid @enderror"
                                id="google_map_link" placeholder="Enter Google Map Link" name="google_map_link"
                                value="{{ !empty(old('google_map_link')) ? old('google_map_link') : @$workshop->WorkshopVenueDetail->google_map_link }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Google Map Link.</div>
                            @error('google_map_link')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12">
                            <h6>3. ChairPerson Detail</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>

                        <div class="col-md-5 form-group mb-3">
                            <label for="chairperson_id">Chairperson <code>*</code></label>
                            <select name="chairperson_id" class="form-control select2" id="chairperson_id" required>
                                <option value="" hidden>-- Select Chairperson --</option>

                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        @if (isset($workshop)) {{ $workshop->WorkshopChairPersonDetail->chairperson_id == $user->id ? 'selected' : '' }} @else @selected(old('chairperson_id') == $user->id) @endif>
                                        {{ $user->fullName($user) }}</option>
                                @endforeach
                            </select>
                            @error('chairperson_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="mb-6 col-md-5">
                            <label class="form-label" for="photo">Photo <code> (Only JPG/PNG) (Max:
                                    250
                                    KB)</code></label>
                            <input type="file" class="form-control" name="photo" id="image2"
                                value="{{ !empty(old('photo')) ? old('photo') : @$workshop->WorkshopChairPersonDetail->photo }}" />
                            <div class="row" id="imgPreview2">
                                @if (isset($workshop))
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/workshop/chairperson/photo/' . $workshop->WorkshopChairPersonDetail->photo) }}"
                                            target="_blank"><img
                                                src="{{ asset('storage/workshop/chairperson/photo/' . $workshop->WorkshopChairPersonDetail->photo) }}"
                                                class="img-fluid" alt="image"></a>
                                    </div>
                                @endif
                            </div>
                            @error('photo')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="short_cv">Short Cv <code>*</code></label>
                            <textarea class="form-control ckeditor" id="short_cv" name="short_cv" rows="5" cols="30">{{ !empty(old('short_cv')) ? old('short_cv') : @$workshop->WorkshopChairPersonDetail->short_cv }}</textarea>
                            @error('short_cv')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($workshop) ? 'Update' : 'Submit' }}</button>
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
            const startTime = document.querySelector('#start-time');
            const endTime = document.querySelector('#end-time');
            const startInput = document.querySelector('#start_date');
            const endInput = document.querySelector('#end_date');
            const registrationDeadlineInput = document.querySelector('#registration_deadline');

            const endDatePicker = flatpickr(endInput, {
                dateFormat: "Y-m-d"
            });

            const registrationDeadlinePicker = flatpickr(registrationDeadlineInput, {
                dateFormat: "Y-m-d"
            });

            flatpickr(startInput, {
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        const startDate = selectedDates[0];

                        // Set end_date >= start_date
                        endDatePicker.set('minDate', startDate);

                        // Set registration_deadline < start_date
                        const maxDeadline = new Date(startDate.getTime() - 1);
                        registrationDeadlinePicker.set('maxDate', maxDeadline);
                    }
                }
            });



            if (startTime) {
                startTime.flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    static: true
                });
            }
            if (endTime) {
                endTime.flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    static: true
                });
            }

        });
    </script>
@endsection
