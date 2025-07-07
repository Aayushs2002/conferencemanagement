   @extends('backend.layouts.conference.main')
   @section('title')
       Conference Registration
   @endsection
   @section('content')
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

           #paymentGuide {
               animation: blink 1s infinite;
           }
       </style>
   @endsection
   @include('backend.layouts.conference-navigation')
   @if (!old() && !isset($conference_registration))
       <div class="modal fade" id="openModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
           abindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
           <div class="modal-dialog modal-md">
               <div class="modal-content" id="modalContent">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title" id="exampleModalCenterTitle">Do you also want to present your
                               submission
                               or just attend conference?</h5>
                       </div>
                       <div class="modal-body">
                           <form action="" id="chooseRegistratantType">
                               <div class="col-md-12 form-group mb-3">
                                   <label for="registrantType">Registrant Type <code>*</code></label>
                                   <select name="registrant_type" class="form-control mt-2" id="registrantType">
                                       <option value="" hidden>-- Select Registrant Type --</option>
                                       <option value="1">Attendee</option>
                                       <option value="2">Speaker</option>
                                   </select>
                                   <div class="text-end mt-4">
                                       <button type="submit" id="chooseRegistrantButton"
                                           class="btn btn-primary mt-3">Submit</button>
                                       <a href="{{ route('conference.openConferencePortal', [$society, $conference]) }}"
                                           class="btn btn-danger mt-3">Cancel</a>
                                   </div>
                               </div>
                           </form>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   @endif

   <div class="main-content card container py-4">
       <div class="breadcrumb">
           <h3>{{ isset($conference_registration) ? 'Edit Conference Registration' : 'Register Conference' }}
               ({{ !empty($conference) ? $conference->conference_theme : 'No any conference added yet.' }})
           </h3>
       </div>
       <div class="separator-breadcrumb border-top mb-4"></div>
       <h5 class="text-danger text-center" id="paymentGuide">Please calculate price first for payment.</h5>
       <div class="col-md-12">
           <div>
               <h4>Modes of Payment:</h4>
               <div class="row">

                   @if (
                       (current_user()->userDetail->country_id == 125 || current_user()->userDetail->country->country_name == 'India') &&
                           $national_payemnt_setting?->profile_id)
                       <div class="col-md-3">
                           <div class="card mb-4 position-relative border-2">
                               <label for="fonePayRadio">
                                   <h4 class="text-center mt-2" style="color: blue">Online QR Scan</h4>
                                   <div class="text-center">
                                       <img src="{{ asset('default-image/fonepay.png') }}" style="width: 50%;"
                                           alt="fonepay logo">
                                       @if (current_user()->userDetail->country->country_name == 'India')
                                           <p>Cross Border Support</p>
                                       @endif
                                   </div>
                                   <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                       <input class="form-check-input" type="radio" name="paymentMode" value="fonePay"
                                           id="fonePayRadio" style="transform: scale(2);">
                                   </div>
                               </label>
                           </div>
                       </div>
                   @endif
                   @if (current_user()->userDetail->country_id != 125)
                       <div class="col-md-3">
                           <div class="card mb-4 position-relative">
                               <label for="dollarCardRadio">
                                   <h4 class="text-center mt-2" style="color: blue">Dollar Card</h4>
                                   <p style="padding-left: 10%;">We Accept</p>
                                   <div class="text-center">
                                       <img src="{{ asset('default-image/dollar-card.png') }}" style="width: 70%;"
                                           alt="dollar card logo">
                                   </div>
                                   <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                       <input class="form-check-input" type="radio" name="paymentMode"
                                           value="dollarCard" id="dollarCardRadio" style="transform: scale(2);">
                                   </div>
                               </label>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="card mb-4 position-relative">
                               <label for="bankTranserRadio">
                                   <h4 class="text-center mt-2" style="color: blue">Bank Transfer</h4>
                                   <p style="padding-left: 10%;">We Accept</p>
                                   <div class="text-center pb-2">
                                       <img src="{{ asset('default-image/bankTransfer.jpg') }}" style="width: 70%;"
                                           alt="dollar card logo">
                                   </div>
                                   <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                       <input class="form-check-input" type="radio" name="paymentMode"
                                           value="bankTransfer" id="bankTranserRadio" style="transform: scale(2);">
                                   </div>
                               </label>
                           </div>
                       </div>
                   @endif

                   @if (current_user()->userDetail->country_id == 125)
                       @if ($national_payemnt_setting?->moco_merchant_id)
                           <div class="col-md-3">
                               <div class="card mb-4 pb-4 position-relative border-2">
                                   <label for="mocoRadio">
                                       <h4 class="text-center mt-2" style="color: blue">Moco Payment</h4>
                                       <div class="text-center">
                                           <img src="{{ asset('default-image/moco.png') }}" style="width: 50%;"
                                               alt="moco logo">
                                       </div>
                                       <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                           <input class="form-check-input" type="radio" name="paymentMode"
                                               value="moco" id="mocoRadio" style="transform: scale(2);">
                                       </div>
                                   </label>
                               </div>
                           </div>
                       @endif
                       @if ($national_payemnt_setting?->esewa_secret_key)
                           <div class="col-md-3">
                               <div class="card mb-4 pb-3 position-relative border-2">
                                   <label for="esewaRadio">
                                       <h4 class="text-center mt-2" style="color: blue">Esewa Payment</h4>
                                       <div class="text-center">
                                           <img src="{{ asset('default-image/Esewa_logo.webp.png') }}"
                                               style="width: 50%;" alt="moco logo">
                                       </div>
                                       <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                           <input class="form-check-input" type="radio" name="paymentMode"
                                               value="esewa" id="esewaRadio" style="transform: scale(2);">
                                       </div>
                                   </label>
                               </div>
                           </div>
                       @endif
                       @if ($national_payemnt_setting?->khalti_live_secret_key)
                           <div class="col-md-3">
                               <div class="card mb-4 pb-0.5 position-relative border-2">
                                   <label for="khaltiRadio">
                                       <h4 class="text-center mt-2" style="color: blue">Khalti Payment</h4>
                                       <div class="text-center">
                                           <img src="{{ asset('default-image/khalti-logo.png') }}" style="width: 50%;"
                                               alt="khalti logo">
                                       </div>
                                       <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                           <input class="form-check-input" type="radio" name="paymentMode"
                                               value="khalti" id="khaltiRadio" style="transform: scale(2);">
                                       </div>
                                   </label>
                               </div>
                           </div>
                       @endif
                   @endif
               </div>
           </div>
           <div class="row" id="processingDiv" {{ !old() ? 'hidden' : '' }}>
               <div class="col-md-4 mt-4">
                   <div class="card mb-4 border-2">
                       <div class="card-body">
                           <div class="fonePayProcessingDiv">
                               @if (current_user()->userDetail->country_id == 125 || current_user()->userDetail->country->country_name == 'India')
                                   <h3><code>(For Registration Via Online QR Scan)</code><br>Payment Through QR Scan
                                   </h3>
                                   <img src="{{ asset('default-image/fonepay.png') }}" height="40">
                               @endif
                           </div>
                           <div class="dollarCardProcessingDiv">
                               @if (current_user()->userDetail->country_id != 125)
                                   <h2><code>(For Registration Via Card Payment)</code><br>Payment Through Card</h2>
                                   <p>We Accept<br>
                                       <img src="https://www.setopati.com/themes/setopati/images/card.png"
                                           height="40"><br>
                                   </p>
                               @endif
                           </div>
                           <div class="mocoProcessingDiv">
                               @if (current_user()->userDetail->country_id == 125)
                                   <h3><code>(For Registration Via Moco)</code><br>Payment Through Moco</h3>
                                   <img src="{{ asset('default-image/moco.png') }}" height="40"><br>
                                   </p>
                               @endif
                           </div>

                           <div class="esewaProcessingDiv">
                               @if (current_user()->userDetail->country_id == 125)
                                   <h3><code>(For Registration Via esewa)</code><br>Payment Through Esewa</h3>
                                   <img src="{{ asset('default-image/Esewa_logo.webp.png') }}" height="40"><br>
                                   </p>
                               @endif
                           </div>
                           <div class="khaltiProcessingDiv">
                               @if (current_user()->userDetail->country_id == 125)
                                   <h3><code>(For Registration Via khalti)</code><br>Payment Through Khalti</h3>
                                   <img src="{{ asset('default-image/khalti-logo.png') }}" height="40"><br>
                               @endif
                           </div>
                           <div class="bankTransferProcessingDiv">
                               @if (current_user()->userDetail->country_id != 125)
                                   <h5><code>(For Registration Via Bank Transer)</code><br>Payment Through Bank Transfer
                                   </h5>
                                   <img src="{{ asset('default-image/bankTransfer.jpg') }}" height="40"><br>
                                   <p>
                                       {!! $international_payemnt_setting?->bank_detail !!}
                                   </p>
                               @endif
                           </div>
                       </div>
                   </div>
               </div>
               <div class="col-md-7">
                   <div class="card mb-4">
                       <div class="card-body">
                           <form {{-- action="{{ isset($conference_registration) ? route('conference-registration.update', $conference_registration->id) : route('conference-registration.store') }}" --}} method="POST" id="registrationForm"
                               enctype="multipart/form-data">
                               @csrf
                               @isset($conference_registration)
                                   @method('patch')
                               @endisset
                               <div class="row mb-4">
                                   <div class="col-md-5 form-group mb-3">
                                       <label for="total_attendee">Accompany Person <code>(Excluding
                                               You)</code></label>
                                       <select name="total_attendee" id="total_attendee"
                                           class="form-control @error('total_attendee') is-invalid @enderror">
                                           @if (!isset($conference_registration))
                                               <option value="">-- Select Number Of Guests --</option>
                                               <option value="1" @selected(old('total_attendee') == 1)>1</option>
                                               <option value="2" @selected(old('total_attendee') == 2)>2</option>
                                               <option value="3" @selected(old('total_attendee') == 3)>3</option>
                                               <option value="4" @selected(old('total_attendee') == 4)>4</option>
                                               <option value="5" @selected(old('total_attendee') == 5)>5</option>
                                           @else
                                               @if ($conference_registration->total_attendee == 1)
                                                   <option value="">-- Select Number Of Guests --</option>
                                                   <option value="1">1</option>
                                                   <option value="2">2</option>
                                                   <option value="3">3</option>
                                                   <option value="4">4</option>
                                                   <option value="5">5</option>
                                               @else
                                                   <option value="">-- Select Number Of Guests --</option>
                                                   <option value="1" @selected($conference_registration->total_attendee - 1 == 1)>1</option>
                                                   <option value="2" @selected($conference_registration->total_attendee - 1 == 2)>2</option>
                                                   <option value="3" @selected($conference_registration->total_attendee - 1 == 3)>3</option>
                                                   <option value="4" @selected($conference_registration->total_attendee - 1 == 4)>4</option>
                                                   <option value="5" @selected($conference_registration->total_attendee - 1 == 5)>5</option>
                                               @endif
                                           @endif
                                       </select>
                                       @error('total_attendee')
                                           <p class="text-danger">{{ $message }}</p>
                                       @enderror
                                   </div>
                                   <div class="col-md-3 form-group mt-4">
                                       <button id="calculatePrice" class="btn btn-primary"
                                           {{ empty($conference) ? 'disabled' : '' }}>Calculate Price</button>
                                   </div>
                               </div>
                               <div class="row">
                                   <div class="col-md-12">
                                       <div id="priceTable" hidden>
                                           <h4>Calculated Price: </h4>
                                           <table class="table table-bordered">
                                               <thead>
                                                   <tr>
                                                       <th>#</th>
                                                       <th>Type</th>
                                                       <th>No. of Persons</th>
                                                       <th>Total</th>
                                                   </tr>
                                               </thead>
                                               <tbody id="calculatedData">
                                               </tbody>
                                           </table>
                                       </div>
                                       <hr style="border-top: 3px solid red;">
                                   </div>

                               </div>
                           </form>
                           @if (current_user()->userDetail->country_id == 125 || current_user()->userDetail->country->country_name == 'India')
                               <div class="fonePayProcessingDiv">

                                   <form
                                       action="{{ route('my-society.conference.fonepay', [$society, $conference]) }}"
                                       method="POST" enctype="multipart/form-data" id="fonePayForm">
                                       @csrf
                                       <div class="row">
                                           <input type="hidden" name="registrant_type" id="registrant_type_fonepay">
                                           <input type="hidden" name="accompany_person" id="accompany_person">
                                           <input type="hidden" name="payment_type" value="1">
                                           <div class="col-md-12 form-group mb-3" hidden>
                                               <label for="amount">Amount
                                                   <code>* (Click on "Calculate Price" to get amount
                                                       value)</code></label>
                                               <input type="text"
                                                   class="form-control @error('amount') is-invalid @enderror amount"
                                                   name="amount" id="fonePayAmount"
                                                   value="{{ isset($conference_registration) ? $conference_registration->amount : old('amount') }}"
                                                   placeholder="Enter amount" readonly />
                                               @error('amount')
                                                   <p class="text-danger">{{ $message }}</p>
                                               @enderror
                                           </div>
                                           <div class="col-md-12 text-end">
                                               <button type="submit" id="submitFonePay"
                                                   class="btn btn-primary {{ current_user()->userDetail->country->country_name == 'India' ? 'mb-1' : '' }}"
                                                   disabled>Pay Now Via QR Scan</button>
                                           </div>
                                       </div>
                                   </form>
                               </div>
                           @endif
                           @if (current_user()->userDetail->country_id == 125)
                               <div class="esewaProcessingDiv">

                                   <form action="{{ route('my-society.conference.esewa', [$society, $conference]) }}"
                                       method="POST" enctype="multipart/form-data" id="esewaForm">
                                       @csrf
                                       <div class="row">
                                           <input type="hidden" name="registrant_type" id="registrant_type_esewa">
                                           <input type="hidden" name="registrant_type" id="registrant_type_esewa">
                                           <input type="hidden" name="accompany_person" id="accompany_person">
                                           <input type="hidden" name="payment_type" value="3">
                                           <div class="col-md-12 form-group mb-3" hidden>
                                               <label for="amount">Amount
                                                   <code>* (Click on "Calculate Price" to get amount
                                                       value)</code></label>
                                               <input type="text"
                                                   class="form-control @error('amount') is-invalid @enderror amount"
                                                   name="amount" id="esewaAmount"
                                                   value="{{ isset($conference_registration) ? $conference_registration->amount : old('amount') }}"
                                                   placeholder="Enter amount" readonly />
                                               @error('amount')
                                                   <p class="text-danger">{{ $message }}</p>
                                               @enderror
                                           </div>
                                           <div class="col-md-12 text-end">
                                               <button type="submit" id="submitEsewa" class="btn btn-primary"
                                                   disabled>Pay Now Via Esewa</button>
                                           </div>
                                       </div>
                                   </form>
                               </div>
                           @endif
                           @if (current_user()->userDetail->country_id == 125)
                               <div class="khaltiProcessingDiv">

                                   <form action="{{ route('my-society.conference.khalti', [$society, $conference]) }}"
                                       method="POST" enctype="multipart/form-data" id="khaltiForm">
                                       @csrf
                                       <div class="row">
                                           <input type="hidden" name="registrant_type" id="registrant_type_khalti">
                                           <input type="hidden" name="accompany_person" id="accompany_person">
                                           <input type="hidden" name="payment_type" value="4">
                                           <div class="col-md-12 form-group mb-3" hidden>
                                               <label for="amount">Amount
                                                   <code>* (Click on "Calculate Price" to get amount
                                                       value)</code></label>
                                               <input type="text"
                                                   class="form-control @error('amount') is-invalid @enderror amount"
                                                   name="amount" id="khaltiAmount"
                                                   value="{{ isset($conference_registration) ? $conference_registration->amount : old('amount') }}"
                                                   placeholder="Enter amount" readonly />
                                               @error('amount')
                                                   <p class="text-danger">{{ $message }}</p>
                                               @enderror
                                           </div>
                                           <div class="col-md-12 text-end">
                                               <button type="submit" id="submitKhalti" class="btn btn-primary"
                                                   disabled>Pay Now Via Khalti</button>
                                           </div>
                                       </div>
                                   </form>
                               </div>
                           @endif
                           <div class="mocoProcessingDiv">
                               @if (current_user()->userDetail->country_id == 125)
                                   <div id="mocoFormSection">
                                       <form method="POST" enctype="multipart/form-data" id="mocoForm">
                                           @csrf
                                           <div class="row">
                                               <input type="hidden" name="registrant_type"
                                                   id="registrant_type_moco">
                                               <input type="hidden" name="accompany_person"
                                                   id="accompany_person_moco">
                                               <input type="hidden" name="payment_type" value="2">
                                               <div class="col-md-12 form-group mb-3" hidden>
                                                   <label for="amount">Amount
                                                       <code>* (Click on "Calculate Price" to get amount value)</code>
                                                   </label>
                                                   <input type="text"
                                                       class="form-control @error('amount') is-invalid @enderror amount"
                                                       name="amount" id="mocoAmount"
                                                       value="{{ isset($conference_registration) ? $conference_registration->amount : old('amount') }}"
                                                       placeholder="Enter amount" readonly />
                                                   @error('amount')
                                                       <p class="text-danger">{{ $message }}</p>
                                                   @enderror
                                               </div>
                                               <div class="col-md-12 text-end">
                                                   <button type="submit" id="submitMoco" class="btn btn-primary"
                                                       disabled>
                                                       <span class="spinner-border spinner-border-sm d-none"
                                                           role="status"></span>
                                                       Pay Now Via Moco
                                                   </button>
                                               </div>
                                           </div>
                                       </form>
                                   </div>
                               @endif
                           </div>
                           @if (current_user()->userDetail->country_id != 125)
                               <div class="dollarCardProcessingDiv">

                                   <form
                                       action="{{ route('my-society.conference.internationalPayment', [$society, $conference]) }}"
                                       method="POST" enctype="multipart/form-data" id="internationalPaymentForm">
                                       @csrf
                                       <div class="row">
                                           <input type="hidden" name="registrant_type" id="registrant_type">
                                           <input type="hidden" name="accompany_person" id="accompany_person">
                                           <div class="col-md-12 form-group mb-3" hidden>
                                               <label for="amount">Amount
                                                   <code>* (Click on "Calculate Price" to get amount
                                                       value)</code></label>
                                               <input type="text"
                                                   class="form-control @error('amount') is-invalid @enderror amount"
                                                   name="amount" id="amount"
                                                   value="{{ isset($conference_registration) ? $conference_registration->amount : old('amount') }}"
                                                   placeholder="Enter amount" readonly />
                                               @error('amount')
                                                   <p class="text-danger">{{ $message }}</p>
                                               @enderror
                                           </div>
                                           <div class="col-md-12 text-end">
                                               <button type="submit" id="submitButtonInternationalPayment"
                                                   class="btn btn-primary" disabled>Pay Now
                                                   {{ current_user()->userDetail->country->country_name == 'India' ? 'Via Dollar Card' : '' }}</button>
                                           </div>
                                       </div>
                                   </form>
                               </div>
                               <div class="bankTransferProcessingDiv">

                                   <form action="{{ route('my-society.conference.store', [$society, $conference]) }}"
                                       method="POST" enctype="multipart/form-data" id="bankTranferForm">
                                       @csrf
                                       <div class="row">
                                           <div class="col-md-6 form-group mb-3">
                                               <label for="transaction_id">Transaction ID/Bill No/Reference Code
                                                   <code>*</code></label>
                                               <input type="text"
                                                   class="form-control @error('transaction_id') is-invalid @enderror"
                                                   name="transaction_id" id="transaction_id"
                                                   value="{{ old('transaction_id') }}"
                                                   placeholder="Enter transaction id or bill number" required />
                                               @error('transaction_id')
                                                   <p class="text-danger">{{ $message }}</p>
                                               @enderror
                                           </div>
                                           <div class="col-md-6 form-group mb-3">
                                               <label for="payment_voucher">Payment Voucher <code>(Only JPG/PNG/PDF)
                                                   </code></label>
                                               <input type="file"
                                                   class="form-control @error('payment_voucher') is-invalid @enderror"
                                                   name="payment_voucher" id="image2" />
                                               @error('payment_voucher')
                                                   <p class="text-danger">{{ $message }}</p>
                                               @enderror
                                               <div class="row" id="imgPreview2"></div>
                                           </div>
                                           <input type="hidden" name="registrant_type"
                                               id="registrant_type_bank_transfer">
                                           <input type="hidden" name="accompany_person" id="accompany_person">
                                           <input type="hidden" name="payment_type" value="6">

                                           <div class="col-md-12 form-group mb-3" hidden>
                                               <label for="amount">Amount
                                                   <code>* (Click on "Calculate Price" to get amount
                                                       value)</code></label>
                                               <input type="text"
                                                   class="form-control @error('amount') is-invalid @enderror amount"
                                                   name="amount" id="amount"
                                                   value="{{ isset($conference_registration) ? $conference_registration->amount : old('amount') }}"
                                                   placeholder="Enter amount" readonly />
                                               @error('amount')
                                                   <p class="text-danger">{{ $message }}</p>
                                               @enderror
                                           </div>
                                           <div class="col-md-12 text-end">
                                               <button type="submit" id="submitButtonBankTransfer"
                                                   class="btn btn-primary" disabled>Submit
                                               </button>
                                           </div>
                                       </div>
                                   </form>
                               </div>
                           @endif
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>

   <!-- MoCo QR Code Modal -->
   <div class="modal fade" id="mocoQrModal" tabindex="-1" aria-labelledby="mocoQrModalLabel" aria-hidden="true"
       data-bs-backdrop="static" data-bs-keyboard="false">
       <div class="modal-dialog modal-dialog-centered modal-md">
           <div class="modal-content">
               <div class="modal-header ">
                   <h5 class="modal-title" id="mocoQrModalLabel">Scan QR Code to Pay</h5>
                   {{-- <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                       aria-label="Close"></button> --}}
               </div>
               <div class="modal-body text-center">
                   <div id="mocoQrCode" class="mb-3"></div>
                   <div id="mocoPaymentDetails" class="alert alert-info text-start">
                       <p><strong>Reference Number:</strong> <span id="mocoRefNumber"></span></p>
                       <p><strong>Amount:</strong> Rs. <span id="mocoPayAmount"></span></p>
                       <p><strong>Status:</strong> <span id="mocoPayStatus" class="badge bg-warning">Pending</span>
                       </p>
                   </div>
                   <div class="alert alert-warning text-start">
                       <small>
                           <i class="fas fa-exclamation-triangle"></i>
                           Please scan the QR code using your mobile banking app.
                           Do not close this window until the payment is completed.
                       </small>
                   </div>
                   <div class="d-flex justify-content-center gap-2">
                       <button type="button" class="btn btn-secondary" id="mocoCheckStatus">
                           <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                           Check Payment Status
                       </button>
                       <button type="button" class="btn btn-danger" id="mocoCancelPayment"
                           data-bs-dismiss="modal">Cancel Payment</button>
                   </div>
               </div>
           </div>
       </div>
   </div>
