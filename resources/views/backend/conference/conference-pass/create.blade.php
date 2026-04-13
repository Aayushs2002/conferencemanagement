@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($pass_setting) ? 'Edit' : 'Add' }} Pass Setting
@endsection

@section('styles')
    <style>
        .select2-container--default .select2-selection--multiple {
            height: auto !important;
        }
    </style>
@endsection

@section('content')
    <div class="col-md">
        <div class="card"> 
            <h4 class="card-header">
                <a href="{{ route('pass-setting.index', [$society, $conference]) }}">
                    <i class="ti tabler-arrow-narrow-left"></i>
                </a>
                {{ isset($pass_setting) ? 'Edit' : 'Add' }} Pass Setting
            </h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($pass_setting) ? route('pass-setting.update', [$society, $conference, $pass_setting->id]) : route('pass-setting.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @isset($pass_setting)
                        @method('patch')
                    @endisset

                    <div class="row g-6">
                        <div class="col-12">
                            <h6>1. Pass Image And Meal Setting</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="image">Pass Image</label>
                            <input type="file" class="form-control" name="image" id="image"
                                value="{{ old('image', @$pass_setting->image) }}" />
                            <div class="row" id="imgPreview">
                                @if (isset($pass_setting) && $pass_setting->image)
                                    <div class="col-3 mt-2">
                                        <a href="{{ asset('storage/conference/conference/pass/' . $pass_setting->image) }}"
                                            target="_blank">
                                            <img src="{{ asset('storage/conference/conference/pass/' . $pass_setting->image) }}"
                                                class="img-fluid" alt="image">
                                        </a>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="lunch_start_time">Lunch Start Time <code>*</code></label>
                            <input type="text" class="form-control @error('lunch_start_time') is-invalid @enderror"
                                id="lunch_start_time" placeholder="Enter Lunch Start Time" name="lunch_start_time"
                                value="{{ old('lunch_start_time', @$pass_setting->lunch_start_time) }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Lunch Start Time.</div>
                            @error('lunch_start_time')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="lunch_end_time">Lunch End Time <code>*</code></label>
                            <input type="text" class="form-control @error('lunch_end_time') is-invalid @enderror"
                                id="lunch_end_time" placeholder="Enter Lunch End Time" name="lunch_end_time"
                                value="{{ old('lunch_end_time', @$pass_setting->lunch_end_time) }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Lunch End Time.</div>
                            @error('lunch_end_time')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="dinner_start_time">Dinner Start Time <code>*</code></label>
                            <input type="text" class="form-control @error('dinner_start_time') is-invalid @enderror"
                                id="dinner_start_time" placeholder="Enter Dinner Start Time" name="dinner_start_time"
                                value="{{ old('dinner_start_time', @$pass_setting->dinner_start_time) }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Dinner Start Time.</div>
                            @error('dinner_start_time')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="dinner_end_time">Dinner End Time <code>*</code></label>
                            <input type="text" class="form-control @error('dinner_end_time') is-invalid @enderror"
                                id="dinner_end_time" placeholder="Enter Dinner End Time" name="dinner_end_time"
                                value="{{ old('dinner_end_time', @$pass_setting->dinner_end_time) }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Dinner End Time.</div>
                            @error('dinner_end_time')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-4">
                            <label class="form-label" for="border_color">Border Color</label>
                            <input type="color" class="form-control @error('border_color') is-invalid @enderror"
                                id="border_color" name="border_color"
                                value="{{ old('border_color', @$pass_setting->border_color ?? '#00aeef') }}"
                                style="height: 40px;" />
                            @error('border_color')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <h6>1.1. Workshop Pass Setting</h6>
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                            <p class="text-muted small">Configure pass designations for workshop participants and trainers.</p>
                        </div>

                        <div class="mb-6 col-md-3">
                            <label class="form-label" for="workshop_participant_name_tag">Workshop Participant Name Tag</label>
                            <input type="text" class="form-control @error('workshop_participant_name_tag') is-invalid @enderror"
                                id="workshop_participant_name_tag" name="workshop_participant_name_tag"
                                placeholder="e.g., Workshop Participant"
                                value="{{ old('workshop_participant_name_tag', @$pass_setting->workshop_participant_name_tag) }}" />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter workshop participant name tag.</div>
                            @error('workshop_participant_name_tag')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-3">
                            <label class="form-label" for="workshop_participant_color">Workshop Participant Color</label>
                            <input type="color" class="form-control @error('workshop_participant_color') is-invalid @enderror"
                                id="workshop_participant_color" name="workshop_participant_color"
                                value="{{ old('workshop_participant_color', @$pass_setting->workshop_participant_color ?? '#7367f0') }}"
                                style="height: 40px;" />
                            @error('workshop_participant_color')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-3">
                            <label class="form-label" for="workshop_trainer_name_tag">Workshop Trainer Name Tag</label>
                            <input type="text" class="form-control @error('workshop_trainer_name_tag') is-invalid @enderror"
                                id="workshop_trainer_name_tag" name="workshop_trainer_name_tag"
                                placeholder="e.g., Workshop Trainer"
                                value="{{ old('workshop_trainer_name_tag', @$pass_setting->workshop_trainer_name_tag) }}" />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter workshop trainer name tag.</div>
                            @error('workshop_trainer_name_tag')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-3">
                            <label class="form-label" for="workshop_trainer_color">Workshop Trainer Color</label>
                            <input type="color" class="form-control @error('workshop_trainer_color') is-invalid @enderror"
                                id="workshop_trainer_color" name="workshop_trainer_color"
                                value="{{ old('workshop_trainer_color', @$pass_setting->workshop_trainer_color ?? '#7367f0') }}"
                                style="height: 40px;" />
                            @error('workshop_trainer_color')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 col-md-6">
                            <label class="form-label d-block" for="include_country_for_international">International Designation Country</label>
                            <input type="hidden" name="include_country_for_international" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('include_country_for_international') is-invalid @enderror"
                                    id="include_country_for_international" name="include_country_for_international" value="1"
                                    {{ old('include_country_for_international', @$pass_setting->include_country_for_international) ? 'checked' : '' }}>
                                <label class="form-check-label" for="include_country_for_international">
                                    Show participant country in designation for international participants
                                </label>
                            </div>
                            @error('include_country_for_international')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <h6>2. Pass Name Tag Configuration</h6> 
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>

                        <table class="table table-bordered" id="dynamic_field">
                            <thead>
                                <tr>
                                    <th>S.N.</th>
                                    <th>Member Type</th>
                                    <th>Registrant Type</th>
                                    <th>Name Tag</th>
                                    <th>Color</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rows = old('member_type_id')
                                        ? count(old('member_type_id'))
                                        : (isset($passNameTags)
                                            ? $passNameTags->count()
                                            : 1);
                                @endphp

                                @for ($i = 0; $i < $rows; $i++)
                                    @php
                                        $selectedMemberTypes =
                                            old("member_type_id.$i") ??
                                            (isset($passNameTags[$i]) ? $passNameTags[$i]->member_type_id : []);
                                        if (!is_array($selectedMemberTypes)) {
                                            $selectedMemberTypes = explode(',', $selectedMemberTypes);
                                        }
                                        $registrantType =
                                            old("registrant_type.$i") ??
                                            (isset($passNameTags[$i]) ? $passNameTags[$i]->registrant_type : '');
                                        $nameTag =
                                            old("name_tag.$i") ??
                                            (isset($passNameTags[$i]) ? $passNameTags[$i]->name_tag : '');
                                        $color =
                                            old("color.$i") ??
                                            (isset($passNameTags[$i]) ? $passNameTags[$i]->color : '#7367f0');
                                        $id = $passNameTags[$i]->id ?? null;
                                    @endphp
                                    <tr id="row{{ $i + 1 }}">
                                        <td>{{ $i + 1 }}.</td>
                                        <td>
                                            <select name="member_type_id[{{ $i }}][]" 
                                                class="form-control member-select" multiple required>
                                                @foreach ($memberTypes as $memberType)
                                                    <option value="{{ $memberType->id }}"
                                                        {{ in_array($memberType->id, $selectedMemberTypes) ? 'selected' : '' }}>
                                                        {{ $memberType->type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="registrant_type[{{ $i }}]" class="form-control"
                                                required>
                                                <option value="" hidden>-- Select Registrant Type --</option>
                                                <option value="1" {{ $registrantType == 1 ? 'selected' : '' }}>
                                                    Attendee</option>
                                                <option value="2" {{ $registrantType == 2 ? 'selected' : '' }}>
                                                    Speaker/Presenter</option>
                                                <option value="3" {{ $registrantType == 3 ? 'selected' : '' }}>
                                                    Session Chair</option>
                                                <option value="4" {{ $registrantType == 4 ? 'selected' : '' }}>
                                                    Special Guest</option>
                                                <option value="5" {{ $registrantType == 5 ? 'selected' : '' }}>
                                                    Organizer</option>

                                            </select>
                                        </td> 
                                        <td>
                                            <input type="text" name="name_tag[{{ $i }}]" class="form-control"
                                                placeholder="Enter Name Tag" value="{{ $nameTag }}" required>
                                        </td>
                                        <td>
                                            <input type="color" name="color[{{ $i }}]" class="form-control color-picker"
                                                value="{{ $color }}" style="height: 40px;">
                                        </td>
                                        <td>
                                            @if ($i == 0)
                                                <button type="button" name="add" id="add"
                                                    class="btn btn-success">Add</button>
                                            @else
                                                <button type="button" name="remove"
                                                    class="btn btn-danger btn_remove">Remove</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endfor

                                @if ($rows == 0)
                                    <tr id="row1">
                                        <td>1.</td>
                                        <td>
                                            <select name="member_type_id[0][]" class="form-control member-select" multiple required>
                                                <option value="" hidden>-- Select Member Type --</option>
                                                @foreach ($memberTypes as $memberType)
                                                    <option value="{{ $memberType->id }}">{{ $memberType->type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="registrant_type[0]" class="form-control" required>
                                                <option value="" hidden>-- Select Registrant Type --</option>
                                                <option value="1">Attendee</option>
                                                <option value="2">Speaker/Presenter</option>
                                                <option value="3">
                                                    Session Chair</option> 
                                                <option value="4">
                                                    Special Guest</option> 
                                                <option value="5">
                                                    Organizer</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="name_tag[0]" class="form-control"
                                                placeholder="Enter Name Tag" required>
                                        </td>
                                        <td>
                                            <input type="color" name="color[0]" class="form-control color-picker"
                                                value="#7367f0" style="height: 40px;">
                                        </td>
                                        <td>
                                            <button type="button" name="add" id="add"
                                                class="btn btn-success">Add</button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <div class="col-12 mt-6">
                            <h6>3. Committee Pass Configuration</h6> 
                            <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                        </div>

                        <table class="table table-bordered" id="committee_dynamic_field">
                            <thead>
                                <tr>
                                    <th>S.N.</th>
                                    <th>Committee</th>
                                    <th>Committee Designation</th>
                                    <th>Name Tag</th>
                                    <th>Color</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $committeeRows = old('committee_id')
                                        ? count(old('committee_id'))
                                        : (isset($committeePassDesignations)
                                            ? $committeePassDesignations->count()
                                            : 0);
                                @endphp

                                @for ($i = 0; $i < $committeeRows; $i++)
                                    @php
                                        $selectedCommitteeIds =
                                            old("committee_id.$i") ??
                                            (isset($committeePassDesignations[$i]) ? $committeePassDesignations[$i]->committee_id : []);
                                        if (!is_array($selectedCommitteeIds)) {
                                            $selectedCommitteeIds = explode(',', $selectedCommitteeIds);
                                        }
                                    @endphp
                                    <tr id="committee_row{{ $i + 1 }}">
                                        <td>{{ $i + 1 }}.</td>
                                        <td>
                                            <select name="committee_id[{{ $i }}][]" class="form-control committee-select" multiple required>
                                                @foreach ($committees as $committee)
                                                    <option value="{{ $committee->id }}"
                                                        {{ in_array($committee->id, $selectedCommitteeIds) ? 'selected' : '' }}>
                                                        {{ $committee->committee_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="committee_designation_id[{{ $i }}]" class="form-control" required>
                                                <option value="" hidden>-- Select Designation --</option>
                                                @foreach ($committeeDesignations as $designation)
                                                    <option value="{{ $designation->id }}"
                                                        {{ old("committee_designation_id.$i", isset($committeePassDesignations[$i]) ? $committeePassDesignations[$i]->designation_id : '') == $designation->id ? 'selected' : '' }}>
                                                        {{ $designation->designation }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="committee_name_tag[{{ $i }}]" class="form-control"
                                                placeholder="Enter Name Tag"
                                                value="{{ old("committee_name_tag.$i", isset($committeePassDesignations[$i]) ? $committeePassDesignations[$i]->name_tag : '') }}"
                                                required>
                                        </td>
                                        <td>
                                            <input type="color" name="committee_color[{{ $i }}]" class="form-control color-picker"
                                                value="{{ old("committee_color.$i", isset($committeePassDesignations[$i]) ? $committeePassDesignations[$i]->color : '#7367f0') }}"
                                                style="height: 40px;">
                                        </td>
                                        <td>
                                            @if ($i == 0)
                                                <button type="button" name="add_committee" id="add_committee"
                                                    class="btn btn-success">Add</button>
                                            @else
                                                <button type="button" class="btn btn-danger btn_remove_committee">Remove</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endfor

                                @if ($committeeRows == 0)
                                    <tr id="committee_row1">
                                        <td>1.</td>
                                        <td>
                                            <select name="committee_id[0][]" class="form-control committee-select" multiple required>
                                                <option value="" hidden>-- Select Committee --</option>
                                                @foreach ($committees as $committee)
                                                    <option value="{{ $committee->id }}">{{ $committee->committee_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="committee_designation_id[0]" class="form-control" required>
                                                <option value="" hidden>-- Select Designation --</option>
                                                @foreach ($committeeDesignations as $designation)
                                                    <option value="{{ $designation->id }}">{{ $designation->designation }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="committee_name_tag[0]" class="form-control"
                                                placeholder="Enter Name Tag" required>
                                        </td>
                                        <td>
                                            <input type="color" name="committee_color[0]" class="form-control color-picker"
                                                value="#7367f0" style="height: 40px;">
                                        </td>
                                        <td>
                                            <button type="button" name="add_committee" id="add_committee"
                                                class="btn btn-success">Add</button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <div class="row mt-6">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($pass_setting) ? 'Update' : 'Submit' }}</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#lunch_start_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });

            flatpickr("#lunch_end_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });

            flatpickr("#dinner_start_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });

            flatpickr("#dinner_end_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
        });
    </script>

    <script>
        let i = {{ $rows > 0 ? $rows : 1 }};

        function initializeSelect2() {
            $('.member-select').select2({
                placeholder: 'Select Member Type',
                width: '100%'
            });
        }

        $(document).ready(function() {
            initializeSelect2();
        });

        $('#add').click(function() {
            i++;
            let newRow = `
        <tr id="row${i}">
            <td>${i}.</td>
            <td>
                <select name="member_type_id[${i - 1}][]" class="form-control member-select" multiple required>
                    @foreach ($memberTypes as $memberType)
                        <option value="{{ $memberType->id }}">{{ $memberType->type }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="registrant_type[${i - 1}]" class="form-control" required>
                    <option value="" hidden>-- Select Registrant Type --</option>
                    <option value="1">Attendee</option>
                    <option value="2">Speaker/Presenter</option>
                    <option value="3">Session Chair</option>
                    <option value="4">Special Guest</option>
                    <option value="5">Organizer</option>
                </select>
            </td>
            <td>
                <input type="text" name="name_tag[${i - 1}]" class="form-control" placeholder="Enter Name Tag" required>
            </td>
            <td>
                <input type="color" name="color[${i - 1}]" class="form-control color-picker" value="#7367f0" style="height: 40px;">
            </td>
            <td>
                <button type="button" name="remove" class="btn btn-danger btn_remove">Remove</button>
            </td>
        </tr>
    `;
            $('#dynamic_field tbody').append(newRow);
            initializeSelect2();
        });

        $(document).on('click', '.btn_remove', function() {
            $(this).closest('tr').remove();

            // Re-number rows and fix input names
            $('#dynamic_field tbody tr').each(function(index) {
                let rowIndex = index + 1;
                $(this).attr('id', 'row' + rowIndex);
                $(this).find('td:first').text(rowIndex + '.');

                $(this).find('select.member-select').attr('name', `member_type_id[${index}][]`);
                $(this).find('select[name^="registrant_type"]').attr('name', `registrant_type[${index}]`);
                $(this).find('input[name^="name_tag"]').attr('name', `name_tag[${index}]`);
                $(this).find('input[name^="color"]').attr('name', `color[${index}]`);
            });

            i = $('#dynamic_field tbody tr').length;
        });

        // Committee dynamic fields
        let j = {{ isset($committeePassDesignations) && $committeePassDesignations->count() > 0 ? $committeePassDesignations->count() : 1 }};

        function initializeCommitteeSelect2() {
            $('.committee-select').select2({
                placeholder: 'Select Committee',
                width: '100%'
            });
        }

        $(document).ready(function() {
            initializeCommitteeSelect2();
        });

        $('#add_committee').click(function() {
            j++;
            let newCommitteeRow = `
        <tr id="committee_row${j}">
            <td>${j}.</td>
            <td>
                <select name="committee_id[${j - 1}][]" class="form-control committee-select" multiple required>
                    @foreach ($committees as $committee)
                        <option value="{{ $committee->id }}">{{ $committee->committee_name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="committee_designation_id[${j - 1}]" class="form-control" required>
                    <option value="" hidden>-- Select Designation --</option>
                    @foreach ($committeeDesignations as $designation)
                        <option value="{{ $designation->id }}">{{ $designation->designation }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="committee_name_tag[${j - 1}]" class="form-control" placeholder="Enter Name Tag" required>
            </td>
            <td>
                <input type="color" name="committee_color[${j - 1}]" class="form-control color-picker" value="#7367f0" style="height: 40px;">
            </td>
            <td>
                <button type="button" name="remove" class="btn btn-danger btn_remove_committee">Remove</button>
            </td>
        </tr>
    `;
            $('#committee_dynamic_field tbody').append(newCommitteeRow);
            initializeCommitteeSelect2();
        });

        $(document).on('click', '.btn_remove_committee', function() {
            $(this).closest('tr').remove();

            // Re-number rows and fix input names
            $('#committee_dynamic_field tbody tr').each(function(index) {
                let rowIndex = index + 1;
                $(this).attr('id', 'committee_row' + rowIndex);
                $(this).find('td:first').text(rowIndex + '.');

                $(this).find('select.committee-select').attr('name', `committee_id[${index}][]`);
                $(this).find('select[name^="committee_designation_id"]').attr('name', `committee_designation_id[${index}]`);
                $(this).find('input[name^="committee_name_tag"]').attr('name', `committee_name_tag[${index}]`);
                $(this).find('input[name^="committee_color"]').attr('name', `committee_color[${index}]`);
            });

            j = $('#committee_dynamic_field tbody tr').length;
        });
    </script>
@endsection
