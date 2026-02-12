@extends('backend.layouts.main')
@section('content')
    @if (current_user()->type == 3)
        <style>
            .societies-container {
                max-width: 1200px;
                margin: 0 auto;
            }

            .section-title {
                text-align: center;
                margin-bottom: 40px;
                color: #2c3e50;
                font-weight: 600;
            }

            .society-card {
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                border: 1px solid rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                overflow: hidden;
                height: 280px;
                display: flex;
                flex-direction: column;
                margin-bottom: 24px;
            }

            .society-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            }

            .society-card-link {
                text-decoration: none;
                color: inherit;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .society-logo-container {
                padding: 32px 24px 20px 24px;
                text-align: center;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .society-logo {
                width: 80px;
                height: 80px;
                border-radius: 12px;
                object-fit: cover;
                border: 2px solid #f0f0f0;
                margin-bottom: 16px;
            }

            .society-name {
                font-weight: 600;
                color: #2c3e50;
                font-size: 1.1rem;
                text-align: center;
                margin: 0;
            }

            .society-description {
                color: #6c757d;
                font-size: 0.9rem;
                text-align: center;
                margin-top: 8px;
                line-height: 1.4;
            }

            .society-footer {
                padding: 16px 24px;
                background: #f8f9fa;
                border-top: 1px solid #e9ecef;
                text-align: center;
            }

            .society-status {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 0.85rem;
                color: #28a745;
                font-weight: 500;
            }

            .society-status i {
                font-size: 0.75rem;
            }

            /* Join Society Card - Dotted Design */
            .join-society-card {
                background: #ffffff;
                border: 2px dashed #5865f2;
                border-radius: 16px;
                height: 280px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                transition: all 0.3s ease;
                cursor: pointer;
                margin-bottom: 24px;
                position: relative;
                overflow: hidden;
            }

            .join-society-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 40px rgba(88, 101, 242, 0.15);
                border-color: #4752c4;
                background: rgba(88, 101, 242, 0.02);
            }

            .join-icon {
                width: 60px;
                height: 60px;
                background: rgba(88, 101, 242, 0.1);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 16px;
                transition: all 0.3s ease;
            }

            .join-society-card:hover .join-icon {
                background: #5865f2;
                transform: scale(1.1);
            }

            .join-icon i {
                font-size: 1.5rem;
                color: #5865f2;
                transition: all 0.3s ease;
            }

            .join-society-card:hover .join-icon i {
                color: white;
            }

            .join-title {
                font-weight: 600;
                color: #2c3e50;
                font-size: 1.1rem;
                margin-bottom: 8px;
            }

            .join-subtitle {
                color: #6c757d;
                font-size: 0.9rem;
                line-height: 1.4;
                max-width: 200px;
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 60px 20px;
                color: #6c757d;
                grid-column: 1 / -1;
            }

            .empty-state i {
                font-size: 3rem;
                color: #dee2e6;
                margin-bottom: 20px;
                display: block;
            }

            .empty-state h5 {
                color: #2c3e50;
                margin-bottom: 12px;
            }

            .empty-state p {
                margin: 0;
                font-size: 0.9rem;
            }

            /* Responsive Grid */
            .societies-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 24px;
                margin-bottom: 40px;
            }

            @media (max-width: 768px) {
                .societies-grid {
                    grid-template-columns: 1fr;
                    gap: 16px;
                }

                .society-card {
                    height: 240px;
                }

                .join-society-card {
                    height: 240px;
                }
            }
        </style>

        <div class="societies-container">
            <h2 class="section-title">My Institutions</h2>

            <div class="societies-grid">
                @foreach ($joinedSocities as $society)
                    <!-- Society Card -->
                    <div class="society-card">
                        <a href="{{ route('my-society.conference', $society) }}" class="society-card-link">
                            <div class="society-logo-container">
                                <img src="{{ asset('storage/society/logo/' . $society->logo) }}" class="society-logo"
                                    alt="{{ $society->abbreviation }} Logo">
                                <h5 class="society-name">{{ $society->abbreviation }}</h5>
                                @if ($society->name)
                                    <p class="society-description">{{ $society->name }}</p>
                                @endif
                            </div>
                            <div class="society-footer">
                                <span class="society-status">
                                    <i class="ti tabler-eye"></i>
                                    View Institution
                                </span>
                            </div>
                        </a>
                    </div>
                @endforeach


                <!-- Join New Society Card -->
                <div class="join-society-card joinSociety" data-bs-toggle="modal" data-bs-target="#JoinSociety">
                    <div class="join-icon">
                        <i class="ti tabler-plus"></i>
                    </div>
                    <h5 class="join-title">Join New Institution</h5>
                    <p class="join-subtitle">Discover and join institutions that match your interests and goals.</p>
                </div>
            </div>
        </div>

        <div class="modal fade" id="JoinSociety" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const societyItems = document.querySelectorAll('.society-item');

                societyItems.forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateX(5px) scale(1.02)';
                    });

                    item.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateX(0) scale(1)';
                    });
                });
            });
        </script>
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
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-success fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Total</span>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">{{ $namePrfixCount }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Name Prefix</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-info fs-4"></i>
                                </div>
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">Total</span>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">{{ $intitutionCount }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Institution</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-info" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-danger fs-4"></i>
                                </div>
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Total</span>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">{{ $designationCount }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Designation</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-danger" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="icon-base ti tabler-truck text-warning fs-4"></i>
                                </div>
                                <span
                                    class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Total</span>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">{{ $departmentCount }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Department</p>
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: 85%"></div>
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
