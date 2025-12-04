<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <b class="text-center mb-4">Society Setting <code>(Society:
                {{ $society->abbreviation }})</code></b>

    </div>
    <form action="#" method="POST" enctype="multipart/form-data" id="societySettingForm">
        <div class="row">
 
            <input type="hidden" name="id" value="{{ $societySetting?->id }}">
            <input type="hidden" name="society_id" value="{{ $society?->id }}">

            <div class="col-12">
                <h6>1. Api Details</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-6 mb-4">
                <label>Member Type Api</label>
                <input type="text" class="form-control" name="member_type_api"
                    value="{{ $societySetting?->member_type_api }}">
            </div>

            <div class="col-md-6 mb-4">
                <label>Member Detail Api</label>
                <input type="text" class="form-control" name="member_detail_api"
                    value="{{ $societySetting?->member_detail_api }}">
            </div>

            <div class="col-12 mt-3">
                <h6>2. Society Banner Content</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-12 mb-4">
                <label>Banner Title</label>
                <input type="text" class="form-control" name="banner_title"
                    value="{{ $societySetting?->banner_title }}" placeholder="e.g., Advancing Women's Health">
            </div>

            <div class="col-md-12 mb-4">
                <label>Banner Subtitle</label>
                <textarea class="form-control" name="banner_subtitle" rows="3"
                    placeholder="e.g., Institution conferences empower obstetricians and gynecologists...">{{ $societySetting?->banner_subtitle }}</textarea>
            </div>

        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary"
                id="submitData">{{ empty($societySetting?->id) ? 'Submit' : 'Update' }}</button>
        </div>
    </form>
</div>
<script>
    $("#submitData").on('click', function(e) {
        e.preventDefault();
        var data = new FormData($('#societySettingForm')[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route('society.setting.submit') }}',
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#submitData').attr('disabled', true);
                $('#submitData').append(
                    '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                );
            },
            success: function(response) {
                $('#submitData').attr('disabled', false);
                $('#submitData').text('Update');
                if (response.type == 'success') {
                    $(".modal").modal("hide");
                    notyf.success(response.message);
                    // setTimeout(function() {
                    //     window.location.reload();
                    // }, 1000);
                } else {
                    notyf.error(response.message);
                }
            },
            error: function(response) {
                var errors = response.responseJSON.errors;
                $.each(errors, function(key, val) {
                    $('.' + key).html('');
                    $('.' + key).append(val);
                    $('#' + key).addClass('border-danger');
                    $('#' + key).on('change', function() {
                        $('.' + key).html('');
                        $('#' + key).removeClass('border-danger');
                    });
                });
                $('#submitData').attr('disabled', false);
                $('#submitData').text('Update');
            }
        });
    });
</script>
