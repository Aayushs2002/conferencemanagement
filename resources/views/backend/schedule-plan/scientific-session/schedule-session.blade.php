@extends('backend.layouts.conference.main')

@section('title')
    Scientific Session
@endsection
@section('content')
    <style>
        .schedule-container {
            overflow-x: auto;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: 100px repeat(var(--hall-count, 3), 1fr);
            gap: 10px;
            min-width: 800px;
        }

        .time-slot {
            background: white;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            min-height: 60px;
            padding: 8px;
            position: relative;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .time-slot:hover {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.05);
        }

        .time-slot.drag-over {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.1);
            transform: scale(1.02);
        }

        .time-label {
            background: #343a40;
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
        }

        .hall-header {
            background: linear-gradient(135deg, #6f42c1, #495057);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            padding: 10px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .session-card {
            background: linear-gradient(135deg, #17a2b8, #20c997);
            color: white;
            border-radius: 8px;
            padding: 8px;
            cursor: grab;
            user-select: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin: 2px 0;
            position: relative;
            overflow: hidden;
        }

        .session-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .session-card.dragging {
            opacity: 0.7;
            transform: rotate(5deg);
            cursor: grabbing;
            z-index: 1000;
        }

        .session-card .session-title {
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .session-card .session-time {
            font-size: 10px;
            opacity: 0.9;
        }

        .session-card .session-category {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 2px 6px;
            font-size: 8px;
        }

        .session-card .edit-btn {
            position: absolute;
            bottom: 4px;
            right: 4px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: white;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .session-card:hover .edit-btn {
            opacity: 1;
        }

        .day-section {
            margin-bottom: 30px;
        }

        .day-header {
            background: linear-gradient(135deg, #fd7e14, #e83e8c);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .session-categories {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .category-filter {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            background: #e9ecef;
            color: #495057;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
        }

        .category-filter.active {
            background: #0d6efd;
            color: white;
        }

        .conflict-indicator {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            z-index: 5;
        }

        .session-card.conflict {
            border: 2px solid #dc3545;
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }

        .legend {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 5px 0;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .actions-panel {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .action-btn {
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .save-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .reset-btn {
            background: linear-gradient(135deg, #6c757d, #495057);
        }

        .add-session-btn {
            background: linear-gradient(135deg, #0d6efd, #6f42c1);
        }

        /* Modal Styles */
        .modal-header {
            background: linear-gradient(135deg, #6f42c1, #495057);
            color: white;
        }

        .time-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .time-input {
            flex: 1;
        }

        .duration-helper {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 14px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .schedule-grid {
                grid-template-columns: 80px repeat(var(--hall-count, 3), 150px);
            }

            .session-card {
                font-size: 10px;
                padding: 6px;
            }

            .actions-panel {
                position: static;
                justify-content: center;
                margin: 20px 0;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-primary">
                        <i class="fas fa-calendar-alt"></i> Scientific Session Scheduler
                    </h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="toggleView()">
                            <i class="fas fa-th-large"></i> Toggle View
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="autoSchedule()">
                            <i class="fas fa-magic"></i> Auto Schedule
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="showAddSessionModal()">
                            <i class="fas fa-plus"></i> Add Session
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="session-categories">
                    <button class="category-filter active" data-category="all">All Sessions</button>
                    <button class="category-filter" data-category="keynote">Keynote</button>
                    <button class="category-filter" data-category="oral">Oral Presentation</button>
                    <button class="category-filter" data-category="poster">Poster Session</button>
                    <button class="category-filter" data-category="workshop">Workshop</button>
                    <button class="category-filter" data-category="break">Coffee Break</button>
                </div>

                <!-- Legend -->
                <div class="legend">
                    <h6><i class="fas fa-info-circle"></i> Legend</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #17a2b8, #20c997);">
                                </div>
                                <small>Regular Session</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #dc3545, #fd7e14);">
                                </div>
                                <small>Time Conflict</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                                </div>
                                <small>Keynote Session</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="legend-item">
                                <div class="legend-color" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
                                </div>
                                <small>Workshop</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small><i class="fas fa-info-circle text-info"></i> Click on empty time slots to add new sessions,
                            or click the edit button on existing sessions to modify them.</small>
                    </div>
                </div>

                <!-- Schedule Grid -->
                <div id="schedule-container">
                    <!-- Days will be generated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Session Modal -->
    <div class="modal fade" id="sessionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus"></i>
                        <span id="modalTitle">Add New Session</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="sessionForm">
                        <input type="hidden" id="sessionId" name="sessionId">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sessionTitle" class="form-label">Session Title</label>
                                    <input type="text" class="form-control" id="sessionTitle" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sessionCategory" class="form-label">Category</label>
                                    <select class="form-select" id="sessionCategory" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="keynote">Keynote</option>
                                        <option value="oral">Oral Presentation</option>
                                        <option value="poster">Poster Session</option>
                                        <option value="workshop">Workshop</option>
                                        <option value="break">Coffee Break</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sessionDay" class="form-label">Day</label>
                                    <select class="form-select" id="sessionDay" name="day" required>
                                        <option value="">Select Day</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sessionHall" class="form-label">Hall</label>
                                    <select class="form-select" id="sessionHall" name="hall_id" required>
                                        <option value="">Select Hall</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Duration</label>
                                    <select class="form-select" id="durationPreset" onchange="updateDuration()">
                                        <option value="">Quick Select</option>
                                        <option value="30">30 minutes</option>
                                        <option value="45">45 minutes</option>
                                        <option value="60">1 hour</option>
                                        <option value="90">1.5 hours</option>
                                        <option value="120">2 hours</option>
                                        <option value="180">3 hours</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="startTime" class="form-label">Start Time</label>
                                    <input type="time" class="form-control" id="startTime" name="start_time" required
                                        onchange="calculateEndTime()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="endTime" class="form-label">End Time</label>
                                    <input type="time" class="form-control" id="endTime" name="end_time" required
                                        onchange="calculateDuration()">
                                </div>
                            </div>
                        </div>

                        <div class="duration-helper" id="durationHelper">
                            <i class="fas fa-clock"></i> Duration will be calculated automatically
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sessionPresenter" class="form-label">Presenter</label>
                                    <input type="text" class="form-control" id="sessionPresenter" name="presenter">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sessionChair" class="form-label">Session Chair</label>
                                    <input type="text" class="form-control" id="sessionChair" name="chair">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sessionDescription" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="sessionDescription" name="description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteSession()"
                        style="display: none;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveSession()">
                        <i class="fas fa-save"></i> Save Session
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Panel -->
    <div class="actions-panel">
        <button class="action-btn add-session-btn" onclick="showAddSessionModal()">
            <i class="fas fa-plus"></i> Add Session
        </button>
        <button class="action-btn reset-btn" onclick="resetSchedule()">
            <i class="fas fa-undo"></i> Reset
        </button>
        <button class="action-btn save-btn" onclick="saveSchedule()">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sample data - replace with your actual data from Laravel
        const conferenceData = {
            days: ['2024-07-15', '2024-07-16', '2024-07-17'],
            halls: [{
                    id: 1,
                    name: 'Main Hall'
                },
                {
                    id: 2,
                    name: 'Conference Room A'
                },
                {
                    id: 3,
                    name: 'Conference Room B'
                }
            ],
            timeSlots: generateTimeSlots('08:00', '18:00', 30), // 30-minute slots for better granularity
            sessions: [{
                    id: 1,
                    title: 'Opening Keynote: Future of AI',
                    day: '2024-07-15',
                    hall_id: 1,
                    start_time: '09:00',
                    end_time: '10:00',
                    category: 'keynote',
                    presenter: 'Dr. Smith',
                    chair: 'Prof. Johnson',
                    description: 'An inspiring talk about the future of artificial intelligence'
                },
                {
                    id: 2,
                    title: 'Machine Learning Applications',
                    day: '2024-07-15',
                    hall_id: 2,
                    start_time: '10:30',
                    end_time: '12:00',
                    category: 'oral',
                    presenter: 'Dr. Brown',
                    chair: 'Dr. Wilson',
                    description: 'Practical applications of machine learning in various domains'
                },
                {
                    id: 3,
                    title: 'Coffee Break',
                    day: '2024-07-15',
                    hall_id: 1,
                    start_time: '10:00',
                    end_time: '10:30',
                    category: 'break',
                    presenter: '',
                    chair: '',
                    description: 'Networking and refreshments'
                }
            ]
        };

        let draggedSession = null;
        let originalData = JSON.parse(JSON.stringify(conferenceData.sessions));
        let currentEditingSession = null;
        let sessionModal = null;

        function generateTimeSlots(start, end, intervalMinutes) {
            const slots = [];
            const startTime = parseTime(start);
            const endTime = parseTime(end);

            for (let time = startTime; time < endTime; time += intervalMinutes) {
                slots.push(formatTime(time));
            }
            return slots;
        }

        function parseTime(timeStr) {
            const [hours, minutes] = timeStr.split(':').map(Number);
            return hours * 60 + minutes;
        }

        function formatTime(minutes) {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
        }

        function getCategoryColor(category) {
            const colors = {
                keynote: 'linear-gradient(135deg, #ffc107, #fd7e14)',
                oral: 'linear-gradient(135deg, #17a2b8, #20c997)',
                poster: 'linear-gradient(135deg, #28a745, #20c997)',
                workshop: 'linear-gradient(135deg, #6f42c1, #e83e8c)',
                break: 'linear-gradient(135deg, #6c757d, #495057)'
            };
            return colors[category] || colors.oral;
        }

        function initializeModal() {
            sessionModal = new bootstrap.Modal(document.getElementById('sessionModal'));

            // Populate day options
            const daySelect = document.getElementById('sessionDay');
            daySelect.innerHTML = '<option value="">Select Day</option>';
            conferenceData.days.forEach((day, index) => {
                const option = document.createElement('option');
                option.value = day;
                option.textContent = `Day ${index + 1} - ${new Date(day).toLocaleDateString()}`;
                daySelect.appendChild(option);
            });

            // Populate hall options
            const hallSelect = document.getElementById('sessionHall');
            hallSelect.innerHTML = '<option value="">Select Hall</option>';
            conferenceData.halls.forEach(hall => {
                const option = document.createElement('option');
                option.value = hall.id;
                option.textContent = hall.name;
                hallSelect.appendChild(option);
            });
        }

        function showAddSessionModal(day = '', hallId = '', time = '') {
            currentEditingSession = null;
            document.getElementById('modalTitle').textContent = 'Add New Session';
            document.getElementById('deleteBtn').style.display = 'none';

            // Reset form
            document.getElementById('sessionForm').reset();
            document.getElementById('sessionId').value = '';

            // Pre-fill if slot was clicked
            if (day) document.getElementById('sessionDay').value = day;
            if (hallId) document.getElementById('sessionHall').value = hallId;
            if (time) document.getElementById('startTime').value = time;

            sessionModal.show();
        }

        function editSession(sessionId) {
            const session = conferenceData.sessions.find(s => s.id === sessionId);
            if (!session) return;

            currentEditingSession = session;
            document.getElementById('modalTitle').textContent = 'Edit Session';
            document.getElementById('deleteBtn').style.display = 'inline-block';

            // Fill form with session data
            document.getElementById('sessionId').value = session.id;
            document.getElementById('sessionTitle').value = session.title;
            document.getElementById('sessionCategory').value = session.category;
            document.getElementById('sessionDay').value = session.day;
            document.getElementById('sessionHall').value = session.hall_id;
            document.getElementById('startTime').value = session.start_time;
            document.getElementById('endTime').value = session.end_time;
            document.getElementById('sessionPresenter').value = session.presenter || '';
            document.getElementById('sessionChair').value = session.chair || '';
            document.getElementById('sessionDescription').value = session.description || '';

            calculateDuration();
            sessionModal.show();
        }

        function saveSession() {
            const form = document.getElementById('sessionForm');
            const formData = new FormData(form);

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const sessionData = {
                id: currentEditingSession ? currentEditingSession.id : Date.now(),
                title: formData.get('title'),
                category: formData.get('category'),
                day: formData.get('day'),
                hall_id: parseInt(formData.get('hall_id')),
                start_time: formData.get('start_time'),
                end_time: formData.get('end_time'),
                presenter: formData.get('presenter') || '',
                chair: formData.get('chair') || '',
                description: formData.get('description') || ''
            };

            if (currentEditingSession) {
                // Update existing session
                const index = conferenceData.sessions.findIndex(s => s.id === currentEditingSession.id);
                if (index !== -1) {
                    conferenceData.sessions[index] = sessionData;
                }
            } else {
                // Add new session
                conferenceData.sessions.push(sessionData);
            }

            sessionModal.hide();
            renderSchedule();
            showNotification('Session saved successfully!', 'success');
        }

        function deleteSession() {
            if (!currentEditingSession) return;

            if (confirm('Are you sure you want to delete this session?')) {
                conferenceData.sessions = conferenceData.sessions.filter(s => s.id !== currentEditingSession.id);
                sessionModal.hide();
                renderSchedule();
                showNotification('Session deleted successfully!', 'info');
            }
        }

        function calculateEndTime() {
            const startTime = document.getElementById('startTime').value;
            const endTimeInput = document.getElementById('endTime');

            if (startTime && !endTimeInput.value) {
                // Default to 1 hour if no end time is set
                const startMinutes = parseTime(startTime);
                const endMinutes = startMinutes + 60;
                endTimeInput.value = formatTime(endMinutes);
            }

            calculateDuration();
        }

        function calculateDuration() {
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            const helper = document.getElementById('durationHelper');

            if (startTime && endTime) {
                const startMinutes = parseTime(startTime);
                const endMinutes = parseTime(endTime);
                const duration = endMinutes - startMinutes;

                if (duration > 0) {
                    const hours = Math.floor(duration / 60);
                    const minutes = duration % 60;
                    let durationText = '';

                    if (hours > 0) durationText += `${hours} hour${hours > 1 ? 's' : ''}`;
                    if (minutes > 0) {
                        if (durationText) durationText += ' ';
                        durationText += `${minutes} minute${minutes > 1 ? 's' : ''}`;
                    }

                    helper.innerHTML = `<i class="fas fa-clock text-success"></i> Duration: ${durationText}`;
                    helper.className = 'duration-helper text-success';
                } else {
                    helper.innerHTML =
                        `<i class="fas fa-exclamation-triangle text-warning"></i> End time must be after start time`;
                    helper.className = 'duration-helper text-warning';
                }
            } else {
                helper.innerHTML = `<i class="fas fa-clock"></i> Duration will be calculated automatically`;
                helper.className = 'duration-helper';
            }
        }

        function updateDuration() {
            const preset = document.getElementById('durationPreset').value;
            const startTime = document.getElementById('startTime').value;

            if (preset && startTime) {
                const startMinutes = parseTime(startTime);
                const endMinutes = startMinutes + parseInt(preset);
                document.getElementById('endTime').value = formatTime(endMinutes);
                calculateDuration();
            }
        }

        function renderSchedule() {
            const container = document.getElementById('schedule-container');
            container.innerHTML = '';

            conferenceData.days.forEach((day, dayIndex) => {
                const daySection = document.createElement('div');
                daySection.className = 'day-section';

                const dayHeader = document.createElement('div');
                dayHeader.className = 'day-header';
                dayHeader.innerHTML = `
                    <div>
                        <h5><i class="fas fa-calendar-day"></i> Day ${dayIndex + 1}</h5>
                        <small>${new Date(day).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</small>
                    </div>
                    <div>
                        <small>${conferenceData.sessions.filter(s => s.day === day).length} sessions</small>
                    </div>
                `;

                const scheduleContainer = document.createElement('div');
                scheduleContainer.className = 'schedule-container';

                const scheduleGrid = document.createElement('div');
                scheduleGrid.className = 'schedule-grid';
                scheduleGrid.style.setProperty('--hall-count', conferenceData.halls.length);

                // Header row
                const timeHeaderCell = document.createElement('div');
                timeHeaderCell.className = 'time-label';
                timeHeaderCell.textContent = 'Time';
                scheduleGrid.appendChild(timeHeaderCell);

                conferenceData.halls.forEach(hall => {
                    const hallHeader = document.createElement('div');
                    hallHeader.className = 'hall-header';
                    hallHeader.innerHTML = `<i class="fas fa-door-open"></i> ${hall.name}`;
                    scheduleGrid.appendChild(hallHeader);
                });

                // Time slots
                conferenceData.timeSlots.forEach(timeSlot => {
                    const timeLabel = document.createElement('div');
                    timeLabel.className = 'time-label';
                    timeLabel.textContent = timeSlot;
                    scheduleGrid.appendChild(timeLabel);

                    conferenceData.halls.forEach(hall => {
                        const slot = document.createElement('div');
                        slot.className = 'time-slot';
                        slot.dataset.day = day;
                        slot.dataset.hall = hall.id;
                        slot.dataset.time = timeSlot;

                        // Add click event for empty slots
                        slot.addEventListener('click', function(e) {
                            if (!e.target.closest('.session-card')) {
                                showAddSessionModal(day, hall.id, timeSlot);
                            }
                        });

                        // Add drop event listeners
                        slot.addEventListener('dragover', handleDragOver);
                        slot.addEventListener('drop', handleDrop);
                        slot.addEventListener('dragleave', handleDragLeave);

                        // Check for sessions in this slot
                        const sessionsInSlot = conferenceData.sessions.filter(session =>
                            session.day === day &&
                            session.hall_id === hall.id &&
                            isTimeInRange(timeSlot, session.start_time, session.end_time)
                        );

                        sessionsInSlot.forEach(session => {
                            const sessionCard = createSessionCard(session);
                            slot.appendChild(sessionCard);
                        });

                        scheduleGrid.appendChild(slot);
                    });
                });

                scheduleContainer.appendChild(scheduleGrid);
                daySection.appendChild(dayHeader);
                daySection.appendChild(scheduleContainer);
                container.appendChild(daySection);
            });

            checkConflicts();
        }

        function createSessionCard(session) {
            const card = document.createElement('div');
            card.className = 'session-card';
            card.draggable = true;
            card.dataset.sessionId = session.id;
            card.style.background = getCategoryColor(session.category);

            card.innerHTML = `
                <div class="session-category">${session.category}</div>
                <div class="session-title">${session.title}</div>
                <div class="session-time">${session.start_time} - ${session.end_time}</div>
                ${session.presenter ? `<small><i class="fas fa-user"></i> ${session.presenter}</small>` : ''}
                <button class="edit-btn" onclick="editSession(${session.id}); event.stopPropagation();">
                    <i class="fas fa-edit"></i>
                </button>
            `;

            card.addEventListener('dragstart', handleDragStart);
            card.addEventListener('dragend', handleDragEnd);

            return card;
        }

        function isTimeInRange(checkTime, startTime, endTime) {
            const check = parseTime(checkTime);
            const start = parseTime(startTime);
            const end = parseTime(endTime);
            return check >= start && check < end;
        }

        function handleDragStart(e) {
            draggedSession = {
                id: parseInt(e.target.dataset.sessionId),
                element: e.target
            };
            e.target.classList.add('dragging');
        }

        function handleDragEnd(e) {
            e.target.classList.remove('dragging');
            draggedSession = null;
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('drag-over');
        }

        function handleDragLeave(e) {
            e.currentTarget.classList.remove('drag-over');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('drag-over');

            if (!draggedSession) return;

            const targetSlot = e.currentTarget;
            const newDay = targetSlot.dataset.day;
            const newHall = parseInt(targetSlot.dataset.hall);
            const newTime = targetSlot.dataset.time;

            // Find the session and update it
            const session = conferenceData.sessions.find(s => s.id === draggedSession.id);
            if (session) {
                // Calculate duration
                const duration = parseTime(session.end_time) - parseTime(session.start_time);
                const newEndTime = formatTime(parseTime(newTime) + duration);

                // Update session data
                session.day = newDay;
                session.hall_id = newHall;
                session.start_time = newTime;
                session.end_time = newEndTime;

                // Re-render schedule
                renderSchedule();

                // Show success message
                showNotification('Session moved successfully!', 'success');
            }
        }

        function checkConflicts() {
            const sessions = conferenceData.sessions;
            const conflicts = [];

            for (let i = 0; i < sessions.length; i++) {
                for (let j = i + 1; j < sessions.length; j++) {
                    const session1 = sessions[i];
                    const session2 = sessions[j];

                    if (session1.day === session2.day && session1.hall_id === session2.hall_id) {
                        const start1 = parseTime(session1.start_time);
                        const end1 = parseTime(session1.end_time);
                        const start2 = parseTime(session2.start_time);
                        const end2 = parseTime(session2.end_time);

                        if ((start1 < end2 && end1 > start2)) {
                            conflicts.push(session1.id, session2.id);
                        }
                    }
                }
            }

            // Mark conflicted sessions
            document.querySelectorAll('.session-card').forEach(card => {
                const sessionId = parseInt(card.dataset.sessionId);
                if (conflicts.includes(sessionId)) {
                    card.classList.add('conflict');
                    if (!card.querySelector('.conflict-indicator')) {
                        const indicator = document.createElement('div');
                        indicator.className = 'conflict-indicator';
                        indicator.innerHTML = '<i class="fas fa-exclamation"></i>';
                        indicator.title = 'Time conflict detected';
                        card.appendChild(indicator);
                    }
                } else {
                    card.classList.remove('conflict');
                    const indicator = card.querySelector('.conflict-indicator');
                    if (indicator) indicator.remove();
                }
            });
        }

        function filterByCategory(category) {
            document.querySelectorAll('.category-filter').forEach(btn => {
                btn.classList.remove('active');
            });

            event.target.classList.add('active');

            document.querySelectorAll('.session-card').forEach(card => {
                const sessionId = parseInt(card.dataset.sessionId);
                const session = conferenceData.sessions.find(s => s.id === sessionId);

                if (category === 'all' || session.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function saveSchedule() {
            // Here you would send the updated schedule to your Laravel backend
            const updatedSessions = conferenceData.sessions;
            console.log(updatedSessions)
            // Example AJAX call (replace with your actual endpoint)
            /*
            fetch('/api/update-schedule', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ sessions: updatedSessions })
            })
            .then(response => response.json())
            .then(data => {
                showNotification('Schedule saved successfully!', 'success');
            })
            .catch(error => {
                showNotification('Error saving schedule!', 'error');
            });
            */

            showNotification('Schedule saved successfully!', 'success');
        }

        function resetSchedule() {
            if (confirm('Are you sure you want to reset all changes?')) {
                conferenceData.sessions = JSON.parse(JSON.stringify(originalData));
                renderSchedule();
                showNotification('Schedule reset to original state!', 'info');
            }
        }

        function autoSchedule() {
            // Simple auto-scheduling algorithm
            const unscheduledSessions = conferenceData.sessions.filter(s => !s.day || !s.hall_id);

            if (unscheduledSessions.length === 0) {
                showNotification('All sessions are already scheduled!', 'info');
                return;
            }

            unscheduledSessions.forEach(session => {
                // Find the first available slot
                for (const day of conferenceData.days) {
                    for (const hall of conferenceData.halls) {
                        for (const timeSlot of conferenceData.timeSlots) {
                            if (isSlotAvailable(day, hall.id, timeSlot, session)) {
                                session.day = day;
                                session.hall_id = hall.id;
                                session.start_time = timeSlot;
                                // Assume 1-hour duration if not specified
                                const duration = session.end_time ?
                                    parseTime(session.end_time) - parseTime(session.start_time) : 60;
                                session.end_time = formatTime(parseTime(timeSlot) + duration);
                                return;
                            }
                        }
                    }
                }
            });

            renderSchedule();
            showNotification('Auto-scheduling completed!', 'success');
        }

        function isSlotAvailable(day, hallId, timeSlot, sessionToPlace) {
            const sessionDuration = sessionToPlace.end_time ?
                parseTime(sessionToPlace.end_time) - parseTime(sessionToPlace.start_time) : 60;
            const sessionEndTime = formatTime(parseTime(timeSlot) + sessionDuration);

            const conflictingSessions = conferenceData.sessions.filter(session =>
                session.id !== sessionToPlace.id &&
                session.day === day &&
                session.hall_id === hallId &&
                ((parseTime(timeSlot) < parseTime(session.end_time) && parseTime(sessionEndTime) > parseTime(session
                    .start_time)))
            );
            return conflictingSessions.length === 0;
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className =
                `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        function toggleView() {
            // Toggle between grid and list view
            const container = document.getElementById('schedule-container');
            container.classList.toggle('list-view');
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            initializeModal();
            renderSchedule();

            // Category filter listeners
            document.querySelectorAll('.category-filter').forEach(btn => {
                btn.addEventListener('click', (e) => filterByCategory(e.target.dataset.category));
            });
        });
    </script>
@endsection
