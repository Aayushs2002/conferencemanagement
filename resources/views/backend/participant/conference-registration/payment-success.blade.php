   @extends('backend.layouts.conference.main')
   @section('title')
       Conference Registration
   @endsection
   @section('content')
       @include('backend.layouts.conference-navigation')
       <div class="modal fade" id="openModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
           aria-hidden="true" data-keyboard="false" data-backdrop="static">
           <div class="modal-dialog modal-md" style="margin-top: 100px">
               <div class="modal-content" id="modalContent">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h4 class="modal-title" id="exampleModalCenterTitle">Submitting your registration.....</h4>
                       </div>
                       <div class="modal-body text-center">
                           <div class=" d-flex justify-content-center">

                               <div class="sk-chase my-4" style="height: 80px; width: 80px;">
                                   <div class="sk-chase-dot"></div>
                                   <div class="sk-chase-dot"></div>
                                   <div class="sk-chase-dot"></div>
                                   <div class="sk-chase-dot"></div>
                                   <div class="sk-chase-dot"></div>
                                   <div class="sk-chase-dot"></div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
       <div class="main-content">
           <div class="breadcrumb">
               <h3>Register Conference</h3>
           </div>
           <div class="separator-breadcrumb border-top"></div>
           <div class="col-md-12">
               <div class="row">
                   <div class="col-md-12">
                       <div class="card mb-4">
                           @php
                               $onlinePayment = session()->get('onlinePayment');
                               // dd($onlinePayment);
                           @endphp
                           {{-- @dd($transactionId) --}}
                           <div class="card-body">
                               <form action="{{ route('my-society.conference.submit', [$society, $conference]) }}"
                                   method="POST" id="registrationForm" enctype="multipart/form-data">
                                   @csrf
                                   @isset($conference_registration)
                                       @method('patch')
                                   @endisset
                                   <input type="hidden" name="registrant_type"
                                       value="{{ $onlinePayment['registrant_type'] }}">
                                   <input type="hidden" name="accompany_person"
                                       value="{{ $onlinePayment['accompany_person'] }}">
                                   <input type="hidden" name="payment_type" value="{{ $onlinePayment['payment_type'] }}">
                                   <div class="row">
                                       <h2 class="col-md-12"><code>Conference Registration Form:</code></h2>
                                       <div class="col-md-4 form-group mb-3">
                                           <label for="transaction_id">Transaction ID/Bill No./Reference Code
                                               <code>*</code></label>
                                           <input type="text"
                                               class="form-control @error('transaction_id') is-invalid @enderror"
                                               name="transaction_id" id="transaction_id"
                                               value="{{ old('transaction_id') ? old('transaction_id') : $transactionId }}"
                                               placeholder="Enter transaction id" readonly />
                                           @error('transaction_id')
                                               <p class="text-danger">{{ $message }}</p>
                                           @enderror
                                       </div>
                                       <div class="col-md-4 form-group mb-3">
                                           <label for="amount">Amount
                                               <code>*
                                                   ({{ auth()->user()->userDetail->country->country_name == 'Nepal' ? 'Rs.' : '$' }})</code></label>
                                           <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                               name="amount" id="amount"
                                               value="{{ old('amount') ? old('amount') : $amount }}"
                                               placeholder="Enter transaction id" readonly />
                                           @error('amount')
                                               <p class="text-danger">{{ $message }}</p>
                                           @enderror
                                       </div>

                                       <div class="col-md-8 text-end mt-4">
                                           <button type="submit" hidden id="submitButton"
                                               class="btn btn-primary">Submit</button>

                                       </div>
                                   </div>
                               </form>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   @endsection
   @section('scripts')

       @if (!old())
           <script>
               notyf.success('Your payment is success. Now please proceed further for registration.');
           </script>
       @endif

       @if ($errors->any())
           @foreach ($errors->all() as $error)
               <script>
                   var error = '{{ $error }}';
                   notyf.error(error);
               </script>
           @endforeach
       @endif

       <script>
           $(document).ready(function() {
               $("#openModal").modal('show');
               $("#registrationForm").submit();

               $("#submitButton").click(function(e) {
                   e.preventDefault();
                   $(this).attr('disabled', true);
                   $("#registrationForm").submit();
               });
           });
       </script>
   @endsection
