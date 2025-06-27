<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <h5 class="modal-title" id="exampleModalCenterTitle">{{ isset($author) ? 'Edit' : 'Add' }} Author for topic:
        {{ $submission->topic }} <small class="text-danger">(NOTE: Total number of Authors Limitation is
            {{ !empty(@$authorLimit->authors_limit) ? @$authorLimit->authors_limit : 'infinity' }})</small></h5>
    <hr class="py-3">
    <div class="rounded-top">
        <form
            action="{{ isset($author) ? route('my-society.conference.submission.author.update', [$society, $conference, $author]) : route('my-society.conference.submission.author.store', [$society, $conference]) }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @isset($author)
                @method('patch')
            @endisset
            <div class="row">
                <input type="hidden" name="submission_id" value="{{ $submission->id }}">

                @if (!in_array(1, $checkMainAuthor) || (isset($author) ? $author->main_author == 1 : ''))
                    <div class="col-md-12 form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="main_author" id="main_author"
                            value="1"
                            @isset($author) @if ($author->main_author == 1) checked @endif @endisset />
                        <label for="main_author" class="form-check-label">Is Main Author ? </label>
                    </div>
                @endif
                <div class="@if (!in_array(1, $checkMainAuthor) || (isset($author) ? $author->main_author == 1 : '')) col-md-6 @else col-md-6 @endif form-group mb-3">
                    <label for="name">Full Name <code>*</code></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        id="name" value="{{ isset($author) ? $author->name : old('name') }}"
                        placeholder="Enter author name" required />
                    @error('name')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="@if (!in_array(1, $checkMainAuthor) || (isset($author) ? $author->main_author == 1 : '')) col-md-6 @else col-md-6 @endif form-group mb-3">
                    <label for="email">Email <code>*</code></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                        id="email" value="{{ isset($author) ? $author->email : old('email') }}"
                        placeholder="Enter author email" required />
                    @error('email')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                @if ($author == null)
                    <div class="col-md-12 form-group my-3">
                        <label for="old_author">Is Designation/Institution/Institution Address same as any of the
                            following Author?</label>
                        <select name="old_author" id="oldAuthor" class="form-control">
                            <option value="0">-- Select Author --</option>
                            @foreach ($authors as $auth)
                                <option value="{{ $auth->id }}">{{ $auth->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-6 form-group mb-3">
                    <label for="designation">Designation <code>*</code></label>
                    <input type="text" class="form-control @error('designation') is-invalid @enderror"
                        name="designation" id="designation"
                        value="{{ isset($author) ? $author->designation : old('designation') }}"
                        placeholder="Enter author designation" required />
                    @error('designation')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label for="institution">Institution <code>*</code></label>
                    <input type="text" class="form-control @error('institution') is-invalid @enderror"
                        name="institution" id="institution"
                        value="{{ isset($author) ? $author->institution : old('institution') }}"
                        placeholder="Enter author institution" required />
                    @error('institution')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label for="institution_address">Institution Address <code>*</code></label>
                    <input type="text" class="form-control @error('institution_address') is-invalid @enderror"
                        name="institution_address" id="institutionAddress"
                        value="{{ isset($author) ? $author->institution_address : old('institution_address') }}"
                        placeholder="Enter author institution address" required />
                    @error('institution_address')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-md-6 form-group mb-3">
                    <label for="phone">Phone <code id="phoneCondition">(Optional)</code></label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                        id="phone" value="{{ isset($author) ? $author->phone : old('phone') }}"
                        placeholder="Enter author phone number" />
                    @error('phone')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">{{ isset($author) ? 'Update' : 'Submit' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
<script>
    $("#main_author").change(function() {
        if ($(this).is(":checked")) {
            $("#phoneCondition").text('*')
            $("#phone").attr('required', true)
        } else {
            $("#phoneCondition").text('(Optional)')
            $("#phone").attr('required', false)
        }
    });

    $("#main_author").trigger("change");

    $("#oldAuthor").change(function(e) {
        e.preventDefault();
        var oldAuthor = $(this).val();
        if (oldAuthor == 0) {
            $("#designation").val('');
            $("#institution").val('');
            $("#institutionAddress").val('');
        } else {
            var url =
                '{{ route('my-society.conference.submission.author.oldAuthor', [$society, $conference]) }}';
            var _token = '{{ csrf_token() }}';
            var data = {
                _token: _token,
                oldAuthor: oldAuthor
            };
            $.post(url, data, function(response) {
                $("#designation").val(response.designation);
                $("#institution").val(response.institution);
                $("#institutionAddress").val(response.institution_address);
            });
        }
    });
</script>
