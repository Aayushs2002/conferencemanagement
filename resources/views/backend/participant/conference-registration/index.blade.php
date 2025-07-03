   @extends('backend.layouts.conference.main')
   @section('title')
       Conference Registration
   @endsection
   @section('content')
       @include('backend.layouts.conference-navigation')
       @if (!empty($conference_registration) && empty($conference_registration->meal_type))
           <div class="modal fade" id="openModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
               aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
               <div class="modal-dialog modal-md">
                   <div class="modal-content" id="modalContent">
                       <div class="modal-content">
                           <div class="modal-header">
                               <h5 class="modal-title" id="exampleModalCenterTitle">Complete the registration Process</h5>
                           </div>
                           <div class="modal-body">
                               <form
                                   action="{{ route('my-society.conference.updateRegistration', [$society, $conference]) }}"
                                   method="post" id="chooseRegistratantType">
                                   @method('PATCH')
                                   @csrf
                                   <div class="col-md-12 form-group mb-3">
                                       <label for="meal_type">Meal Preference <code>*</code></label>
                                       <select name="meal_type" class="form-control" id="meal_type">
                                           <option value="" hidden>-- Select Veg/Non-veg --</option>
                                           <option value="1"
                                               @if (isset($conference_registration)) {{ $conference_registration->meal_type == '1' ? 'selected' : '' }} @else @selected(old('meal_type') == '1') @endif>
                                               Veg</option>
                                           <option value="2"
                                               @if (isset($conference_registration)) {{ $conference_registration->meal_type == '2' ? 'selected' : '' }} @else @selected(old('meal_type') == '2') @endif>
                                               Non-veg</option>
                                       </select>
                                       @error('meal_type')
                                           <p class="text-danger">{{ $message }}</p>
                                       @enderror
                                   </div>
                                   @if ($conference_registration->registrant_type == 2)
                                       <div class="col-md-12 form-group mb-3 speakerAdditionalSection">
                                           <label for="short_cv">Short CV Description<code>*</code></label>
                                           <textarea name="short_cv" class="form-control @error('short_cv') is-invalid @enderror" id="short_cv" cols="30"
                                               rows="10">{{ isset($conference_registration) ? $conference_registration->short_cv : old('short_cv') }}</textarea>
                                           @error('short_cv')
                                               <p class="text-danger">{{ $message }}</p>
                                           @enderror
                                       </div>
                                   @endif

                                   @if ($conference_registration->total_attendee > 1)
                                       <div class="col-12">
                                           <h5>Accompany Persons Detail
                                               <code>({{ $conference_registration->total_attendee - 1 }})</code>:
                                           </h5>
                                       </div>
                                       @for ($i = 0; $i < $conference_registration->total_attendee - 1; $i++)
                                           <div class="col-md-12 form-group mb-3">
                                               <label for="person_name">Name <code>*</code></label>
                                               <input type="text" class="form-control" name="person_name[]"
                                                   value="{{ old('person_name') ? old('person_name')[$i] : '' }}"
                                                   placeholder="Enter accompany person name" required />
                                               @error('person_name.' . $i)
                                                   <p class="text-danger">Accompany Person Name is required.</p>
                                               @enderror
                                           </div>
                                       @endfor
                                   @endif
                                   <div class="d-flex justify-content-end">
                                       <button type="submit" id="chooseRegistrantButton"
                                           class="btn btn-primary mt-3">Submit</button>
                                   </div>
                               </form>
                           </div>
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
                       <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Conference
                           Registrations
                       </h5>
                   </div>

               </div>
               <table class="datatables-basic table">
                   <thead>
                       <tr>
                           <th>#</th>
                           <th>Conference</th>
                           <th>Registrant Type</th>
                           <th>Payment</th>
                           <th>Transaction Id</th>
                           <th>Verified Status</th>
                           <th>No. of People</th>
                           <th>Action</th>
                       </tr>
                   </thead>
                   <tbody>
                       @foreach ($registrations as $registration)
                           <tr>
                               <th scope="row">{{ $loop->iteration }}</th>
                               <td>{{ $registration->conference->conference_theme }}</td>
                               <td>{{ $registration->registrant_type == 1 ? 'Attendee' : 'Speaker' }}</td>
                               <td>
                                   @if (!empty($registration->amount))
                                       {{ $registration->payment_type == 5 ? '$ ' : 'Rs.' }}{{ $registration->amount }}
                                   @elseif (!empty($registration->payment_voucher))
                                       @php
                                           $explodeFileName = explode('.', $registration->payment_voucher);
                                       @endphp
                                       @if ($explodeFileName[1] == 'pdf')
                                           <a href="{{ asset('storage/conference/registration/payment-voucher/' . $registration->payment_voucher) }}"
                                               target="_blank"><img src="{{ asset('default-images/pdf.png') }}"
                                                   alt="voucher" height="50" width="40"></a>
                                       @else
                                           <a href="{{ asset('storage/conference/registration/payment-voucher/' . $registration->payment_voucher) }}"
                                               target="_blank"><img
                                                   src="{{ asset('storage/conference/registration/payment-voucher/' . $registration->payment_voucher) }}"
                                                   alt="voucher" height="50" width="40"></a>
                                       @endif
                                   @else
                                       Registered By Admin
                                   @endif
                               </td>
                               <td>{{ $registration->transaction_id }}</td>
                               <td>
                                   @if ($registration->verified_status == 0)
                                       <span class="badge bg-warning">Unverified</span>
                                   @elseif ($registration->verified_status == 1)
                                       <span class="badge bg-success">Verified</span>
                                   @else
                                       <span class="badge bg-danger">Rejected</span>
                                   @endif
                               </td>
                               <td>{{ $registration->total_attendee }}</td>
                               <td>
                                   @if ($registration)
                                       {{-- @if ($registration->attendances->where('status', 1)->count() == 2) --}}
                                       {{-- <a href="" target="_blank" class="btn btn-primary btn-sm mb-1"><i
                                               class="nav-icon i-File"></i> Generate Certificate</a> --}}
                                   @elseif ($registration->verified_status == 1)
                                       <span class="badge bg-success">Verified</span>
                                   @else
                                       <form action="" method="POST">
                                           @method('delete')
                                           @csrf
                                           @if (!empty($registration->payment_voucher))
                                               @if ($registration->payment_voucher != 'Fone-Pay')
                                                   <a href="" class="btn btn-sm btn-success" title="Edit Data"><i
                                                           class="nav-icon i-Pen-2"></i></a>
                                               @endif
                                           @else
                                               Paid Online
                                           @endif
                                           {{-- <button title="Delete Data" class="btn btn-sm btn-danger delete" type="submit"><i class="nav-icon i-Close-Window"></i></button> --}}
                                       </form>
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
       <script>
           $(document).ready(function() {
               $("#openModal").modal('show');

           });
       </script>
   @endsection
