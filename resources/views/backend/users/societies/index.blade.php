@extends('backend.layouts.main')
@section('title')
    Society
@endsection
@section('content')
    <!-- DataTable with Buttons -->
    @if (is_super_admin())
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                    <div
                        class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                        <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Society</h5>
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
                                    <li><a class="dropdown-item" href="#" onclick="exportTo('excel')">Export to
                                            Excel</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#" onclick="exportTo('pdf')">Export to PDF</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#" onclick="exportTo('csv')">Export to CSV</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#" onclick="window.print()">Print</a></li>
                                </ul>
                            </div>
                            <a href="{{ route('society.create') }}" class="btn btn-primary" tabindex="0">
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
                            <th>Society Name</th>
                            <th>Abbreaviation</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Contact Person</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($societies as $society)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $society->users->value('f_name') }}</td>
                                <td> <a href="{{ asset('storage/society/logo/' . $society->logo) }}" target="_blank"><img
                                            src="{{ asset('storage/society/logo/' . $society->logo) }}" alt="logo"
                                            height="50" width="40"></a></td>
                                <td>{{ $society->users->value('email') }}</td>
                                <td>{{ $society->phone }}</td>
                                <td>{{ $society->contact_person }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        {{-- @dd($society) --}}
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('society.edit', $society) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>

                                            <a class="dropdown-item viewData" data-id="{{ $society->id }}"
                                                data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                                    class="icon-base ti tabler-eye me-1 "></i> View</a>
                                            <hr>
                                            <form action="{{ route('society.destroy', $society->id) }}" method="POST">
                                                @method('delete')
                                                @csrf
                                                <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                        class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                            </form>
                                        </div>
                                        <a href="{{ route('society.dashboard', $society) }}"
                                            class="btn btn-info btn-sm mt-1">Open Portal</a>
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
    @else
        <div class="row mb-12 g-6">
            <h2 class="text-center">My Society</h2>
            @foreach ($societies as $society)
                <div class="col-md-6 col-lg-6">
                    <div class="card h-100">
                        <div class="d-flex justify-content-center">
                            <img class="card-img-top" src="{{ asset('storage/society/logo/' . $society->logo) }}"
                                style="width: 20%" alt="Card image cap" />
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $society->users->where('type', 2)->value('f_name') }}
                                ({{ $society->abbreviation }})
                            </h5>
                            <p class="card-text">
                                {!! $society->description !!}
                            </p>
                            <a href="{{ route('my-society.conference', $society) }}" class="btn btn-outline-primary">Go To
                                Society</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on("click", ".viewData", function(e) {
                e.preventDefault();
                var url = '{{ route('society.show') }}';
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
