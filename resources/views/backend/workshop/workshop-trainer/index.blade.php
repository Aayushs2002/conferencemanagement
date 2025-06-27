@extends('backend.layouts.conference.main')

@section('title')
    Workshop Trainer
@endsection
@section('content')
    <div class="card mb-6">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Workshop Trainer (Workshop:
                        {{ $workshop->workshop_title }})
                    </h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <a href="{{ route('workshop.generatePass', ['workshop' => $workshop, 'registrant_type' => 2]) }}"
                            class="btn btn-success me-2" tabindex="0">
                            <span class="d-none d-sm-inline-block">Generate Pass</span>
                        </a>
                        
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Workshop Trainer'))
                            <a href="{{ route('workshop.workshop-trainer.create', [$society, $conference, $workshop]) }}"
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
                        <th>#</th>
                        <th scope="col">Trainer Name</th>
                        <th scope="col">Trainer Email</th>
                        {{-- <th scope="col">Affiliation</th>
                        <th scope="col">Image</th>
                        <th scope="col">CV</th> --}}
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trainers as $trainer)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $trainer->user->fullName($trainer->user) }}</td>
                            <td>{{ $trainer->user->email }}</td>
                            {{-- <td>{{ $trainer->affiliation }}</td>
                            <td><a href="{{ asset('storage/workshop/trainers/image/' . $trainer->image) }}"
                                    target="_blank"><img
                                        src="{{ asset('storage/workshop/trainers/image/' . $trainer->image) }}"
                                        height="30" width="25" alt="cv"></a></td>
                            <td><a href="{{ asset('storage/workshop/trainers/cv/' . $trainer->cv) }}" target="_blank"><img
                                        src="{{ asset('default-image/pdf.png') }}" height="30" width="25"
                                        alt="cv"></a></td> --}}
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Workshop Trainer'))
                                            <a class="dropdown-item"
                                                href="{{ route('workshop.workshop-trainer.edit', [$society, $conference, $workshop, $trainer->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        <hr>
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Delete Workshop Trainer'))
                                            <form
                                                action="{{ route('workshop.workshop-trainer.destroy', [$society, $conference, $trainer->id]) }}"
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
