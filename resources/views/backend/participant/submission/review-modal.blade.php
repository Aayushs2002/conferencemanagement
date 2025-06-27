<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="rounded-top">
        <h4 class="text-center mb-4">Review for <span class="text-danger">(Topic:
                {{ $submission->title }})</h4>

    </div>
    <hr class="py-2">
    @if ($submission->expert_id == current_user()->id)
        <div class="closeModal">
            <label class="card-text mb-2">Do you want to accept the request?</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" value="1" name="request" id="yes">
                <label class="form-check-label" for="yes">Yes</label>
            </div>
            <div class="form-check form-check-inline pt-1">
                <input class="form-check-input" type="radio" value="2" name="request" id="no">
                <label class="form-check-label" for="no">No</label>
            </div>
        </div>


        <form id="decisionForm">
            @csrf
            <div class="row">
                <input type="hidden" id="requestType" name="requestType">
                <input type="hidden" id="submissionId" name="id" value="{{ $submission->id }}">
                <div class="col-md-12 form-group mb-3">


                    <input type="hidden" name="type" value="{{ $submission->presentation_type == 1 ? 2 : 1 }}">


                    {{-- @if ($submission->presentation_type == 2 || $submission->presentation_type == 3) --}}
                    <div class="col-md-12 form-group mt-3 mb-3 decisionForm" style="display: none;"
                        id="abstractContent">
                        <label for="abstract_content">Abstract Content <code>* <span>(NOTE: Total number of
                                    Abstract
                                    Words limitation is
                                    {{ !empty(@$setting->abstract_word_limit) ? $setting->abstract_word_limit : 'infinity' }})</span></code></label>
                        <textarea class="form-control" name="abstract_content" id="abstract_content" cols="30" rows="5">{{ !empty(old('abstract_content')) ? old('abstract_content') : $submission->abstract_content }}</textarea>
                        @error('abstract_content')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- @endif --}}

                    <div class="row pl-3 decisionForm" style="display: none;">
                        <div class="col-md-12 mb-3">
                            <label class="text-danger" for="defaultCheck1">Score Base On below Topic
                                <code>(Check the box if the structure not applicable <input
                                        class="form-check-input mt-1" type="checkbox" value="1" name="structure"
                                        id="defaultCheck1" />) </code></label>
                        </div>
                        <div class="col-md-4 form-group mb-3 ifAcceptContents">
                            <label for="introduction">Introduction/Background <code>*</code></label>
                            <select name="introduction" id="introduction"
                                class="form-control @error('introduction') is-invalid @enderror">
                                <option value="" hidden>-- Select Score --</option>
                                <option value="0" @selected(old('introduction') === 0)>0</option>
                                <option value="1" @selected(old('introduction') == 1)>1</option>
                                <option value="2" @selected(old('introduction') == 2)>2</option>
                            </select>
                            <p class="text-danger introduction"></p>
                        </div>
                        <div class="col-md-4 form-group mb-3 ifAcceptContents">
                            <label for="method">Methods <code>*</code></label>
                            <select name="method" id="method"
                                class="form-control @error('method') is-invalid @enderror">
                                <option value="" hidden>-- Select Score --</option>
                                <option value="0" @selected(old('method') === 0)>0</option>
                                <option value="1" @selected(old('method') == 1)>1</option>
                                <option value="2" @selected(old('method') == 2)>2</option>
                            </select>
                            <p class="text-danger method"></p>
                        </div>
                        <div class="col-md-4 form-group mb-3 ifAcceptContents">
                            <label for="result">Results/Findings <code>*</code></label>
                            <select name="result" id="result"
                                class="form-control @error('result') is-invalid @enderror">
                                <option value="" hidden>-- Select Score --</option>
                                <option value="0" @selected(old('result') === 0)>0</option>
                                <option value="1" @selected(old('result') == 1)>1</option>
                                <option value="2" @selected(old('result') == 2)>2</option>
                            </select>
                            <p class="text-danger result"></p>
                        </div>
                        <div class="col-md-5 form-group mb-3 ifAcceptContents">
                            <label for="conclusion">Conclusion <code>*</code></label>
                            <select name="conclusion" id="conclusion"
                                class="form-control @error('conclusion') is-invalid @enderror">
                                <option value="" hidden>-- Select Score --</option>
                                <option value="0" @selected(old('conclusion') === 0)>0</option>
                                <option value="1" @selected(old('conclusion') == 1)>1</option>
                                <option value="2" @selected(old('conclusion') == 2)>2</option>
                            </select>
                            <p class="text-danger conclusion"></p>
                        </div>
                        <div class="col-md-5 form-group mb-3 ifAcceptContents">
                            <label for="grammar">Grammar/Languages <code>*</code></label>
                            <select name="grammar" id="grammar"
                                class="form-control @error('grammar') is-invalid @enderror">
                                <option value="" hidden>-- Select Score --</option>
                                <option value="0" @selected(old('grammar') === 0)>0</option>
                                <option value="1" @selected(old('grammar') == 1)>1</option>
                                <option value="2" @selected(old('grammar') == 2)>2</option>
                            </select>
                            <p class="text-danger grammar"></p>
                        </div>
                    </div>
                    <div class="col-md-5 overall_ratings mb-3" style="display: none;">
                        <label for="overall_rating">Overall Rating <code>*</code></label>
                        <input type='number' class="form-control" name="overall_rating" id="overall_rating">
                        <p class="text-danger overall_rating"></p>
                    </div>
                    <div class="col-md-12 form-group mb-3 decisionForm" style="display: none;" id="remarksDiv">
                        <label for="remarks">Remarks <code>*</code></label>
                        <textarea class="form-control" name="remarks" id="remarks" cols="30" rows="5">{{ isset($submission) ? $submission->remarks : old('remarks') }}</textarea>
                        <p class="text-danger remarks"></p>
                    </div>

                    <div class="col-md-12 form-group mb-3 decisionRejectRemark" style="display: none;">
                        <label for="reject_remarks">Remarks <code>*</code></label>
                        <textarea class="form-control" name="reject_remarks" id="reject_remarks" cols="30" rows="5">{{ isset($submission) ? $submission->reject_remarks : old('reject_remarks') }}</textarea>
                        <p class="text-danger reject_remarks"></p>
                    </div>



                    <div class="col-md-12 text-end formbutton" style="display: none;">
                        <button type="submit" id="decideRequest" class="btn btn-primary">Send</button>
                    </div>
                </div>
        </form>
    @endif
