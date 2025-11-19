@extends('backend.layouts.society.main')
@section('title')
    Name Prefix
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Name Prefix Settings</h5>
                </div>
            </div>

            <div class="card-body">
                <p class="text-muted mb-4">Select the name prefixes that will be available for your society members during registration.</p>
                
                <form action="{{ route('society.name-prefix.update', $society) }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        @if($namePrefixes->count() > 0)
                            @foreach ($namePrefixes as $namePrefix)
                                <div class="col-md-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            name="name_prefixes[]" 
                                            value="{{ $namePrefix->id }}" 
                                            id="prefix_{{ $namePrefix->id }}"
                                            {{ in_array($namePrefix->id, $selectedPrefixes) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="prefix_{{ $namePrefix->id }}">
                                            {{ $namePrefix->prefix }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-warning" role="alert">
                                    <i class="icon-base ti tabler-alert-triangle me-2"></i>
                                    No name prefixes available. Please contact the administrator to add name prefixes.
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($namePrefixes->count() > 0)
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
                <h6 class="mb-3">Currently Selected Name Prefixes</h6>
                @if(count($selectedPrefixes) > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($namePrefixes->whereIn('id', $selectedPrefixes) as $prefix)
                            <span class="badge bg-label-primary">{{ $prefix->prefix }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No name prefixes selected yet.</p>
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
                const checkedCount = $('input[name="name_prefixes[]"]:checked').length;
                
                if (checkedCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Prefix Selected',
                        text: 'Please select at least one name prefix for your society.',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
