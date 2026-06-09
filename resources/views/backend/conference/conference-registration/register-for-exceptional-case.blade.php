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
                                @foreach ($registrantTypes as $rType)
                                    <option value="{{ $rType->id }}" @selected(old('registrant_type') == $rType->id)>
                                        {{ $rType->name }}
                                    </option>
                                @endforeach
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
                            <label for="payment_status">Payment Status <code>*</code></label>
                            <select name="payment_status" class="form-control" id="payment_status" required>
                                <option value="" hidden>-- Select Payment Status --</option>
                                <option value="paid" @selected(old('payment_status', 'paid') == 'paid')>Paid</option>
                                <option value="unpaid" @selected(old('payment_status') == 'unpaid')>Unpaid</option>
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please select payment status.</div>
                            @error('payment_status')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="amount" id="amountLabel">Amount <code>* (Only Numeric Value)</code></label>
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
                        <div class="col-md-12 form-group mb-3">
                            <label for="addons_section">Add Ons</label>
                            <div id="addons_container" class="row">
                                <div class="col-md-12 text-muted">
                                    <em>Please select a user to view available add-ons</em>
                                </div>
                            </div>
                            <input type="hidden" name="selected_addons" id="selected_addons">
                            @error('conference_addon_id')
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
                                name="payment_voucher" id="payment_voucher" />
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
                    // Allow minus keys for credit amount
                    event.keyCode == 189 || event.keyCode == 109 ||
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

            function togglePaymentFieldsByStatus() {
                const paymentStatus = $('#payment_status').val() || 'paid';
                const isUnpaid = paymentStatus === 'unpaid';

                if (isUnpaid) {
                    $('#transaction_id').prop('required', false).closest('.form-group').attr('hidden', true);
                    $('#payment_voucher').prop('required', false).closest('.form-group').attr('hidden', true);
                    $('#amountLabel').html('Due/Credit Amount <code>* (Only Numeric Value)</code>');
                    if (!$('#transaction_id').val()) {
                        $('#transaction_id').val('CREDIT-' + Date.now());
                    }
                } else {
                    $('#transaction_id').prop('required', true).closest('.form-group').attr('hidden', false);
                    $('#payment_voucher').closest('.form-group').attr('hidden', false);
                    $('#amountLabel').html('Amount <code>* (Only Numeric Value)</code>');
                    if ($('#transaction_id').val() && $('#transaction_id').val().startsWith('CREDIT-')) {
                        $('#transaction_id').val('');
                    }
                }
            }

            $('#payment_status').on('change', togglePaymentFieldsByStatus);
            togglePaymentFieldsByStatus();

            // Load addons when user is selected
            $('#user_id').on('change', function() {
                var userId = $(this).val();
                if (!userId) {
                    $('#addons_container').html('<div class="col-md-12 text-muted"><em>Please select a user to view available add-ons</em></div>');
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: '{{ route('conference.conference-registration.getUserMemberTypeAddons', [$society, $conference]) }}',
                    data: {
                        user_id: userId
                    },
                    success: function(response) {
                        if (response.success && response.addons.length > 0) {
                            var html = '';
                            $.each(response.addons, function(index, addon) {
                                html += '<div class="col-md-6 mb-3">';
                                html += '<div class="card">';
                                html += '<div class="card-body">';
                                html += '<div class="form-check">';
                                html += '<input class="form-check-input addon-checkbox" type="checkbox" ';
                                html += 'name="conference_addon_id[]" value="' + addon.id + '" ';
                                html += 'id="addon_' + addon.id + '" ';
                                html += 'data-addon-id="' + addon.id + '" ';
                                html += 'data-name="' + addon.addon_name + '" ';
                                html += 'data-amount="' + addon.amount + '" ';
                                html += 'data-guest-amount="' + addon.guest_amount + '">';
                                html += '<label class="form-check-label" for="addon_' + addon.id + '">';
                                html += '<strong>' + addon.addon_name + '</strong><br>';
                                html += '<small>Main: Rs. ' + addon.amount + ' | Guest: Rs. ' + addon.guest_amount + '</small>';
                                html += '</label>';
                                html += '</div>';
                                html += '<div id="guest_option_' + addon.id + '" class="mt-2" style="display: none;">';
                                html += '<div class="form-check">';
                                html += '<input class="form-check-input addon-guest-checkbox" type="checkbox" ';
                                html += 'id="include_guest_' + addon.id + '" data-addon-id="' + addon.id + '" checked>';
                                html += '<label class="form-check-label" for="include_guest_' + addon.id + '">Include for guests</label>';
                                html += '</div></div></div></div></div>';
                            });
                            $('#addons_container').html(html);
                        } else {
                            $('#addons_container').html('<div class="col-md-12 text-muted"><em>No add-ons available for this user\'s member type</em></div>');
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr);
                        $('#addons_container').html('<div class="col-md-12 text-danger"><em>Error loading add-ons</em></div>');
                    }
                });
            });

            // Handle addon checkbox changes
            $(document).on('change', '.addon-checkbox', function() {
                const addonId = $(this).data('addon-id');
                const guestOption = $(`#guest_option_${addonId}`);
                
                if ($(this).is(':checked')) {
                    guestOption.slideDown(200);
                } else {
                    guestOption.slideUp(200);
                }
                
                updateSelectedAddons();
            });

            // Handle guest inclusion checkbox changes
            $(document).on('change', '.addon-guest-checkbox', function() {
                updateSelectedAddons();
            });

            // Update selected addons hidden field
            function updateSelectedAddons() {
                const selectedAddons = [];
                $('.addon-checkbox:checked').each(function() {
                    const addonId = $(this).val();
                    const addonAmount = $(this).data('amount');
                    const guestAmount = $(this).data('guest-amount');
                    const includeGuest = $(`#include_guest_${addonId}`).is(':checked') ? '1' : '0';
                    
                    selectedAddons.push(`${addonId}:${addonAmount}:${guestAmount}:${includeGuest}`);
                });
                
                $('#selected_addons').val(selectedAddons.join(','));
            }

            // Update on form submit
            $('#registrationForm').on('submit', function() {
                updateSelectedAddons();
                if (($('#payment_status').val() || 'paid') === 'unpaid' && !$('#transaction_id').val()) {
                    $('#transaction_id').val('CREDIT-' + Date.now());
                }
            });

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
