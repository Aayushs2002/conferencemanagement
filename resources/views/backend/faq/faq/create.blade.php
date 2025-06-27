@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($faq) ? 'Edit' : 'Add' }} Faq
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('faq.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($faq) ? 'Edit' : 'Add' }} Faq</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($faq) ? route('faq.update', [$society, $conference, $faq->id]) : route('faq.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($faq)
                        @method('patch')
                    @endisset
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="faq_category_id">Category <code>*</code></label>
                            <select name="faq_category_id"
                                class="form-control @error('faq_category_id') is-invalid @enderror" required>
                                <option value="" hidden>-- Select Category --</option>
                                @foreach ($faq_categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('faq_category_id', @$faq->faq_category_id) == $category->id)>
                                        {{ $category->category_name }}</option>
                                @endforeach
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Select Category.</div>
                            @error('faq_category_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6">
                            <label class="form-label" for="question">Question <code>*</code></label>
                            <input type="text" class="form-control @error('question') is-invalid @enderror"
                                id="question" placeholder="Enter Question" name="question"
                                value="{{ !empty(old('question')) ? old('question') : @$faq->question }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Question.</div>
                            @error('question')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="answer">Answer </label>
                            <textarea class="form-control ckeditor" name="answer" id="answer" cols="30" rows="5">{{ isset($faq) ? $faq->answer : old('answer') }}</textarea>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter answer.</div>
                            @error('answer')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($faq) ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
