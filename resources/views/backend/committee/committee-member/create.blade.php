@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($committee_member) ? 'Edit' : 'Add' }} Committee Member
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('committee.index', [$society, $conference, $committee->slug]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($committee_member) ? 'Edit' : 'Add' }} Committee Member</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($committee_member) ? route('committeeMember.update', [$society, $conference, $committee_member->id]) : route('committeeMember.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($committee_member)
                        @method('patch')
                    @endisset
                    <div class="row">
                        <input type="hidden" name="committee_id" value="{{ $committee->id }}">
                        <div class="col-md-6 form-group mb-3">
                            <label for="user_id">Committee Members <code>*</code></label>
                            <select name="user_id[]" class="form-control @error('user_id') is-invalid @enderror select2"
                                multiple required>
                                @if (!isset($committee_member))
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                            {{ $user->fullName($user) }}</option>
                                    @endforeach
                                @else
                                    <option value="{{ $committee_member->user_id }}" selected>
                                        {{ $committee_member->user->fullName($committee_member->user) }}</option>
                                @endif
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Select Committee Name.</div>
                            @error('user_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="designation_id">Designation <code>*</code></label>
                            <select name="designation_id" class="form-control @error('designation_id') is-invalid @enderror"
                                required>
                                <option value="" hidden>-- Select Designation --</option>
                                @foreach ($committee_designations as $designation)
                                    <option value="{{ $designation->id }}" @selected(old('designation_id', @$committee_member->designation_id) == $designation->id)>
                                        {{ $designation->designation }}</option>
                                @endforeach
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Select designation.</div>
                            @error('designation_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label for="message">Message </label>
                            <textarea name="message" class="form-control ckeditor @error('message') is-invalid @enderror" id="description"
                                cols="30" rows="10">{{ isset($committee_member) ? $committee_member->message : old('message') }}</textarea>
                            @error('message')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="row">
                            <div class=" text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($committee_member) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
