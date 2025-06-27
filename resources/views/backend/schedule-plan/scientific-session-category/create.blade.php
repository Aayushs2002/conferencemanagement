@extends('backend.layouts.conference.main')
@section('title')
    {{ isset($category) ? 'Edit' : 'Add' }} Scientific Category
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('category.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($category) ? 'Edit' : 'Add' }} Scientific Category</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($category) ? route('category.update', [$society, $conference, $category->id]) : route('category.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($category)
                        @method('patch')
                    @endisset
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="category_name">Category Name <code>*</code></label>
                            <input type="text" class="form-control @error('category_name') is-invalid @enderror"
                                id="category_name" placeholder="Enter Category Name" name="category_name"
                                value="{{ !empty(old('category_name')) ? old('category_name') : @$category->category_name }}"
                                required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Category Name.</div>
                            @error('category_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($category) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
