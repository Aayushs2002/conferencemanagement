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
               <h3>Register Workshop</h3>
           </div>
           <div class="separator-breadcrumb border-top"></div>
           <div class="col-md-12">
               <div class="row">
                   <div class="col-md-12">
                       <div class="card mb-4">

                           <div class="card-body">
                               <form
                                   action="{{ route('my-society.conference.workshop.submitData', [$society, $conference]) }}"
                                   method="POST" id="registrationForm" enctype="multipart/form-data">
                                   @csrf
                                   <input type="hidden" name="payment_type" value="{{ $paymetType }}" />
                                   <div class="row">
                                       <h3 class="col-md-12" hidden>Registration Form:</h3>
                                       <div class="col-md-4 form-group mb-3">
                                           <label for="workshop_id">Workshop <code>*</code></label>
                                           <select name="workshop_id" class="form-control" id="workshop_id">
                                               <option value="{{ $workshop->id }}" @selected(old('workshop_id') == $workshop->id)>
                                                   {{ $workshop->workshop_title }}</option>
                                           </select>
                                           @error('workshop_id')
                                               <p class="text-danger">{{ $message }}</p>
                                           @enderror
                                       </div>
                                       <div class="col-md-4 form-group mb-3">
                                           <label for="transaction_id">Transaction ID/Bill No/Reference Code
                                               <code>*</code></label>
                                           <input type="text"
                                               class="form-control @error('transaction_id') is-invalid @enderror"
                                               name="transaction_id" id="transaction_id" value="{{ $transactionId }}"
                                               placeholder="Enter transaction id" readonly />
                                           @error('transaction_id')
                                               <p class="text-danger">{{ $message }}</p>
                                           @enderror
                                       </div>
                                       <div class="col-md-4 form-group mb-3">
                                           <label for="amount">Amount <code>*</code></label>
                                           <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                               name="amount" id="amount" value="{{ $amount }}"
                                               placeholder="Enter amount" readonly />
                                           @error('amount')
                                               <p class="text-danger">{{ $message }}</p>
                                           @enderror
                                       </div>
                                       <div class="col-md-12">
                                           <button type="submit" id="submitButton" class="btn btn-primary">Submit</button>
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
