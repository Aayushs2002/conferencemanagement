@extends('backend.layouts.main')

@section('title')
    Global Registration Types
@endsection

@section('content')
    <div class="main-content">
        <div class="breadcrumb">
            <h3>Global Registration Types</h3>
            <small class="text-muted">These types are available to all conferences by default. Conferences can hide individual types.</small>
        </div>
        <div class="separator-breadcrumb border-top mb-4"></div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('delete'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('delete') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            {{-- Add New Global Type --}}
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Global Type</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('registrant-type.global.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Type Name <code>*</code></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="e.g., Industry Expert"
                                    value="{{ old('name') }}" />
                                @error('name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-plus me-1"></i> Add Type
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- List --}}
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">All Global Types</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th class="text-center" style="width:80px">Status</th>
                                    <th class="text-center" style="width:200px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($globalTypes as $type)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <form action="{{ route('registrant-type.global.update', $type->id) }}" method="POST" class="d-flex gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" class="form-control form-control-sm" value="{{ $type->name }}" required />
                                                <button type="submit" class="btn btn-sm btn-success text-nowrap">
                                                    <i class="ti tabler-check"></i> Save
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            @if($type->status)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Disabled</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('registrant-type.global.destroy', $type->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm {{ $type->status ? 'btn-danger' : 'btn-secondary' }}"
                                                    onclick="return confirm('{{ $type->status ? 'Disable' : 'Enable' }} this type?')">
                                                    <i class="ti tabler-trash me-1"></i>
                                                    {{ $type->status ? 'Disable' : 'Already Disabled' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No global types found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
