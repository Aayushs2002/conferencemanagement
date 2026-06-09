@extends('backend.layouts.conference.main')

@section('title')
    Invitation Categories
@endsection

@section('content')
    <div class="main-content">
        <div class="breadcrumb">
            <h3>Invitation Categories</h3>
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
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Manage Invitation Categories</h5>
                        <a href="{{ route('conference.invitation-category.create', [$society, $conference]) }}" class="btn btn-primary btn-sm">
                            <i class="ti tabler-plus me-1"></i> Add Category
                        </a>
                    </div>
                    <div class="card-body">
                        @if ($categories->isEmpty())
                            <div class="alert alert-info" role="alert">
                                <i class="ti tabler-info-circle me-2"></i> No invitation categories created yet. 
                                <a href="{{ route('conference.invitation-category.create', [$society, $conference]) }}" class="alert-link">Create one now</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0" id="categoriesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">
                                                <i class="ti tabler-arrows-move"></i>
                                            </th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th style="width: 100px;">Status</th>
                                            <th style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sortable" data-update-url="{{ route('conference.invitation-category.updateOrder', [$society, $conference]) }}">
                                        @foreach ($categories as $category)
                                            <tr class="sortable-item" data-id="{{ $category->id }}">
                                                <td class="text-center cursor-move">
                                                    <i class="ti tabler-grip-vertical text-muted"></i>
                                                </td>
                                                <td>
                                                    <strong>{{ $category->name }}</strong>
                                                </td>
                                                <td>
                                                    @if ($category->description)
                                                        <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($category->status)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('conference.invitation-category.edit', [$society, $conference, $category->id]) }}" class="btn btn-outline-primary" title="Edit">
                                                            <i class="ti tabler-edit"></i> Edit
                                                        </a>
                                                        <form action="{{ route('conference.invitation-category.destroy', [$society, $conference, $category->id]) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this category?')">
                                                                <i class="ti tabler-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sortableElement = document.querySelector('.sortable');
                if (!sortableElement) return;

                new Sortable(sortableElement, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    handle: '.cursor-move',
                    onEnd: function(evt) {
                        const items = document.querySelectorAll('.sortable-item');
                        const order = [];
                        items.forEach((item, index) => {
                            order.push({
                                id: item.getAttribute('data-id'),
                                display_order: index + 1
                            });
                        });

                        const updateUrl = sortableElement.getAttribute('data-update-url');
                        
                        fetch(updateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ order: order })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.type === 'success') {
                                console.log('Order updated successfully');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    }
                });
            });
        </script>
    @endpush
@endsection
