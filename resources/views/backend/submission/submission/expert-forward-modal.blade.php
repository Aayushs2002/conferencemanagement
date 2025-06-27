<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Assign To Expert <span class="text-danger">(Topic:
                {{ $submission->title }})</span></h5>
        <hr class="py-4">
        <form id="dataForm">
            @csrf
            <div class="row">
                <input type="hidden" id="submissionId" name="id" value="{{ $submission->id }}">
                <div class="col-md-6 form-group mb-3">
                    <label for="expert_id">Expert <code>*</code></label>
                    <select name="expert_id" class="form-control @error('expert_id') is-invalid @enderror">
                        <option value="" hidden>-- Select Expert --</option>
                        @foreach ($experts as $expert)
                            <option value="{{ $expert->user_id }}" @selected(old('expert_id', @$submission->expert_id) == $expert->user_id)>
                                {{ $expert->expert->fullName($expert->expert) }}</option>
                        @endforeach
                    </select>
                    <p class="text-danger expert_id"></p>
                </div>
                <div class="col-md-12 form-group mb-3 decisionForm" id="abstractContent">
                    <label for="abstract_content">Abstract Content <code>* <span>(NOTE: Total number of
                                Abstract
                                Words limitation is
                                {{ !empty(@$setting->abstract_word_limit) ? $setting->abstract_word_limit : 'infinity' }})</span></code></label>
                    <textarea class="form-control" name="abstract_content" id="abstract_content" cols="30" rows="5">{{ !empty(old('abstract_content')) ? old('abstract_content') : $submission->abstract_content }}</textarea>
                    @error('abstract_content')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" id="forwardRequest"
                        class="btn btn-primary">{{ $submission->forward_expert == 0 ? 'Submit' : 'Update' }}</button>
                </div>
            </div>
        </form>

    </div>
</div>

</div>
<script>
    CKEDITOR.replace('abstract_content', {
        filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
        filebrowserUploadMethod: "form",
        extraPlugins: 'wordcount',
        wordcount: {
            showWordCount: true,
            maxWordCount: {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'Infinity' }},
        }
    });
    $("#forwardRequest").on('click', function(e) {
        e.preventDefault();
        var data = new FormData($('#dataForm')[0]);
        data.append('abstract_content', CKEDITOR.instances['abstract_content'].getData());

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route('submission.expertForward', [$society, $conference]) }}',
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#forwardRequest').attr('disabled', true);
                $('#forwardRequest').append(
                    '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                );
            },
            success: function(response) {
                $('#forwardRequest').attr('disabled', false);
                $('#forwardRequest').text('Update');
                if (response.type == 'success') {
                    $(".modal").modal("hide");
                    notyf.success(response.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else if (response.type == 'error') {
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
                $('#forwardRequest').attr('disabled', false);
                $('#forwardRequest').text('Update');
            }
        });
    });
</script>
