@extends('backend.layouts.conference.main')

@section('title')
    {{ $contribution ? 'Edit' : 'Add' }} Contribution
@endsection

@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('contribution.index', [$society, $conference]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ $contribution ? 'Edit' : 'Add' }} Contribution Track</h4>
            <div class="card-body">
                <form class="needs-validation" id="contributionForm"
                    action="{{ $contribution ? route('contribution.update', [$society, $conference, $contribution->id]) : route('contribution.store', [$society, $conference]) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @if ($contribution)
                        @method('PATCH')
                    @endif

                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                id="name" value="{{ old('name', $contribution->name ?? '') }}" required />
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 form-group mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description"
                                rows="4">{{ old('description', $contribution->description ?? '') }}</textarea>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-12 text-end">
                            <a href="{{ route('contribution.index', [$society, $conference]) }}"
                                class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                {{ $contribution ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $("#submitBtn").click(function(e) {
                e.preventDefault();
                $(this).attr('disabled', true);
                $("#contributionForm").submit();
            });
        });
    </script>
@endsection
