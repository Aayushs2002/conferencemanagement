   @extends('backend.layouts.conference.main')
   @section('title')
       Review Submission
   @endsection
   @section('content')
       @include('backend.layouts.conference-navigation')
   @section('styles')
       <style>
           @keyframes blink {
               0% {
                   opacity: 1;
               }

               50% {
                   opacity: 0;
               }

               100% {
                   opacity: 1;
               }
           }

           #reviewNow {
               animation: blink 2s infinite;

           }
       </style>
   @endsection

   {{-- @if ($submissionSetting->poster_reviewer_guide)
       <div class="modal fade" id="openExpertPosterGuidelineModal" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitleDuideline" aria-hidden="true">
           <div class="modal-dialog modal-lg modal-simple modal-pricing">
               <div class="modal-content" id="modalContent">
                   <div class="modal-body">
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       <h4 class="text-center mb-4">Poster Reviewer Guidelines</h4>
                       {!! $submissionSetting->poster_reviewer_guide !!}
                   </div>
                   <div class="text-center">
                       <button type="button" class="btn btn-primary m-3" data-bs-dismiss="modal">Ok</button>
                   </div>
               </div>
           </div>
       </div>
   @endif
   @if ($submissionSetting->oral_reviewer_guide)
       <div class="modal fade" id="openExpertOralGuidelineModal" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitleDuideline" aria-hidden="true">
           <div class="modal-dialog modal-lg modal-simple modal-pricing">
               <div class="modal-content" id="modalContent"> 
                   <div class="modal-body">
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       <h4 class="text-center mb-4">Oral Reviewer Guidelines</h4>
                       {!! $submissionSetting->oral_reviewer_guide !!}
                   </div>
                   <div class="text-center">
                       <button type="button" class="btn btn-primary m-3" data-bs-dismiss="modal">Ok</button>
                   </div>
               </div>
           </div>
       </div>
   @endif --}}

   {{-- Video Guidelines Section --}}
   @php
       $conferenceSettings = $conference->conferenceSetting ?? null;
       $hasVideoGuidelines = false;
       if ($conferenceSettings) {
           $hasVideoGuidelines = $conferenceSettings->expert_guideline_youtube;
       }
   @endphp

   @if ($hasVideoGuidelines)
       <div class="card mb-4">
           <div class="card-body">
               <div class="d-flex align-items-center mb-3">
                   <i class="ti tabler-video" style="font-size: 24px; margin-right: 10px; color: #7367f0;"></i>
                   <h5 class="mb-0">Video Guidelines</h5>
               </div>
               <div class="row g-3">
                   {{-- @if ($submissions->where('expert_id', current_user()->id)->where('presentation_type', 1)->isNotEmpty() && $conferenceSettings->expert_guideline_youtube) --}}
                   <div class="col-md-6">
                       <div class="guideline-video-card"
                           style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 20px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"
                           onclick="showExpertVideoModal('{{ $conferenceSettings->expert_guideline_youtube }}')">
                           <div class="d-flex align-items-center text-white">
                               <div
                                   style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                   <i class="ti tabler-player-play" style="font-size: 30px;"></i>
                               </div>
                               <div>
                                   <h6 class="text-white mb-1" style="font-weight: 600;">Expert/Reviewer
                                       Guidelines</h6>
                                   <p class="mb-0" style="font-size: 13px; opacity: 0.9;">Watch video tutorial
                                   </p>
                               </div>
                           </div>
                       </div>
                   </div>
                   {{-- @endif --}}
               </div>
           </div>
       </div>

       {{-- Video Modal for Submission and Expert Guidelines --}}
       <div class="modal fade" id="videoGuideModal" tabindex="-1" aria-labelledby="videoGuideModalLabel"
           aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
           <div class="modal-dialog modal-xl modal-dialog-centered">
               <div class="modal-content">
                   <div class="modal-header">
                       <h5 class="modal-title" id="videoGuideModalLabel">Video Guidelines</h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                           onclick="closeGuideVideoModal()"></button>
                   </div>
                   <div class="modal-body p-0">
                       <div class="ratio ratio-16x9">
                           <iframe id="videoGuideFrame" src="" frameborder="0"
                               allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                               allowfullscreen>
                           </iframe>
                       </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                           onclick="closeGuideVideoModal()">Close</button>
                   </div>
               </div>
           </div>
       </div>
   @endif

   {{-- Deadline Notifications --}}
   @php
       $overdueSubmissions = $submissions->filter(function($submission) {
           return $submission->review_deadline && 
                  \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($submission->review_deadline)) &&
                  $submission->expert_id == current_user()->id &&
                  ($submission->review_status == 0 || $submission->review_status == 2);
       });
       
       $dueSoonSubmissions = $submissions->filter(function($submission) {
           if (!$submission->review_deadline) return false;
           $deadline = \Carbon\Carbon::parse($submission->review_deadline);
           $hoursRemaining = \Carbon\Carbon::now()->diffInHours($deadline, false);
           return $hoursRemaining <= 24 && $hoursRemaining > 0 &&
                  $submission->expert_id == current_user()->id &&
                  ($submission->review_status == 0 || $submission->review_status == 2);
       });
   @endphp

   @if ($overdueSubmissions->isNotEmpty())
       <div class="alert alert-danger alert-dismissible fade show" role="alert">
           <h5 class="alert-heading">
               <i class="ti tabler-lock me-2"></i>
               <strong>Expired Review Deadlines!</strong>
           </h5>
           <p class="mb-2">You have <strong>{{ $overdueSubmissions->count() }}</strong> submission(s) with expired review deadlines:</p>
           <ul class="mb-2">
               @foreach ($overdueSubmissions->take(3) as $overdue)
                   <li>
                       <strong>{{ \Illuminate\Support\Str::limit($overdue->title, 50) }}</strong> - 
                       Deadline expired on {{ \Carbon\Carbon::parse($overdue->review_deadline)->format('M d, Y h:i A') }}
                       ({{ \Carbon\Carbon::parse($overdue->review_deadline)->diffForHumans() }})
                   </li>
               @endforeach
               @if ($overdueSubmissions->count() > 3)
                   <li><em>... and {{ $overdueSubmissions->count() - 3 }} more</em></li>
               @endif
           </ul>
           <div class="alert alert-dark mb-0" style="background-color: #343a40; color: white; border-color: #343a40;">
               <i class="ti tabler-info-circle me-1"></i>
               <strong>Important:</strong> Review period has ended for these submissions. Reviews can no longer be submitted after the deadline.
           </div>
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
       </div>
   @endif

   @if ($dueSoonSubmissions->isNotEmpty())
       <div class="alert alert-warning alert-dismissible fade show" role="alert">
           <h5 class="alert-heading">
               <i class="ti tabler-clock-hour-4 me-2"></i>
               <strong>Reviews Due Soon!</strong>
           </h5>
           <p class="mb-2">You have <strong>{{ $dueSoonSubmissions->count() }}</strong> submission(s) due within 24 hours:</p>
           <ul class="mb-0">
               @foreach ($dueSoonSubmissions->take(3) as $dueSoon)
                   <li>
                       <strong>{{ \Illuminate\Support\Str::limit($dueSoon->title, 50) }}</strong> - 
                       Due {{ \Carbon\Carbon::parse($dueSoon->review_deadline)->format('M d, Y h:i A') }}
                       ({{ \Carbon\Carbon::parse($dueSoon->review_deadline)->diffForHumans() }})
                   </li>
               @endforeach
               @if ($dueSoonSubmissions->count() > 3)
                   <li><em>... and {{ $dueSoonSubmissions->count() - 3 }} more</em></li>
               @endif
           </ul>
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
       </div>
   @endif

   <div class="card mb-6">

       <div class="card-datatable table-responsive pt-0">
           <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
               <div
                   class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                   <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Review Submission
                   </h5>
               </div>
               {{-- <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                   <div class="dt-buttons btn-group flex-wrap mb-0">

                       <a href="{{ route('my-society.conference.submission.create', [$society, $conference]) }}"
                           class="btn btn-primary" tabindex="0">
                           <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                           <span class="d-none d-sm-inline-block">New Submission</span>
                       </a>
                   </div>
               </div> --}}
           </div>
           <table class="datatables-basic table">
               <thead>
                   <tr>
                       <th>#</th>
                       <th>Presentation Category</th>
                       <th>Topic</th>
                       <th>Presentation Type</th>
                       <th>Review Deadline</th>
                       <th>Request Status</th>
                       @if ($submissionSetting->scoring_allowed == 1)
                           <th>Score</th>
                       @endif
                       <th>Action</th>
                   </tr>
               </thead>
               <tbody>
                   @foreach ($submissions as $submission)
                       <tr>
                           <th scope="row">{{ $loop->iteration }}</th>
                           <td> {{ $submission->articleType?->name ?? 'N/A' }}
                           </td>
                           <td>
                               {{ \Illuminate\Support\Str::words($submission->title, 5, '...') }}
                           </td>
                           <td>
                               @if ($submission->presentation_type == 1)
                                   Poster
                               @elseif($submission->presentation_type == 2)
                                   Oral(Abstract)
                                 @elseif($submission->presentation_type == 3)
                                   <span class="badge bg-label-danger"><i class="ti tabler-brand-youtube me-1"></i>Video</span>
                                 
                               @endif
                               <br>
                               @if ($submission->presentation_type_change === 0)
                                   <a href="{{ route('my-society.conference.submission.convertPresentationType', [$society, $conference, $submission->id]) }}"
                                       class="btn btn-sm btn-success mt-2 convertPresentationType"
                                       {{ $submission->user_id != current_user()->id ? 'hidden' : '' }}>Change
                                       Presentation Type</a>
                               @endif
                           </td>
                           <td>
                               @if ($submission->review_deadline)
                                   @php
                                       $deadline = \Carbon\Carbon::parse($submission->review_deadline);
                                       $now = \Carbon\Carbon::now();
                                       $isOverdue = $now->greaterThan($deadline);
                                       $hoursRemaining = $now->diffInHours($deadline, false);
                                   @endphp
                                   
                                   @if ($isOverdue)
                                       <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; padding: 8px;">
                                           <i class="ti tabler-alert-circle text-danger"></i>
                                           <strong class="text-danger">Overdue!</strong><br>
                                           <small class="text-danger">{{ $deadline->format('M d, Y h:i A') }}</small><br>
                                           <small class="text-muted">{{ $deadline->diffForHumans() }}</small>
                                       </div>
                                   @elseif ($hoursRemaining <= 24 && $hoursRemaining > 0)
                                       <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 8px;">
                                           <i class="ti tabler-clock text-warning"></i>
                                           <strong class="text-warning">Due Soon!</strong><br>
                                           <small>{{ $deadline->format('M d, Y h:i A') }}</small><br>
                                           <small class="text-muted">{{ $deadline->diffForHumans() }}</small>
                                       </div>
                                   @else
                                       <div style="background-color: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; padding: 8px;">
                                           <i class="ti tabler-calendar-event text-info"></i>
                                           <strong class="text-info">Deadline:</strong><br>
                                           <small>{{ $deadline->format('M d, Y h:i A') }}</small><br>
                                           <small class="text-muted">{{ $deadline->diffForHumans() }}</small>
                                       </div>
                                   @endif
                               @else
                                   <span class="badge bg-secondary">No deadline set</span>
                               @endif
                           </td>
                           <td>
                               @if ($submission->request_status === 0)
                                   <span class="badge fw-light bg-primary text-white">Pending</span>
                               @endif
                               @if ($submission->request_status === 1)
                                   <span class="badge fw-light bg-success text-white">Accepted</span>
                               @endif
                               @if ($submission->request_status === 2)
                                   <span class="badge fw-light bg-warning text-white">Correction</span>
                               @endif
                               @if ($submission->request_status === 3)
                                   <span class="badge fw-light bg-danger text-white">Rejected</span>
                               @endif
                           </td>
                           @if ($submissionSetting->scoring_allowed == 1)
                               <td>
                                   @if ($submission->submissionRating)
                                       @php
                                           $totalScore = 0;
                                           // Check if section ratings exist
                                           if (
                                               !empty($submission->submissionRating->section_ratings) &&
                                               is_array($submission->submissionRating->section_ratings)
                                           ) {
                                               $totalScore =
                                                   collect($submission->submissionRating->section_ratings)->sum(
                                                       'rating',
                                                   ) +
                                                   ($submission->submissionRating->title_rating ?? 0) +
                                                   ($submission->submissionRating->grammar ?? 0) +
                                                   ($submission->submissionRating->overall_rating ?? 0);
                                           }
                                           // Check if overall rating exists
                                           elseif ($submission->submissionRating->overall_rating) {
                                               $totalScore = $submission->submissionRating->overall_rating;
                                           }
                                           // Default rating calculation
                                           else {
                                               $totalScore =
                                                   ($submission->submissionRating->introduction ?? 0) +
                                                   ($submission->submissionRating->method ?? 0) +
                                                   ($submission->submissionRating->result ?? 0) +
                                                   ($submission->submissionRating->conclusion ?? 0) +
                                                   ($submission->submissionRating->grammar ?? 0);
                                           }
                                       @endphp
                                       <a class="btn viewScore" data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                           data-bs-target="#pricingModal">
                                           {{ $totalScore }}
                                       </a>
                                   @else
                                       N/A
                                   @endif
                               </td>
                           @endif
                           <td>
                               <div class="dropdown">
                                   <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                       data-bs-toggle="dropdown">
                                       <i class="icon-base ti tabler-dots-vertical"></i>
                                   </button>
                                   <div class="dropdown-menu">
                                       @if ($submission->expert_id != current_user()->id)
                                           <a class="dropdown-item"
                                               href="{{ route('my-society.conference.submission.edit', [$society, $conference, $submission]) }}"><i
                                                   class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                       @endif
                                       <a class="dropdown-item viewData" data-id="{{ $submission->id }}"
                                           data-bs-toggle="modal" data-bs-target="#pricingModal"><i
                                               class="icon-base ti tabler-eye me-1"></i>
                                           View</a>
                                   </div>

                                   <a href="{{ route('my-society.conference.submission.author.index', [$society, $conference, $submission]) }}"
                                       class="btn btn-sm btn-success"
                                       {{ $submission->expert_id == current_user()->id ? 'hidden' : '' }}>Authors</a>
                                   @if (
                                       $submission->expert_id == current_user()->id &&
                                           ($submission->review_status == 2 || $submission->review_status == 0))
                                       @php
                                           $isReviewOverdue = false;
                                           if ($submission->review_deadline) {
                                               $deadline = \Carbon\Carbon::parse($submission->review_deadline);
                                               $isReviewOverdue = \Carbon\Carbon::now()->greaterThan($deadline);
                                           }
                                       @endphp
                                       
                                       @if($isReviewOverdue)
                                           <span class="badge bg-danger" title="Review deadline has expired">
                                               <i class="ti tabler-lock me-1"></i> Deadline Expired
                                           </span>
                                           <small class="d-block text-danger mt-1">
                                               <i class="ti tabler-calendar-x"></i> Review period ended
                                           </small>
                                       @else
                                           <a class="reviewNow btn btn-sm btn-danger text-white"
                                               data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                               data-bs-target="#pricingModal">
                                               <span id="reviewNow">
                                                   Review Now
                                               </span>
                                           </a>
                                       @endif
                                   @endif
                                   @if ($submission->discussions->isNotEmpty())
                                       <span class="mt-1">
                                           <a href="{{ route('my-society.conference.submission.viewDiscussion', [$society, $conference, $submission]) }}"
                                               class="btn btn-sm btn-info">Discussion</a>
                                       </span>
                                   @endif
                               </div>

                           </td>
                       </tr>
                   @endforeach
               </tbody>

           </table>
       </div>
       <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
           <div class="modal-dialog modal-lg modal-simple modal-pricing">
               <div class="modal-content" id="modalData">
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
   <script>
       let guideVideoModalInstance = null;



       function showExpertVideoModal(url) {
           showGuideVideo('Expert/Reviewer Video Guidelines', url);
       }

       function showGuideVideo(title, url) {
           let embedUrl = convertYoutubeToEmbed(url);
           document.getElementById('videoGuideModalLabel').textContent = title;
           document.getElementById('videoGuideFrame').src = embedUrl;

           if (guideVideoModalInstance) {
               guideVideoModalInstance.dispose();
           }
           guideVideoModalInstance = new bootstrap.Modal(document.getElementById('videoGuideModal'), {
               backdrop: true,
               keyboard: true,
               focus: true
           });
           guideVideoModalInstance.show();
       }

       function closeGuideVideoModal() {
           document.getElementById('videoGuideFrame').src = '';
           if (guideVideoModalInstance) {
               guideVideoModalInstance.hide();
           }
       }

       function convertYoutubeToEmbed(url) {
           let videoId = '';
           if (url.includes('youtube.com/watch?v=')) {
               videoId = url.split('watch?v=')[1].split('&')[0];
           } else if (url.includes('youtu.be/')) {
               videoId = url.split('youtu.be/')[1].split('?')[0];
           } else if (url.includes('youtube.com/embed/')) {
               return url;
           }
           return `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
       }

       // Add hover effect to guideline cards
       document.addEventListener('DOMContentLoaded', function() {
           const cards = document.querySelectorAll('.guideline-video-card');
           cards.forEach(card => {
               card.addEventListener('mouseenter', function() {
                   this.style.transform = 'translateY(-5px)';
                   this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.2)';
               });
               card.addEventListener('mouseleave', function() {
                   this.style.transform = 'translateY(0)';
                   this.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
               });
           });

           // Clear iframe when modal is closed
           const videoModal = document.getElementById('videoGuideModal');
           if (videoModal) {
               videoModal.addEventListener('hidden.bs.modal', function() {
                   document.getElementById('videoGuideFrame').src = '';
                   if (guideVideoModalInstance) {
                       guideVideoModalInstance.dispose();
                       guideVideoModalInstance = null;
                   }
               });
           }
       });

       $(document).ready(function() {
           //    $(document).off("click", ".viewData");
           $(document).on("click", ".viewData", function(e) {
               e.preventDefault();
               var url = '{{ route('my-society.conference.submission.view', [$society, $conference]) }}';
               var _token = '{{ csrf_token() }}';
               var id = $(this).data('id');
               $('#modalData').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div> 
                    </div>
                `);
               var data = {
                   _token: _token,
                   id: id
               };
               $('#pricingModal .modal-dialog').removeClass('modal-xl');
               $('#pricingModal .modal-dialog').addClass('modal-lg');
               $.post(url, data, function(response) {
                   setTimeout(function() {
                       $('#modalData').html(response);
                   }, 1000);
               });
           });

           $(document).off("click", ".reviewNow");
           $(document).on("click", ".reviewNow", function(e) {
               // alert('ok');
               e.preventDefault();
               var url =
                   '{{ route('my-society.conference.submission.review', [$society, $conference]) }}';
               var _token = '{{ csrf_token() }}';
               var id = $(this).data('id');

               $('#modalData').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
               var data = {
                   _token: _token,
                   id: id
               };
               $('#pricingModal .modal-dialog').removeClass('modal-lg');
               $('#pricingModal .modal-dialog').addClass('modal-xl');
               $.post(url, data, function(response) {
                   // Check if deadline has expired
                   if (typeof response === 'object' && response.deadline_expired) {
                       $('#pricingModal').modal('hide');
                       notyf.error(response.message || 'Review deadline has expired');
                       setTimeout(function() {
                           window.location.reload();
                       }, 2000);
                   } else {
                       setTimeout(function() {
                           $('#modalData').html(response);
                       }, 1000);
                   }
               }).fail(function(xhr) {
                   $('#pricingModal').modal('hide');
                   if (xhr.responseJSON && xhr.responseJSON.message) {
                       notyf.error(xhr.responseJSON.message);
                   } else {
                       notyf.error('An error occurred while loading the review form');
                   }
               });
           });

           $('.convertPresentationType').click(function(e) {
               e.preventDefault();
               let href = $(this).attr('href');

               Swal.fire({
                   title: 'Are you sure to convert presentation type?',
                   icon: 'question',
                   showCancelButton: true,
                   showDenyButton: true,
                   confirmButtonText: 'Yes, Convert!',
                   denyButtonText: 'No, Reject',
                   cancelButtonText: 'Cancel'
               }).then((result) => {
                   if (result.isConfirmed) {
                       // Yes, Convert!
                       location.href = href + "?confirmation=yes";
                   } else if (result.isDenied) {
                       // No, Reject
                       location.href = href + "?confirmation=no";
                   }
               });
           });

           $('#openExpertOralGuidelineModal').modal('show');
           $('#openExpertPosterGuidelineModal').modal('show');

           $(document).off("click", ".viewScore");
           $(document).on("click", ".viewScore", function(e) {
               e.preventDefault();
               var url =
                   '{{ route('my-society.conference.submission.viewScore', [$society, $conference]) }}';
               var _token = '{{ csrf_token() }}';
               var id = $(this).data('id');
               $('#modalData').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
               var data = { 
                   _token: _token,
                   id: id
               };
               $.post(url, data, function(response) {
                   $('#openModal .modal-dialog').removeClass('custom-modal-width');
                   setTimeout(function() {
                       $('#modalData').html(response);
                   }, 1000);
               });
           });
       });
   </script>
@endsection
