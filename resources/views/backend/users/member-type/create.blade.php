@extends('backend.layouts.society.main')

@section('title')
    {{ isset($memberType) ? 'Edit' : 'Add' }} Member Type
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('memberType.index', $society) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($memberType) ? 'Edit' : 'Add' }} Member Type</h4>
            <div class="card-body">
                <form class="needs-validation" 
                    action="{{ isset($memberType) ? route('memberType.update', [$society, $memberType->id]) : route('memberType.store', $society) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($memberType)
                        @method('patch')
                    @endisset
                    <div class="row">
                        <div class="mb-6 col-md-6">
                            <label for="delegate" class="form-label">Delegate <code>*</code></label>
                            <select class="form-select" name="delegate" id="delegate" required>
                                <option hidden>-- Select Delegate --</option>
                                <option value="1"
                                    @if (isset($memberType)) {{ $memberType->delegate == '1' ? 'selected' : '' }} @else @selected(old('delegate') == '1') @endif>
                                    National</option>
                                <option value="2"
                                    @if (isset($memberType)) {{ $memberType->delegate == '2' ? 'selected' : '' }} @else @selected(old('delegate') == '2') @endif>
                                    International</option>
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please select delegate.</div>
                            @error('delegate')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="type">Member Type <code>*</code></label>
                            <input type="text" class="form-control @error('type') is-invalid @enderror" id="type"
                                placeholder="Enter Type" name="type"
                                value="{{ !empty(old('type')) ? old('type') : @$memberType->type }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Type.</div>
                            @error('type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($memberType) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
