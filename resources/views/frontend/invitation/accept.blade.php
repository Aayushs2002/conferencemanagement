{{-- @extends('frontend.main-page.layouts.main')

@section('title', 'Accept Conference Invitation')

@section('content') --}}
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Accept Conference Invitation</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <!-- SweetAlert2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Conference Invitation</h4>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h5>{{ $registration->conference->conference_name }}</h5>
                                <p class="text-muted">{{ $registration->conference->conference_theme }}</p>
                            </div>

                            <div class="alert alert-info">
                                <strong>Dear {{ $registration->user->f_name }} {{ $registration->user->l_name }},</strong>
                                <p class="mb-0 mt-2">
                                    You have been invited to participate in the
                                    {{ $registration->conference->conference_name }}
                                    as a <strong>{{ $registration->registrant_type_text }}</strong>.
                                </p>
                            </div>

                            @if ($registration->isInternationalParticipant())
                                <div class="alert alert-success">
                                    <i class="fas fa-hotel me-2"></i>
                                    <strong>Accommodation Assistance Available</strong>
                                    <p class="mb-0 mt-2">
                                        As an international participant, you will be able to submit your accommodation
                                        requirements after accepting this invitation.
                                    </p>
                                </div>
                            @endif

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6>Conference Details:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Date:</strong> {{ $registration->conference->start_date }} to
                                            {{ $registration->conference->end_date }}</li>
                                        <li><strong>Venue:</strong> {{ $registration->conference->venue }}</li>
                                        <li><strong>Registration Type:</strong> {{ $registration->registrant_type_text }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Your Details:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Name:</strong> {{ $registration->user->f_name }}
                                            {{ $registration->user->l_name }}</li>
                                        <li><strong>Email:</strong> {{ $registration->user->email }}</li>
                                        <li><strong>Country:</strong>
                                            {{ $registration->user->userDetail->country->country_name ?? 'N/A' }}</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <form id="invitationForm">
                                    @csrf
                                    <button type="button" id="acceptBtn" class="btn btn-success btn-lg me-3">
                                        <i class="fas fa-check me-2"></i>Accept Invitation
                                    </button>
                                    <button type="button" id="declineBtn" class="btn btn-outline-danger btn-lg">
                                        <i class="fas fa-times me-2"></i>Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                $('#acceptBtn').on('click', function() {
                    processInvitation('accept');
                });

                $('#declineBtn').on('click', function() {
                    Swal.fire({
                        title: 'Decline Invitation?',
                        text: 'Are you sure you want to decline this conference invitation?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, decline it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            processInvitation('decline');
                        }
                    });
                });

                function processInvitation(action) {
                    const url = `{{ url('/invitation/' . $registration->invitation_response_token) }}/${action}`;
                    const btn = action === 'accept' ? $('#acceptBtn') : $('#declineBtn');

                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: url,
                        type: 'POST',
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    window.location.reload();
                                }
                            });
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message ||
                                    'An error occurred while processing your request.'
                            });

                            btn.prop('disabled', false);
                            if (action === 'accept') {
                                btn.html('<i class="fas fa-check me-2"></i>Accept Invitation');
                            } else {
                                btn.html('<i class="fas fa-times me-2"></i>Decline');
                            }
                        }
                    });
                }
            });
        </script>
    </body>

    </html>


{{-- @endsection --}}

{{-- @section('scripts')

@endsection --}}
