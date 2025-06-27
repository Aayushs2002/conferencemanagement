   @extends('backend.layouts.conference.main')
   @section('title')
       Workshop Registration
   @endsection
   @section('content')
       @include('backend.layouts.conference-navigation')
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

           #meal {
               animation: blink 1s infinite;
           }
       </style>
       @if (auth()->user()->userDetail->country->country_name == 'India')
           {{-- modal start --}}
           <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
               <div class="modal-dialog modal-lg modal-simple modal-pricing">
                   <div class="modal-content" id="modalContent">
                       <div class="modal-body">
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                           <div class="rounded-top">
                               <h5 class="modal-title" id="exampleModalCenterTitle">Please select your payment method.
                               </h5>
                               <hr class="py-4">
                               <div class="modal-body">
                                   <form action="" id="chooseRegistratantType" method="POST">
                                       @csrf
                                       <input type="hidden" value="" name="price" id="price">
                                       <div class="row">
                                           <div class="col-md-6">
                                               <div class="card mb-4 position-relative">
                                                   <label for="fonePayRadio">
                                                       <h4 class="text-center mt-2" style="color: blue">Online QR Scan
                                                       </h4>
                                                       <div class="text-center">
                                                           <img src="{{ asset('default-image/fonepay.png') }}"
                                                               style="width: 70%;" alt="fonepay logo">
                                                           <p>Cross Border Support</p>
                                                       </div>
                                                       <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                                           <input class="form-check-input" type="radio" name="paymentMode"
                                                               value="fonePay" id="fonePayRadio"
                                                               style="transform: scale(2);">
                                                       </div>
                                                   </label>
                                               </div>
                                           </div>
                                           <div class="col-md-6">
                                               <div class="card mb-4 position-relative">
                                                   <label for="dollarCardRadio">
                                                       <h4 class="text-center mt-2" style="color: blue">Dollar Card</h4>
                                                       <p style="padding-left: 10%;">We Accept</p>
                                                       <div class="text-center">
                                                           <img src="{{ asset('default-image/dollar-card.png') }}"
                                                               style="width: 70%;" alt="dollar card logo">
                                                       </div>
                                                       <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                                           <input class="form-check-input" type="radio" name="paymentMode"
                                                               value="dollarCard" id="dollarCardRadio"
                                                               style="transform: scale(2);">
                                                       </div>
                                                   </label>
                                               </div>
                                               <div class="" id="priceDisplay"
                                                   style="display: flex; justify-content: end;">
                                               </div>
                                           </div>

                                       </div>
                                       <div class="d-flex justify-content-end mt-3 gap-3">
                                           <button type="submit" id="submitPaymentMode"
                                               class="btn btn-primary">Pay</button>
                                           <button id="closeModel" type="button" class="btn btn-danger">Cancel</button>
                                       </div>
                                   </form>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
           {{-- modal end --}}
       @endif
       <div class="main-content">
           <div class="separator-breadcrumb border-top"></div>
           <div class="breadcrumb">
               <h1>Workshops</h1>
           </div>
           @php
               $hashedWorkshop = null;
               $hashedConference = null;
               $hashedSociety = null;
           @endphp
           <div class="row">
               @foreach ($workshops as $w_item)
                   @php
                       $hashedWorkshop = \Vinkla\Hashids\Facades\Hashids::encode($w_item->id) ?? null;
                       $hashedConference = \Vinkla\Hashids\Facades\Hashids::encode($conference->id);
                       $hashedSociety = \Vinkla\Hashids\Facades\Hashids::encode($society->id);
                   @endphp
                   <div class="col-lg-4 col-md-6 col-sm-6">
                       <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                           <div class="card-body"><i class="i-Conference"></i>
                               <div style="margin-left: 6%;">

                                   <h5 class="text-muted mt-2 mb-0">{{ $w_item->title }}
                                       ({{ $w_item->start_date <= $conference->start_date ? 'Pre-Conference Workshop' : 'Post-Conference Workshop' }})
                                   </h5>
                                   @php
                                       $totalQuota = $w_item->no_of_participants;
                                       $appliedQuota = $w_item->registrations->where('verified_status', 1)->count();
                                   @endphp
                                   <p class="text-muted mt-2 mb-0">Total Quota: {{ $totalQuota }}</p>
                                   <p class="text-muted mt-2 mb-0">Applied Quota: {{ $appliedQuota }}</p>
                                   <p class="text-muted mt-2 mb-0">Remaining Quota:
                                       {{ $totalQuota - $appliedQuota < 0 ? 0 : $totalQuota - $appliedQuota }}</p>
                                   @php
                                       $memberType = $societyUser?->pivot?->memberType;
                                       $price = DB::table('workshop_registration_prices')
                                           ->where([
                                               'workshop_id' => $w_item->id,
                                               'member_type_id' => $memberType->id,
                                           ])
                                           ->first();
                                   @endphp
                                   @if (current_user()->userDetail->country->country_name != 'India')
                                       <p class="text-muted mt-2 mb-0">Price:
                                           {{ current_user()->userDetail->country->country_name == 'Nepal' ? 'Rs.' : '$' }}
                                           {{ !empty($price->price) ? $price->price : 'Price Not Allocated' }}</p>
                                   @endif
                                   @php
                                       $checkRegistration = $w_item->registrations
                                           ->where('user_id', current_user()->id)
                                           ->where('status', 1)
                                           ->first();
                                   @endphp
                                   @if (!empty($checkRegistration))
                                       @if ($checkRegistration->verified_status == 0)
                                           <span class="badge bg-success">Registered</span>
                                       @elseif ($checkRegistration->verified_status == 1)
                                           <span class="badge bg-success">Accepted</span>
                                       @elseif ($checkRegistration->verified_status == 2)
                                           <span class="badge bg-danger">Rejected</span>
                                       @endif
                                   @endif
                                   @if (empty($checkRegistration) && $appliedQuota <= $totalQuota)
                                       @if (current_user()->userDetail->country->country_name == 'Nepal')
                                           {{-- <a
                                               href="{{ route('workshop-registration.fonePay', [$w_item->slug, !empty($price->price) ? $price->price : 0]) }}"> --}}
                                           <form
                                               action="{{ route('my-society.conference.workshop-registration.fonePay', [$society, $conference, $w_item]) }}"
                                               method="POST">
                                               @csrf
                                               <input type="hidden" name="price"
                                                   value="{{ !empty($price->price) ? $price->price : 0 }}">
                                               <button type="submit" class="btn btn-primary btn-sm mt-4"
                                                   {{ empty($price->price) ? 'disabled' : '' }}>
                                                   Register This Workshop
                                               </button>
                                           </form>
                                           {{-- </a> --}}
                                       @endif

                                       @if (current_user()->userDetail->country->country_name == 'India')
                                           <button data-workshop="{{ $hashedWorkshop }}"
                                               data-price="{{ $price->price ?? 0 }}"
                                               class="btn btn-primary btn-sm india-button"
                                               {{ empty($price->price) ? 'disabled' : '' }}>
                                               Register This Workshop
                                           </button>
                                       @endif

                                       @if (@$userDetail->memberType->delegate == 2 && current_user()->userDetail->country->country_name != 'India')
                                           <a
                                               href="{{ route('workshop-registration.internationalPayment', [$w_item->slug, !empty($price->price) ? $price->price : 0, $conference]) }}">

                                               <button class="btn btn-primary btn-sm"
                                                   {{ empty($price->price) ? 'disabled' : '' }}>
                                                   Register This Workshop
                                               </button>
                                           </a>
                                       @endif
                                   @endif
                               </div>
                           </div>
                       </div>
                   </div>
               @endforeach
           </div>
       </div>
       <div class="separator-breadcrumb border-top mb-4"></div>

       <div class="card mb-6">

           <div class="card-datatable table-responsive pt-0">
               <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                   <div
                       class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                       <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Workshop Registered
                       </h5>
                   </div>

               </div>
               <table class="datatables-basic table">
                   <thead>
                       <tr>
                           <th>#</th>
                           <th>Workshop</th>
                           <th>Transaction Id</th>
                           <th>Payment Type/Voucher</th>
                           <th>Verified Status</th>
                           <th>Meal Type</th>
                           <th>Remark</th>
                           <th>Action</th>
                       </tr>
                   </thead>
                   <tbody>
                       @foreach ($registrations as $registration)
                           <tr>
                               <th scope="row">{{ $loop->iteration }}</th>
                               <td>{{ $registration->workshop->workshop_title }}</td>
                               <td>{{ $registration->transaction_id }}</td>
                               <td>
                                   @if ($registration->payment_type == 1)
                                       Fone-Pay
                                   @elseif ($registration->payment_type == 2)
                                       Card Payment
                                   @elseif (!empty($registration->payment_voucher) && $registration->payment_type == 3)
                                       @php
                                           $extension = explode('.', $registration->payment_voucher);
                                       @endphp
                                       @if ($extension[1] == 'pdf')
                                           <a href="{{ asset('storage/workshop/registration/payment-voucher/' . $registration->payment_voucher) }}"
                                               target="_blank"><img src="{{ asset('default-images/pdf.png') }}"
                                                   height="60" alt="voucher"></a>
                                       @else
                                           <a href="{{ asset('storage/workshop/registration/payment-voucher/' . $registration->payment_voucher) }}"
                                               target="_blank"><img
                                                   src="{{ asset('storage/workshop/registration/payment-voucher/' . $registration->payment_voucher) }}"
                                                   height="60" alt="voucher"></a>
                                       @endif
                                   @endif
                               </td>
                               <td>
                                   @if ($registration->verified_status == 1)
                                       <span class="badge bg-success">Verified</span>
                                   @elseif ($registration->verified_status == 2)
                                       <span class="badge bg-danger">Rejected</span>
                                   @else
                                       <span class="badge bg-warning">Unverified</span>
                                   @endif
                               </td>
                               @if ($registration->meal_type)
                                   <td>{{ $registration->meal_type == 1 ? 'Veg' : 'Non-veg' }}</td>
                               @else
                                   <td> <span class="badge bg-danger text-white btn meal"
                                           data-id="{{ $registration->id }}" data-bs-toggle="modal"
                                           data-bs-target="#pricingModal" id="meal">Click Here For Meal
                                           Preference</span>
                                   </td>
                               @endif
                               <td>{{ !empty($registration->remarks) ? $registration->remarks : '-' }}</td>
                               <td>
                                   @if ($registration->verified_status == 0 || $registration->verified_status == 2)
                                       <form action="" method="POST">
                                           @method('delete')
                                           @csrf
                                           @if ($registration->payment_type == 1)
                                               @if ($registration->verified_status == 0)
                                                   <span class="badge bg-warning">Unverified</span>
                                               @else
                                                   <span class="badge bg-danger">Rejected</span>
                                               @endif
                                           @else
                                               <a href="" class="btn btn-sm btn-success mb-1" title="Edit Data"><i
                                                       class="nav-icon i-Pen-2"></i></a>
                                               <button title="Delete Data" class="btn btn-sm btn-danger delete mb-1"
                                                   type="submit"><i class="nav-icon i-Close-Window"></i></button>
                                           @endif
                                       </form>
                                   @else
                                       <span class="badge bg-success">Verified</span>
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
   @section('scripts')
       @if ($checkPayment == 'failed')
           <script>
               notyf.error("Your payment has been failed.");
           </script>
       @endif

       @if ($checkPayment == 'cancelled')
           <script>
               notyf.error("Your payment has been cancelled.");
           </script>
       @endif

       @if ($checkPayment == 'terminated')
           <script>
               notyf.error("Your payment has been terminated.");
           </script>
       @endif

       <script>
           $(document).ready(function() {


               $('#closeModel').click(function() {
                   $("#pricingModal").modal('hide');

               });


               var checkCountry = '{{ auth()->user()->userDetail->country->country_name }}';
               var workshopPrice;
               var workshopSlug;
               var priceText = "";
               $('.india-button').click(function() {

                   $("#pricingModal").modal('show');
                   workshopSlug = $(this).data("workshop");
                   console.log(workshopSlug)
                   workshopPrice = $(this).data("price");
                   $('#submitPaymentMode').attr('disabled', true);
               });

               $('input[name="paymentMode"]').on('change', function() {
                   var selectedPaymentMode = $(this).val();
                   $('#submitPaymentMode').attr('disabled', true);

                   var form = $('#chooseRegistratantType')
                   if (selectedPaymentMode === "fonePay") {

                       var indianFonePayUrl = '{{ route('convertUsdToInr') }}';
                       var currencyData = {
                           'usd': workshopPrice,
                           'paymentMode': selectedPaymentMode
                       };
                       $.post(indianFonePayUrl, currencyData, function(response) {
                           if (response.type == "success") {
                               $('#submitPaymentMode').attr('disabled', false);
                               priceText = `<h3>Price: INR. ${response.amount}</h3>`;
                               $("#priceDisplay").html(priceText);
                               $("#price").val(response.amount);
                               var postData = {
                                   amount: response.amount,
                               };
                               form.attr('action',
                                   '{{ route('my-society.conference.workshop-registration.fonePay', ['SOCIETY', 'CONFERENCE', 'WORKSHOP']) }}'
                                   .replace("WORKSHOP", '{{ $hashedWorkshop }}')
                                   .replace("CONFERENCE", '{{ $hashedConference }}')
                                   .replace("SOCIETY", '{{ $hashedSociety }}')
                               );
                           } else {
                               notyf.error(response.message);
                           }
                       });

                   } else if (selectedPaymentMode === "dollarCard") {
                       $('#submitPaymentMode').attr('disabled', false);
                       form.attr('action',
                           ''
                           .replace('WORKSHOP_SLUG', workshopSlug)
                           .replace('PRICE', workshopPrice)
                       );

                       priceText =
                           `<h3>Price: $ ${workshopPrice}</h3>`;
                   }

                   $("#priceDisplay").html(priceText);
               });


               $.ajaxSetup({
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   }
               });


               $(document).on("click", ".meal", function(e) {
                   e.preventDefault();
                   var url = '{{ route('my-society.conference.workshop.meal', [$society, $conference]) }}';
                   var _token = '{{ csrf_token() }}';
                   var id = $(this).data('id');
                   var data = {
                       _token: _token,
                       id: id
                   };
                   $.post(url, data, function(response) {
                       $('#modalContent').html(response);
                   });
               });

               $("#submitButton").click(function(e) {
                   e.preventDefault();
                   $(this).attr('disabled', true);
                   $("#submitFormData").submit();
               });
           });
       </script>
   @endsection
