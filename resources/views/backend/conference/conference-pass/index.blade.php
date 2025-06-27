@extends('backend.layouts.conference.main')

@section('title')
    Pass Setting
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Pass Setting
                    </h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Edit Pass Setting'))
                            @if (!$pass_setting)
                                <a href="{{ route('pass-setting.create', [$society, $conference]) }}" class="btn btn-primary"
                                    tabindex="0">
                                    <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                                    <span class="d-none d-sm-inline-block">Add New</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($pass_setting)
                        <tr>
                            <th scope="row">1</th>
                            <td> <a href="{{ asset('storage/conference/conference/pass/' . $pass_setting->image) }}"
                                    target="_blank"><img
                                        src="{{ asset('storage/conference/conference/pass/' . $pass_setting->image) }}"
                                        alt="image" height="50" width="40"></a></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade($conference, 'Edit Pass Setting'))
                                            <a class="dropdown-item"
                                                href="{{ route('pass-setting.edit', [$society, $conference, $pass_setting->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i>
                                                Edit</a>
                                        @endif
                                    </div>

                                </div>

                            </td>
                        </tr>
                    @endif
                </tbody>

            </table>
        </div>
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>
    </div>
@endsection
