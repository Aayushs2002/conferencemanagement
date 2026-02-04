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
                        <button type="button" class="btn btn-secondary dropdown-toggle me-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti tabler-sort-ascending me-1"></i>
                            <span class="d-none d-sm-inline-block">Sort by Name</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request('sort') == 'name_asc' ? 'active' : '' }}" href="{{ route('workshop.workshop-trainer.index', [$society, $conference, $workshop, 'sort' => 'name_asc']) }}">
                                    <i class="ti tabler-sort-ascending me-2"></i> Name (A-Z)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('sort') == 'name_desc' ? 'active' : '' }}" href="{{ route('workshop.workshop-trainer.index', [$society, $conference, $workshop, 'sort' => 'name_desc']) }}">
                                    <i class="ti tabler-sort-descending me-2"></i> Name (Z-A)
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ !request('sort') ? 'active' : '' }}" href="{{ route('workshop.workshop-trainer.index', [$society, $conference, $workshop]) }}">
                                    <i class="ti tabler-refresh me-2"></i> Default (Recent First)
                                </a>
                            </li>
                        </ul>
                        <button type="button" class="btn btn-success dropdown-toggle me-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-none d-sm-inline-block">Generate Pass</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('workshop.generatePass', ['workshop' => $workshop, 'registrant_type' => 2]) }}" target="_blank">
                                    <i class="ti tabler-users me-2"></i> Generate for Registered Trainers
                                </a>
                            </li>
                            @php
                                $trainerCount = \App\Models\Workshop\WorkshopRegistration::where(['workshop_id' => $workshop->id, 'registrant_type' => 2, 'status' => 1])->count();
                            @endphp
                            @if($trainerCount > 100)
                            <li>
                                <a class="dropdown-item" href="{{ route('workshop.generatePassBatch', ['workshop' => $workshop, 'registrant_type' => 2, 'batch' => 1]) }}" target="_blank">
                                    <i class="ti tabler-file-stack me-2"></i> Generate in Batches - Recommended ({{ $trainerCount }} trainers)
                                </a>
                            </li>
                            @endif
                            @if($trainerCount > 100)
                            <li><hr class="dropdown-divider"></li>
                            <li><small class="dropdown-item-text text-warning">⚠️ Use batch mode for >100 trainers to avoid errors</small></li>
                            @endif
                            <li>
                                <a class="dropdown-item"   href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#dummyPassModalTrainer">
                                    <i class="ti tabler-user-plus me-2"></i> Generate Dummy Pass
                                </a>
                            </li>
                        </ul>
                        
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
                            <td>{{ $trainer->user?->fullName($trainer->user) }}</td>
                            <td>{{ $trainer->user?->email }}</td>
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
                                                href="{{ route('workshop.workshop-registration.edit', [$society, $conference, $workshop, $trainer->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit Registration</a>
                                        @endif
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Workshop Trainer'))
                                            <a class="dropdown-item"
                                                href="{{ route('workshop.workshop-trainer.edit', [$society, $conference, $workshop, $trainer->id]) }}"><i
                                                    class="icon-base ti tabler-user-edit me-1"></i> Edit Trainer Profile</a>
                                        @endif
                                        <hr>
                                        {{-- @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Delete Workshop Trainer')) --}}
                                            <form
                                                action="{{ route('workshop.workshop-trainer.destroy', [$society, $conference, $workshop, $trainer->id]) }}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                        class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                            </form>
                                        {{-- @endif --}}
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

        <!-- Dummy Pass Generation Modal for Trainers -->
        <div class="modal fade" id="dummyPassModalTrainer" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Dummy Pass for Trainers</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('workshop.generateDummyPass', ['workshop' => $workshop]) }}" method="POST" target="_blank">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="dummy_count_trainer" class="form-label">Number of Dummy Passes <code>*</code></label>
                                <input type="number" class="form-control" id="dummy_count_trainer" name="dummy_count" 
                                       min="1" max="100" value="1" required>
                                <small class="text-muted">Enter the number of dummy trainer passes to generate (Max: 100)</small>
                            </div>
                            <input type="hidden" name="registrant_type" value="2">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Generate Passes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
