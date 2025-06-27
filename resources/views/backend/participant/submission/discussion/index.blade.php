     @extends('backend.layouts.conference.main')

     @section('title')
         Submission Discussion
     @endsection
     @section('content')
         @include('backend.layouts.conference-navigation')
         <div class="card mb-6">

             <div class="card-datatable table-responsive pt-0">
                 <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                     <div
                         class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                         <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Discussion for topic
                             {{ $submission->title }}
                         </h5>
                     </div>

                 </div>
                 <table class="datatables-basic table">
                     <thead>
                         <tr>
                             <th>#</th>
                             <th>Remarks</th>
                             <th>Scientific Commitee Comment</th>
                         </tr>
                     </thead>
                     <tbody>
                         @foreach ($discussions as $discussion)
                             <tr>
                                 <th scope="row">{{ $loop->iteration }}</th>
                                 <td>{{ $discussion->remarks }}</td>
                                 <td>
                                     @if ($discussion->expert_visible && $discussion->submission->expert_id == current_user()->id)
                                         {{ $discussion->committee_remarks }}
                                     @elseif ($discussion->presenter_visible && $discussion->submission->user_id == current_user()->id)
                                         {{ $discussion->committee_remarks }}
                                     @else
                                         -
                                     @endif
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
