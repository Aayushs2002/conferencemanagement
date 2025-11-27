@extends('backend.layouts.conference.main')

@section('title')
    Submission Setting
@endsection
@section('content')
    <div class="main-content">
        <div class="breadcrumb">
            <h1>Submission Setting</h1>
        </div> 
        <div class="separator-breadcrumb border-top"></div> 
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body"> 
                    <form action="{{ route('submission.settingSubmit', [$society, $conference]) }}" method="POST"
                        enctype="multipart/form-data" id="submissionSettingForm">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="conference_id" value="{{ $conference->id }}">
                            <input type="hidden" name="id"
                                value="{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->id : '' }}">
                            <div class="col-md-4 form-group mb-3">
                                <label for="deadline">Submission Deadline</label>
                                <input type="date" class="form-control @error('deadline') is-invalid @enderror deadline"
                                    name="deadline" id="deadline"
                                    value="{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->deadline : '' }}" />
                                @error('deadline')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="submission_open_date">Abstract Submission Open Date</label>
                                <input type="date" class="form-control @error('submission_open_date') is-invalid @enderror"
                                    name="submission_open_date" id="submission_open_date"
                                    value="{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->submission_open_date : '' }}" />
                                @error('submission_open_date')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="abstract_word_limit">Abstract Word Limit <code>(In Number)</code></label>
                                <input type="number"
                                    class="form-control @error('abstract_word_limit') is-invalid @enderror numericValue abstract_word_limit"
                                    name="abstract_word_limit" id="abstract_word_limit"
                                    value="{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->abstract_word_limit : '' }}" />
                                @error('abstract_word_limit')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="key_word_limit">Keyword Word Limit <code>(In Number)</code></label>
                                <input type="number"
                                    class="form-control @error('key_word_limit') is-invalid @enderror numericValue key_word_limit"
                                    name="key_word_limit" id="key_word_limit"
                                    value="{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->key_word_limit : '' }}" />
                                @error('key_word_limit')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="authors_limit">Authors Limit <code>(In Number)</code></label>
                                <input type="number"
                                    class="form-control @error('authors_limit') is-invalid @enderror numericValue authors_limit"
                                    name="authors_limit" id="authors_limit"
                                    value="{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->authors_limit : '' }}" />
                                @error('authors_limit')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="attachment_name">Attachment Document Name</code></label>
                                <input type="text"
                                    class="form-control @error('attachment_name') is-invalid @enderror attachment_name"
                                    name="attachment_name" id="attachment_name"
                                    value="{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->attachment_name : '' }}" />
                                @error('attachment_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3" id="attachment_required_wrapper" style="display: none;">
                                <label for="attachment_required">Is Attachment Mandatory?</label>
                                <select name="attachment_required" id="attachment_required"
                                    class="form-control @error('attachment_required') is-invalid @enderror">
                                    <option value="0"
                                        {{ !empty($conference->submissionSetting) && $conference->submissionSetting->attachment_required == 0 ? 'selected' : '' }}>
                                        No</option>
                                    <option value="1"
                                        {{ !empty($conference->submissionSetting) && $conference->submissionSetting->attachment_required == 1 ? 'selected' : '' }}>
                                        Yes</option>
                                </select>
                                @error('attachment_required')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-6 col-md-4">
                                <label class="form-label" for="signature">Signature <code> (Only JPG/PNG) (Max:
                                        250
                                        KB)</code></label>
                                <input type="file" class="form-control" name="signature" id="image"
                                    value="{{ !empty(old('signature')) ? old('signature') : @$conference->signature }}" />
                                <div class="row" id="imgPreview">
                                    @if (isset($conference->submissionSetting->signature))
                                        <div class="col-3 mt-2">
                                            <a href="{{ asset('storage/submission/setting/signature/' . $conference->submissionSetting->signature) }}"
                                                target="_blank"><img
                                                    src="{{ asset('storage/submission/setting/signature/' . $conference->submissionSetting->signature) }}"
                                                    class="img-fluid" alt="image"></a>
                                        </div>
                                    @endif
                                </div>
                                @error('signature')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="scoring_allowed">Enable Scoring for Reviewers</label>
                                <select name="scoring_allowed" id="scoring_allowed"
                                    class="form-control @error('scoring_allowed') is-invalid @enderror">
                                    <option value="1"
                                        {{ !empty($conference->submissionSetting) && $conference->submissionSetting->scoring_allowed == 1 ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="0"
                                        {{ !empty($conference->submissionSetting) && $conference->submissionSetting->scoring_allowed == 0 ? 'selected' : '' }}>
                                        No</option>
                                </select>
                                @error('scoring_allowed')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="contribution_enabled">Enable Contribution</label>
                                <select name="contribution_enabled" id="contribution_enabled"
                                    class="form-control @error('contribution_enabled') is-invalid @enderror">
                                    <option value="1"
                                        {{ !empty($conference->submissionSetting) && $conference->submissionSetting->contribution_enabled == 1 ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="0"
                                        {{ !empty($conference->submissionSetting) && $conference->submissionSetting->contribution_enabled == 0 ? 'selected' : '' }}>
                                        No</option>
                                </select>
                                @error('contribution_enabled')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group mb-3">
                                <label for="competition_enabled">Enable Competition</label>
                                <select name="competition_enabled" id="competition_enabled"
                                    class="form-control @error('competition_enabled') is-invalid @enderror">
                                    <option value="1"
                                        {{ !empty($conference->submissionSetting) && $conference->submissionSetting->competition_enabled == 1 ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="0"
                                        {{ !empty($conference->submissionSetting) && $conference->submissionSetting->competition_enabled == 0 ? 'selected' : '' }}>
                                        No</option>
                                </select>
                                @error('competition_enabled')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-12 form-group mb-3">
                                <label for="abstract_guidelines">Abstract Submission Guidelines </label>
                                <textarea name="abstract_guidelines" class="form-control ckeditor @error('abstract_guidelines') is-invalid @enderror"
                                    id="description" cols="30" rows="10">{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->abstract_guidelines : '' }}</textarea>
                                @error('abstract_guidelines')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="oral_guidelines">Oral Presentation Guidelines </label>
                                <textarea name="oral_guidelines" class="form-control ckeditor @error('oral_guidelines') is-invalid @enderror"
                                    id="description4" cols="30" rows="10">{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->oral_guidelines : '' }}</textarea>
                                @error('oral_guidelines')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="poster_guidelines">Poster Presentation Guidelines </label>
                                <textarea name="poster_guidelines" class="form-control ckeditor @error('poster_guidelines') is-invalid @enderror"
                                    id="description5" cols="30" rows="10">{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->poster_guidelines : '' }}</textarea>
                                @error('poster_guidelines')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="oral_reviewer_guide">Oral Reviewer Guide</label>
                                <textarea name="oral_reviewer_guide" class="form-control ckeditor @error('oral_reviewer_guide') is-invalid @enderror"
                                    id="description3" cols="30" rows="10">{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->oral_reviewer_guide : '' }}</textarea>
                                @error('oral_reviewer_guide')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="poster_reviewer_guide">Poster Reviewer Guide</label>
                                <textarea name="poster_reviewer_guide"
                                    class="form-control ckeditor @error('poster_reviewer_guide') is-invalid @enderror" id="description2"
                                    cols="30" rows="10">{{ !empty($conference->submissionSetting) ? $conference->submissionSetting->poster_reviewer_guide : '' }}</textarea>
                                @error('poster_reviewer_guide')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            @if (auth()->user()->hasConferencePermissionBlade($conference, 'Add/Edit Submission Setting'))
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary"
                                        id="submitData">{{ empty($conference->submissionSetting) ? 'Save' : 'Update' }}</button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $(".numericValue").on("keydown", function(event) {
                // Allow backspace, delete, tab, escape, and enter keys
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode ==
                    27 || event.keyCode == 13 ||
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

            $("#submitData").click(function(e) {
                e.preventDefault();
                $(this).attr('disabled', true);
                $("#submissionSettingForm").submit();
            });

            // Toggle attachment_required field based on attachment_name
            function toggleAttachmentRequired() {
                var attachmentName = $('#attachment_name').val();
                if (attachmentName && attachmentName.trim() !== '') {
                    $('#attachment_required_wrapper').show();
                } else {
                    $('#attachment_required_wrapper').hide();
                    $('#attachment_required').val('0');
                }
            }

            // Check on page load
            toggleAttachmentRequired();

            // Check when attachment_name field changes
            $('#attachment_name').on('input', function() {
                toggleAttachmentRequired();
            });
        });
    </script>
@endsection
