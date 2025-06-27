@extends('backend.layouts.main')
@section('title')
    {{ isset($permission) ? 'Edit' : 'Add' }} Permission
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('permission.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($permission) ? 'Edit' : 'Add' }} Permission</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($permission) ? route('permission.update', $permission->id) : route('permission.store') }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($permission)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="name">Permission Name <code>*</code></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                placeholder="Enter Permission Name" name="name"
                                value="{{ !empty(old('name')) ? old('name') : @$permission->name }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Permission Name.</div>
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="parent">Parent <code>*</code></label>
                            <input type="text" class="form-control @error('parent') is-invalid @enderror" id="parent"
                                placeholder="Enter Parent" name="parent"
                                value="{{ !empty(old('parent')) ? old('parent') : @$permission->parent }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Parent.</div>
                            @error('parent')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($permission) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
