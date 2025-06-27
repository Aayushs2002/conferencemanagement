@extends('backend.layouts.main')
@section('content')
    @if (current_user()->type == 3)
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Joined Society</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($joinedSocities as $society)
                        <a href="{{ route('my-society.conference', $society) }}">
                            <li class="list-group-item">{{ $society->abbreviation }}</li>
                        </a>
                    @endforeach
                </ul>
                <div class="card-body">
                    <a href="javascript:void(0)" class="card-link joinSociety" data-bs-toggle="modal"
                        data-bs-target="#JoinSociety">Join The Society</a>
                </div>
            </div>
        </div>
        <div class="modal fade" id="JoinSociety" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>
    @else
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-4">
                <!-- Total Registrations Card -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-primary fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Total</span>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">{{ $societyCount }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Society</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-primary fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Total</span>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">{{ $namePrfixCount }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Name Prefix</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-primary fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Total</span>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">{{ $intitutionCount }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Institution</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-primary fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Total</span>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">{{ $designationCount }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Designation</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-primary fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Total</span>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">{{ $departmentCount }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Department</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- National Registrants Card -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-users text-success fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">National</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-3">National Registrants</h6>
                            <div class="list-group list-group-flush">
                                {{-- @foreach ($totalNationalRegistrants as $nr_item)
                                    <div
                                        class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                        <span class="text-muted fw-medium">{{ $nr_item->type }}</span>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                            {{ $nr_item->user_count }}
                                        </span>
                                    </div>
                                @endforeach --}}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- International Registrants Card -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-world text-info fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">International</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-3">International Registrants</h6>
                            <div class="list-group list-group-flush">
                                {{-- @foreach ($totalInternationalRegistrants as $inr_item)
                                    <div
                                        class="list-group-item border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                        <span class="text-muted fw-medium">{{ $inr_item->type }}</span>
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">
                                            {{ $inr_item->user_count }}
                                        </span>
                                    </div>
                                @endforeach --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on("click", ".joinSociety", function(e) {
                e.preventDefault();
                var url = '{{ route('joinSociety') }}';
                var _token = '{{ csrf_token() }}';

                $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                var data = {
                    _token: _token,
                };
                $.post(url, data, function(response) {
                    setTimeout(function() {
                        $('#modalContent').html(response);
                    }, 1000);
                });
            });

        });
    </script>
@endsection
