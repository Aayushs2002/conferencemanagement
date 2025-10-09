@extends('backend.layouts.main')
@section('title')
    Officail Message
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Officail Message</h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <a href="{{ route('official-message.create',[$society,$conference]) }}" class="btn btn-primary" tabindex="0">
                            <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New</span>
                        </a>
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Full Name</th>
                        <th>Image</th>
                        <th>Designation</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($official_messages as $official_message)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $official_message->full_name }}</td>
                            <td> <img src="{{ asset('storage/offical-message/image/' . $official_message->image) }}"
                                    height="50" alt="image"></td>
                            <td>{{ $official_message->designation }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ route('official-message.edit', [$society, $conference, $official_message->id]) }}"><i
                                                class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        <hr>
                                        <form
                                            action="{{ route('official-message.destroy', [$society, $conference, $official_message->id]) }}"
                                            method="POST">
                                            @method('delete')
                                            @csrf
                                            <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                    class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                        </form>
                                    </div>

                                </div>

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
