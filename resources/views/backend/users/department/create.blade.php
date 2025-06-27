@extends('backend.layouts.main')
@section('title')
    {{ isset($department) ? 'Edit' : 'Add' }} Department
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('department.index') }}"><i class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($department) ? 'Edit' : 'Add' }} Department</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($department) ? route('department.update', $department->id) : route('department.store') }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($department)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="name">Department Name<code>*</code></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                placeholder="Enter Department Name" name="name"
                                value="{{ !empty(old('name')) ? old('name') : @$department->name }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Department Name.</div>
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($department) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
