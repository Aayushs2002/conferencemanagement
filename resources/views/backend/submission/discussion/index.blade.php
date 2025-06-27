@extends('backend.layouts.conference.main')

   @section('title')
       Submission
   @endsection
   @section('content')
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
                           <th scope="col">Sender</th>
                           <th scope="col">Remarks</th>
                           <th scope="col">Scientific Commitee Comment</th>
                           <th scope="col">Date/Time</th>
                       </tr>
                   </thead>
                   <tbody>
                       @foreach ($discussions as $discussion)
                           <tr>
                               <th scope="row">{{ $loop->iteration }}</th>
                               <td>
                                   {{ $discussion->sender->fullName($discussion->sender) }}
                               </td>
                               <td>{{ $discussion->remarks }}</td>

                               <td>
                                   {{ !empty($discussion->committee_remarks) ? $discussion->committee_remarks : '-' }}
                               </td>

                               <td>{{ $discussion->created_at }}
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
