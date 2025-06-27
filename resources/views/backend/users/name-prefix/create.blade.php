@extends('backend.layouts.main')
@section('title')
    {{ isset($name_prefix) ? 'Edit' : 'Add' }} Name Prefix
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('name-prefix.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($name_prefix) ? 'Edit' : 'Add' }} Name Prefix</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($name_prefix) ? route('name-prefix.update', $name_prefix->id) : route('name-prefix.store') }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($name_prefix)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="prefix">Name Prefix <code>*</code></label>
                            <input type="text" class="form-control @error('prefix') is-invalid @enderror" id="prefix"
                                placeholder="Enter Name Prefix" name="prefix"
                                value="{{ !empty(old('prefix')) ? old('prefix') : @$name_prefix->prefix }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Name Prefix.</div>
                            @error('prefix')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($name_prefix) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
