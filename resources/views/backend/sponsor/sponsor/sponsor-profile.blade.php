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
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow: hidden;
        }

        .main-container {
            width: 100%;
            max-width: 900px;
            height: 100vh;
            max-height: 600px;
            display: flex;
            overflow: hidden;
        }

        .profile-card {
            background: var(--primary-gradient);
            color: white;
            width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border-radius: 24px 0 0 24px;
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
            margin: 0 0 0.5rem 0;
            text-align: center;
        }

        .user-title {
            font-size: 0.9rem;
            opacity: 0.9;
            text-align: center;
            margin: 0;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            flex: 1;
            padding: 2rem;
            border-radius: 0 24px 24px 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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
                padding: 0;
                height: 100vh;
                overflow: hidden;
            }

            .main-container {
                flex-direction: column;
                max-height: 100vh;
                height: 100vh;
                width: 100vw;
                max-width: 100vw;
            }

            .profile-card {
                width: 100%;
                padding: 0.8rem;
                border-radius: 0;
                min-height: auto;
                height: auto;
                flex: 0 0 auto;
            }

            .content-card {
                border-radius: 0;
                padding: 0.8rem;
                flex: 1;
                height: auto;
                overflow: hidden;
            }

            .action-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
                height: 100%;
            }

            .action-item {
                padding: 0.8rem 0.4rem;
                min-height: auto;
            }

            .user-name {
                font-size: 1rem;
                margin: 0 0 0.2rem 0;
            }

            .user-title {
                font-size: 0.8rem;
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
                top: 0.5rem;
                right: 0.5rem;
                padding: 0.3rem 0.6rem;
                font-size: 0.65rem;
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

            .content-card {
                padding: 0.6rem;
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
    </style>
</head>

<body>
    <div class="floating-particles">
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="main-container">
        <!-- Profile Section -->
        <div class="profile-card">
            <div class="avatar">
                <img src="{{ asset('default-image/avatar.png') }}" alt="Profile Avatar" />
            </div>
            <h2 class="user-name">{{ $sponsor->name }}</h2>
            <p class="user-title">Conference Sponsor</p>
        </div>

        <!-- Content Section -->
        <div class="content-card">
            <div class="time-info">
                <i class="fas fa-clock me-1"></i>
                <span id="currentTime">14:30</span>
            </div>

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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const config = {
            attendanceTaken: {{ empty($checkAttendance) ? 'false' : 'true' }},
            lunchRemaining: {{ $totalLunchRemaining }},
            dinnerRemaining: {{ $totalDinnerRemaining }},
            sponsorId: {{ $sponsor->id }},
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
                document.getElementById('lunchSection').classList.add('disabled');
                document.getElementById('dinnerSection').classList.add('disabled');
            } else {
                // Show attendance taken
                document.getElementById('takeAttendanceBtn').classList.add('hidden');
                document.getElementById('attendanceTaken').classList.remove('hidden');

                // Enable kit and meal sections
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
                    fetch('{{ route('sponsor.takeAttendance') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                sponsor_id: config.sponsorId
                            })
                        }).then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                console.log(data)
                                config.attendanceTaken = true;
                                initializeUI();
                                Swal.fire('Marked!', 'Your attendance has been recorded.', 'success');
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
                        fetch('{{ route('sponsor.takeMeal') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    sponsor_id: config.sponsorId,
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
    </script>
</body>

</html>
