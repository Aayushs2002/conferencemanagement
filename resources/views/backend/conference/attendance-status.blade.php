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

            <table class="datatables-basic table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width: 50px;">#</th>
                        <th scope="col" style="width: 100px;">Type</th>
                        <th scope="col" style="width: 120px;">Reg ID</th>
                        <th scope="col" style="width: 180px;">Name</th>
                        <th scope="col" style="width: 160px;">Contact</th>
                        <th scope="col" style="width: 120px;">Country</th>
                        <th scope="col" style="width: 80px;" class="text-center">Attendees</th>
                        <th scope="col" style="width: 140px;">Accompanying</th>
                        <th scope="col" style="width: 140px;">Attendance</th>
                        <th scope="col" style="width: 160px;">Meal</th>
                        <th scope="col" style="width: 120px;" class="text-center">Kit</th>
                    </tr>
                </thead>
                <tbody> 
                    @forelse ($allData as $item)
                        <tr>
                            <th scope="row" style="font-size: 0.85rem;">{{ $loop->iteration }}</th>
                            <td>
                                @if($item->record_type === 'registrant')
                                    <span class="badge bg-primary" style="font-size: 0.7rem;">
                                        <i class="ti tabler-user" style="font-size: 0.65rem;"></i> Registrant
                                    </span>
                                @else
                                    <span class="badge bg-success" style="font-size: 0.7rem;">
                                        <i class="ti tabler-building" style="font-size: 0.65rem;"></i> Sponsor
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-dark" style="font-size: 0.75rem;">{{ $item->registration_id ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if($item->record_type === 'registrant')
                                    @php
                                        $middleName = !empty($item->m_name) ? $item->m_name . ' ' : '';
                                        $name = $item->f_name . ' ' . $middleName . $item->l_name;
                                    @endphp
                                    <div class="fw-bold" style="font-size: 0.9rem;">{{ $name }}</div>
                                @else
                                    <div class="fw-bold" style="font-size: 0.9rem;">{{ $item->sponsor_name }}</div>
                                    @if($item->contact_person)
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            <i class="ti tabler-user-circle" style="font-size: 0.7rem;"></i> {{ $item->contact_person }}
                                        </small>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 0.8rem;">
                                    <i class="ti tabler-mail text-primary" style="font-size: 0.75rem;"></i> 
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $item->email }}">{{ $item->email }}</span>
                                </div>
                                @if(isset($item->phone) && $item->phone)
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <i class="ti tabler-phone text-success" style="font-size: 0.7rem;"></i> {{ $item->phone }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item->country_name ?? 'N/A' }}</span>
                                @if($item->record_type === 'sponsor' && isset($item->category_name))
                                    <small class="badge bg-secondary mt-1 d-block">{{ $item->category_name }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary" style="font-size: 0.85rem;">{{ $item->total_attendee }}</span>
                            </td>
                            <td>
                                @if ($item->total_attendee > 1)
                                    @if (isset($item->accompanyPersons) && count($item->accompanyPersons) > 0)
                                        <span class="badge bg-primary mb-1" style="font-size: 0.75rem;">{{ count($item->accompanyPersons) }} Guest(s)</span>
                                        <details class="small">
                                            <summary class="cursor-pointer text-primary">View Names</summary>
                                            <ul class="list-unstyled mb-0 mt-2 ps-2">
                                                @foreach ($item->accompanyPersons as $accompanyPerson)
                                                    <li class="mb-1" style="font-size: 0.8rem;">
                                                        <i class="ti tabler-user text-primary" style="font-size: 0.75rem;"></i>
                                                        {{ $accompanyPerson->person_name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </details>
                                    @else
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            {{ $item->record_type === 'sponsor' ? 'N/A for Sponsors' : 'Names not recorded' }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;">None</span>
                                @endif
                            </td>
                            <td>
                                @if (count($item->attendences) > 0)
                                    <div class="mb-1">
                                        <span class="badge bg-success">{{ count($item->attendences) }}x</span>
                                    </div>
                                    <details class="small">
                                        <summary class="cursor-pointer text-primary">View Details</summary>
                                        <ul class="list-unstyled mb-0 mt-2 ps-2">
                                            @foreach ($item->attendences as $attendance)
                                                <li class="mb-1">
                                                    <i class="ti tabler-clock text-muted" style="font-size: 0.75rem;"></i>
                                                    {{ \Carbon\Carbon::parse($attendance->created_at)->format('M d, h:i A') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                @else
                                    <span class="badge bg-warning">Not Marked</span>
                                @endif
                            </td>
                            <td>
                                @if (count($item->meals) > 0)
                                    <div class="d-flex gap-1 mb-2">
                                        <span class="badge bg-warning" style="font-size: 0.75rem;">
                                            <i class="ti tabler-sun" style="font-size: 0.7rem;"></i> {{ $item->total_lunch_consumed }}
                                        </span>
                                        <span class="badge bg-info" style="font-size: 0.75rem;">
                                            <i class="ti tabler-moon" style="font-size: 0.7rem;"></i> {{ $item->total_dinner_consumed }}
                                        </span>
                                    </div>
                                    @php
                                        $mealsByDate = [];
                                        foreach ($item->meals as $meal) {
                                            $dateKey = \Carbon\Carbon::parse($meal->created_at)->format('Y-m-d');
                                            $dateLabel = \Carbon\Carbon::parse($meal->created_at)->format('M d');
                                            $time = \Carbon\Carbon::parse($meal->created_at)->format('h:i A');
                                            
                                            if (!isset($mealsByDate[$dateKey])) {
                                                $mealsByDate[$dateKey] = [
                                                    'label' => $dateLabel,
                                                    'lunch' => 0,
                                                    'dinner' => 0,
                                                    'lunch_time' => null,
                                                    'dinner_time' => null
                                                ];
                                            }
                                            
                                            if ($meal->lunch_taken > 0) {
                                                $mealsByDate[$dateKey]['lunch'] += $meal->lunch_taken;
                                                $mealsByDate[$dateKey]['lunch_time'] = $time;
                                            }
                                            if ($meal->dinner_taken > 0) {
                                                $mealsByDate[$dateKey]['dinner'] += $meal->dinner_taken;
                                                $mealsByDate[$dateKey]['dinner_time'] = $time;
                                            }
                                        }
                                    @endphp
                                    <details class="small">
                                        <summary class="cursor-pointer text-primary">Day-wise Details</summary>
                                        <div class="mt-2">
                                            @foreach ($mealsByDate as $dateKey => $mealData)
                                                <div class="mb-2 p-2 bg-light rounded" style="font-size: 0.8rem;">
                                                    <strong class="d-block mb-1">{{ $mealData['label'] }}</strong>
                                                    @if($mealData['lunch'] > 0)
                                                        <div class="text-warning">
                                                            <i class="ti tabler-sun" style="font-size: 0.7rem;"></i> 
                                                            {{ $mealData['lunch'] }}/{{ $item->total_attendee }}
                                                            <span class="text-muted">@ {{ $mealData['lunch_time'] }}</span>
                                                            @if ($item->total_attendee > 1 && $mealData['lunch'] < $item->total_attendee)
                                                                <small class="text-danger">({{ $item->total_attendee - $mealData['lunch'] }} pending)</small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if($mealData['dinner'] > 0)
                                                        <div class="text-info">
                                                            <i class="ti tabler-moon" style="font-size: 0.7rem;"></i> 
                                                            {{ $mealData['dinner'] }}/{{ $item->total_attendee }}
                                                            <span class="text-muted">@ {{ $mealData['dinner_time'] }}</span>
                                                            @if ($item->total_attendee > 1 && $mealData['dinner'] < $item->total_attendee)
                                                                <small class="text-danger">({{ $item->total_attendee - $mealData['dinner'] }} pending)</small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @else
                                    <span class="badge bg-secondary">No Meals</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->record_type === 'registrant')
                                    @if (isset($item->conferenceRegistrationKit) && $item->conferenceRegistrationKit)
                                        <span class="badge bg-success mb-1" style="font-size: 0.75rem;">
                                            <i class="ti tabler-check" style="font-size: 0.7rem;"></i> Taken
                                        </span>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            <i class="ti tabler-clock" style="font-size: 0.65rem;"></i>
                                            {{ \Carbon\Carbon::parse($item->conferenceRegistrationKit->created_at)->format('M d, h:i A') }}
                                        </div>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 0.75rem;">
                                            <i class="ti tabler-x" style="font-size: 0.7rem;"></i> Not Taken
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary" style="font-size: 0.75rem;">
                                        <i class="ti tabler-minus" style="font-size: 0.7rem;"></i> N/A
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

    <style>
        /* Collapsible details styling */
        details {
            cursor: pointer;
        }
        
        details summary {
            cursor: pointer;
            user-select: none;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
            font-size: 0.8rem;
        }
        
        details summary:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }
        
        details[open] summary {
            margin-bottom: 8px;
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        /* Hide default marker and add custom icon */
        details summary {
            list-style: none;
            position: relative;
            padding-left: 20px;
        }
        
        details summary::-webkit-details-marker {
            display: none;
        }
        
        details summary::before {
            content: '▶';
            position: absolute;
            left: 2px;
            transition: transform 0.2s;
            font-size: 0.65rem;
            color: #0d6efd;
        }
        
        details[open] summary::before {
            transform: rotate(90deg);
        }
        
        /* Table styling for better readability */
        .table {
            font-size: 0.875rem;
        }
        
        .table td {
            vertical-align: middle;
            padding: 0.75rem 0.5rem;
        }
        
        .table thead th {
            font-weight: 600;
            font-size: 0.8rem;
            padding: 0.75rem 0.5rem;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        /* Badge sizing */
        .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            font-weight: 500;
        }
        
        /* Compact spacing */
        .small {
            font-size: 0.8rem !important;
        }
        
        /* Text truncation */
        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Meal grid styling */
        .bg-light {
            background-color: #f8f9fa !important;
        }
        
        /* Icon sizing consistency */
        .ti {
            vertical-align: middle;
        }
        
        /* Details content animation */
        details div,
        details ul {
            animation: slideDown 0.2s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection
