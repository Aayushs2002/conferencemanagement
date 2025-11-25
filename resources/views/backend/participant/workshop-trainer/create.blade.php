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
                        action="{{ isset($trainer) ? route('my-society.conference.my-workshop.trainer.update', [$society, $conference, $trainer]) : route('my-society.conference.my-workshop.trainer.store', [$society, $conference]) }}"
                        method="POST" enctype="multipart/form-data" id="trainerForm" novalidate>
                        @csrf
                        @isset($trainer)
                            @method('patch')
                        @endisset
                        <div class="row">
                            <input type="hidden" name="workshop_id" value="{{ $workshop->id }}">
                            <div class="col-md-6 form-group mb-3">
                                <label for="user_id">Select Trainer <code>*</code></label>
                                <select name="user_id" class="form-control select2" id="user_id" required>
                                    <option value="" hidden>-- Select Trainer --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            @if (isset($trainer)) {{ $trainer->user_id == $user->id ? 'selected' : '' }} @else @selected(old('user_id') == $user->id) @endif>
                                            {{ $user->fullName($user) }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12 text-end mt-3">
                            <a href="{{ route('my-society.conference.my-workshop.trainer.index', [$society, $conference, $workshop]) }}"
                                class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitButton">
                                {{ isset($trainer) ? 'Update Trainer' : 'Add Trainer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
