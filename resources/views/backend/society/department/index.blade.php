@extends('backend.layouts.society.main')
@section('title')
    Department
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Department Settings</h5>
                </div>
            </div>

            <div class="card-body">
                <p class="text-muted mb-4">Select the departments that will be available for your society members during registration.</p>
                
                <form action="{{ route('society.department.update', $society) }}" method="POST">
                    @csrf
                    
                    <div class="row"> 
                        @if($departments->count() > 0)
                            @foreach ($departments as $department)
                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            name="departments[]" 
                                            value="{{ $department->id }}" 
                                            id="department_{{ $department->id }}"
                                            {{ $selectedDepartments->contains('id', $department->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="department_{{ $department->id }}">
                                            {{ $department->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-warning" role="alert">
                                    <i class="icon-base ti tabler-alert-triangle me-2"></i>
                                    No departments available. Please contact the administrator to add departments.
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($departments->count() > 0)
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-base ti tabler-device-floppy me-1"></i>
                                Save Settings
                            </button>
                            <a href="{{ route('society.dashboard', $society->getRouteKey()) }}" class="btn btn-secondary">
                                <i class="icon-base ti tabler-arrow-left me-1"></i>
                                Back to Dashboard
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="card-body border-top">
                <h6 class="mb-3">Currently Selected Departments</h6>
                @if($selectedDepartments->count() > 0)
                    <div class="mb-3">
                        <small class="text-info">
                            <i class="ti tabler-grip-vertical"></i> <strong>Drag and drop to reorder departments</strong>
                        </small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 30px;"><i class="ti tabler-arrows-move"></i></th>
                                    <th>Order</th>
                                    <th>Department Name</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-departments">
                                @foreach ($selectedDepartments as $department)
                                    <tr data-id="{{ $department->id }}" style="cursor: move;">
                                        <td class="drag-handle text-center">
                                            <i class="ti tabler-grip-vertical" style="font-size: 20px; color: #999;"></i>
                                        </td>
                                        <td class="order-number">{{ $loop->iteration }}</td>
                                        <td>{{ $department->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No departments selected yet.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
    <style>
        #sortable-departments tr.ui-sortable-helper {
            display: table;
            background-color: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        #sortable-departments tr.ui-sortable-placeholder {
            background-color: #e3f2fd;
            visibility: visible !important;
            height: 50px;
        }
        
        .drag-handle:hover {
            background-color: #f0f0f0;
            cursor: grab;
        }
        
        .drag-handle:active {
            cursor: grabbing;
        }
    </style>
    
    <script>
        $(document).ready(function() {
            // Initialize sortable only if there are selected departments
            if ($('#sortable-departments tr').length > 0) {
                $("#sortable-departments").sortable({
                    handle: ".drag-handle",
                    placeholder: "ui-sortable-placeholder",
                    helper: function(e, tr) {
                        var $originals = tr.children();
                        var $helper = tr.clone();
                        $helper.children().each(function(index) {
                            $(this).width($originals.eq(index).width());
                        });
                        return $helper;
                    },
                    update: function(event, ui) {
                        updateOrder();
                    }
                });
            }

            function updateOrder() {
                var orders = [];
                $('#sortable-departments tr').each(function(index) {
                    orders.push({
                        id: $(this).data('id'),
                        position: index + 1
                    });
                });

                $.ajax({
                    url: '{{ route('society.department.update-order', $society->getRouteKey()) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        orders: orders
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update order numbers
                            $('#sortable-departments tr').each(function(index) {
                                $(this).find('.order-number').text(index + 1);
                            });
                            
                            notyf.success('Order updated successfully');
                        }
                    },
                    error: function(xhr) {
                        notyf.error('Failed to update order');
                        console.error(xhr);
                    }
                });
            }
            
            // Show confirmation when form is submitted
            $('form').on('submit', function(e) {
                const checkedCount = $('input[name="departments[]"]:checked').length;
                
                if (checkedCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Department Selected',
                        text: 'Please select at least one department for your society.',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
