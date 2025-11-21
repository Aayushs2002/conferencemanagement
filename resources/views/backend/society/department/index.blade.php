@extends('backend.layouts.society.main')
@section('title')
    Department
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Department Settings</h5>
                </div>
            </div>

            <div class="card-body">
                <p class="text-muted mb-4">Select the departments that will be available for your society members during registration.</p>
                
                <form action="{{ route('society.department.update', $society) }}" method="POST">
                    @csrf
                    
                    <div class="row"> 
                        @if($departments->count() > 0)
                            @foreach ($departments as $department)
                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            name="departments[]" 
                                            value="{{ $department->id }}" 
                                            id="department_{{ $department->id }}"
                                            {{ in_array($department->id, $selectedDepartments) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="department_{{ $department->id }}">
                                            {{ $department->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-warning" role="alert">
                                    <i class="icon-base ti tabler-alert-triangle me-2"></i>
                                    No departments available. Please contact the administrator to add departments.
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($departments->count() > 0)
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-base ti tabler-device-floppy me-1"></i>
                                Save Settings
                            </button>
                            <a href="{{ route('society.dashboard', $society->getRouteKey()) }}" class="btn btn-secondary">
                                <i class="icon-base ti tabler-arrow-left me-1"></i>
                                Back to Dashboard
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="card-body border-top">
                <h6 class="mb-3">Currently Selected Departments</h6>
                @if(count($selectedDepartments) > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($departments->whereIn('id', $selectedDepartments) as $department)
                            <span class="badge bg-label-primary">{{ $department->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No departments selected yet.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Show confirmation when form is submitted
            $('form').on('submit', function(e) {
                const checkedCount = $('input[name="departments[]"]:checked').length;
                
                if (checkedCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Department Selected',
                        text: 'Please select at least one department for your society.',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
