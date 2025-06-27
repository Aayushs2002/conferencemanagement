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
                                        $id = $passNameTags[$i]->id ?? null;
                                    @endphp
                                    <tr id="row{{ $i + 1 }}">
                                        <td>{{ $i + 1 }}.</td>
                                        <td>
                                            <select name="member_type_id[{{ $i }}]"
                                                class="form-control member-select" required>
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
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="name_tag[{{ $i }}]" class="form-control"
                                                placeholder="Enter Name Tag" value="{{ $nameTag }}" required>
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
                                        <input type="hidden" name="member_ids[{{ $i }}]"
                                            value="{{ $id }}">
                                    </tr>
                                @endfor

                                @if ($rows == 0)
                                    <tr id="row1">
                                        <td>1.</td>
                                        <td>
                                            <select name="member_type_id[0]" class="form-control member-select" required>
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
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="name_tag[0]" class="form-control"
                                                placeholder="Enter Name Tag" required>
                                        </td>
                                        <td>
                                            <button type="button" name="add" id="add"
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
                <select name="member_type_id[${i - 1}]" class="form-control member-select" required>
                    <option value="" hidden>-- Select Member Type --</option>
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
                </select>
            </td>
            <td>
                <input type="text" name="name_tag[${i - 1}]" class="form-control" placeholder="Enter Name Tag" required>
            </td>
            <td>
                <button type="button" name="remove" class="btn btn-danger btn_remove">Remove</button>
            </td>
              <input type="hidden" name="member_ids[${i - 1}]" value="">
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

                $(this).find('select.member-select').attr('name', `member_type_id[${index}]`);
                $(this).find('select[name^="registrant_type"]').attr('name', `registrant_type[${index}]`);
                $(this).find('input[name^="name_tag"]').attr('name', `name_tag[${index}]`);
            });

            i = $('#dynamic_field tbody tr').length;
        });
    </script>
@endsection
