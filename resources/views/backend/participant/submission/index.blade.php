   @extends('backend.layouts.conference.main')
   @section('title')
       Submission
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
   @if ($submissionSetting?->abstract_guidelines)
       <div class="modal fade" id="openAbstractGuidelineModal" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitleDuideline" aria-hidden="true">
           <div class="modal-dialog modal-lg modal-simple modal-pricing">
               <div class="modal-content" id="modalContent">
                   <div class="modal-body">
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       <h4 class="text-center mb-4">Abstract Submission Guidelines</h4>
                       {!! $submissionSetting->abstract_guidelines !!}
                   </div>
               </div>
           </div>
       </div>
   @endif
   {{-- @dd($submissions->where('user_id', current_user()->id)->where('request_status', 1)->where('presentation_type', 1)->isNotEmpty()) --}}
   {{-- @dd($submissions) --}}
   @if ($submissions->where('user_id', current_user()->id)->where('request_status', 1)->where('presentation_type', 2)->isNotEmpty() && $submissionSetting->oral_guidelines)
       {{-- @dd('da') --}}
       <div class="modal fade" id="openOralGuidelineModal" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitleDuideline" aria-hidden="true">
           <div class="modal-dialog modal-lg modal-simple modal-pricing">
               <div class="modal-content" id="modalContent">
                   <div class="modal-body">
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       <h4 class="text-center mb-4">Oral Presentation Guidelines</h4>
                       {!! $submissionSetting->oral_guidelines !!}
                   </div>
               </div>
           </div>
       </div>
   @endif
   @if ($submissions->where('user_id', current_user()->id)->where('request_status', 1)->where('presentation_type', 1)->isNotEmpty() && $submissionSetting->poster_guidelines)
       <div class="modal fade" id="openPosterGuidelineModal" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitleDuideline" aria-hidden="true">
           <div class="modal-dialog modal-lg modal-simple modal-pricing">
               <div class="modal-content" id="modalContent">
                   <div class="modal-body">
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       <h4 class="text-center mb-4">Poster Presentation Guidelines</h4>
                       {!! $submissionSetting->poster_guidelines !!}
                   </div>
               </div>
           </div>
       </div>
   @endif

   @if (
       $submissions->where('expert_id', current_user()->id)->where('presentation_type', 1)->isNotEmpty() &&
           $submissionSetting->poster_reviewer_guide)
       <div class="modal fade" id="openExpertPosterGuidelineModal" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitleDuideline" aria-hidden="true">
           <div class="modal-dialog modal-lg modal-simple modal-pricing">
               <div class="modal-content" id="modalContent">
                   <div class="modal-body">
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       <h4 class="text-center mb-4">Poster Reviewer Guidelines</h4>
                       {!! $submissionSetting->poster_reviewer_guide !!}
                   </div>
               </div>
           </div>
       </div>
   @endif
   @if (
       $submissions->where('expert_id', current_user()->id)->where('presentation_type', 2)->isNotEmpty() &&
           $submissionSetting->oral_reviewer_guide)
       <div class="modal fade" id="openExpertOralGuidelineModal" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitleDuideline" aria-hidden="true">
           <div class="modal-dialog modal-lg modal-simple modal-pricing">
               <div class="modal-content" id="modalContent">
                   <div class="modal-body">
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       <h4 class="text-center mb-4">Oral Reviewer Guidelines</h4>
                       {!! $submissionSetting->oral_reviewer_guide !!}
                   </div>
               </div>
           </div>
       </div>
   @endif

   {{-- Video Guidelines Section --}}
   @php
       $conferenceSettings = $conference->conferenceSetting ?? null;
       $hasVideoGuidelines = false;
       if ($conferenceSettings) {
           $hasVideoGuidelines = $conferenceSettings->submission_guideline_youtube || 
                                 $conferenceSettings->expert_guideline_youtube;
       }
   @endphp

   @if ($hasVideoGuidelines)
       <div class="card mb-4">
           <div class="card-body">
               <div class="d-flex align-items-center mb-3">
                   <i class="ti ti-video" style="font-size: 24px; margin-right: 10px; color: #7367f0;"></i>
                   <h5 class="mb-0">Video Guidelines</h5>
               </div>
               <div class="row g-3">
                   @if ($conferenceSettings->submission_guideline_youtube)
                       <div class="col-md-6">
                           <div class="guideline-video-card"  
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 20px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"
                                onclick="showSubmissionVideoModal('{{ $conferenceSettings->submission_guideline_youtube }}')">
                               <div class="d-flex align-items-center text-white">
                                   <div style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                       <i class="ti tabler-player-play" style="font-size: 30px;"></i>
                                   </div>
                                   <div>
                                       <h6 class="text-white mb-1" style="font-weight: 600;">Submission Guidelines</h6>
                                       <p class="mb-0" style="font-size: 13px; opacity: 0.9;">Watch video tutorial</p>
                                   </div>
                               </div>
                           </div>
                       </div>
                   @endif

                   @if ($submissions->where('expert_id', current_user()->id)->where('presentation_type', 1)->isNotEmpty() &&$conferenceSettings->expert_guideline_youtube)
                       <div class="col-md-6">
                           <div class="guideline-video-card" 
                                style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 20px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"
                                onclick="showExpertVideoModal('{{ $conferenceSettings->expert_guideline_youtube }}')">
                               <div class="d-flex align-items-center text-white">
                                   <div style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                       <i class="ti tabler-player-play" style="font-size: 30px;"></i>
                                   </div>
                                   <div>
                                       <h6 class="text-white mb-1" style="font-weight: 600;">Expert/Reviewer Guidelines</h6>
                                       <p class="mb-0" style="font-size: 13px; opacity: 0.9;">Watch video tutorial</p>
                                   </div>
                               </div>
                           </div>
                       </div>
                   @endif
               </div>
           </div>
       </div>

       {{-- Video Modal for Submission and Expert Guidelines --}}
       <div class="modal fade" id="videoGuideModal" tabindex="-1" aria-labelledby="videoGuideModalLabel"
           aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
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

   <div class="card mb-6">

       <div class="card-datatable table-responsive pt-0">
           <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
               <div
                   class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                   <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Presentation Submission
                   </h5>
               </div>
               <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                   <div class="dt-buttons btn-group flex-wrap mb-0">

                       <a href="{{ route('my-society.conference.submission.create', [$society, $conference]) }}"
                           class="btn btn-primary" tabindex="0">
                           <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                           <span class="d-none d-sm-inline-block">New Submission</span>
                       </a>
                   </div>
               </div>
           </div>
           <table class="datatables-basic table">
               <thead>
                   <tr>
                       <th>#</th>
                       <th>Article Type</th>
                       <th>Topic</th>
                       <th>Presentation Type</th>
                       <th>Request Status</th>
                       <th>Action</th>
                   </tr>
               </thead>
               <tbody>
                   @foreach ($submissions as $submission)
                       <tr>
                           <th scope="row">{{ $loop->iteration }}</th>
                           <td> {{ $submission->article_type == 1 ? 'Original' : 'Review' }}
                           </td>
                           <td>
                               {{ \Illuminate\Support\Str::words($submission->title, 5, '...') }}
                           </td>
                           <td>
                               @if ($submission->presentation_type == 1)
                                   Poster
                               @elseif($submission->presentation_type == 2)
                                   Oral(Abstract) 
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
                                       <a class="reviewNow btn btn-sm btn-danger text-white"
                                           data-id="{{ $submission->id }}" data-bs-toggle="modal"
                                           data-bs-target="#pricingModal">
                                           <span id="reviewNow">
                                               Review Now
                                           </span> 
                                       </a>
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
       <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
           <div class="modal-dialog modal-lg modal-simple modal-pricing">
               <div class="modal-content" id="modalContent">
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
   <script>
       let guideVideoModalInstance = null;

       function showSubmissionVideoModal(url) {
           showGuideVideo('Submission Video Guidelines', url);
       }

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
           $(document).off("click", ".viewData");
           $(document).on("click", ".viewData", function(e) {
               e.preventDefault();
               var url = '{{ route('my-society.conference.submission.view', [$society, $conference]) }}';
               var _token = '{{ csrf_token() }}';
               var id = $(this).data('id');
               $('#modalContent').html(`
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
                       $('#modalContent').html(response);
                   }, 1000);
               });
           });

           $(document).off("click", ".reviewNow");
           $(document).on("click", ".reviewNow", function(e) {
               e.preventDefault();
               var url =
                   '{{ route('my-society.conference.submission.review', [$society, $conference]) }}';
               var _token = '{{ csrf_token() }}';
               var id = $(this).data('id');  
               $('#modalContent').html(`
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
                   setTimeout(function() {
                       $('#modalContent').html(response);
                   }, 1000);
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

           $('#openAbstractGuidelineModal').modal('show');
           $('#openExpertOralGuidelineModal').modal('show');
           $('#openExpertPosterGuidelineModal').modal('show');

           var shouldShowFirstModal =
               {{ $submissions->where('user_id', current_user()->id)->where('request_status', 1)->where('presentation_type', 1)->isNotEmpty() ? 'true' : 'false' }};

           var shouldShowSecondModal =
               {{ $submissions->where('user_id', current_user()->id)->where('request_status', 1)->where('presentation_type', 2)->isNotEmpty() ? 'true' : 'false' }};

           if (shouldShowFirstModal) {
               $('#openPosterGuidelineModal').modal('show');
           }

           $('#openPosterGuidelineModal').on('hidden.bs.modal', function() {
               if (shouldShowSecondModal) {
                   $('#openOralGuidelineModal').modal('show');
               }
           });

           if (shouldShowSecondModal && !shouldShowFirstModal) {
               $('#openOralGuidelineModal').modal('show');
           }
       });
   </script>
@endsection
