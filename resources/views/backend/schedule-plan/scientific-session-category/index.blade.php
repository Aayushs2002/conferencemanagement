@extends('backend.layouts.conference.main')
@section('title')
    Scientific Session Category
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Scientific Session Category</h5>
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
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Scientific Session Category'))
                            {{-- <a href="{{ route('category.create', [$society, $conference]) }}" class="btn btn-primary"
                                tabindex="0">
                                <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New</span>
                            </a> --}}
                            @if (request('category'))
                                <a href="{{ route('category.create', [$society, $conference, 'category' => request('category')]) }}"
                                    class="btn btn-primary" tabindex="0">
                                    <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                                    <span class="d-none d-sm-inline-block">Add Sub Category</span>
                                </a>
                            @else
                                <a href="{{ route('category.create', [$society, $conference]) }}" class="btn btn-primary"
                                    tabindex="0">
                                    <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                                    <span class="d-none d-sm-inline-block">Add Category</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Category Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($categories as $category)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $category->category_name }}</td>
                            <td>
                                @if (!in_array($category->id, [1, 2, 3, 4, 5]))
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Scientific Session Category'))
                                                <a class="dropdown-item"
                                                    href="{{ route('category.edit', [$society, $conference, $category->id]) }}"><i
                                                        class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                            @endif
                                            <a href=" {{ route('category.index', [$society, $conference, 'category' => $category->id]) }}"
                                                class="dropdown-item">
                                                <i class="icon-base ti tabler-category me-1"></i>
                                                Sub Category
                                            </a>
                                            <hr>
                                            @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Delete Scientific Session Category'))
                                                <form
                                                    action="{{ route('category.destroy', [$society, $conference, $category->id]) }}"
                                                    method="POST">
                                                    @method('delete')
                                                    @csrf
                                                    <a class="dropdown-item text-danger delete"
                                                        href="javascript:void(0);"><i
                                                            class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                                </form>
                                            @endif

                                        </div>

                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>
    </div>
@endsection
