<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

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
                <h6>2. Registation Setting</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>


            <div class="col-md-4 mb-4">
                <label>Registration Required for Speaker <code>*</code></label>
                <select class="form-control" name="speaker_registration_required" required>
                    <option value="1"
                        {{ $conferenceSetting?->speaker_registration_required == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0"
                        {{ $conferenceSetting?->speaker_registration_required == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <div class="col-md-4 mb-4">
                <label>Conference Registration Open Date</label>
                <input type="date" class="form-control" name="registration_open_date" value="{{ $conferenceSetting?->registration_open_date }}">
            </div>

            <div class="col-md-4 mb-4">
                <label>Workshop Registration Open Date</label>
                <input type="date" class="form-control" name="workshop_registration_open_date" value="{{ $conferenceSetting?->workshop_registration_open_date }}">
            </div>

            <div class="col-md-4 mb-4">
                <label>Workshop Application Deadline</label>
                <input type="date" class="form-control" name="workshop_application_deadline" value="{{ $conferenceSetting?->workshop_application_deadline }}">
                <small class="text-muted">Users cannot apply for workshops after this date</small>
            </div>

            <div class="col-12 mt-3">
                <h6>3. Conference Registration Guideline</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-4 mb-4">
                <label>Registration Guideline <code>(Only PDF, Max: 5MB)</code></label>
                <input type="file" class="form-control" name="registration_guideline" id="registration_guideline"
                    accept=".pdf">
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
                <h6>4. YouTube Guideline Links</h6>
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

            <div class="col-12 mt-3">
                <h6>5. Navbar Display Settings</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-6 mb-4">
                <label>What to Display in Navbar <code>*</code></label>
                <select class="form-control" name="logo_display_type">
                    <option value="logo" {{ $conferenceSetting?->logo_display_type == 'logo' ? 'selected' : '' }}>
                        Conference Logo
                    </option>
                    <option value="abbreviation"
                        {{ $conferenceSetting?->logo_display_type == 'abbreviation' ? 'selected' : '' }}>
                        Conference Abbreviation
                    </option>
                </select>
                <small class="text-muted">If both logo and abbreviation are empty, society logo will be shown</small>
            </div>

            <div class="col-12 mt-3">
                <h6>6. Payment Instruction</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-12 mb-4">
                <label>Payment Instruction</label>
                <textarea class="form-control ckeditor" name="payment_instruction" id="payment_instruction" rows="5">{{ $conferenceSetting?->payment_instruction }}</textarea>
            </div>

            <div class="col-12 mt-3">
                <h6>7. Terms & Conditions and Privacy Policy</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
            </div>

            <div class="col-md-12 mb-4">
                <label>Terms & Conditions</label>
                <textarea class="form-control ckeditor" name="terms_conditions" id="terms_conditions" rows="8">{{ $conferenceSetting?->terms_conditions }}</textarea>
            </div>

            <div class="col-md-12 mb-4">
                <label>Privacy Policy</label>
                <textarea class="form-control ckeditor" name="privacy_policy" id="privacy_policy" rows="8">{{ $conferenceSetting?->privacy_policy }}</textarea>
            </div>

            <div class="col-12 mt-3">
                <h6>8. Custom CSS</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                <p class="text-muted small">Add custom CSS code to style your conference page. You can target any element using class selectors.</p>
            </div>

            @php
                $customCss = $conference->customCss->first();
            @endphp

            <div class="col-md-12 mb-4"> 
                <label>Custom CSS Code</label> 
                <textarea class="form-control font-monospace css-editor" name="custom_css" rows="15"
                    placeholder="/* Example: */&#10;.navbar-brand span {&#10;    color: #ff0000;&#10;    font-size: 1.8rem;&#10;}&#10;&#10;.conference-hero {&#10;    background-size: cover;&#10;    min-height: 500px;&#10;}&#10;&#10;.dash-card {&#10;    border-radius: 12px;&#10;    box-shadow: 0 4px 8px rgba(0,0,0,0.1);&#10;}">{{ $customCss?->custom_css ?? '' }}</textarea>
                <small class="text-muted">
                    <i class="ti tabler-info-circle"></i>
                    Enter CSS code with class selectors. Example: .navbar-brand, .hero-title, .countdown-box, etc.
                </small>
            </div>
 
            <div class="col-12 mt-3">
                <h6>9. Section Visibility Settings</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                <p class="text-muted small">Control which sections are displayed on the conference home page.</p>
            </div>

            <div class="col-md-12 mb-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="show_stats_dashboard" value="0">
                            <input class="form-check-input" type="checkbox" name="show_stats_dashboard" id="show_stats_dashboard" value="1"
                                {{ ($conferenceSetting?->show_stats_dashboard ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_stats_dashboard">
                                <strong>Show Stats Dashboard</strong>
                                <small class="d-block text-muted">Speakers, participants count</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <h6>10. Add-on Availability Settings</h6>
                <hr class="mt-0" style="height:1px;border:none;color:#333;background-color:#333;" />
                <p class="text-muted small">Control who can select add-ons during conference registration.</p>
            </div>

            <div class="col-md-6 mb-4">
                <label>Add-on Availability <code>*</code></label>
                <select class="form-control" name="addon_availability" required>
                    <option value="both" {{ ($conferenceSetting?->addon_availability ?? 'both') == 'both' ? 'selected' : '' }}>
                        Both Participant & Accompanying Persons
                    </option>
                    <option value="participant_only" {{ $conferenceSetting?->addon_availability == 'participant_only' ? 'selected' : '' }}>
                        Participant Only
                    </option>
                    <option value="accompany_only" {{ $conferenceSetting?->addon_availability == 'accompany_only' ? 'selected' : '' }}>
                        Accompanying Persons Only
                    </option>
                </select>
                <small class="text-muted">
                    <i class="ti tabler-info-circle"></i>
                    Determines who can select add-ons: participant, accompanying persons, or both
                </small>
            </div>

        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary"
                id="submitData">{{ empty($conferenceSetting?->id) ? 'Submit' : 'Update' }}</button>
        </div>
    </form>

</div>

<style>
    .css-editor {
        background-color: #f8f9fa;
        font-family: 'Courier New', Courier, monospace;
        font-size: 13px;
        line-height: 1.5;
        border: 1px solid #dee2e6;
    }

    .css-editor:focus {
        background-color: #fff;
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    }
</style>

<script>
    // Initialize CKEditor for payment instruction
    if (typeof CKEDITOR !== 'undefined') {
        // Destroy existing instances if any
        if (CKEDITOR.instances['payment_instruction']) {
            CKEDITOR.instances['payment_instruction'].destroy(true);
        }
        if (CKEDITOR.instances['terms_conditions']) {
            CKEDITOR.instances['terms_conditions'].destroy(true);
        }
        if (CKEDITOR.instances['privacy_policy']) {
            CKEDITOR.instances['privacy_policy'].destroy(true); 
        }

        // Initialize CKEditor for all textareas
        ['payment_instruction', 'terms_conditions', 'privacy_policy'].forEach(function(editorId) {
            CKEDITOR.replace(editorId, {
                filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                filebrowserUploadMethod: "form",
                extraPlugins: 'wordcount',
                wordcount: {
                    showWordCount: true,
                    showCharCount: true,
                    countSpacesAsChars: true,
                    countHTML: false,
                    maxCharCount: -1,
                    maxWordCount: -1
                }
            });
        });
    }

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

        // Update CKEditor data before submitting
        if (CKEDITOR.instances['payment_instruction']) {
            CKEDITOR.instances['payment_instruction'].updateElement();
        }
        if (CKEDITOR.instances['terms_conditions']) {
            CKEDITOR.instances['terms_conditions'].updateElement();
        }
        if (CKEDITOR.instances['privacy_policy']) {
            CKEDITOR.instances['privacy_policy'].updateElement();
        }

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
