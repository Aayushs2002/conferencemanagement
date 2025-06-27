<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h4 class="text-center mb-4 " style="background: white;">Meal Preference</h4>
        <div class="row">
            <div class="col-md-12 form-group mb-3">
                <label for="meal_type">Meal Preference <code>*</code></label>
                <select name="meal_type" class="form-control" id="meal_type">
                    <option value="" hidden>-- Select Veg/Non-veg --</option>
                    <option value="1">Veg</option>
                    <option value="2">Non-veg</option>
                </select>
                <p class="text-danger" id="meal_type_error"></p>
                <div class="text-right mt-4">
                    <button type="submit" data-id="{{ $registrant->id }}" class="btn btn-primary"
                        id="submitButton">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#submitButton').click(function(e) {
            e.preventDefault();

            $('#meal_type_error').text("");

            let mealType = $('#meal_type').val();
            var id = $(this).data('id');
            let submitButton = $('#submitButton');

            submitButton.prop('disabled', true).html(
                'Submitting... <span class="spinner-border spinner-border-sm"></span>');
            $.ajax({
                url: "{{ route('my-society.conference.workshop.submitMealPreference', [$society, $conference]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    meal_type: mealType,
                    id: id

                },
                success: function(response) {
                    notyf.success(response.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.meal_type) {
                            $('#meal_type_error').text(errors.meal_type[
                                0]);
                        }
                    }
                    submitButton.prop('disabled', false).html(
                        'Submit');
                }
            });
        });
    });
</script>
