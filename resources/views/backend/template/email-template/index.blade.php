@extends('backend.layouts.conference.main')

@section('title')
    Email Template
@endsection
@section('content')
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Email Template</h5>
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
                        {{-- @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add News/Notice')) --}}
                        <a href="{{ route('email-template.create', [$society, $conference]) }}" class="btn btn-primary"
                            tabindex="0">
                            <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New</span>
                        </a>
                        {{-- @endif --}}
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>Template Type</th>
                        <th>Subject</th>
                        <th>Body</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($email_templates as $email_template)
                        <tr>
                            <td>{{ $email_template->key }}</td>
                            <td>{{ $email_template->subject }}</td>
                            <td>{!! $email_template->body !!}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="{{ route('email-template.edit', [$society, $conference, $email_template->id]) }}"><i
                                                class="icon-base ti tabler-pencil me-1"></i> Edit</a>
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
