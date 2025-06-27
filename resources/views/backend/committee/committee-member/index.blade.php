@extends('backend.layouts.conference.main')

@section('title')
    Committee Member
@endsection
@section('content')
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Committee Members Of <span
                            class="text-danger">({{ $committee->committee_name }})</h5>
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
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Committee Member'))
                            <a href="{{ route('committeeMember.create', [$society, $conference, $committee->slug]) }}"
                                class="btn btn-primary" tabindex="0">
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
                        <th scope="col">Name</th>
                        <th scope="col">Designation</th>
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Change Featured Committee Member'))
                            <th scope="col">Is Featured ?</th>
                        @endif
                        <th scope="col" style="width: 12%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($committee_members as $member)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $member->user->fullName($member->user) }}</td>
                            <td>{{ $member->designation->designation }}</td>
                            @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Change Featured Committee Member'))
                                <td>
                                    @if ($member->is_featured == 1)
                                        <a href="{{ route('committeeMember.changeFeatured', [$society, $conference, $member->id]) }}"
                                            class="btn btn-success btn-sm ml-2" title="Remove Featured"><i
                                                class="icon-base ti tabler-circle-check"></i></a>
                                    @else
                                        <a href="{{ route('committeeMember.changeFeatured', [$society, $conference, $member->id]) }}"
                                            class="btn btn-danger btn-sm ml-2" title="Make Featured"><i
                                                class="icon-base ti tabler-circle-x"></i></a>
                                    @endif
                                </td>
                            @endif
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Committee Member'))
                                            <a class="dropdown-item"
                                                href="{{ route('committeeMember.edit', [$society, $conference, $member->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        <hr>
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Delete Committee Member'))
                                            <form
                                                action="{{ route('committeeMember.destroy', [$society, $conference, $member->id]) }}"
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
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>
    </div>
@endsection
