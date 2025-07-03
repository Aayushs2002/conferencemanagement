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
                           <span class="d-none d-sm-inline-block">Add New</span>
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
                                           ($submission->review_status !== 1 && $submission->review_status !== 0))
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
