@extends('backend.layouts.society.main')
@section('title')
    Member Type
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Member Type</h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="icon-base ti tabler-upload icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Export</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportTo('excel')">Export to Excel</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="exportTo('pdf')">Export to PDF</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportTo('csv')">Export to CSV</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="window.print()">Print</a></li>
                            </ul>
                        </div>
                        <a href="{{ route('memberType.create', $society) }}" class="btn btn-primary" tabindex="0">
                            <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <p class="text-muted mb-2">Drag and drop rows to reorder member types. The order is used in all member type dropdowns across the project.</p>
                <div class="alert alert-info py-2 mb-4">
                    <i class="ti tabler-grip-vertical me-1"></i>
                    Reorder member types to control how they appear in registration, workshop, and user management forms.
                </div>

                @if ($types->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th style="width: 80px;">Order</th>
                                    <th>Used In</th>
                                    <th>Type</th>
                                    <th style="width: 120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-member-types">
                                @foreach ($types as $type)
                                    <tr data-id="{{ $type->id }}" style="cursor: move;">
                                        <td class="drag-handle text-center">
                                            <i class="ti tabler-grip-vertical" style="font-size: 20px; color: #999;"></i>
                                        </td>
                                        <td class="order-number">{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($type->delegate == 1)
                                                <span class="badge bg-success">National</span>
                                            @elseif ($type->delegate == 2)
                                                <span class="badge bg-primary">International</span>
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                        <td>{{ $type->type }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <form action="{{ route('memberType.destroy', [$society, $type->id]) }}"
                                                        method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                                class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                                    </form>
                                                    <a class="dropdown-item"
                                                        href="{{ route('memberType.edit', [$society, $type->id]) }}"><i
                                                            class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        No active member types found.
                    </div>
                @endif
            </div>

        </div>
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <style>
        #sortable-member-types tr.ui-sortable-helper {
            display: table;
            background-color: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        #sortable-member-types tr.ui-sortable-placeholder {
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
            if ($('#sortable-member-types tr').length > 0) {
                $('#sortable-member-types').sortable({
                    handle: '.drag-handle',
                    placeholder: 'ui-sortable-placeholder',
                    helper: function(e, tr) {
                        var $originals = tr.children();
                        var $helper = tr.clone();
                        $helper.children().each(function(index) {
                            $(this).width($originals.eq(index).width());
                        });
                        return $helper;
                    },
                    update: function() {
                        updateOrder();
                    }
                });
            }

            function updateOrder() {
                var orders = [];
                $('#sortable-member-types tr').each(function(index) {
                    orders.push({
                        id: $(this).data('id'),
                        position: index + 1
                    });
                });

                $.ajax({
                    url: '{{ route('memberType.update-order', $society->getRouteKey()) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        orders: orders
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#sortable-member-types tr').each(function(index) {
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
        });
    </script>
@endsection
