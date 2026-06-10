@extends('backend.layouts.conference.main')

@section('title')
    Edit Invitation Category
@endsection

@section('content')
    <div class="main-content">
        <div class="breadcrumb">
            <h3>Edit Invitation Category</h3>
        </div>
        <div class="separator-breadcrumb border-top mb-4"></div>

        <div class="row">
            <div class="col-md-8 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $invitationCategory->name }}</h5>
                        <small class="text-muted">Update category details</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('conference.invitation-category.update', [$society, $conference, $invitationCategory->id]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Category Name <code class="text-danger">*</code></label>
                                <input type="text" 
                                    name="name" 
                                    id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="e.g., VIP Guest, Speaker, Participant"
                                    value="{{ old('name', $invitationCategory->name) }}" 
                                    required />
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" 
                                    id="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="4"
                                    placeholder="Optional description for this category">{{ old('description', $invitationCategory->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="status" class="form-label">Status <code class="text-danger">*</code></label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('status') is-invalid @enderror" 
                                        type="checkbox" 
                                        name="status" 
                                        id="status" 
                                        value="1"
                                        {{ old('status', $invitationCategory->status) ? 'checked' : '' }} />
                                    <label class="form-check-label" for="status">
                                        Active
                                    </label>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Display Order</label>
                                <div class="alert alert-info" role="alert">
                                    <i class="ti tabler-info-circle me-2"></i>
                                    Order can be changed from the categories list using drag and drop.
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti tabler-save me-1"></i> Update Category
                                </button>
                                <a href="{{ route('conference.invitation-category.index', [$society, $conference]) }}" class="btn btn-secondary">
                                    <i class="ti tabler-x me-1"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
