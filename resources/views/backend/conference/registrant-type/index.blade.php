@extends('backend.layouts.conference.main')

@section('title')
    Registration Types
@endsection

@section('content')
    <div class="main-content">
        <div class="breadcrumb">
            <h3>Registration Types</h3>
        </div>
        <div class="separator-breadcrumb border-top mb-4"></div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('delete'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('delete') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            {{-- Add New Custom Type --}}
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Custom Registration Type</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('conference.registrant-type.store', [$society, $conference]) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Type Name <code>*</code></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="e.g., Industry Expert"
                                    value="{{ old('name') }}" />
                                @error('name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-plus me-1"></i> Add Type
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                {{-- Global Types with show/hide toggle --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Global Registration Types</h5>
                            <small class="text-muted">Toggle to show or hide each type for this conference.</small>
                        </div>
                        @if(is_super_admin())
                            <a href="{{ route('registrant-type.global.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti tabler-settings me-1"></i> Manage Global Types
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-center" style="width:130px">Visibility</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($globalTypes as $type)
                                    @php $isHidden = in_array($type->id, $hiddenIds); @endphp
                                    <tr>
                                        <td>{{ $type->name }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('conference.registrant-type.toggleGlobal', [$society, $conference, $type->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $isHidden ? 'btn-danger' : 'btn-success' }}">
                                                    @if($isHidden)
                                                        <i class="ti tabler-eye-off me-1"></i> Hidden
                                                    @else
                                                        <i class="ti tabler-eye me-1"></i> Visible
                                                    @endif
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Conference-Specific Types --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Conference-Specific Types</h5>
                        <small class="text-muted">Types added specifically for this conference. You can edit or remove them.</small>
                    </div>
                    <div class="card-body">
                        @if ($conferenceTypes->isEmpty())
                            <p class="text-muted mb-0">No custom types added yet. Use the form on the left to add one.</p>
                        @else
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($conferenceTypes as $type)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <form action="{{ route('conference.registrant-type.update', [$society, $conference, $type->id]) }}" method="POST" class="d-flex gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $type->name }}" required />
                                                    <button type="submit" class="btn btn-sm btn-success text-nowrap">
                                                        <i class="ti tabler-check"></i> Save
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{ route('conference.registrant-type.destroy', [$society, $conference, $type->id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Remove this registration type?')">
                                                        <i class="ti tabler-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
