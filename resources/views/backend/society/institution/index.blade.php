@extends('backend.layouts.society.main')
@section('title')
    Institution
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Institution Settings</h5>
                </div>
            </div>

            <div class="card-body">
                <p class="text-muted mb-4">Select the institutions that will be available for your society members during registration.</p>
                
                <form action="{{ route('society.institution.update', $society) }}" method="POST">
                    @csrf 
                    
                    <div class="row"> 
                        @if($institutions->count() > 0)
                            @foreach ($institutions as $institution)
                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            name="institutions[]" 
                                            value="{{ $institution->id }}" 
                                            id="institution_{{ $institution->id }}"
                                            {{ in_array($institution->id, $selectedInstitutions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="institution_{{ $institution->id }}">
                                            {{ $institution->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-warning" role="alert">
                                    <i class="icon-base ti tabler-alert-triangle me-2"></i>
                                    No institutions available. Please contact the administrator to add institutions.
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($institutions->count() > 0)
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
                <h6 class="mb-3">Currently Selected Institutions</h6>
                @if(count($selectedInstitutions) > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($institutions->whereIn('id', $selectedInstitutions) as $institution)
                            <span class="badge bg-label-primary">{{ $institution->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No institutions selected yet.</p>
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
                const checkedCount = $('input[name="institutions[]"]:checked').length;
                
                if (checkedCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Institution Selected',
                        text: 'Please select at least one institution for your society.',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
