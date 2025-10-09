@extends('backend.layouts.main')
@section('title')
    Contact
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Contact</h5>
                </div>
                {{-- <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <a href="{{ route('blog.create') }}" class="btn btn-primary" tabindex="0">
                            <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New</span>
                        </a>
                    </div>
                </div> --}}
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Conference Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($contacts as $contact)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $contact->full_name }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->contact_number }}</td>
                            <td>{{ $contact->conference_type }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item viewData" data-id="{{ $contact->id }}"
                                            data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                class="icon-base ti tabler-eye me-1 "></i> View</a>
                                        <hr>
                                        <form action="{{ route('contact.destroy', $contact->id) }}" method="POST">
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
@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                var url = '{{ route('contact.show') }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');

                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });
        });
    </script>
@endsection
