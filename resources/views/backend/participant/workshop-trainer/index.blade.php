@extends('backend.layouts.conference.main')

@section('title')
    Workshop Trainers - {{ $workshop->workshop_title }}
@endsection

@section('content')
    <div class="card mb-6">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">
                        Workshop Trainers
                        <br><small class="text-muted">Workshop: {{ $workshop->workshop_title }}</small>
                    </h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <a href="{{ route('my-society.conference.my-workshop.index', [$society, $conference]) }}"
                            class="btn btn-secondary me-2" tabindex="0">
                            <i class="icon-base ti tabler-arrow-left icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Back to Workshops</span>
                        </a>
                        <a href="{{ route('my-society.conference.my-workshop.trainer.create', [$society, $conference, $workshop]) }}"
                            class="btn btn-primary" tabindex="0">
                            <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add Trainer</span>
                        </a>
                    </div>
                </div>
            </div>
            
            @if($trainers->isEmpty())
                <div class="text-center py-5">
                    <i class="icon-base ti tabler-users mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h5 class="text-muted">No trainers added yet</h5>
                    <p class="text-muted">Click "Add Trainer" to add trainers to this workshop.</p>
                </div>
            @else
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Trainer Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trainers as $trainer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $trainer->user->fullName($trainer->user) }}</td>
                                <td>{{ $trainer->user->email }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('my-society.conference.my-workshop.trainer.edit', [$society, $conference, $workshop, $trainer->id]) }}">
                                                <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                            </a>
                                            <hr>
                                            <form action="{{ route('my-society.conference.my-workshop.trainer.destroy', [$society, $conference, $workshop, $trainer->id]) }}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <a class="dropdown-item text-danger delete" href="javascript:void(0);">
                                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