@endsection
@section('scripts')
   @if ($checkPayment == 'failed')
       <script>
           $(document).ready(function() {
               notyf.error("Your payment has been failed.");
           });
       </script>
   @endif

   @if ($checkPayment == 'cancelled')
       <script>
           $(document).ready(function() {
               notyf.error("Your payment has been cancelled.");
           });
       </script>
   @endif

   @if ($checkPayment == 'terminated')
       <script>
           $(document).ready(function() {
               notyf.error("Your payment has been terminated.");
           });
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

   @if (old('person_name'))
       <script>
           var personsValue = @json(old('person_name', []));
           var errorMessages = @json($errors->get('person_name.*'));
       </script>
   @elseif(isset($conference_registration) && $conference_registration->accompanyPersons->where('status', 1))
       @php
           $accompanyingPersons = $conference_registration->accompanyPersons
               ->where('status', 1)
               ->pluck('person_name')
               ->toArray();
       @endphp
       <script>
           var personsValue = @json($accompanyingPersons);
           var errorMessages = @json([]);
       </script>
   @else
       <script>
           var personsValue = @json([]);
           var errorMessages = @json([]);
       </script>
   @endif
   <script>
       $(document).ready(function() {
           $("#openModal").modal('show');
           $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });
           $("#total_attendee").on("keydown", function(event) {
               // Allow backspace, delete, tab, escape, and enter keys
               if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode ==
                   27 || event.keyCode == 13 ||
                   // Allow Ctrl+A
                   (event.keyCode == 65 && event.ctrlKey === true) ||
                   // Allow home, end, left, right
                   (event.keyCode >= 35 && event.keyCode <= 39) ||
                   // Allow numbers from the main keyboard (0-9) and the numpad (96-105)
                   (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <=
                       105)) {
                   return;
               } else {
                   event.preventDefault();
               }
           }); 
           var totalPrice = 0;
           $("#calculatePrice").click(function(e) { 
               var selectedPaymentMode = $('input[name="paymentMode"]:checked').val();
               e.preventDefault();
               var registrationPrice = '{{ $amount }}';
               var checkCountry = '{{ auth()->user()->userDetail->country->country_name }}';
               var guestPrice = '{{ @$memberTypePrice->guest_amount }}';
               var additionalGuest = $("#total_attendee").val();
               var calculatedData = $("#calculatedData");
               var delegate = '{{ @$memberTypePrice->memberType->delegate }}';
               var currencyCondition = (delegate == 1 ? 'Rs. ' : '$ ');
               if (delegate == 2) {
                   var preTotalPrice = registrationPrice;
               } else {
                   var totalPrice = registrationPrice;
               }
               var memberType = '{{ @$memberTypePrice->memberType->type }}';
               if (registrationPrice == '') {
                   notyf.error("Price has not been updated by admin.");
               } else {
                   $("#priceTable").attr('hidden', false);
                   calculatedData.empty();
                   calculatedData.append('<tr>' +
                       '<td>1</td>' +
                       '<td>' + memberType + '</td>' +
                       '<td>1</td>' +
                       '<td>' + currencyCondition + registrationPrice + '</td>' +
                       '</tr>');
                   if (additionalGuest > 0) {
                       var guestsTotalPrice = additionalGuest * guestPrice;
                       if (delegate == 2) {
                           preTotalPrice = parseInt(registrationPrice) + parseInt(guestsTotalPrice);
                       } else {
                           totalPrice = parseInt(registrationPrice) + parseInt(guestsTotalPrice);
                       }
                       if (delegate == 2) {
                           var additionalCharge = preTotalPrice * 0.035;
                           totalPrice = parseInt(preTotalPrice) + additionalCharge;
                       }
                       if (delegate == 2) {
                           calculatedData.append('<tr>' +
                               '<td>2</td>' +
                               '<td>Additional Guests</td>' +
                               '<td>' + additionalGuest + '</td>' +
                               '<td>' + currencyCondition + guestsTotalPrice + '</td>' +
                               '</tr>' +
                               '<tr>' +
                               '<td>3</td>' +
                               '<td>Service Charge</td>' +
                               '<td></td>' +
                               '<td>' + currencyCondition + additionalCharge.toFixed(2) + '</td>' +
                               '</tr>');
                       } else {
                           calculatedData.append('<tr>' +
                               '<td>2</td>' +
                               '<td>Additional Guests</td>' +
                               '<td>' + additionalGuest + '</td>' +
                               '<td>' + currencyCondition + guestsTotalPrice + '</td>' +
                               '</tr>' +
                               '<tr>');
                       }
                       var totalAttendee = parseInt(additionalGuest) + 1;
                   } else {
                       var totalAttendee = 1;
                       if (delegate == 2 && selectedPaymentMode != 'fonePay') {
                           var additionalCharge = preTotalPrice * 0.035;
                           totalPrice = parseInt(preTotalPrice) + additionalCharge;
                           calculatedData.append('<tr>' +
                               '<td>2</td>' +
                               '<td>Service Charge</td>' +
                               '<td></td>' +
                               '<td>' + currencyCondition + additionalCharge.toFixed(2) + '</td>' +
                               '</tr>');
                       }
                       if (delegate == 2 && checkCountry == 'India' && selectedPaymentMode == 'fonePay') {
                           $("#submitFonePay").attr('disabled', false);

                           var indianFonePayUrl =
                               '{{ route('convertUsdToInr') }}';
                           var currencyData = {
                               'usd': preTotalPrice,
                               'paymentMode': selectedPaymentMode
                           };
                           $.post(indianFonePayUrl, currencyData, function(response) {
                               console.log(response);
                               $("#fonePayAmount").val(response.amount);
                               if (response.type == 'success') {
                                   inrAmount = response.amount;
                                   calculatedData.append('<tr>' +
                                       '<td></td>' +
                                       '<td>Total</td>' +
                                       '<td>' + totalAttendee + '</td>' +
                                       '<td> INR ' + inrAmount + '</td>' +
                                       '</tr>');
                                   $("#submitFonePay").attr('disabled', false);
                               } else {
                                   notyf.error(response.message);
                                   calculatedData.append('<tr>' +
                                       '<td></td>' +
                                       '<td>Total</td>' +
                                       '<td>' + totalAttendee + '</td>' +
                                       '<td> INR ' + totalPrice + '</td>' +
                                       '</tr>');
                               }
                           });
                       }
                   }
                   if (delegate == 2) {

                       totalPrice = totalPrice.toFixed(2)
                   }
                   calculatedData.append('<tr>' +
                       '<td></td>' +
                       '<td>Total</td>' +
                       '<td>' + totalAttendee + '</td>' +
                       '<td>' + currencyCondition + totalPrice + '</td>' +
                       '</tr>');
               }
               $(".amount").val(totalPrice);
               $("#submitButtonInternationalPayment").attr('disabled', false);
               $("#submitButtonBankTransfer").attr('disabled', false);
               $("#submitFonePay").attr('disabled', false);
               $("#submitMoco").attr('disabled', false);
               $("#submitKhalti").attr('disabled', false);
               $("#submitEsewa").attr('disabled', false);
           });

           $("#registrant_type").change(function(e) {
               e.preventDefault();
               if ($(this).val() == 2) {
                   $(".speakerAdditionalSection").attr('hidden', false);
               } else {
                   $(".speakerAdditionalSection").attr('hidden', true);
                   $(".placeOfPresentationDiv").attr('hidden', true);
               }
           });
           $("#registrant_type").trigger("change");

           $("#total_attendee").change(function(e) {
               $("#accompanyPersonsDetail").empty();
               var totalAccompanyPersons = $(this).val();
               $("#accompany_person").val(totalAccompanyPersons);
               if (totalAccompanyPersons >= 1) {
                   var title =
                       '<div class="col-md-12 mt-3"><h3 class="text-danger">Accompanying Person Details:</h3><h5 class="text-danger">Note: All names are reuired</h5></div>';
                   $("#accompanyPersonsDetail").append(title);
                   for (let index = 0; index < totalAccompanyPersons; index++) {
                       var oldValue = personsValue[index] || '';
                       var errorMessage = errorMessages['person_name.' + index] ? errorMessages[
                           'person_name.' + index][0] : '';;
                       var htmlCode = '<div class="col-md-7 form-group mb-3">' +
                           '<label for="person_name">Name <code>*</code></label>' +
                           '<input type="text" class="form-control" name="person_name[]" value="' +
                           oldValue + '" placeholder="Enter accompany person name" required/>' +
                           '<p class="text-danger">' + errorMessage + '</p>' +
                           '</div>';

                       $("#accompanyPersonsDetail").append(htmlCode);
                   }
               }
           });
           $("#total_attendee").trigger("change");

           $("#chooseRegistrantButton").on('click', function(e) {
               e.preventDefault();
               var registrantValue = $("#registrantType").val();
               if (registrantValue == '') {
                   notyf.error('Select one value to continue.');
               } else if (registrantValue == 1) {
                   $("#openModal").modal('hide');
                   $('#registrant_type').val('1');
                   $('#registrant_type_fonepay').val('1');
                   $('#registrant_type_moco').val('1');
                   $('#registrant_type_esewa').val('1');
                   $('#registrant_type_khalti').val('1');
                   $('#registrant_type_bank_transfer').val('1');
               } else {
                   var data = new FormData($('#chooseRegistratantType')[0]);
                   $.ajaxSetup({
                       headers: {
                           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                       }
                   });
                   $.ajax({
                       type: "POST",
                       url: '{{ route('my-society.conference.checkSubmission', [$society, $conference]) }}',
                       data: data,
                       dataType: "json",
                       processData: false,
                       contentType: false,
                       beforeSend: function() {
                           $('#chooseRegistrantButton').attr('disabled', true);
                           $('#chooseRegistrantButton').append(
                               '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                           );
                       },
                       success: function(response) {
                           $('#chooseRegistrantButton').attr('disabled', false);
                           $('#chooseRegistrantButton').text('Submit');
                           if (response.checkSubmission == 'not-submitted') {
                               notyf.error('Submit your presentation');
                               setTimeout(function() {
                                   window.location.href =
                                       '{{ route('my-society.conference.submission.create', [$society, $conference]) }}';
                               }, 1000);
                           } else {
                               $("#openModal").modal('hide');
                               $('#registrant_type').val('2');
                               $("#registrant_type").trigger("change");
                               $('#registrant_type_bank_transfer').val('2');
                               $("#registrant_type").trigger("change");
                               $('#registrant_type_fonepay').val('2');
                               $("#registrant_type_fonepay").trigger("change");
                               $('#registrant_type_esewa').val('2');
                               $("#registrant_type_esewa").trigger("change");
                               $('#registrant_type_khalti').val('2');
                               $("#registrant_type_khalti").trigger("change");
                               $('#registrant_type_moco').val('2');
                               $("#registrant_type_moco").trigger("change");
                           }
                       }
                   });
               }
           });

           var checkCountrs = '{{ auth()->user()->userDetail->country->country_name }}';
           $('input[name="paymentMode"]').on('change', function() {
               var paymentMode = $(this).val();
               if (paymentMode == 'fonePay' && checkCountrs == 'India') {
                   $("#submitFonePay").attr('disabled', true);
               } else if (paymentMode == 'bankTransfer' && checkCountrs == 'India') {
                   $("#submitButtonBankTransfer").attr('disabled', true);
               } else if (paymentMode == 'dollarCard' && checkCountrs == 'India') {
                   $("#submitButtonInternationalPayment").attr('disabled', true);
               }
           });

           $("#submitButton").click(function(e) {
               e.preventDefault();
               $(this).attr('disabled', true);
               $("#registrationForm").submit();
           });

           $("#submitButtonInternationalPayment").click(function(e) {
               e.preventDefault();
               $(this).attr('disabled', true);
               $("#internationalPaymentForm").submit();
           });

           $("#submitButtonBankTransfer").click(function(e) {
               e.preventDefault();
               $(this).attr('disabled', true);
               $("#bankTranferForm").submit();
           });


           $('input[name="paymentMode"]').change(function() {
               var selectedValue = $(this).val();
               $("#processingDiv").attr('hidden', false);
               $(".sbiProcessingDiv").attr('hidden', true);
               $(".dollarCardProcessingDiv").attr('hidden', true);
               $(".fonePayProcessingDiv").attr('hidden', true);
               $(".mocoProcessingDiv").attr('hidden', true);
               $(".esewaProcessingDiv").attr('hidden', true);
               $(".khaltiProcessingDiv").attr('hidden', true);
               $(".bankTransferProcessingDiv").attr('hidden', true);
               if (selectedValue == "sbiBank") {
                   $(".sbiProcessingDiv").attr('hidden', false);
               } else if (selectedValue == "fonePay") {
                   $(".fonePayProcessingDiv").attr('hidden', false);
               } else if (selectedValue == "dollarCard") {
                   $(".dollarCardProcessingDiv").attr('hidden', false);
               } else if (selectedValue == "moco") {
                   $(".mocoProcessingDiv").attr('hidden', false);
               } else if (selectedValue == "esewa") {
                   $(".esewaProcessingDiv").attr('hidden', false);
               } else if (selectedValue == "khalti") {
                   $(".khaltiProcessingDiv").attr('hidden', false);
               } else if (selectedValue == "bankTransfer") {
                   $(".bankTransferProcessingDiv").attr('hidden', false);
               }
           });

           let paymentCheckInterval;
           let mocoReferenceNumber = null;

           $("#mocoForm").on('submit', function(e) {
               e.preventDefault();

               const submitButton = $("#submitMoco");
               const spinner = submitButton.find('.spinner-border');

               submitButton.prop('disabled', true);
               spinner.removeClass('d-none');

               const formData = {
                   registrant_type: $("#registrant_type_moco").val(),
                   accompany_person: $("#accompany_person_moco").val(),
                   payment_type: 2,
                   amount: $("#mocoAmount").val(),
                   _token: $('meta[name="csrf-token"]').attr('content')
               };

               $.ajax({
                   url: "{{ route('my-society.conference.moco', [$society, $conference]) }}",
                   method: 'POST',
                   data: formData,
                   dataType: 'json',
                   success: function(response) {
                       if (response.status === 'success') {
                           if (response.data.qr_data) {
                               $("#mocoQrCode").html('<img src="' + response.data.qr_data +
                                   '" alt="QR Code" class="img-fluid" style="max-width: 300px;">'
                               );
                           } else {
                               $("#mocoQrCode").html(
                                   '<div class="alert alert-danger">QR Code could not be loaded</div>'
                               );
                           }

                           $("#mocoRefNumber").text(response.data.referenceNumber);
                           $("#mocoPayAmount").text(response.data.amount);
                           mocoReferenceNumber = response.data.referenceNumber;

                           const modal = new bootstrap.Modal(document.getElementById(
                               'mocoQrModal'));
                           modal.show();

                           startPaymentStatusCheck();
                       } else {
                           notyf.error('Error generating QR code: ' + (response.message ||
                               'Unknown error'));
                       }
                   },
                   error: function(xhr) {
                       console.error('MoCo API Error:', xhr);
                       let errorMessage = 'Failed to generate QR code. Please try again.';
                       if (xhr.responseJSON && xhr.responseJSON.message) {
                           errorMessage = xhr.responseJSON.message;
                       }
                       notyf.error(errorMessage);
                   },
                   complete: function() {
                       submitButton.prop('disabled', false);
                       spinner.addClass('d-none');
                   }
               });
           });

           $("#mocoCheckStatus").on('click', function() {
               checkPaymentStatus();
           });

           $("#mocoCancelPayment").on('click', function(e) {
               e.preventDefault();

               const userConfirmed = confirm('Are you sure you want to cancel this payment?');
               if (userConfirmed === true) {
                   cancelPayment();
                   const modal = bootstrap.Modal.getInstance(document.getElementById('mocoQrModal'));
                   if (modal) {
                       modal.hide();
                   }
               }
           });

           function startPaymentStatusCheck() {
               paymentCheckInterval = setInterval(checkPaymentStatus, 30000);
           }

           function checkPaymentStatus() {
               if (!mocoReferenceNumber) return;

               const checkButton = $("#mocoCheckStatus");
               const spinner = checkButton.find('.spinner-border');
               spinner.removeClass('d-none');

               $.ajax({
                   url: "{{ route('my-society.conference.mocoCheckStatus', [$society, $conference]) }}",
                   method: 'POST',
                   data: {
                       reference_number: mocoReferenceNumber,
                       _token: $('meta[name="csrf-token"]').attr('content')
                   },
                   dataType: 'json',
                   success: function(response) {
                       console.log(response);
                       if (response.txnStatus === 'success') {
                           $("#mocoPayStatus").removeClass('bg-warning bg-danger').addClass(
                               'bg-success').text('Completed');
                           clearInterval(paymentCheckInterval);
                           notyf.success('Payment completed successfully!');

                           setTimeout(function() {
                               const baseUrl =
                                   "{{ route('my-society.conference.mocoSuccess', [$society, $conference]) }}";
                               window.location.href =
                                   `${baseUrl}?txnID=${encodeURIComponent(response.txnID)}`;
                           }, 2000);

                       } else if (response.txnStatus === 'failed') {
                           $("#mocoPayStatus").removeClass('bg-warning bg-success').addClass(
                               'bg-danger').text('Failed');
                           clearInterval(paymentCheckInterval);
                           notyf.error('Payment failed. Please try again.');
                       } else {
                           $("#mocoPayStatus").removeClass('bg-success bg-danger').addClass(
                               'bg-warning').text('Pending');
                       }
                   },
                   error: function(xhr) {
                       console.error('Status check error:', xhr);
                   },
                   complete: function() {
                       spinner.addClass('d-none');
                   }
               });
           }

           function cancelPayment() {
               clearInterval(paymentCheckInterval);
               mocoReferenceNumber = null;
           }

           $(window).on('beforeunload', function() {
               if (paymentCheckInterval) {
                   clearInterval(paymentCheckInterval);
               }
           });

           $('#bankTranferForm').on('submit', function(e) {
               e.preventDefault();

               let form = $('#bankTranferForm')[0];
               let formData = new FormData(form);

               $('#submitButtonBankTransfer').prop('disabled', true).text('Submitting...');

               $.ajax({
                   url: $(this).attr('action'),
                   type: 'POST',
                   data: formData,
                   contentType: false,
                   processData: false,
                   success: function(response) {
                       notyf.success('Conference Registered successfully!');

                       setTimeout(function() {
                           window.location.href =
                               '{{ route('my-society.conference.index', [$society, $conference]) }}';
                       }, 1500);
                   },
                   error: function(xhr) {
                       $('#submitButtonBankTransfer').prop('disabled', false).text('Submit');

                       if (xhr.status === 422) {
                           let errors = xhr.responseJSON.errors;
                           $('.text-danger').remove();
                           for (let key in errors) {
                               let input = $('[name="' + key + '"]');
                               input.addClass('is-invalid');
                               input.after('<p class="text-danger">' + errors[key][0] +
                                   '</p>');
                           }
                       } else {
                           notyf.error('An error occurred. Please try again.');
                       }
                   }
               });
           });
       });
   </script>
@endsection
