@extends('backend.layouts.conference.main')
@section('content')
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Atttendance Status</h5>
                </div>

            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Applicant Name</th>
                        <th scope="col">Attendance</th>
                        <th scope="col">Meal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registrants as $registrant)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            @php
                                $middleName = !empty($registrant->m_name) ? $registrant->m_name . ' ' : '';
                                $name = $registrant->f_name . ' ' . $middleName . $registrant->l_name;
                            @endphp
                            <td>{{ $name }}</td>
                            <td>
                                <ul>
                                    @foreach ($registrant->attendences as $attendance)
                                        <li>{{ Carbon\Carbon::parse($attendance->created_at)->format('d M') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                @php
                                    $totalLunch = 0;
                                    $totalDinner = 0;
                                @endphp

                                @foreach ($registrant->meals as $meal)
                                    @php
                                        $remainingLunch = $registrant->total_attendee - $meal->lunch_taken;
                                        $remainingDinner = $registrant->total_attendee - $meal->dinner_taken;

                                        $totalLunch += $meal->lunch_taken;
                                        $totalDinner += $meal->dinner_taken;
                                    @endphp

                                    <li>{{ \Carbon\Carbon::parse($meal->meal_date)->format('d M') }}</li>
                                    <ul style="margin-left: 1rem">
                                        <li>Lunch: {{ $meal->lunch_taken > 0 ? 'Taken' : 'Not Taken' }} (Remaining:
                                            {{ $remainingLunch }})</li>
                                        <li>Dinner: {{ $meal->dinner_taken > 0 ? 'Taken' : 'Not Taken' }} (Remaining:
                                            {{ $remainingDinner }})</li>
                                    </ul>
                                @endforeach
                            </td>
                            {{-- <td>
                                            Attendance: @if ($registrant->attendance_status_2 == 0) <span class="badge bg-warning">Not Taken</span> @else <span class="badge bg-success">Taken</span> @endif <br>
                                            Lunch/Dinner: @if ($registrant->remaining_dinner_2 == 0) <span class="badge bg-success">Taken</span> @else Remaining-{{$registrant->remaining_dinner_2}} @endif <br>
                                        </td> --}}
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