</div>

</div>

<script>
    $('#yes').on('change', function() {
        if ($(this).is(':checked')) {
            var data = $(this).val();
            $('#requestType').val(data);
            $('.decisionRejectRemark').hide();
            $('.decisionForm').show();
            $('.formbutton').show();
        }
    });


    $('#no').on('change', function() {
        if ($(this).is(':checked')) {

            var data = $(this).val();
            $('#requestType').val(data);
            $('.decisionForm').hide();
            $('.formbutton').show();
            $('.decisionRejectRemark').show();
        }
    });

    $('#defaultCheck1').on('change', function() {
        if ($(this).is(':checked')) {
            $('.ifAcceptContents').hide();
            $('.overall_ratings').show();
        } else {
            $('.ifAcceptContents').show();
            $('.overall_ratings').hide();
        }
    });

    CKEDITOR.replace('abstract_content', {
        filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
        filebrowserUploadMethod: "form",
        extraPlugins: 'wordcount',
        wordcount: {
            showWordCount: true,
            maxWordCount: {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'Infinity' }},
        }
    });


    $("#decideRequest").on('click', function(e) {
        e.preventDefault();
        var data = new FormData($('#decisionForm')[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: '{{ route('my-society.conference.submission.reviewSubmit', [$society, $conference]) }}',
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#decideRequest').attr('disabled', true);
                $('#decideRequest').append(
                    ' <div class="spinner-border spinner-border-sm text-secondary" role="status"><span class="visually-hidden">Loading...</span> </div>'
                );
            },
            success: function(response) {
                $(".modal").modal("hide");
                notyf.success(response.message);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function(response) {
                var errors = response.responseJSON.errors;
                $.each(errors, function(key, val) {
                    $('.' + key).html('');
                    $('.' + key).append(val);
                    $('#' + key).addClass('border-danger');
                    $('#' + key).on('input', function() {
                        $('.' + key).html('');
                        $('#' + key).removeClass('border-danger');
                    });
                });
                // }
                $('#decideRequest').attr('disabled', false);
                $('#decideRequest').text('Send');
            }
        });
    });
</script>
