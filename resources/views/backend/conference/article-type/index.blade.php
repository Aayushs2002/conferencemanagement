@extends('backend.layouts.conference.main')

@section('title')
    Article Type
@endsection
@section('content')
    <div class="card">
 
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Article Type</h5>
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
                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Add Article Type'))
                            <a href="javascript:void(0);" class="btn btn-primary createArticleType" tabindex="0"
                                data-bs-toggle="modal" data-bs-target="#pricingModal">
                                <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="px-3 py-2">
                <small class="text-info">
                    <i class="ti tabler-grip-vertical"></i> <strong>Drag and drop rows to reorder article types</strong>
                </small>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th style="width: 30px;"><i class="ti tabler-arrows-move"></i></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="sortable-table">
                    @foreach ($articleTypes as $articleType)
                        <tr data-id="{{ $articleType->id }}" style="cursor: move;">
                            <td class="drag-handle text-center">
                                <i class="ti tabler-grip-vertical" style="font-size: 20px; color: #999;"></i>
                            </td>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $articleType->name }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Edit Article Type'))
                                            <a class="dropdown-item editArticleType" href="javascript:void(0);"
                                                data-id="{{ $articleType->id }}" data-bs-toggle="modal"
                                                data-bs-target="#pricingModal"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Edit Article Type'))
                                            <a class="dropdown-item settingArticleType" href="javascript:void(0);"
                                                data-id="{{ $articleType->id }}" data-bs-toggle="modal"
                                                data-bs-target="#settingModal"><i
                                                    class="icon-base ti tabler-settings me-1"></i> Settings</a>
                                        @endif
                                        <hr>
                                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Delete Article Type'))
                                            <form
                                                action="{{ route('articleType.destroy', [$society, $conference, $articleType->id]) }}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                        class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                            </form>
                                        @endif
                                    </div>

                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="settingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="settingModalContent">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
    <style>
        #sortable-table tr.ui-sortable-helper {
            display: table;
            background-color: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        #sortable-table tr.ui-sortable-placeholder {
            background-color: #e3f2fd;
            visibility: visible !important;
            height: 60px;
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
            // Initialize sortable
            $("#sortable-table").sortable({
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

            function updateOrder() {
                var orders = [];
                $('#sortable-table tr').each(function(index) {
                    orders.push({
                        id: $(this).data('id'),
                        position: index + 1
                    });
                });

                $.ajax({
                    url: '{{ route('articleType.update-order', [$society, $conference]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        orders: orders
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update serial numbers
                            $('#sortable-table tr').each(function(index) {
                                $(this).find('th:eq(0)').text(index + 1);
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


            $(document).off("click", ".createArticleType");
            $(document).on("click", ".createArticleType", function(e) {
                e.preventDefault();
                var url = '{{ route('articleType.create', [$society, $conference]) }}';
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                $.get(url, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                }); 
            });

            $(document).off("click", ".editArticleType");
            $(document).on("click", ".editArticleType", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ route('articleType.edit', [$society, $conference, ':id']) }}';
                url = url.replace(':id', id);
                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                $.get(url, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

            $(document).off("click", ".settingArticleType");
            $(document).on("click", ".settingArticleType", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ route('articleType.setting', [$society, $conference]) }}';
                $('#settingModalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        article_type_id: id
                    },
                    success: function(response) {
                        setTimeout(function() {
                            $('#settingModalContent').html(response);
                        }, 500);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to load settings'
                        });
                        $('#settingModal').modal('hide');
                    }
                });
            });
        });
    </script>
@endsection
