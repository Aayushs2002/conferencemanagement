@extends('backend.layouts.conference.main')
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-3">Attendance Status</h5>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="card-body">
                <form method="GET" id="filterForm" action="{{ route('conference.viewAttendanceStatus', [$society, $conference]) }}">
                    <div class="row g-3 mb-4">
                        <!-- View Type Filter -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="ti tabler-eye me-1"></i>View Type
                            </label>
                            <select name="view_type" class="form-select" id="viewTypeFilter">
                                <option value="registrants" {{ request('view_type', 'registrants') == 'registrants' ? 'selected' : '' }}>
                                    📋 Registrants Only
                                </option>
                                <option value="sponsors" {{ request('view_type') == 'sponsors' ? 'selected' : '' }}>
                                    🏢 Sponsors Only
                                </option>
                                <option value="both" {{ request('view_type') == 'both' ? 'selected' : '' }}>
                                    👥 Both (Registrants & Sponsors)
                                </option>
                            </select>
                        </div>

                        <!-- Date Filter --> 
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="ti tabler-calendar me-1"></i>Filter by Date
                            </label>
                            <select name="date" class="form-select" id="dateFilter">
                                <option value="all" {{ request('date') == 'all' || !request('date') ? 'selected' : '' }}>All Days</option>
                                @foreach ($dates as $index => $date)
                                    <option value="{{ $date }}" {{ request('date') == $date ? 'selected' : '' }}>
                                        Day {{ $index + 1 }} ({{ \Carbon\Carbon::parse($date)->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Country Filter -->
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="ti tabler-map-pin me-1"></i>Country
                            </label>
                            <select name="country" class="form-select" id="countryFilter">
                                <option value="all" {{ request('country') == 'all' || !request('country') ? 'selected' : '' }}>All Countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>
                                        {{ $country->country_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kit Status Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Kit Status</label>
                            <select name="kit_status" class="form-select" id="kitStatusFilter">
                                <option value="all" {{ request('kit_status') == 'all' || !request('kit_status') ? 'selected' : '' }}>All</option>
                                <option value="taken" {{ request('kit_status') == 'taken' ? 'selected' : '' }}>Taken</option>
                                <option value="not_taken" {{ request('kit_status') == 'not_taken' ? 'selected' : '' }}>Not Taken</option>
                            </select>
                        </div>

                        <!-- Registrant Type Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Registrant Type</label>
                            <select name="registrant_type" class="form-select" id="registrantTypeFilter">
                                <option value="all" {{ request('registrant_type') == 'all' || !request('registrant_type') ? 'selected' : '' }}>All</option>
                                <option value="1" {{ request('registrant_type') == '1' ? 'selected' : '' }}>Attendee</option>
                                <option value="2" {{ request('registrant_type') == '2' ? 'selected' : '' }}>Speaker</option>
                                <option value="3" {{ request('registrant_type') == '3' ? 'selected' : '' }}>Session Chair</option>
                                <option value="4" {{ request('registrant_type') == '4' ? 'selected' : '' }}>Special Guest</option>
                                <option value="5" {{ request('registrant_type') == '5' ? 'selected' : '' }}>Organizer</option>
                            </select>
                        </div>

                        <!-- Has Accompanying Person Filter -->
                        <div class="col-md-2">
                            <label class="form-label">Accompanying</label>
                            <select name="has_accompany" class="form-select" id="hasAccompanyFilter">
                                <option value="all" {{ request('has_accompany') == 'all' || !request('has_accompany') ? 'selected' : '' }}>All</option>
                                <option value="yes" {{ request('has_accompany') == 'yes' ? 'selected' : '' }}>With Guest</option>
                                <option value="no" {{ request('has_accompany') == 'no' ? 'selected' : '' }}>Solo</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Search -->
                        <div class="col-md-6">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, or registration ID..." value="{{ request('search') }}">
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-6 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('conference.viewAttendanceStatus', [$society, $conference]) }}" class="btn btn-secondary">
                                <i class="ti tabler-refresh me-1"></i> Reset
                            </a>
                            <button type="button" class="btn btn-success" id="exportBtn">
                                <i class="ti tabler-download me-1"></i> Export to Excel
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Results Summary -->
                <div class="alert alert-info mb-3 d-flex align-items-center justify-content-between">
                    <div>
                        <i class="ti tabler-info-circle me-2"></i>
                        @if($viewType === 'both')
                            Showing <strong>{{ count($allData) }}</strong> record(s) - 
                            <span class="badge bg-primary">{{ count($registrants) }} Registrants</span>
                            <span class="badge bg-success">{{ count($sponsors) }} Sponsors</span>
                        @elseif($viewType === 'sponsors')
                            Showing <strong>{{ count($sponsors) }}</strong> sponsor(s)
                        @else
                            Showing <strong>{{ count($registrants) }}</strong> registrant(s)
                        @endif
                    </div>
                    <div>
                        @if($viewType === 'registrants')
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                <i class="ti tabler-users me-1"></i> Registrants View
                            </span>
                        @elseif($viewType === 'sponsors')
                            <span class="badge bg-success rounded-pill px-3 py-2">
                                <i class="ti tabler-building me-1"></i> Sponsors View
                            </span>
                        @else
                            <span class="badge bg-info rounded-pill px-3 py-2">
                                <i class="ti tabler-layout-grid me-1"></i> Combined View
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <table class="datatables-basic table table-striped">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50px;">#</th>
                        <th scope="col">Type</th>
                        <th scope="col">Registration ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Country</th>
                        <th scope="col">Total Attendees</th>
                        <th scope="col">Accompanying Persons</th>
                        <th scope="col">Attendance</th>
                        <th scope="col">Meal</th>
                        <th scope="col">Kit</th>
                    </tr>
                </thead>
                <tbody> 
                    @forelse ($allData as $item)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>
                                @if($item->record_type === 'registrant')
                                    <span class="badge bg-primary">
                                        <i class="ti tabler-user me-1"></i>Registrant
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="ti tabler-building me-1"></i>Sponsor
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-dark">{{ $item->registration_id ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if($item->record_type === 'registrant')
                                    @php
                                        $middleName = !empty($item->m_name) ? $item->m_name . ' ' : '';
                                        $name = $item->f_name . ' ' . $middleName . $item->l_name;
                                    @endphp
                                    <div class="fw-bold">{{ $name }}</div>
                                @else
                                    <div class="fw-bold">{{ $item->sponsor_name }}</div>
                                    @if($item->contact_person)
                                        <small class="text-muted">Contact: {{ $item->contact_person }}</small>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    <i class="ti tabler-mail text-primary me-1"></i>{{ $item->email }}
                                </div>
                                @if(isset($item->phone))
                                    <div class="small text-muted">
                                        <i class="ti tabler-phone text-success me-1"></i>{{ $item->phone }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item->country_name ?? 'N/A' }}</span>
                                @if($item->record_type === 'sponsor' && isset($item->category_name))
                                    <br><small class="badge bg-secondary mt-1">{{ $item->category_name }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $item->total_attendee }}</span>
                            </td>
                            <td>
                                @if ($item->total_attendee > 1)
                                    @if (isset($item->accompanyPersons) && count($item->accompanyPersons) > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($item->accompanyPersons as $accompanyPerson)
                                                <li>
                                                    <i class="ti tabler-user text-primary me-1"></i>
                                                    {{ $accompanyPerson->person_name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">
                                            {{ $item->record_type === 'sponsor' ? 'N/A for Sponsors' : 'Names not recorded' }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                @if (count($item->attendences) > 0)
                                    <div class="mb-2">
                                        <span class="badge bg-success">Total: {{ count($item->attendences) }}</span>
                                    </div>
                                    <ul class="list-unstyled mb-0 small">
                                        @foreach ($item->attendences as $attendance)
                                            <li>
                                                <i class="ti tabler-calendar-check text-success me-1"></i>
                                                {{ \Carbon\Carbon::parse($attendance->created_at)->format('M d, h:i A') }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="badge bg-warning">Not Marked</span>
                                @endif
                            </td>
                            <td>
                                @if (count($item->meals) > 0)
                                    <div class="mb-2">
                                        <span class="badge bg-warning">Lunch: {{ $item->total_lunch_consumed }}</span>
                                        <span class="badge bg-info">Dinner: {{ $item->total_dinner_consumed }}</span>
                                    </div>
                                    @php
                                        $mealsByDate = [];
                                        foreach ($item->meals as $meal) {
                                            $date = \Carbon\Carbon::parse($meal->created_at)->format('M d');
                                            if (!isset($mealsByDate[$date])) {
                                                $mealsByDate[$date] = ['lunch' => 0, 'dinner' => 0, 'time' => $meal->created_at];
                                            }
                                            $mealsByDate[$date]['lunch'] += $meal->lunch_taken;
                                            $mealsByDate[$date]['dinner'] += $meal->dinner_taken;
                                        }
                                    @endphp
                                    <ul class="list-unstyled mb-0 small">
                                        @foreach ($mealsByDate as $date => $mealData)
                                            <li class="mb-1">
                                                <strong>{{ $date }}:</strong><br>
                                                <span class="ms-2">
                                                    <i class="ti tabler-sun text-warning"></i> Lunch: {{ $mealData['lunch'] }}
                                                    @if ($item->total_attendee > 1 && $mealData['lunch'] < $item->total_attendee)
                                                        <small class="text-danger">({{ $item->total_attendee - $mealData['lunch'] }} pending)</small>
                                                    @endif
                                                </span><br>
                                                <span class="ms-2">
                                                    <i class="ti tabler-moon text-info"></i> Dinner: {{ $mealData['dinner'] }}
                                                    @if ($item->total_attendee > 1 && $mealData['dinner'] < $item->total_attendee)
                                                        <small class="text-danger">({{ $item->total_attendee - $mealData['dinner'] }} pending)</small>
                                                    @endif
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="badge bg-secondary">No Meals</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->record_type === 'registrant')
                                    @if (isset($item->conferenceRegistrationKit) && $item->conferenceRegistrationKit)
                                        <span class="badge bg-success">
                                            <i class="ti tabler-check me-1"></i> Taken
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="ti tabler-x me-1"></i> Not Taken
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="ti tabler-minus me-1"></i> N/A
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ti tabler-alert-circle fs-3 mb-2"></i>
                                    <p>No records found matching the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('exportBtn').addEventListener('click', function() {
            // Get current filter values
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Redirect to export route with filters
            window.location.href = '{{ route('conference.exportAttendanceStatus', [$society, $conference]) }}?' + params.toString();
        });
    </script>
@endsection
