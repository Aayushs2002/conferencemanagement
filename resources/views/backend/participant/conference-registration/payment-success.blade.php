   @extends('backend.layouts.conference.main')
   @section('title')
       Conference Registration
   @endsection
   @section('content')
       @include('backend.layouts.conference-navigation')
       
       @php
           $onlinePaymentExists = session()->has('onlinePayment');
       @endphp
       
       @if($onlinePaymentExists)
       {{-- Modal only for online payment flow --}}
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
       @endif
       <div class="main-content">
           <div class="breadcrumb">
               <h3>Registration Successful!</h3>
           </div>
           <div class="separator-breadcrumb border-top"></div>
           <div class="col-md-12">
               <div class="row justify-content-center">
                   <div class="col-md-8">
                       <div class="card mb-4">
                           @php
                               $onlinePayment = session()->get('onlinePayment');
                           @endphp
                           
                           @if($onlinePayment)
                           {{-- Case 1: Session data exists (online payment flow) - auto-submit form --}}
                           <div class="card-body">
                               <form action="{{ route('my-society.conference.submit', [$society, $conference]) }}"
                                   method="POST" id="registrationForm" enctype="multipart/form-data">
                                   @csrf
                                   @isset($conference_registration) 
                                       @method('patch') 
                                   @endisset
                                   <input type="hidden" name="registrant_type" value="{{ $onlinePayment['registrant_type'] ?? 1 }}">
                                   <input type="hidden" name="accompany_person" value="{{ $onlinePayment['accompany_person'] ?? 0 }}">
                                   <input type="hidden" name="payment_type" value="{{ $onlinePayment['payment_type'] ?? '' }}">
                                   <div class="row">
                                       <h2 class="col-md-12"><code>Conference Registration Form:</code></h2>
                                       <div class="col-md-6 form-group mb-3">
                                           <label for="transaction_id">Transaction ID/Bill No./Reference Code
                                               <code>*</code></label>
                                           <input type="text"
                                               class="form-control @error('transaction_id') is-invalid @enderror"
                                               name="transaction_id" id="transaction_id"
                                               value="{{ old('transaction_id', $transactionId ?? '') }}"
                                               placeholder="Enter transaction id" readonly />
                                           @error('transaction_id')
                                               <p class="text-danger">{{ $message }}</p>
                                           @enderror
                                       </div>
                                       <div class="col-md-6 form-group mb-3">
                                           <label for="amount">Amount
                                               <code>*
                                                   @php
                                                       $currencyDisplay = '$';
                                                       if (isset($paymentCurrency) && $paymentCurrency === 'INR') {
                                                           $currencyDisplay = 'INR';
                                                       } elseif (auth()->user()->userDetail->country->country_name == 'Nepal') {
                                                           $currencyDisplay = 'Rs.';
                                                       }
                                                   @endphp
                                                   ({{ $currencyDisplay }})</code></label>
                                           <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                               name="amount" id="amount"
                                               value="{{ old('amount', $amount ?? '') }}"
                                               placeholder="Enter amount" readonly />
                                           @error('amount')
                                               <p class="text-danger">{{ $message }}</p>
                                           @enderror
                                       </div>

                                       <div class="col-md-12 text-end mt-4">
                                           <button type="submit" hidden id="submitButton"
                                               class="btn btn-primary">Submit</button>
                                       </div>
                                   </div>
                               </form>
                           </div>
                           @else
                           {{-- Case 2: No session data (direct bank transfer/static QR completion) - show success message --}}
                           <div class="card-body text-center py-5">
                               <div class="mb-4">
                                   <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                               </div>
                               <h2 class="text-success mb-3">Registration Submitted Successfully!</h2>
                               <p class="lead">Thank you for registering for <strong>{{ $conference->conference_name }}</strong></p>
                               
                               <div class="alert alert-info mt-4">
                                   <h5><i class="fas fa-info-circle"></i> What's Next?</h5>
                                   <p class="mb-2">Your registration is currently <strong>pending verification</strong>.</p>
                                   <p class="mb-0">You will receive a confirmation email once your payment has been verified by our team.</p>
                               </div>

                               <div class="card mt-4">
                                   <div class="card-body">
                                       <h6 class="card-title">Registration Details</h6>
                                       <table class="table table-borderless">
                                           <tr>
                                               <td class="text-end"><strong>Transaction ID:</strong></td>
                                               <td class="text-start">{{ $transactionId ?? 'N/A' }}</td>
                                           </tr>
                                           <tr>
                                               <td class="text-end"><strong>Amount:</strong></td>
                                               <td class="text-start">
                                                   @php
                                                       $displayAmount = $amount ?? 0;
                                                       $currencySymbol = '$';
                                                       $showUSDEquivalent = false;
                                                       
                                                       if (isset($paymentCurrency) && $paymentCurrency === 'INR') {
                                                           $currencySymbol = 'INR';
                                                           // Convert to INR for display
                                                           try {
                                                               $data = [
                                                                   'page' => 1,
                                                                   'per_page' => 10,
                                                                   'from' => date('Y-m-d'),
                                                                   'to' => date('Y-m-d')
                                                               ];
                                                               $currencyExchange = \Illuminate\Support\Facades\Http::get('https://www.nrb.org.np/api/forex/v1/rates/', $data);
                                                               if ($currencyExchange->successful()) {
                                                                   $USDRateSell = $currencyExchange->json()['data']['payload'][0]['rates'][1]['sell'];
                                                                   $rate = floatval($USDRateSell) / 1.6;
                                                                   $convertedAmount = $rate * floatval($amount);
                                                                   $displayAmount = ceil($convertedAmount);
                                                                   $showUSDEquivalent = true;
                                                               }
                                                           } catch (\Exception $e) {
                                                               // If conversion fails, use USD amount
                                                           }
                                                       } elseif (auth()->user()->userDetail->country->country_name == 'Nepal') {
                                                           $currencySymbol = 'Rs.';
                                                       }
                                                   @endphp
                                                   {{ $currencySymbol }} {{ number_format((float)$displayAmount, 2) }}
                                                   @if($showUSDEquivalent)
                                                       <br><small class="text-muted">(USD ${{ number_format((float)$amount, 2) }})</small>
                                                   @endif
                                               </td>
                                           </tr>
                                           <tr>
                                               <td class="text-end"><strong>Date:</strong></td>
                                               <td class="text-start">{{ now()->format('F j, Y g:i A') }}</td>
                                           </tr>
                                       </table>
                                   </div>
                               </div>

                               <div class="mt-4">
                                   <a href="{{ route('my-society.conference.index', [$society, $conference]) }}" class="btn btn-primary">
                                       <i class="fas fa-arrow-left"></i> Back to Conference
                                   </a>
                                   <a href="{{ route('home') }}" class="btn btn-secondary">
                                       <i class="fas fa-home"></i> Go to Dashboard
                                   </a>
                               </div>
                           </div>
                           @endif
                       </div>
                   </div>
               </div>
           </div>
       </div>
   @endsection
   @section('scripts')
       @php
           $onlinePaymentExists = session()->has('onlinePayment');
       @endphp

       @if ($onlinePaymentExists && !old())
           <script>
               notyf.success('Your payment is successful. Processing your registration...');
           </script>
       @elseif(!$onlinePaymentExists)
           <script>
               notyf.success('Your registration has been submitted successfully!');
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

       @if($onlinePaymentExists)
       <script>
           $(document).ready(function() {
               // Only for online payment flow - auto-submit the form
               $("#openModal").modal('show');
               $("#registrationForm").submit();

               $("#submitButton").click(function(e) {
                   e.preventDefault();
                   $(this).attr('disabled', true);
                   $("#registrationForm").submit();
               });
           });
       </script>
       @endif
   @endsection
