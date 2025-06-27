@extends('backend.layouts.conference.main')

@section('title')
    Committee
@endsection
@section('content')
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Committee</h5>
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
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Committee'))
                            <a href="{{ route('committee.create', [$society, $conference]) }}" class="btn btn-primary"
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
                        <th scope="col">#</th>
                        <th scope="col">Committee Name</th>
                        <th scope="col">Focal Person</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col" style="width: 12%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($committees as $committee)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $committee->committee_name }}</td>
                            <td>{{ $committee->focal_person }}</td>
                            <td>{{ $committee->email }}</td>
                            <td>{{ $committee->phone }}</td>
                            {{-- <td>
                                @if ($committee->committeeMembers->isNotEmpty())
                                    @foreach ($committee->committeeMembers->where('conference_id', $latestConference->id)->where('status', 1) as $member)
                                        {{ $member->user->fullName($member, 'user') }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td> --}}
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Committee'))
                                            <a class="dropdown-item"
                                                href="{{ route('committee.edit', [$society, $conference, $committee->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'View Committee'))
                                            <a class="dropdown-item viewData" data-bs-toggle="modal"
                                                data-bs-target="#pricingModal{{ $committee->id }}"><i
                                                    class="icon-base ti tabler-eye me-1 "></i> View</a>
                                        @endif

                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'View Committee Member'))
                                            <a href="{{ route('committeeMember.index', [$society, $conference, $committee->slug]) }}"
                                                class="dropdown-item">Committee Members</a>
                                        @endif
                                        <hr>
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Delete Committee'))
                                            <form
                                                action="{{ route('committee.destroy', [$society, $conference, $committee->id]) }}"
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
                        <div class="modal fade" id="pricingModal{{ $committee->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                                <div class="modal-content" id="modalContent">

                                    <div class="modal-body">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                        <h4 class="modal-title" id="exampleModalCenterTitle">View Committee Detail</h4>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Committee
                                                    Name</p><span>{{ $committee->committee_name }}</span>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Focal Person
                                                </p><span>{{ $committee->focal_person }}</span>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Email</p>
                                                <span>{{ $committee->email }}</span>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Phone</p>
                                                <span>{{ $committee->phone }}</span>
                                            </div>
                                        </div>
                                        @if (!empty($committee->description))
                                            <p class="text-primary mb-1"><i
                                                    class="i-Letter-Open text-16 mr-1"></i>Description</p>
                                            <p>{!! $committee->description !!}</p>
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
