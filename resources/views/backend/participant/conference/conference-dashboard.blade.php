@extends('backend.layouts.main')
@section('title')
    Conference Dashboard
@endsection
@section('content')
    @include('backend.layouts.conference-navigation')
    <div class="row">

        <div class="col-lg-3 col-sm-6">
            <div class="card ">
                <div class="card-body">
                    <p class="mb-1">Conference Registered Status</p>
                    <div class="my-4">
                        @if (checkRegistration($conference))
                            <span class="badge bg-success">Registered</span>
                        @else
                            <a href="{{ route('my-society.conference.create', $conference) }}">
                                <span class="badge bg-danger">Register Now</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card ">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning"><i
                                    class="icon-base ti tabler-alert-triangle icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $submissionCount }}</h4>
                    </div>
                    <p class="mb-1">Submission</p>

                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger"><i
                                    class="icon-base ti tabler-git-fork icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $workshopRegistrationCount }}</h4>
                    </div>
                    <p class="mb-1">WorkShop Registration</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info"><i
                                    class="icon-base ti tabler-clock icon-28px"></i></span>
                        </div>
                        <h4 class="mb-0">13</h4>
                    </div>
                    <p class="mb-1">Late vehicles</p>
                    <p class="mb-0">
                        <span class="text-heading fw-medium me-2">-2.5%</span>
                        <small class="text-body-secondary">than last week</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
