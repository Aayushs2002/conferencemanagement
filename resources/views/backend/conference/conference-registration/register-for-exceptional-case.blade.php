@extends('backend.layouts.conference.main')

@section('title')
    Registered in exceptional case
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header">
                Registered in exceptional case</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ route('conference.conference-registration.registerForExceptionalCaseSubmit', [$society, $conference]) }}"
                    id="registrationForm" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="row">

                        <div class="col-md-4 form-group mb-3">
                            <label for="user_id">User <code>*</code></label>
                            <select name="user_id" class="form-control" id="user_id" required>
                                <option value="" hidden>-- Select User --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                        {{ $user->fullName($user) }}</option>
                                @endforeach
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Select user.</div>
                            @error('user_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="registrant_type">Registrant Type <code>*</code></label>
                            <select name="registrant_type" class="form-control" id="registrant_type" required>
                                <option value="" hidden>-- Select Registrant Type --</option>
                                <option value="1" @selected(old('registrant_type') == '1')>Attendee</option>
                                <option value="2" @selected(old('registrant_type') == '2')>Speaker/Presenter</option>
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Select registrant type.</div>
                            @error('registrant_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="transaction_id">Transaction ID/Bill No/Reference Code <code>*</code></label>
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror"
                                name="transaction_id" id="transaction_id" value="{{ old('transaction_id') }}"
                                placeholder="Enter transaction id or bill number" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter transaction id.</div>
                            @error('transaction_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="amount">Amount <code>* (Only Numeric Value)</code></label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror numericValue"
                                name="amount" id="amount" value="{{ old('amount') }}" placeholder="Enter amount"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter amount.</div>
                            @error('amount')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="meal_type">Meal Preference <code>*</code></label>
                            <select name="meal_type" class="form-control" id="meal_type" required>
                                <option value="" hidden>-- Select Veg/Non-veg --</option>
                                <option value="1"
                                    @if (isset($conference_registration)) {{ $conference_registration->meal_type == '1' ? 'selected' : '' }} @else @selected(old('meal_type') == '1') @endif>
                                    Veg</option>
                                <option value="2"
                                    @if (isset($conference_registration)) {{ $conference_registration->meal_type == '2' ? 'selected' : '' }} @else @selected(old('meal_type') == '2') @endif>
                                    Non-veg</option>
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Select Meal Type.</div>
                            @error('meal_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="additional_guests">Number Of Guests <code>(Excluding Registrant)</code></label>
                            <select name="additional_guests" id="additional_guests"
                                class="form-control @error('additional_guests') is-invalid @enderror">
                                <option value="">-- Select Number Of Guests --</option>
                                <option value="1" @selected(old('additional_guests') == 1)>1</option>
                                <option value="2" @selected(old('additional_guests') == 2)>2</option>
                                <option value="3" @selected(old('additional_guests') == 3)>3</option>
                                <option value="4" @selected(old('additional_guests') == 4)>4</option>
                                <option value="5" @selected(old('additional_guests') == 5)>5</option>
                            </select>
                            @error('additional_guests')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="payment_voucher">Payment Voucher <code>(Only JPG/PNG/PDF) (Max: 250
                                    KB)</code></label>
                            <input type="file" class="form-control @error('payment_voucher') is-invalid @enderror"
                                name="payment_voucher" id="image2" /> 
                            @error('payment_voucher')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div class="row" id="imgPreview2"></div>
                        </div>
                        <div class="col-md-12 form-group mb-3 speakerAdditionalSection" hidden>
                            <label for="short_cv">Short CV <code>*</code></label>
                            <textarea class="form-control ckeditor" name="short_cv" id="short_cv" cols="30" rows="5">{{ isset($participant) ? $participant->short_cv : old('short_cv') }}</textarea>
                            @error('short_cv')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <div class="row" id="accompanyPersonsDetail">

                            </div>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="submit" id="submitButton" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if (old('person_name'))
        <script>
            var personsValue = @json(old('person_name', []));
            var errorMessages = @json($errors->get('person_name.*'));
        </script>
    @else
        <script>
            var personsValue = @json([]);
            var errorMessages = @json([]);
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('#user_id').select2();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(".numericValue").on("keydown", function(event) {
                // Allow backspace, delete, tab, escape, and enter keys
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode ==
                    27 || event.keyCode == 13 ||
                    // Allow Ctrl+A
                    (event.keyCode == 65 && event.ctrlKey === true) ||
                    // Allow home, end, left, right
                    (event.keyCode >= 35 && event.keyCode <= 39) ||
                    // Allow numbers from the main keyboard (0-9) and the numpad (96-105)
                    (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <=
                        105)) {
                    return;
                } else {
                    event.preventDefault();
                }
            });


            $("#registrant_type").change(function(e) {
                e.preventDefault();
                if ($(this).val() == 2) {
                    $(".speakerAdditionalSection").attr('hidden', false);
                } else {
                    $(".speakerAdditionalSection").attr('hidden', true);
                }
            });
            $("#registrant_type").trigger("change");

            //   $("#submitButton").click(function(e) {
            //       e.preventDefault();
            //       $(this).attr('disabled', true);
            //       $("#onSiteRegisterForm").submit();
            //   });

            $("#additional_guests").change(function(e) {
                $("#accompanyPersonsDetail").empty();
                var totalAccompanyPersons = $(this).val();
                if (totalAccompanyPersons >= 1) {
                    var title =
                        '<div class="col-md-12 mt-3"><h3 class="text-danger">Accompanying Person Details:</h3><h5 class="text-danger">Note: All names are reuired</h5></div>';
                    $("#accompanyPersonsDetail").append(title);
                    for (let index = 0; index < totalAccompanyPersons; index++) {
                        var oldValue = personsValue[index] || '';
                        var errorMessage = errorMessages['person_name.' + index] ? errorMessages[
                            'person_name.' + index][0] : '';;
                        var htmlCode = '<div class="col-md-7 form-group mb-3">' +
                            '<label for="person_name">Name <code>*</code></label>' +
                            '<input type="text" class="form-control" name="person_name[]" value="' +
                            oldValue + '" placeholder="Enter accompany person name" required/>' +
                            '<p class="text-danger">' + errorMessage + '</p>' +
                            '</div>';

                        $("#accompanyPersonsDetail").append(htmlCode);
                    }
                }
            });
            $("#additional_guests").trigger("change");
        });
    </script>
@endsection
