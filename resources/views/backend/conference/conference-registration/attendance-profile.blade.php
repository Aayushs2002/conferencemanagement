<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Attendance Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
            overflow-x: hidden;
        }

        .main-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .profile-card {
            background: var(--primary-gradient);
            color: white;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 2rem;
            border-radius: 24px;
            margin-bottom: 2rem;
            position: relative;
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="50" height="50" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" fill-opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .profile-card>* {
            position: relative;
            z-index: 1;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 1rem;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            text-align: left;
        }

        .user-title {
            font-size: 0.9rem;
            opacity: 0.9;
            text-align: left;
            margin: 0;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-radius: 24px;
            margin-bottom: 2rem;
        }

        .sessions-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-radius: 24px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            height: 100%;
        }

        .action-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(248, 249, 250, 0.9) 100%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .action-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .action-icon.attendance {
            background: var(--primary-gradient);
            color: white;
        }

        .action-icon.kit {
            background: var(--warning-gradient);
            color: white;
        }

        .action-icon.meal {
            background: var(--success-gradient);
            color: white;
        }

        .action-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .action-status {
            font-size: 0.8rem;
            color: #6c757d;
            text-align: center;
            margin-bottom: 1rem;
        }

        .modern-btn {
            background: var(--primary-gradient);
            border: none;
            border-radius: 25px;
            padding: 0.6rem 1.5rem;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            min-width: 120px;
            text-align: center;
        }

        .modern-btn:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .modern-btn.btn-success {
            background: var(--success-gradient);
        }

        .modern-btn.btn-warning {
            background: var(--warning-gradient);
        }

        .status-badge {
            background: var(--success-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .status-badge.warning {
            background: var(--danger-gradient);
        }

        .meal-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 0.8rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fa709a;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.7rem;
        }

        .time-info {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            color: #667eea;
            font-weight: 600;
        }

        .hidden {
            display: none !important;
        }

        .disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
            }

            .profile-card {
                flex-direction: column;
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .profile-info {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .content-card,
            .sessions-section {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .action-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .action-item {
                padding: 0.8rem 0.4rem;
            }

            .user-name {
                font-size: 1rem;
                text-align: center;
            }

            .user-title {
                font-size: 0.8rem;
                text-align: center;
            }

            .avatar {
                width: 50px;
                height: 50px;
                margin-bottom: 0.5rem;
            }

            .action-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
                margin-bottom: 0.5rem;
            }

            .action-title {
                font-size: 0.85rem;
                margin-bottom: 0.3rem;
            }

            .action-status {
                font-size: 0.7rem;
                margin-bottom: 0.5rem;
            }

            .modern-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.7rem;
                min-width: 80px;
            }

            .status-badge {
                padding: 0.3rem 0.6rem;
                font-size: 0.7rem;
            }

            .stat-number {
                font-size: 1.2rem;
            }

            .stat-label {
                font-size: 0.6rem;
            }

            .meal-stats {
                margin-bottom: 0.5rem;
            }

            .time-info {
                position: static;
                margin-top: 1rem;
            }

            .nav-tabs .nav-link {
                padding: 0.5rem 0.8rem;
                font-size: 0.85rem;
            }

            .poll-question {
                font-size: 1rem;
            }

            .session-badge {
                font-size: 0.75rem;
                padding: 0.3rem 0.6rem;
            }
        }

        @media (max-width: 480px) {
            .action-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.4rem;
            }

            .action-item {
                padding: 0.6rem 0.3rem;
            }

            .action-icon {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
                margin-bottom: 0.4rem;
            }

            .action-title {
                font-size: 0.75rem;
                margin-bottom: 0.2rem;
            }

            .action-status {
                font-size: 0.65rem;
                margin-bottom: 0.4rem;
            }

            .modern-btn {
                padding: 0.3rem 0.6rem;
                font-size: 0.65rem;
                min-width: 70px;
            }

            .status-badge {
                padding: 0.25rem 0.5rem;
                font-size: 0.65rem;
            }

            .stat-number {
                font-size: 1rem;
            }

            .user-name {
                font-size: 0.9rem;
            }

            .profile-card {
                padding: 0.6rem;
            }

            .content-card,
            .sessions-section {
                padding: 0.8rem;
            }

            .nav-tabs .nav-link {
                padding: 0.4rem 0.6rem;
                font-size: 0.75rem;
            }

            .accordion-button {
                padding: 0.8rem 1rem;
                font-size: 0.85rem;
            }
        }

        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 60px;
            height: 60px;
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 40px;
            height: 40px;
            left: 80%;
            animation-delay: 2s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
                opacity: 0.3;
            }

            50% {
                transform: translateY(-15px);
                opacity: 0.1;
            }
        }

        /* Scientific Session & Poll Styles */
        .nav-tabs .nav-link {
            border: none;
            background: transparent;
            color: #667eea;
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            border-radius: 12px 12px 0 0;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-gradient);
            color: white;
        }

        .session-accordion {
            margin-top: 1.5rem;
        }

        .accordion-item {
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px !important;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .accordion-button {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            color: #333;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }

        .accordion-button:not(.collapsed) {
            background: var(--primary-gradient);
            color: white;
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(102, 126, 234, 0.3);
        }

        .session-details {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .session-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .session-badge.time {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .session-badge.category {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .session-badge.chair {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .poll-container {
            background: linear-gradient(135deg, rgba(248, 249, 250, 0.8) 0%, rgba(255, 255, 255, 0.9) 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .poll-question {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }

        .poll-answers {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .poll-answer-btn {
            background: white;
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 10px;
            padding: 1rem;
            text-align: left;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .poll-answer-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-color: #667eea;
            transform: translateX(5px);
        }

        .poll-answer-btn:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .poll-result {
            background: white;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 10px;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }

        .poll-result.selected {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .poll-result-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
            transition: width 0.6s ease;
            z-index: 0;
        }

        .poll-result-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .poll-result-text {
            font-weight: 500;
        }

        .poll-result-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #667eea;
        }

        .no-polls-message {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
            font-style: italic;
        }

        .attendance-required-message {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .category-section {
            margin-bottom: 2rem;
        }

        .category-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
    </style>
</head>

<body>
    @if (Auth::check() && (current_user()->type == 1 || current_user()->type == 2 || auth()->user()->hasConferencePermissionBlade($conference, 'View Pass Setting')))
        <div class="floating-particles">
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        <div class="main-container">
            <!-- Profile Section -->
            <div class="profile-card">
                <div class="profile-info">
                    <div class="avatar">
                        <img src="{{ $participant->user?->userDetail->image ? asset('storage/profile/image/' . $participant->user?->userDetail->image) : asset('default-image/avatar.png') }}" alt="Profile Avatar" />

                    </div>
                    <div>
                        <h2 class="user-name">{{ $participant->user?->fullName($participant->user) ?? 'Participant' }}</h2>
                        <p class="user-title">Conference Participant</p>
                    </div>
                </div>
                <div class="time-info">
                    <i class="fas fa-clock me-1"></i>
                    <span id="currentTime">14:30</span>
                </div>
            </div>

            <!-- Content Section -->
            <div class="content-card">
                <div class="action-grid">
                    <!-- Attendance Section -->
                    <div class="action-item">
                        <div class="action-icon attendance">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h5 class="action-title">Attendance</h5>
                        <p class="action-status">Mark your presence</p>

                        <div id="takeAttendanceBtn">
                            <a href="#" id="takeAttendance" data-id="" class="modern-btn">
                                Take Attendance
                            </a>
                        </div>

                        <div id="attendanceTaken" class="hidden">
                            <span class="status-badge">
                                <i class="fas fa-check"></i>
                                Completed
                            </span>
                        </div>
                    </div>

                    <!-- Conference Kit Section -->
                    <div class="action-item" id="conferenceKitSection">
                        <div class="action-icon kit">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h5 class="action-title">Conference Kit</h5>
                        <p class="action-status">Collect your materials</p>

                        <div id="takeKitBtn">
                            <a href="#" id="takeConferenceKit" data-id="" class="modern-btn btn-warning">
                                Take Kit
                            </a>
                        </div>

                        <div id="kitTaken" class="hidden">
                            <span class="status-badge">
                                <i class="fas fa-check"></i>
                                Collected
                            </span>
                        </div>
                    </div>

                    <!-- Lunch Section -->
                    <div class="action-item" id="lunchSection">
                        <div class="action-icon meal">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h5 class="action-title">Lunch</h5>
                        <div class="meal-stats">
                            <div class="stat-item">
                                <div class="stat-number" id="lunchRemaining">3</div>
                                <div class="stat-label">Remaining</div>
                            </div>
                        </div>

                        <div id="takeLunchBtn">
                            <a href="#" class="takeMeal modern-btn btn-success" data-id="">
                                Take Lunch
                            </a>
                        </div>

                        <div id="lunchCompleted" class="hidden">
                            <span class="status-badge warning">
                                <i class="fas fa-times"></i>
                                Completed
                            </span>
                        </div>
                    </div>

                    <!-- Dinner Section -->
                    <div class="action-item" id="dinnerSection">
                        <div class="action-icon meal">
                            <i class="fas fa-moon"></i>
                        </div>
                        <h5 class="action-title">Dinner</h5>
                        <div class="meal-stats">
                            <div class="stat-item">
                                <div class="stat-number" id="dinnerRemaining">2</div>
                                <div class="stat-label">Remaining</div>
                            </div>
                        </div>

                        <div id="takeDinnerBtn">
                            <a href="#" class="takeMeal modern-btn btn-success" data-id="">
                                Take Dinner
                            </a>
                        </div>

                        <div id="dinnerCompleted" class="hidden">
                            <span class="status-badge warning">
                                <i class="fas fa-times"></i>
                                Completed
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scientific Sessions & Polls Section -->
            <div class="sessions-section">
                <h3 class="mb-4" style="color: #667eea; font-weight: 700;">
                    <i class="fas fa-calendar-alt me-2"></i>Today's Scientific Sessions & Polls
                </h3>

                @if (empty($checkAttendance))
                    <div class="attendance-required-message">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Please mark your attendance first to participate in polls
                    </div>
                @endif

                @if ($halls->count() > 0)
                    <!-- Hall Tabs -->
                    <ul class="nav nav-tabs" id="hallTabs" role="tablist">
                        @foreach ($halls as $index => $hall)
                            @if ($hall->scientificSessions->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                        id="hall-{{ $hall->id }}-tab" data-bs-toggle="tab"
                                        data-bs-target="#hall-{{ $hall->id }}" type="button" role="tab">
                                        <i class="fas fa-door-open me-1"></i>{{ $hall->name }}
                                    </button>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    <!-- Hall Tab Content -->
                    <div class="tab-content" id="hallTabContent">
                        @foreach ($halls as $index => $hall)
                            @if ($hall->scientificSessions->count() > 0)
                                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                    id="hall-{{ $hall->id }}" role="tabpanel">

                                    @foreach ($hall->sessionsByCategory as $categoryId => $sessions)
                                        @php
                                            $category = $sessions->first()->category;
                                        @endphp

                                        @if ($category)
                                            <div class="category-section">
                                                <div class="category-header">
                                                    <h5 class="mb-0" style="color: #667eea; font-weight: 700;">
                                                        <i class="fas fa-tag me-2"></i>{{ $category->category_name }}
                                                        <span class="badge bg-primary ms-2">{{ $sessions->count() }}
                                                            Sessions</span>
                                                    </h5>
                                                </div>

                                                <!-- Scientific Sessions Accordion -->
                                                <div class="accordion session-accordion"
                                                    id="accordion-hall-{{ $hall->id }}-cat-{{ $categoryId }}">
                                                    @foreach ($sessions as $sessionIndex => $session)
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header"
                                                                id="heading-{{ $session->id }}">
                                                                <button
                                                                    class="accordion-button {{ $sessionIndex != 0 ? 'collapsed' : '' }}"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse-{{ $session->id }}">
                                                                    <i
                                                                        class="fas fa-presentation me-2"></i>{{ $session->topic }}
                                                                </button>
                                                            </h2>
                                                            <div id="collapse-{{ $session->id }}"
                                                                class="accordion-collapse collapse {{ $sessionIndex == 0 ? 'show' : '' }}"
                                                                data-bs-parent="#accordion-hall-{{ $hall->id }}-cat-{{ $categoryId }}">
                                                                <div class="accordion-body">
                                                                    <!-- Session Details -->
                                                                    <div class="session-details">
                                                                        <span class="session-badge time">
                                                                            <i class="fas fa-clock"></i>
                                                                            {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                                                                            -
                                                                            {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}
                                                                        </span>
                                                                        @if ($session->sessionChair)
                                                                            <span class="session-badge chair">
                                                                                <i class="fas fa-user-tie"></i>Chair:
                                                                                {{ $session->sessionChair->name }}
                                                                            </span>
                                                                        @endif
                                                                    </div>

                                                                    @if ($session->description)
                                                                        <p class="text-muted mb-3">
                                                                            {{ $session->description }}</p>
                                                                    @endif

                                                                    <!-- Polls for this session -->
                                                                    @php
                                                                        $sessionPolls = $polls->where(
                                                                            'scientific_session_id',
                                                                            $session->id,
                                                                        );
                                                                    @endphp

                                                                    @if ($sessionPolls->count() > 0)
                                                                        @foreach ($sessionPolls as $poll)
                                                                            <div class="poll-container"
                                                                                data-poll-id="{{ $poll->id }}">
                                                                                <div class="poll-question">
                                                                                    <i class="fas fa-poll me-2"
                                                                                        style="color: #667eea;"></i>{{ $poll->question_text }}
                                                                                </div>

                                                                                @if (!$poll->user_voted && !empty($checkAttendance))
                                                                                    <!-- Show voting buttons -->
                                                                                    <div class="poll-answers">
                                                                                        @foreach ($poll->answers as $answer)
                                                                                            <button
                                                                                                class="poll-answer-btn vote-btn"
                                                                                                data-poll-id="{{ $poll->id }}"
                                                                                                data-answer-id="{{ $answer->id }}">
                                                                                                {{ $answer->answer_text }}
                                                                                            </button>
                                                                                        @endforeach
                                                                                    </div>
                                                                                    @elseif($poll->user_voted)
                                                                                    <!-- Show results -->
                                                                                    <div class="poll-results">
                                                                                        @php
                                                                                            $totalVotes = $poll->votes->count();
                                                                                        @endphp
                                                                                        @foreach ($poll->answers as $answer)
                                                                                            @php
                                                                                                $voteCount = $answer->votes->count();
                                                                                                $percentage =
                                                                                                    $totalVotes > 0
                                                                                                        ? round(
                                                                                                            ($voteCount /
                                                                                                                $totalVotes) *
                                                                                                                100,
                                                                                                            1,
                                                                                                        )
                                                                                                        : 0;
                                                                                                $isUserChoice =
                                                                                                    $answer->id ==
                                                                                                    $poll->user_answer_id;
                                                                                            @endphp
                                                                                            <div
                                                                                                class="poll-result {{ $isUserChoice ? 'selected' : '' }}">
                                                                                                <div class="poll-result-bar"
                                                                                                    style="width: {{ $percentage }}%">
                                                                                                </div>
                                                                                                <div
                                                                                                    class="poll-result-content">
                                                                                                    <span
                                                                                                        class="poll-result-text">
                                                                                                        {{ $answer->answer_text }}
                                                                                                        @if ($isUserChoice)
                                                                                                            <i class="fas fa-check-circle ms-2"
                                                                                                                style="color: #667eea;"></i>
                                                                                                        @endif
                                                                                                    </span>
                                                                                                    <span
                                                                                                        class="poll-result-stats">
                                                                                                        <span>{{ $percentage }}%</span>
                                                                                                        <span>({{ $voteCount }}
                                                                                                            votes)</span>
                                                                                                    </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                @else
                                                                                    <div class="attendance-required-message"
                                                                                        style="font-size: 0.9rem; padding: 0.8rem;">
                                                                                        <i
                                                                                            class="fas fa-lock me-2"></i>Mark
                                                                                        attendance to vote
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="no-polls-message">
                                                                            <i class="fas fa-info-circle me-2"></i>No
                                                                            polls available for this session
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="no-polls-message">
                        <i class="fas fa-calendar-times me-2"></i>No scientific sessions scheduled for today
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="sessions-section">
            <h3 class="mb-4" style="color: #667eea; font-weight: 700;">
                <i class="fas fa-calendar-alt me-2"></i>Today's Scientific Sessions & Polls
            </h3>

            @if (empty($checkAttendance))
                <div class="attendance-required-message">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Please mark your attendance first to participate in polls
                </div>
            @endif

            @if ($halls->count() > 0)
                <!-- Hall Tabs -->
                <ul class="nav nav-tabs" id="hallTabs" role="tablist">
                    @foreach ($halls as $index => $hall)
                        @if ($hall->scientificSessions->count() > 0)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                    id="hall-{{ $hall->id }}-tab" data-bs-toggle="tab"
                                    data-bs-target="#hall-{{ $hall->id }}" type="button" role="tab">
                                    <i class="fas fa-door-open me-1"></i>{{ $hall->name }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <!-- Hall Tab Content -->
                <div class="tab-content" id="hallTabContent">
                    @foreach ($halls as $index => $hall)
                        @if ($hall->scientificSessions->count() > 0)
                            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                id="hall-{{ $hall->id }}" role="tabpanel">

                                @foreach ($hall->sessionsByCategory as $categoryId => $sessions)
                                    @php
                                        $category = $sessions->first()->category;
                                    @endphp

                                    @if ($category)
                                        <div class="category-section">
                                            <div class="category-header">
                                                <h5 class="mb-0" style="color: #667eea; font-weight: 700;">
                                                    <i class="fas fa-tag me-2"></i>{{ $category->category_name }}
                                                    <span class="badge bg-primary ms-2">{{ $sessions->count() }}
                                                        Sessions</span>
                                                </h5>
                                            </div>

                                            <!-- Scientific Sessions Accordion -->
                                            <div class="accordion session-accordion"
                                                id="accordion-hall-{{ $hall->id }}-cat-{{ $categoryId }}">
                                                @foreach ($sessions as $sessionIndex => $session)
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header"
                                                            id="heading-{{ $session->id }}">
                                                            <button
                                                                class="accordion-button {{ $sessionIndex != 0 ? 'collapsed' : '' }}"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapse-{{ $session->id }}">
                                                                <i
                                                                    class="fas fa-presentation me-2"></i>{{ $session->topic }}
                                                            </button>
                                                        </h2>
                                                        <div id="collapse-{{ $session->id }}"
                                                            class="accordion-collapse collapse {{ $sessionIndex == 0 ? 'show' : '' }}"
                                                            data-bs-parent="#accordion-hall-{{ $hall->id }}-cat-{{ $categoryId }}">
                                                            <div class="accordion-body">
                                                                <!-- Session Details -->
                                                                <div class="session-details">
                                                                    <span class="session-badge time">
                                                                        <i class="fas fa-clock"></i>
                                                                        {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}
                                                                    </span>
                                                                    @if ($session->sessionChair)
                                                                        <span class="session-badge chair">
                                                                            <i class="fas fa-user-tie"></i>Chair:
                                                                            {{ $session->sessionChair->name }}
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                @if ($session->description)
                                                                    <p class="text-muted mb-3">
                                                                        {{ $session->description }}</p>
                                                                @endif

                                                                <!-- Polls for this session -->
                                                                @php
                                                                    $sessionPolls = $polls->where(
                                                                        'scientific_session_id',
                                                                        $session->id,
                                                                    );
                                                                @endphp

                                                                @if ($sessionPolls->count() > 0)
                                                                    @foreach ($sessionPolls as $poll)
                                                                        <div class="poll-container"
                                                                            data-poll-id="{{ $poll->id }}">
                                                                            <div class="poll-question">
                                                                                <i class="fas fa-poll me-2"
                                                                                    style="color: #667eea;"></i>{{ $poll->question_text }}
                                                                            </div>

                                                                            @if (!$poll->user_voted && !empty($checkAttendance))
                                                                                <!-- Show voting buttons -->
                                                                                <div class="poll-answers">
                                                                                    @foreach ($poll->answers as $answer)
                                                                                        <button
                                                                                            class="poll-answer-btn vote-btn"
                                                                                            data-poll-id="{{ $poll->id }}"
                                                                                            data-answer-id="{{ $answer->id }}">
                                                                                            {{ $answer->answer_text }}
                                                                                        </button>
                                                                                    @endforeach
                                                                                </div>
                                                                            @elseif ($poll->user_voted)
                                                                                <!-- Show results -->
                                                                                <div class="poll-results">
                                                                                    @php
                                                                                        $totalVotes = $poll->votes->count();
                                                                                    @endphp
                                                                                    @foreach ($poll->answers as $answer)
                                                                                        @php
                                                                                            $voteCount = $answer->votes->count();
                                                                                            $percentage =
                                                                                                $totalVotes > 0
                                                                                                    ? round(
                                                                                                        ($voteCount /
                                                                                                            $totalVotes) *
                                                                                                            100,
                                                                                                        1,
                                                                                                    )
                                                                                                    : 0;
                                                                                            $isUserChoice =
                                                                                                $answer->id ==
                                                                                                $poll->user_answer_id;
                                                                                        @endphp
                                                                                        <div
                                                                                            class="poll-result {{ $isUserChoice ? 'selected' : '' }}">
                                                                                            <div class="poll-result-bar"
                                                                                                style="width: {{ $percentage }}%">
                                                                                            </div>
                                                                                            <div
                                                                                                class="poll-result-content">
                                                                                                <span
                                                                                                    class="poll-result-text">
                                                                                                    {{ $answer->answer_text }}
                                                                                                    @if ($isUserChoice)
                                                                                                        <i class="fas fa-check-circle ms-2"
                                                                                                            style="color: #667eea;"></i>
                                                                                                    @endif
                                                                                                </span>
                                                                                                <span
                                                                                                    class="poll-result-stats">
                                                                                                    <span>{{ $percentage }}%</span>
                                                                                                    <span>({{ $voteCount }}
                                                                                                        votes)</span>
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @else
                                                                                <div class="attendance-required-message"
                                                                                    style="font-size: 0.9rem; padding: 0.8rem;">
                                                                                    <i
                                                                                        class="fas fa-lock me-2"></i>Mark
                                                                                    attendance to vote
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="no-polls-message">
                                                                        <i class="fas fa-info-circle me-2"></i>No polls
                                                                        available for this session
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="no-polls-message">
                    <i class="fas fa-calendar-times me-2"></i>No scientific sessions scheduled for today
                </div>
            @endif
        </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const config = {
            attendanceTaken: {{ empty($checkAttendance) ? 'false' : 'true' }},
            kitTaken: {{ empty($conferenceRegistrationKit) ? 'false' : 'true' }},
            lunchRemaining: {{ $totalLunchRemaining }},
            dinnerRemaining: {{ $totalDinnerRemaining }},
            participantId: {{ $participant->id }},
            mealTimes: {
                lunch_start: "{{ $passSetting->lunch_start_time }}",
                lunch_end: "{{ $passSetting->lunch_end_time }}",
                dinner_start: "{{ $passSetting->dinner_start_time }}",
                dinner_end: "{{ $passSetting->dinner_end_time }}"
            }
        };

        function initializeUI() {
            document.getElementById('lunchRemaining').textContent = config.lunchRemaining;
            document.getElementById('dinnerRemaining').textContent = config.dinnerRemaining;

            if (!config.attendanceTaken) {
                // Disable kit and meal sections
                document.getElementById('conferenceKitSection').classList.add('disabled');
                document.getElementById('lunchSection').classList.add('disabled');
                document.getElementById('dinnerSection').classList.add('disabled');
            } else {
                // Show attendance taken
                document.getElementById('takeAttendanceBtn').classList.add('hidden');
                document.getElementById('attendanceTaken').classList.remove('hidden');

                // Enable kit and meal sections
                document.getElementById('conferenceKitSection').classList.remove('disabled');
                document.getElementById('lunchSection').classList.remove('disabled');
                document.getElementById('dinnerSection').classList.remove('disabled');

                // Handle kit status
                if (config.kitTaken) {
                    document.getElementById('takeKitBtn').classList.add('hidden');
                    document.getElementById('kitTaken').classList.remove('hidden');
                }

                // Handle meal timing and availability
                updateMealSections();
            }
        }

        function updateMealSections() {
            const now = new Date();
            const currentTime = now.getHours() * 100 + now.getMinutes();

            const toTimeInt = (str) => {
                const [h, m] = str.split(':');
                return parseInt(h) * 100 + parseInt(m);
            };

            const lunchStart = toTimeInt(config.mealTimes.lunch_start);
            const lunchEnd = toTimeInt(config.mealTimes.lunch_end);
            const dinnerStart = toTimeInt(config.mealTimes.dinner_start);
            const dinnerEnd = toTimeInt(config.mealTimes.dinner_end);

            // Lunch logic
            if (currentTime >= lunchStart && currentTime <= lunchEnd) {
                document.getElementById('lunchSection').classList.remove('disabled');
                if (config.lunchRemaining <= 0) {
                    document.getElementById('takeLunchBtn').classList.add('hidden');
                    document.getElementById('lunchCompleted').classList.remove('hidden');
                }
            } else {
                document.getElementById('lunchSection').classList.add('disabled');
            }

            // Dinner logic
            if (currentTime >= dinnerStart && currentTime <= dinnerEnd) {
                document.getElementById('dinnerSection').classList.remove('disabled');
                if (config.dinnerRemaining <= 0) {
                    document.getElementById('takeDinnerBtn').classList.add('hidden');
                    document.getElementById('dinnerCompleted').classList.remove('hidden');
                }
            } else {
                document.getElementById('dinnerSection').classList.add('disabled');
            }
        }


        function updateTimeDisplay() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('currentTime').textContent = timeString;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeUI();
            updateTimeDisplay();
            setInterval(updateTimeDisplay, 1000);
            setInterval(updateMealSections, 60000);
        });

        // Event handlers
        document.getElementById('takeAttendance').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Mark Attendance?',
                text: "This will record your presence.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, mark it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('conference.conference-registration.takeAttendance') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                participant_id: config.participantId
                            })
                        }).then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                config.attendanceTaken = true;
                                initializeUI();
                                Swal.fire('Marked!', 'Your attendance has been recorded.', 'success');
                            }
                        });
                }
            });
        });


        document.getElementById('takeConferenceKit').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Collect Conference Kit?',
                text: "Confirm kit collection.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, collect it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('conference.conference-registration.takeConferenceKit') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                participant_id: config.participantId
                            })
                        }).then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                config.kitTaken = true;
                                initializeUI();
                                Swal.fire('Collected!', 'Conference kit has been collected.',
                                    'success');
                            }
                        });
                }
            });
        });


        document.querySelectorAll('.takeMeal').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const toTimeInt = (str) => {
                    const [h, m] = str.split(':');
                    return parseInt(h) * 100 + parseInt(m);
                };
                const now = new Date();
                const currentTime = now.getHours() * 100 + now.getMinutes();

                const lunchStart = toTimeInt(config.mealTimes.lunch_start);
                const lunchEnd = toTimeInt(config.mealTimes.lunch_end);
                const dinnerStart = toTimeInt(config.mealTimes.dinner_start);
                const dinnerEnd = toTimeInt(config.mealTimes.dinner_end);

                let type = '';
                if (currentTime >= lunchStart && currentTime <= lunchEnd) {
                    type = 'lunch';
                } else if (currentTime >= dinnerStart && currentTime <= dinnerEnd) {
                    type = 'dinner';
                } else {
                    Swal.fire('Unavailable', 'It is not mealtime right now.', 'info');
                    return;
                }


                Swal.fire({
                    title: `Take ${type.charAt(0).toUpperCase() + type.slice(1)}?`,
                    text: "Confirm your meal collection.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, take it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('conference.conference-registration.takeMeal') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    participant_id: config.participantId,
                                    type: type
                                })
                            }).then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    if (type === 'lunch') config.lunchRemaining = data
                                        .remaining;
                                    else config.dinnerRemaining = data.remaining;
                                    initializeUI();
                                    Swal.fire('Done!',
                                        `${type.charAt(0).toUpperCase() + type.slice(1)} taken.`,
                                        'success');
                                } else {
                                    Swal.fire('Oops!', data.message, 'error');
                                }
                            });
                    }
                });
            });
        });

        // Poll Voting
        document.querySelectorAll('.vote-btn').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const pollId = this.getAttribute('data-poll-id');
                const answerId = this.getAttribute('data-answer-id');
                const pollContainer = this.closest('.poll-container');

                Swal.fire({
                    title: 'Submit Your Vote?',
                    text: "You won't be able to change your vote later.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, vote!',
                    confirmButtonColor: '#667eea'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('conference.conference-registration.vote') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    participant_id: config.participantId,
                                    poll_id: pollId,
                                    answer_id: answerId
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    // Replace voting buttons with results
                                    const resultsHTML = data.results.map(result => {
                                        const isSelected = result.id == answerId;
                                        return `
                                            <div class="poll-result ${isSelected ? 'selected' : ''}">
                                                <div class="poll-result-bar" style="width: ${result.percentage}%"></div>
                                                <div class="poll-result-content">
                                                    <span class="poll-result-text">
                                                        ${result.text}
                                                        ${isSelected ? '<i class="fas fa-check-circle ms-2" style="color: #667eea;"></i>' : ''}
                                                    </span>
                                                    <span class="poll-result-stats">
                                                        <span>${result.percentage}%</span>
                                                        <span>(${result.votes} votes)</span>
                                                    </span>
                                                </div>
                                            </div>
                                        `;
                                    }).join('');

                                    pollContainer.querySelector('.poll-answers').outerHTML =
                                        `<div class="poll-results">${resultsHTML}</div>`;

                                    Swal.fire({
                                        title: 'Vote Recorded!',
                                        text: 'Thank you for participating.',
                                        icon: 'success',
                                        confirmButtonColor: '#667eea'
                                    });
                                } else {
                                    Swal.fire('Error!', data.message ||
                                        'Failed to record vote.', 'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Error!', 'An error occurred while voting.', 'error');
                                console.error('Voting error:', error);
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>
