<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Assign To Expert <span class="text-danger">(Topic:
                {{ $submission->title }})</span></h5>
        <hr class="py-4">

        <form id="decisionForm">
            @csrf
            <div class="row">
                <input type="hidden" id="submissionId" name="id" value="{{ $submission->id }}">
                <div class="col-md-6 form-group mb-3" id="decisionDiv">
                    <label for="request_status">Decide Request <code>*</code></label>
                    <select name="request_status" id="request_status"
                        class="form-control @error('request_status') is-invalid @enderror">
                        <option value="" hidden>-- Select Status --</option>
                        <option value="1" @selected(old('request_status') == 1)>Accept</option>
                        <option value="2" @selected(old('request_status') == 2)>Correction</option>
                        <option value="3" @selected(old('request_status') == 3)>Reject</option>
                    </select>
                    <p class="text-danger request_status"></p>
                </div>
                @if ($discussions->isNotEmpty())
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="zero_configuration_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Sender</th>
                                    <th scope="col">Remarks</th>
                                    <th scope="col">Scientific Commitee Comment</th>
                                    <th scope="col">Date/Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($discussions as $discussion)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>
                                            {{ $discussion->sender->fullName($discussion->sender) }}
                                        </td>
                                        <td>{{ $discussion->remarks }}</td>

                                        <td>
                                            {{ !empty($discussion->committee_remarks) ? $discussion->committee_remarks : '-' }}
                                        </td>

                                        <td>{{ $discussion->created_at }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="col-md-12 form-group mb-3 remarksDiv">
                    <label for="remarks">Remarks <code>*</code></label>
                    <textarea class="form-control" name="remarks" id="remarks" cols="30" rows="5"></textarea>
                    <p class="text-danger remarks"></p>
                </div>

                <div class="col-md-12 text-end">
                    <button type="submit" id="decideRequest" class="btn btn-primary">Send</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
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
                url: '{{ route('submission.sentToAuthor', [$society, $conference]) }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#decideRequest').attr('disabled', true);
                    $('#decideRequest').append(
                        '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
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

                    $('#decideRequest').attr('disabled', false);
                    $('#decideRequest').text('Update');
                }
            });
        });
    });
</script>
