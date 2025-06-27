@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($trainer) ? 'Edit' : 'Add' }} Trainer
@endsection
@section('content')
    <div class="main-content">
        <div class="breadcrumb">
            <h4>{{ isset($trainer) ? 'Edit' : 'Add' }} Trainer for {{ $workshop->workshop_title }}</h4>
        </div>
        <div class="separator-breadcrumb border-top"></div>
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <form class="needs-validation"
                        action="{{ isset($trainer) ? route('workshop.workshop-trainer.update', [$society, $conference, $trainer]) : route('workshop.workshop-trainer.store', [$society, $conference]) }}"
                        method="POST" enctype="multipart/form-data" id="trainerForm" novalidate>
                        @csrf
                        @isset($trainer)
                            @method('patch')
                        @endisset
                        <div class="row">
                            <input type="hidden" name="workshop_id" value="{{ $workshop->id }}">
                            <div class="col-md-5 form-group mb-3">
                                <label for="user_id">Trainer <code>*</code></label>
                                <select name="user_id" class="form-control select2" id="user_id" required>
                                    <option value="" hidden>-- Select Trainer --</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            @if (isset($trainer)) {{ $trainer->user_id == $user->id ? 'selected' : '' }} @else @selected(old('user_id') == $user->id) @endif>
                                            {{ $user->fullName($user) }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary"
                                id="submitButton">{{ isset($trainer) ? 'Update' : 'Submit' }}</button>
                            <a href="{{ route('workshop.workshop-trainer.index', [$society, $conference, $workshop]) }}"
                                class="btn btn-danger">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
