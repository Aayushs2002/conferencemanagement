@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($workshop) ? 'Edit' : 'Apply for' }}
    Workshop
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header">
                <a href="{{ route('my-society.conference.my-workshop.index', [$society, $conference]) }}">
                    <i class="ti tabler-arrow-narrow-left"></i>
                </a>
                {{ isset($workshop) ? 'Edit' : 'Apply for' }} Workshop
            </h4>
            <div class="card-body">
                @if (isset($workshop) && $workshop->admin_remarks)
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <h6 class="alert-heading mb-2">
                            <i class="ti tabler-alert-triangle me-2"></i>Admin Feedback
                        </h6>
                        <p class="mb-0">{{ $workshop->admin_remarks }}</p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form class="needs-validation"
                    action="{{ isset($workshop) ? route('my-society.conference.my-workshop.update', [$society, $conference, $workshop]) : route('my-society.conference.my-workshop.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($workshop)
                        @method('patch')
                    @endisset
                    <div class="row g-6">
                        <div class="col-12">
                            <h6>1. Organization/Institute Details</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="overview_of_organiztion">Overview of organization/
                                institute

                                <code>*</code></label>
                            <textarea class="form-control ckeditor" id="overview_of_organiztion" name="overview_of_organiztion" rows="5"
                                cols="30">{{ !empty(old('overview_of_organiztion')) ? old('overview_of_organiztion') : @$workshop->overview_of_organiztion }}</textarea>
                            @error('overview_of_organiztion')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12">
                            <h6>2. Workshop Details</h6>
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

                        <!-- Paid-only fields -->
                        <div id="paid-fields" class="row" style="display: none;">
                            <div class="mb-6 col-md-4">
                                <label class="form-label" for="proposed_budget">Proposed Budget <code>*</code></label>
                                <input type="number" min="0" step="0.01"
                                    class="form-control @error('proposed_budget') is-invalid @enderror" id="proposed_budget"
                                    name="proposed_budget"
                                    value="{{ !empty(old('proposed_budget')) ? old('proposed_budget') : @$workshop->proposed_budget }}"
                                    placeholder="Enter Proposed Budget" />
                                @error('proposed_budget')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-6 col-md-4">
                                <label class="form-label" for="registration_fee">Registration Fee <code>*</code></label>
                                <input type="number" min="0" step="0.01"
                                    class="form-control @error('registration_fee') is-invalid @enderror"
                                    id="registration_fee" name="registration_fee"
                                    value="{{ !empty(old('registration_fee')) ? old('registration_fee') : @$workshop->registration_fee }}"
                                    placeholder="Enter Registration Fee" />
                                @error('registration_fee')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
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
                            <input type="text"
                                class="form-control @error('registration_deadline') is-invalid @enderror"
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
                            <input type="text"
                                class="form-control @error('contact_person_phone') is-invalid @enderror"
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

                        <div class="col-md-4 form-group mb-3">
                            <label for="image">Image <code>(Only JPG/PNG)</code></label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                name="image" id="image" />
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div class="row" id="imgPreview">
                                @if (isset($workshop))
                                    <div class="col-3 mt-2">
                                        <img src="{{ asset('storage/workshop/workshop/image/' . $workshop->image) }}"
                                            alt="image" class="img-fluid">
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if (current_user()->type == 3)
                            <div class="col-md-5 form-group mb-3">
                                <label for="schedule_plan_attachment">Schedule/Plan Document <code>*</code></label>
                                <input type="file"
                                    class="form-control @error('schedule_plan_attachment') is-invalid @enderror"
                                    name="schedule_plan_attachment" id="schedule_plan_attachment"
                                    accept=".pdf,.doc,.docx" @if (!isset($workshop)) required @endif />
                                <small class="text-muted">Upload workshop schedule or plan (PDF, DOC, DOCX - Max 5MB) -
                                    Required for application</small>
                                @error('schedule_plan_attachment')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                                @if (isset($workshop) && $workshop->schedule_plan_attachment)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $workshop->schedule_plan_attachment) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ti tabler-download me-1"></i> View Current Document
                                        </a>
                                        <small class="d-block text-muted mt-1">Upload a new file to replace the current
                                            document</small>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mb-6">
                            <label class="form-label" for="description">Workshop Objective/Description
                                <code>*</code></label>
                            <textarea class="form-control ckeditor" id="workshop_description" name="workshop_description" rows="5"
                                cols="30">{{ !empty(old('workshop_description')) ? old('workshop_description') : @$workshop->workshop_description }}</textarea>
                            @error('workshop_description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="training_method_expected_outcome">Training method and expected
                                outcome
                                <code>*</code></label>
                            <textarea class="form-control ckeditor" id="training_method_expected_outcome" name="training_method_expected_outcome"
                                rows="5" cols="30">{{ !empty(old('training_method_expected_outcome')) ? old('training_method_expected_outcome') : @$workshop->training_method_expected_outcome }}</textarea>
                            @error('training_method_expected_outcome')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="resource_requirement">Resource requirement
                                <code>*</code></label>
                            <textarea class="form-control ckeditor" id="resource_requirement" name="resource_requirement" rows="5"
                                cols="30">{{ !empty(old('resource_requirement')) ? old('resource_requirement') : @$workshop->resource_requirement }}</textarea>
                            @error('resource_requirement')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12">
                            <h6>3. Workshop Venue Details</h6>
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
                            <h6>4. Coordinator Detail</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>

                        <div class="col-md-5 form-group mb-3">
                            <label for="chairperson_id">Workshop Coordinator <code>*</code></label>
                            <select name="chairperson_id" class="form-control select2" id="chairperson_id" required>
                                <option value="" hidden>-- Select Workshop Coordinator --</option>

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


                        {{-- <div class="mb-6 col-md-5">
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
                        </div> --}}

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
            const startDatePicker = flatpickr(startInput, {
                dateFormat: "Y-m-d"
            });

            const registrationDeadlinePicker = flatpickr(registrationDeadlineInput, {
                dateFormat: "Y-m-d"
            });

            // Add event listener to the start date picker



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

            // Show/hide paid-only fields
            function togglePaidFields() {
                const type = $('#workshop_type').val();
                if (type === '1') {
                    $('#paid-fields').show();
                } else {
                    $('#paid-fields').hide();
                }
            }
            $('#workshop_type').on('change', togglePaidFields);
            togglePaidFields(); // Initial state

        });
    </script>
@endsection
