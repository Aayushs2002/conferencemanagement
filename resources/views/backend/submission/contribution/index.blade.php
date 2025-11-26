@extends('backend.layouts.conference.main')

@section('title')
    Contribution
@endsection
@section('content')
    <div class="main-content">
        <div class="breadcrumb">
            <h1>Contribution</h1>
        </div>
        <div class="separator-breadcrumb border-top"></div>
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'Add Contribution'))
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <a href="{{ route('contribution.create', [$society, $conference]) }}"
                                    class="btn btn-primary m-1">
                                    Add Contribution
                                </a>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive mt-3">
                        <table class="table table-striped" id="tableData">
                            <thead>
                                <tr>
                                    <th scope="col">S.N</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($contributions) > 0)
                                    @foreach ($contributions as $key => $contribution)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $contribution->name }}</td>
                                            <td>{{ Str::limit($contribution->description, 50) }}</td>
                                            <td>
                                                @if (auth()->user()->hasConferencePermissionBlade($conference, 'Edit Contribution'))
                                                    <a href="{{ route('contribution.create', [$society, $conference]) }}?id={{ $contribution->id }}"
                                                        class="btn btn-primary m-1 btn-sm">
                                                        Edit
                                                    </a>
                                                @endif
                                                @if (auth()->user()->hasConferencePermissionBlade($conference, 'Delete Contribution'))
                                                  

                                                    <form
                                                        action="{{ route('contribution.destroy', [$society, $conference, $contribution->id]) }}"
                                                        method="POST"  class="d-inline">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-danger m-1 btn-sm delete-btn delete"
                                                            href="javascript:void(0);">Delete</a>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No data found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
