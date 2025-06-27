@extends('backend.layouts.conference.main')
@section('title')
    Sponsor
@endsection
@section('content')
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Sponsor</h5>
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
                        <a href="{{ route('sponsor.generaate-pass', [$society, $conference]) }}" target="_blank"
                            class="btn btn-info me-2" tabindex="0">
                            <i class="icon-base ti tabler-id-badge icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Generate Pass</span>
                        </a>
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Sponsor'))
                            <a href="{{ route('sponsor.create', [$society, $conference]) }}" class="btn btn-primary"
                                tabindex="0">
                                <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Name</th>
                        <th>Amount</th>
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Change Publish Sponsor'))
                            <th>Publish</th>
                        @endif
                        <th>Need To Add Participants?</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sponsors as $sponsor)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $sponsor->category->category_name }}</td>
                            <td>{{ $sponsor->name }}</td>
                            <td>{{ $sponsor->amount }}</td>
                            <td>
                                @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Change Publish Sponsor'))
                                    @if ($sponsor->visible_status == 1)
                                        <a href="{{ route('sponsor.changeStatus', $sponsor->id) }}"
                                            class="btn btn-success ml-2" title="Unpublish Executive"><i
                                                class="icon-base ti tabler-circle-check"></i></a>
                                    @else
                                        <a href="{{ route('sponsor.changeStatus', $sponsor->id) }}"
                                            class="btn btn-danger ml-2" title="Publish Executive"><i
                                                class="icon-base ti tabler-circle-x"></i></a>
                                    @endif
                                @endif
                            </td>
                            <td>
                                Total Participants: {{ $sponsor->total_attendee }} <br>
                                @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Participant Sponsor'))
                                    <button class="btn btn-sm btn-primary addParticipant" data-id="{{ $sponsor->id }}"
                                        data-bs-toggle="modal" data-bs-target="#pricingModal">Add Participant's
                                        Number</button>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Sponsor'))
                                            <a class="dropdown-item"
                                                href="{{ route('sponsor.edit', [$society, $conference, $sponsor->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        <a class="dropdown-item viewData" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal{{ $sponsor->id }}"><i
                                                class="icon-base ti tabler-eye me-1 "></i> View</a>
                                        <hr>
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Delete Sponsor'))
                                            <form
                                                action="{{ route('sponsor.destroy', [$society, $conference, $sponsor->id]) }}"
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
                        <div class="modal fade" id="pricingModal{{ $sponsor->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-lg  modal-simple modal-pricing">
                                <div class="modal-content" id="modal-content">

                                    <div class="modal-body">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                        <h4 class="modal-title" id="exampleModalCenterTitle">View Data</h4>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Sponsor
                                                    Category</p>
                                                <span>{{ $sponsor->category->category_name }}</span>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Name</p>
                                                <span>{{ $sponsor->name }}</span>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Amount</p>
                                                <span>{{ $sponsor->amount }}</span>
                                            </div>
                                            @if (!empty($sponsor->logo))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Logo</p>
                                                    <span><img src="{{ asset('storage/sponsor/logo/' . $sponsor->logo) }}"
                                                            height="70" alt="image"></span>
                                                </div>
                                            @endif
                                            @if (!empty($sponsor->address))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i
                                                            class="i-ID-2 text-16 mr-1"></i>Address
                                                    </p>
                                                    <span>{{ $sponsor->address }}</span>
                                                </div>
                                            @endif
                                            @if (!empty($sponsor->contact_person))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i
                                                            class="i-ID-2 text-16 mr-1"></i>Contact
                                                        Person</p>
                                                    <span>{{ $sponsor->contact_person }}</span>
                                                </div>
                                            @endif
                                            @if (!empty($sponsor->email))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Email
                                                    </p>
                                                    <span>{{ $sponsor->email }}</span>
                                                </div>
                                            @endif
                                            <div class="col-md-4 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Phone</p>
                                                <span>{{ $sponsor->phone }}</span>
                                            </div>
                                        </div>
                                        @if (!empty($sponsor->description))
                                            <p class="text-primary mb-1"><i
                                                    class="i-Letter-Open text-16 mr-1"></i>Description</p>
                                            <p>{!! $sponsor->description !!}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on("click", ".addParticipant", function(e) {
                e.preventDefault();
                var url = '{{ route('sponsor.addParticipant') }}';
                var _token = '{{ csrf_token() }}';
                var id = $(this).data('id');
                var data = {
                    _token: _token,
                    id: id
                };
                $.post(url, data, function(response) {
                    $('#modalContent').html(response);
                });
            });
        });
    </script>
@endsection
