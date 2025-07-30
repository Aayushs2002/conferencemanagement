<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <b class="text-center mb-4">Add On for <code>(Conference:
                {{ $conference->conference_theme }})</code></b>

    </div>
    <div class="modal-body">
        <div class="table-responsive">
            <div class="table-responsive">
                <form action="#" method="POST" enctype="multipart/form-data" id="conferenceAddonForm">
                    @csrf
                    <input type="hidden" name="conference_id" value="{{ $conference->id }}">
                    <table class="table table-bordered" id="dynamic_field">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Add On Name</th>
                                <th>National Amount <code>(in Rs.)</code></th>
                                <th>International Amount <code>(in $)</code></th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $rows = old('member_type_id')
                                    ? count(old('member_type_id'))
                                    : (isset($addOns)
                                        ? $addOns->count()
                                        : 1);
                            @endphp

                            @for ($i = 0; $i < $rows; $i++)
                                @php

                                    $addonName =
                                        old("addon_name.$i") ?? (isset($addOns[$i]) ? $addOns[$i]->addon_name : '');
                                    $addonNationalAmount =
                                        old("addon_national_amount.$i") ??
                                        (isset($addOns[$i]) ? $addOns[$i]->addon_national_amount : '');
                                    $addonInternationalAmount =
                                        old("addon_international_amount.$i") ??
                                        (isset($addOns[$i]) ? $addOns[$i]->addon_international_amount : '');
                                    $id = $addOns[$i]->id ?? null;
                                @endphp
                                <tr id="row{{ $i + 1 }}">
                                    <td>{{ $i + 1 }}.</td>
                                    <td>
                                        <input type="text" name="addon_name[{{ $i }}]"
                                            class="form-control" placeholder="Enter Addon Name"
                                            value="{{ $addonName }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="addon_national_amount[{{ $i }}]"
                                            class="form-control numericValue" placeholder="Enter Amount (in Rs)"
                                            value="{{ $addonNationalAmount }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="addon_international_amount[{{ $i }}]"
                                            class="form-control numericValue" placeholder="Enter Amount (in $)"
                                            value="{{ $addonInternationalAmount }}" required>
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
                                    <input type="hidden" name="addon_ids[{{ $i }}]"
                                        value="{{ $id }}">
                                </tr>
                            @endfor

                            @if ($rows == 0)
                                <tr id="row1">
                                    <td>1.</td>
                                    <td>
                                        <input type="text" name="addon_name[0]" class="form-control"
                                            placeholder="Enter Addon Name" value="" required>
                                    </td>
                                    <td>
                                        <input type="text" name="addon_national_amount[0]"
                                            class="form-control numericValue" placeholder="Enter Amount (in Rs)"
                                            required>
                                    </td>
                                    <td>
                                        <input type="text" name="addon_international_amount[0]"
                                            class="form-control numericValue" placeholder="Enter Amount (in $)"
                                            required>
                                    </td>
                                    <td>
                                        <button type="button" name="add" id="add"
                                            class="btn btn-success">Add</button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary" id="submitData">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .border-danger {
        border-color: #dc3545 !important;
    }

    .text-danger {
        font-size: 0.85rem;
    }
</style>
<script>
    $(document).ready(function() {
        let i = {{ $rows > 0 ? $rows : 1 }};

        $('#add').click(function() {
            i++;
            let newRow = `
        <tr id="row${i}">
            <td>${i}.</td>
             <td>
                <input type="text" name="addon_name[${i - 1}]" class="form-control" placeholder="Enter Addon Name" required>
            </td>

             <td>
                <input type="text" name="addon_national_amount[${i - 1}]" class="form-control numericValue" placeholder="Enter Amount (in Rs)" required>
            </td>
            
            <td>
                <input type="text" name="addon_international_amount[${i - 1}]" class="form-control numericValue" placeholder="Enter Amount (in $)" required>
            </td>

            <td>
                <button type="button" name="remove" class="btn btn-danger btn_remove">Remove</button>
            </td>
              <input type="hidden" name="addon_ids[${i - 1}]" value="">
        </tr>
    `;
            $('#dynamic_field tbody').append(newRow);
        });

        $(document).on('click', '.btn_remove', function() {
            $(this).closest('tr').remove();

            // Re-number rows and fix input names
            $('#dynamic_field tbody tr').each(function(index) {
                let rowIndex = index + 1;
                $(this).attr('id', 'row' + rowIndex);
                $(this).find('td:first').text(rowIndex + '.');

                $(this).find('select.member-select').attr('name', `member_type_id[${index}]`);
                $(this).find('select[name^="addon_national_amount"]').attr('name',
                    `addon_national_amount[${index}]`);
                $(this).find('input[name^="addon_international_amount"]').attr('name',
                    `addon_international_amount[${index}]`);
            });

            i = $('#dynamic_field tbody tr').length;
        });

        $(".numericValue").on("keydown", function(event) {
            // Allow backspace, delete, tab, escape, and enter keys
            if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode ==
                27 || event
                .keyCode == 13 ||
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

        $("#submitData").on('click', function(e) {
            e.preventDefault();
            var data = new FormData($('#conferenceAddonForm')[0]);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{ route('conference.addon.submit') }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#submitData').attr('disabled', true).append(
                        '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                    );
                    $('.error-text').remove(); 
                },
                success: function(response) {
                    $('#submitData').attr('disabled', false).text('Update');
                    if (response.type === 'success') {
                        $(".modal").modal("hide");
                        notyf.success(response.message);
                    } else {
                        notyf.error(response.message);
                    }
                },
                error: function(xhr) {
                    $('#submitData').attr('disabled', false).text('Update');

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;

                        $.each(errors, function(field, messages) {
                            let fieldName = field.replace(/\.\d+/g,
                                '[]'); // handle array inputs
                            let input = $(`[name="${fieldName}"]`).first();

                            if (input.length) {
                                input.addClass('border-danger');
                                input.after(
                                    `<small class="text-danger error-text">${messages[0]}</small>`
                                );
                                input.on('input change', function() {
                                    $(this).removeClass('border-danger');
                                    $(this).next('.error-text').remove();
                                });
                            }

                            notyf.error(messages[0]);
                        });
                    } else {
                        notyf.error('An unexpected error occurred.');
                    }
                }
            });
        });
    });
</script>
