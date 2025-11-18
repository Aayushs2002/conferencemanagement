<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <b class="text-center mb-4">Conference Setting <code>(Conference:
                {{ $conference->conference_theme }})</code></b>

    </div>
    <form action="#" method="POST" enctype="multipart/form-data" id="conferenceSettingForm">
        <div class="row">

            <input type="hidden" name="id" value="{{ $conferenceSetting?->id }}">
            <input type="hidden" name="conference_id" value="{{ $conference?->id }}">

            <div class="col-12"> 
                <h6>1. Payment Voucher Details</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-4 mb-4">
                <label>Name</label>
                <input type="text" class="form-control" name="name" value="{{ $conferenceSetting?->name }}">
            </div>
            <div class="col-md-4 mb-4">
                <label>Signature</label>
                <input type="file" class="form-control " name="signature"
                    value="{{ $conferenceSetting?->signature }}" id="image">
                <div class="row" id="imgPreview">
                    @if ($conferenceSetting?->signature)
                        <div class="col-3 mt-2">
                            <a href="{{ asset('storage/conference/voucher/signature/' . $conferenceSetting->signature) }}"
                                target="_blank"><img
                                    src="{{ asset('storage/conference/voucher/signature/' . $conferenceSetting->signature) }}"
                                    class="img-fluid" alt="image"></a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12 mt-3"> 
                <h6>2. Conference Registration Guideline</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-4 mb-4">
                <label>Registration Guideline <code>(Only PDF, Max: 5MB)</code></label>
                <input type="file" class="form-control" name="registration_guideline"
                    id="registration_guideline" accept=".pdf">
                <div class="row" id="guidelinePreview">
                    @if ($conferenceSetting?->registration_guideline)
                        <div class="col-3 mt-2">
                            <a href="{{ asset('storage/conference/registration-guideline/' . $conferenceSetting->registration_guideline) }}"
                                target="_blank">
                                <img src="{{ asset('default-image/pdf.png') }}" class="img-fluid" alt="PDF">
                                <p class="text-center small mt-1">View PDF</p>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12 mt-3"> 
                <h6>3. YouTube Guideline Links</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-4 mb-4">
                <label>Registration Guideline YouTube Link</label>
                <input type="url" class="form-control" name="registration_guideline_youtube" 
                    value="{{ $conferenceSetting?->registration_guideline_youtube }}" 
                    placeholder="https://www.youtube.com/watch?v=...">
            </div>

            <div class="col-md-4 mb-4">
                <label>Submission Guideline YouTube Link</label>
                <input type="url" class="form-control" name="submission_guideline_youtube" 
                    value="{{ $conferenceSetting?->submission_guideline_youtube }}" 
                    placeholder="https://www.youtube.com/watch?v=...">
            </div>

            <div class="col-md-4 mb-4">
                <label>Expert Guideline YouTube Link</label>
                <input type="url" class="form-control" name="expert_guideline_youtube" 
                    value="{{ $conferenceSetting?->expert_guideline_youtube }}" 
                    placeholder="https://www.youtube.com/watch?v=...">
            </div>
        </div> 
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary"
                id="submitData">{{ empty($conferenceSetting?->id) ? 'Submit' : 'Update' }}</button>
        </div>
    </form> 

</div>

<script>
    $(".numericValue").on("keydown", function(event) {
        // Allow backspace, delete, tab, escape, and enter keys
        if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event
            .keyCode == 13 ||
            // Allow Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) ||
            // Allow home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39) ||
            // Allow numbers from the main keyboard (0-9) and the numpad (96-105)
            (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105)) {
            return;
        } else {
            event.preventDefault();
        }
    });

     $("#image").change(function() {
            let reader = new FileReader();

            $("#imgPreview").html('');

            reader.onload = function(e) {
                let fileExtension = $("#image").val().split('.').pop().toLowerCase();

                if (fileExtension === 'pdf') {
                    $("#imgPreview").append(
                        '<div class="col-3 mt-2"><img src="{{ asset('default-image/pdf.png') }}" class="img-fluid" /></div>'
                    );
                } else if (fileExtension === 'ppt' || fileExtension === 'pptx' || fileExtension === 'pptm') {
                    $("#imgPreview").append(
                        '<div class="col-3 mt-2"><img src="{{ asset('default-image/ppt.png') }}" class="img-fluid" /></div>'
                    );
                } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                    $("#imgPreview").append(
                        '<div class="col-3 mt-2"><img src="{{ asset('default-image/word.png') }}" class="img-fluid" /></div>'
                    );
                } else {
                    $("#imgPreview").append('<div class="col-3 mt-2"><img src="' + e.target.result +
                        '" class="img-fluid" /></div>');
                }
            };

            reader.readAsDataURL(this.files[0]);
        });

    $("#registration_guideline").change(function() {
        $("#guidelinePreview").html('');
        
        if (this.files && this.files[0]) {
            let fileExtension = $("#registration_guideline").val().split('.').pop().toLowerCase();
            
            if (fileExtension === 'pdf') {
                $("#guidelinePreview").append(
                    '<div class="col-3 mt-2">' +
                    '<img src="{{ asset('default-image/pdf.png') }}" class="img-fluid" />' +
                    '<p class="text-center small mt-1">PDF Selected</p>' +
                    '</div>'
                );
            }
        }
    });

    $("#submitData").on('click', function(e) {
        e.preventDefault();
        var data = new FormData($('#conferenceSettingForm')[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route('conference.setting.submit') }}',
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
