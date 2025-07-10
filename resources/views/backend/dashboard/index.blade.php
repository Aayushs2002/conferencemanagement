@extends('backend.layouts.main')
@section('content')
    @if (current_user()->type == 3)
        <style>
            .society-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                overflow: hidden;
                position: relative;
            }

            .society-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
                background-size: 200% 100%;
                animation: gradientShift 3s ease infinite;
            }

            @keyframes gradientShift {

                0%,
                100% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }
            }

            .society-card:hover {
                transform: translateY(-10px) scale(1.02);
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            }

            .card-header-custom {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 25px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }

            .card-header-custom::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
                animation: shimmer 4s ease-in-out infinite;
            }

            @keyframes shimmer {

                0%,
                100% {
                    transform: rotate(0deg);
                }

                50% {
                    transform: rotate(180deg);
                }
            }

            .card-title-custom {
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                position: relative;
                z-index: 1;
            }

            .society-list {
                max-height: 300px;
                overflow-y: auto;
                scrollbar-width: thin;
                scrollbar-color: #667eea rgba(0, 0, 0, 0.1);
            }

            .society-list::-webkit-scrollbar {
                width: 6px;
            }

            .society-list::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.05);
            }

            .society-list::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #667eea, #764ba2);
                border-radius: 10px;
            }

            .society-item {
                border: none !important;
                padding: 15px 25px;
                margin: 5px 15px;
                border-radius: 12px;
                transition: all 0.3s ease;
                background: rgba(102, 126, 234, 0.05);
                position: relative;
                overflow: hidden;
            }

            .society-item::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
                transition: left 0.5s ease;
            }

            .society-item:hover::before {
                left: 100%;
            }

            .society-item:hover {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                transform: translateX(5px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            }

            .society-link {
                text-decoration: none;
                color: inherit;
                display: block;
                font-weight: 600;
                position: relative;
                z-index: 1;
            }

            .join-button {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                color: white;
                padding: 15px 30px;
                border-radius: 50px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            }

            .join-button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s ease;
            }

            .join-button:hover::before {
                left: 100%;
            }

            .join-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
                color: white;
            }

            .join-button i {
                transition: transform 0.3s ease;
            }

            .join-button:hover i {
                transform: rotate(360deg);
            }

            .card-footer-custom {
                padding: 25px;
                text-align: center;
                background: rgba(255, 255, 255, 0.5);
                backdrop-filter: blur(5px);
            }

            .empty-state {
                text-align: center;
                padding: 40px 20px;
                color: #666;
            }

            .empty-state i {
                font-size: 3rem;
                color: #667eea;
                margin-bottom: 15px;
            }

            .badge-count {
                background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
                color: white;
                border-radius: 20px;
                padding: 5px 12px;
                font-size: 0.8rem;
                font-weight: 600;
                margin-left: 10px;
            }
        </style>

        <div class="col-md-6 col-lg-4">
            <div class="card society-card">
                <div class="card-header-custom">
                    <h5 class="card-title-custom text-white">
                        <i class="fas fa-users"></i>
                        Joined Societies
                        @if (count($joinedSocities) > 0)
                            <span class="badge-count">{{ count($joinedSocities) }}</span>
                        @endif
                    </h5>
                </div>

                <div class="society-list">
                    @forelse ($joinedSocities as $society)
                        <div class="society-item">
                            <a href="{{ route('my-society.conference', $society) }}" class="society-link">
                          
                                {{ $society->abbreviation }}
                            </a>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-users-slash"></i>
                            <p>No societies joined yet</p>
                        </div>
                    @endforelse
                </div>

                <div class="card-footer-custom">
                    <a href="javascript:void(0)" class="join-button joinSociety" data-bs-toggle="modal"
                        data-bs-target="#JoinSociety">
                        <i class="fas fa-plus"></i>
                        Join New Society
                    </a>
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
