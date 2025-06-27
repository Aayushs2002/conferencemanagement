@extends('backend.layouts.conference.main')

@section('title')
    {{ isset($scientific_session) ? 'Edit' : 'Add' }} Scientific Session
@endsection
@section('styles')
    <style>
        .category-item {
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .category-item:hover {
            background-color: #f8f9fa;
            border-left-color: #0d6efd;
        }

        .category-item.selected {
            background-color: #e7f3ff;
            border-left-color: #0d6efd;
        }

        .collapse-icon {
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .collapse-icon.collapsed {
            transform: rotate(-90deg);
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
@endsection
@section('content')
    <form class="needs-validation"
        action="{{ isset($scientific_session) ? route('scientific-session.update', [$society, $conference, $scientific_session->id]) : route('scientific-session.store', [$society, $conference]) }}"
        method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @isset($scientific_session)
            @method('patch')
        @endisset
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <h4 class="card-header"><a href="{{ route('scientific-session.index', [$society, $conference]) }}"><i
                                class="ti tabler-arrow-narrow-left"></i></a>
                        {{ isset($scientific_session) ? 'Edit' : 'Add' }} Scientific Session</h4>
                    <div class="card-body">

                        <div class="row mb-4">
                            <div class="col-md-4 form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="is_from_submission"
                                    id="scientific_session" value="1"
                                    @if (isset($scientific_session) && $scientific_session->is_from_submission == 1) checked
                                                        @elseif (old('is_from_submission') == 1)
                                                                checked @endif />
                                <label for="scientific_session" class="form-check-label">Select From Scientific
                                    Session?</label>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-3 form-group mb-3">
                                <label for="day">Day <code>*</code></label>
                                <select name="day" id="day"
                                    class="form-control @error('day') is-invalid @enderror" required>
                                    <option value="" hidden>-- Select Day --</option>
                                    @foreach ($dates as $date)
                                        <option value="{{ $date }}"
                                            @if (isset($scientific_session)) {{ $scientific_session->day == $date ? 'selected' : '' }} @else @selected(old('day') == $date) @endif>
                                            Day {{ $loop->iteration }}</option>
                                    @endforeach
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please Enter Category Name.</div>
                                @error('day')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group mb-3 hideDiv">
                                <label for="topic">Title <code>*</code></label>
                                <input type="text" class="form-control @error('topic') is-invalid @enderror"
                                    name="topic" id="topic"
                                    value="{{ isset($scientific_session) ? $scientific_session->topic : old('topic') }}"
                                    placeholder="Enter title" />
                                @error('topic')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group mb-3 submissionDiv" hidden>
                                <label for="submission_id">Submission <code>*</code></label>
                                <select name="submission_id" class="form-control" id="submission_id">
                                    <option value="" hidden>-- Select Submission --</option>

                                    @foreach ($submittedPresentations as $submittedPresentation)
                                        <option value="{{ $submittedPresentation->id }}"
                                            @if (isset($scientific_session)) {{ $scientific_session->submission_id == $submittedPresentation->id ? 'selected' : '' }} @else @selected(old('submission_id') == $submittedPresentation->id) @endif>
                                            {{ $submittedPresentation->title }}</option>
                                    @endforeach
                                </select>
                                @error('submission_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="hall_id">Hall <code>*</code></label>
                                <select name="hall_id" id="hall_id"
                                    class="form-control @error('hall_id') is-invalid @enderror" required>
                                    <option value="" hidden>-- Select Hall --</option>
                                    @foreach ($halls as $hall)
                                        <option value="{{ $hall->id }}"
                                            @if (isset($scientific_session)) @selected($hall->id == $scientific_session->hall_id) @else @selected(old('hall_id') == $hall->id) @endif>
                                            {{ $hall->hall_name }}</option>
                                    @endforeach
                                </select>
                                @error('hall_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="start-time">Start Time <code>*</code></label>
                                <input type="text"
                                    class="form-control @error('start_time') is-invalid @enderror timepicker"
                                    name="start_time" id="start-time"
                                    value="{{ isset($scientific_session) ? $scientific_session->start_time : old('start_time') }}"
                                    placeholder="Select schedule start time" required />
                                @error('start_time')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label for="end-time">End Time <code>*</code></label>
                                <input type="text"
                                    class="form-control @error('end_time') is-invalid @enderror timepicker" name="end_time"
                                    id="end-time"
                                    value="{{ isset($scientific_session) ? $scientific_session->end_time : old('end_time') }}"
                                    placeholder="Select schedule end time" required />
                                @error('end_time')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-3 form-group mb-3">
                                <label for="session_chair_id">Session Chair <code>*</code></label>
                                <select name="session_chair_id" class="form-control select2" id="session_chair_id" required>
                                    <option value="" hidden>-- Select Session Chair --</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            @if (isset($scientific_session)) {{ $scientific_session->session_chair_id == $user->id ? 'selected' : '' }} @else @selected(old('session_chair_id') == $user->id) @endif>
                                            {{ $user->fullName($user) }}</option>
                                    @endforeach
                                </select>
                                @error('session_chair_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-3 form-group mb-3 hideDiv">
                                <label for="presenter_id">Presenter</label>
                                <select name="presenter_id" class="form-control select2" id="presenter_id">
                                    <option value="" hidden>-- Select Presenter --</option>

                                    @foreach ($presenters as $presenter)
                                        <option value="{{ $presenter->id }}"
                                            @if (isset($scientific_session)) {{ $scientific_session->presenter_id == $presenter->id ? 'selected' : '' }} @else @selected(old('presenter_id') == $presenter->id) @endif>
                                            {{ $presenter->fullName($presenter) }}</option>
                                    @endforeach
                                </select>
                                @error('presenter_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control ckeditor" name="description" id="description" cols="30" rows="5">{{ !empty(old('description')) ? old('description') : @$scientific_session->description }}</textarea>
                                @error('description')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($scientific_session) ? 'Update' : 'Submit' }}</button>
                                <a href="{{ route('scientific-session.index', [$society, $conference]) }}"
                                    class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <h5 class="card-header">Available Time Slot</h5>
                    <div class="card-body">
                        <div id="available-slots">
                            <p class="text-muted">Select a Day and Hall to see booked time slots.</p>
                        </div>
                    </div>

                </div>

                <!-- Replace the existing category selection section in your col-md-5 with this: -->
                <div class="card shadow mt-6">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-tags me-2"></i>
                            Select Scientific Session Category
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <!-- Search Box -->
                        <div class="p-3 border-bottom" style="position: sticky; top: 0; z-index: 10; background: white;">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="icon-base ti tabler-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control" id="categorySearch"
                                    placeholder="Search categories..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="icon-base ti tabler-square-x"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Selected Category Display -->
                        <div id="selectedCategory" class="p-3 border-bottom bg-light" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted">Selected Category:</small>
                                    <div class="fw-bold text-primary" id="selectedCategoryName"></div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" type="button" id="clearSelection">
                                    <i class="fas fa-times me-1"></i>Clear
                                </button>
                            </div>
                        </div>

                        <!-- Categories List -->
                        <div style="max-height: 400px; overflow-y: auto;">
                            <div id="categoriesList" class="p-2">
                                @foreach (getCategories(0) as $category)
                                    <div class="category-group">
                                        <div class="category-item p-2 mb-1 rounded" data-level="0"
                                            style="border-left: 3px solid transparent; transition: all 0.2s ease;">
                                            <div class="d-flex align-items-center">
                                                @if (getCategories($category->id)->count() > 0)
                                                    <i class="icon-base ti tabler-chevron-down collapse-icon me-2 text-muted"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#subcategory-{{ $category->id }}"
                                                        style="cursor: pointer; transition: transform 0.2s ease;"></i>
                                                @else
                                                    <span class="me-4"></span>
                                                @endif

                                                <div class="form-check mb-0 flex-grow-1">
                                                    <input class="form-check-input" type="radio"
                                                        name="scientific_session_category_id"
                                                        id="category{{ $category->id }}" value="{{ $category->id }}"
                                                        onchange="selectCategory(this)"
                                                        @if (isset($scientific_session) && $scientific_session->scientific_session_category_id == $category->id) checked @endif>
                                                    <label class="form-check-label fw-semibold"
                                                        for="category{{ $category->id }}">
                                                        {{ $category->category_name }}
                                                    </label>
                                                    @if (getCategories($category->id)->count() > 0)
                                                        <span class="badge bg-secondary ms-2" style="font-size: 0.75rem;">
                                                            {{ getCategories($category->id)->count() }} subcategories
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if (getCategories($category->id)->count() > 0)
                                                <div class="collapse show" id="subcategory-{{ $category->id }}">
                                                    <div class="mt-2" style="margin-left: 1.5rem;">
                                                        @foreach (getCategories($category->id) as $subcategory)
                                                            <div class="category-item p-2 mb-1 rounded" data-level="1">
                                                                <div class="d-flex align-items-center">
                                                                    @if (getCategories($subcategory->id)->count() > 0)
                                                                        <i class="tabler ti-chevron-down collapse-icon me-2 text-muted"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#subcategory-{{ $subcategory->id }}"
                                                                            style="cursor: pointer; transition: transform 0.2s ease;"></i>
                                                                    @else
                                                                        <span class="me-4"></span>
                                                                    @endif

                                                                    <div class="form-check mb-0 flex-grow-1">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="scientific_session_category_id"
                                                                            id="category{{ $subcategory->id }}"
                                                                            value="{{ $subcategory->id }}"
                                                                            onchange="selectCategory(this)"
                                                                            @if (isset($scientific_session) && $scientific_session->scientific_session_category_id == $subcategory->id) checked @endif>
                                                                        <label class="form-check-label"
                                                                            for="category{{ $subcategory->id }}">
                                                                            {{ $subcategory->category_name }}
                                                                        </label>
                                                                        @if (getCategories($subcategory->id)->count() > 0)
                                                                            <span class="badge bg-info ms-2"
                                                                                style="font-size: 0.75rem;">
                                                                                {{ getCategories($subcategory->id)->count() }}
                                                                                topics
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                @if (getCategories($subcategory->id)->count() > 0)
                                                                    <div class="collapse show"
                                                                        id="subcategory-{{ $subcategory->id }}">
                                                                        <div class="mt-2" style="margin-left: 1.5rem;">
                                                                            @foreach (getCategories($subcategory->id) as $twosubcategory)
                                                                                <div class="category-item p-2 mb-1 rounded"
                                                                                    data-level="2">
                                                                                    <div class="form-check">
                                                                                        <input class="form-check-input"
                                                                                            type="radio"
                                                                                            name="scientific_session_category_id"
                                                                                            id="category{{ $twosubcategory->id }}"
                                                                                            value="{{ $twosubcategory->id }}"
                                                                                            onchange="selectCategory(this)"
                                                                                            @if (isset($scientific_session) && $scientific_session->scientific_session_category_id == $twosubcategory->id) checked @endif>
                                                                                        <label class="form-check-label"
                                                                                            for="category{{ $twosubcategory->id }}">
                                                                                            {{ $twosubcategory->category_name }}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- No Results Message -->
                            <div id="noResults" class="p-4 text-center"
                                style="display: none; color: #6c757d; font-style: italic;">
                                <i class="icon-base ti tabler-search fa-2x text-muted mb-2"></i>
                                <p class="mb-0">No categories found matching your search.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer with validation error -->
                    <div class="card-footer bg-light">
                        @error('scientific_session_category_id')
                            <div class="text-danger small mt-4">
                                <i class="icon-base ti tabler-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @else
                            <div class="text-muted small pt-4">
                                <i class="icon-base ti tabler-info-circle me-1"></i>
                                Select the most specific category that matches your scientific session topic.
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script>
        const startTime = document.querySelector('#start-time');
        const endTime = document.querySelector('#end-time');
        if (startTime) {
            startTime.flatpickr({
                enableTime: true,
                noCalendar: true,
                static: true
            });
        }
        if (endTime) {
            endTime.flatpickr({
                enableTime: true,
                noCalendar: true,
                static: true
            });
        }

        $(document).ready(function() {
            $('#scientific_session_category_id').on('change', function() {
                var selectedCategory = $(this).val();
                if (selectedCategory == 5) {
                    $('.hideDiv').hide();
                } else {
                    $('.hideDiv').show();
                }
            });
        });
    </script>
    <script>
        const bookedSessions = @json($sessions);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const daySelect = document.getElementById('day');
            const hallSelect = document.getElementById('hall_id');
            const slotDiv = document.getElementById('available-slots');

            const workingStart = '08:00';
            const workingEnd = '18:00';

            function parseTime(t) {
                const [h, m] = t.split(':').map(Number);
                return h * 60 + m;
            }

            function formatTime(minutes) {
                const h = String(Math.floor(minutes / 60)).padStart(2, '0');
                const m = String(minutes % 60).padStart(2, '0');
                return `${h}:${m}`;
            }

            function findFreeSlots(booked) {
                const free = [];
                let current = parseTime(workingStart);

                booked.forEach(slot => {
                    const start = parseTime(slot.start_time);
                    const end = parseTime(slot.end_time);

                    if (start > current) {
                        free.push({
                            start: formatTime(current),
                            end: formatTime(start)
                        });
                    }
                    current = Math.max(current, end);
                });

                if (current < parseTime(workingEnd)) {
                    free.push({
                        start: formatTime(current),
                        end: formatTime(parseTime(workingEnd))
                    });
                }

                return free;
            }

            function renderSlots() {
                const day = daySelect.value;
                const hall = hallSelect.value;

                if (!day || !hall) {
                    slotDiv.innerHTML =
                        '<p class="text-muted">Select both Day and Hall to view free time slots.</p>';
                    return;
                }

                const key = `${day}-${hall}`;
                const slots = bookedSessions[key] || [];

                const sorted = slots.slice().sort((a, b) => parseTime(a.start_time) - parseTime(b.start_time));
                const freeSlots = findFreeSlots(sorted);

                if (freeSlots.length === 0) {
                    slotDiv.innerHTML = '<p class="text-danger">No free time slots available.</p>';
                    return;
                }

                let html = '<p class="text-success">Free Time Slots:</p><ul class="list-group">';
                freeSlots.forEach(slot => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                            ${slot.start} - ${slot.end}
                         </li>`;
                });
                html += '</ul>';
                slotDiv.innerHTML = html;
            }

            function handleInputChange() {
                if (daySelect.value && hallSelect.value) {
                    renderSlots();
                } else {
                    slotDiv.innerHTML =
                        '<p class="text-muted">Select both Day and Hall to view free time slots.</p>';
                }
            }

            daySelect.addEventListener('change', handleInputChange);
            hallSelect.addEventListener('change', handleInputChange);

            if (daySelect.value && hallSelect.value) {
                renderSlots();
            }


            $("#scientific_session").change(function(e) {
                e.preventDefault();
                if ($(this).is(":checked")) {
                    $('.hideDiv').attr('hidden', true)
                    $('.submissionDiv').attr('hidden', false)
                } else {
                    $('.hideDiv').attr('hidden', false)
                    $('.submissionDiv').attr('hidden', true)
                }
            });
            $("#scientific_session").trigger('change');
        });

        function checkOnlyOne(checkbox) {
            var checkboxes = document.getElementsByName(checkbox.name);
            checkboxes.forEach(function(currentCheckbox) {
                if (currentCheckbox !== checkbox)
                    currentCheckbox.checked = false;
            });
        }
    </script>

    <!-- Add this JavaScript to your existing scripts section -->
    <script>
        // Category selection functionality
        function selectCategory(checkbox) {
            // Clear all other selections
            const checkboxes = document.getElementsByName('scientific_session_category_id');
            checkboxes.forEach(cb => {
                if (cb !== checkbox) {
                    cb.checked = false;
                    cb.closest('.category-item').classList.remove('selected');
                }
            });

            // Update selected state
            const categoryItem = checkbox.closest('.category-item');
            if (checkbox.checked) {
                categoryItem.classList.add('selected');
                showSelectedCategory(checkbox);
            } else {
                categoryItem.classList.remove('selected');
                hideSelectedCategory();
            }
        }

        function showSelectedCategory(checkbox) {
            const label = checkbox.nextElementSibling.textContent.trim();
            document.getElementById('selectedCategoryName').textContent = label;
            document.getElementById('selectedCategory').style.display = 'block';
        }

        function hideSelectedCategory() {
            document.getElementById('selectedCategory').style.display = 'none';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Clear selection functionality
            const clearBtn = document.getElementById('clearSelection');
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    const checkboxes = document.getElementsByName('scientific_session_category_id');
                    checkboxes.forEach(cb => {
                        cb.checked = false;
                        cb.closest('.category-item').classList.remove('selected');
                    });
                    hideSelectedCategory();
                });
            }

            // Search functionality
            const searchInput = document.getElementById('categorySearch');
            const clearSearchBtn = document.getElementById('clearSearch');
            const categoriesList = document.getElementById('categoriesList');
            const noResults = document.getElementById('noResults');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const categoryItems = document.querySelectorAll('.category-item');
                    let hasResults = false;

                    categoryItems.forEach(item => {
                        const label = item.querySelector('.form-check-label');
                        if (label) {
                            const text = label.textContent.toLowerCase();
                            const parentGroup = item.closest('.category-group');

                            if (text.includes(searchTerm)) {
                                item.style.display = 'block';
                                if (parentGroup) parentGroup.style.display = 'block';
                                hasResults = true;

                                // Expand parent if it's a subcategory match
                                const collapseElement = parentGroup?.querySelector('.collapse');
                                if (collapseElement && !collapseElement.classList.contains(
                                        'show')) {
                                    const bsCollapse = new bootstrap.Collapse(collapseElement, {
                                        show: true
                                    });
                                }
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });

                    // Show/hide groups and no results message
                    const categoryGroups = document.querySelectorAll('.category-group');
                    categoryGroups.forEach(group => {
                        const visibleItems = group.querySelectorAll(
                            '.category-item[style*="block"], .category-item:not([style])');
                        group.style.display = visibleItems.length > 0 ? 'block' : 'none';
                    });

                    if (categoriesList) categoriesList.style.display = hasResults ? 'block' : 'none';
                    if (noResults) noResults.style.display = hasResults ? 'none' : 'block';
                });
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                    searchInput.focus();
                });
            }

            // Collapse functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('collapse-icon')) {
                    const icon = e.target;
                    setTimeout(() => {
                        const targetId = icon.getAttribute('data-bs-target');
                        const targetElement = document.querySelector(targetId);

                        if (targetElement && targetElement.classList.contains('show')) {
                            icon.classList.remove('collapsed');
                        } else {
                            icon.classList.add('collapsed');
                        }
                    }, 100);
                }
            });
            // Show selected category if there's already a selection
            const checkedCategory = document.querySelector('input[name="scientific_session_category_id"]:checked');
            console.log(checkedCategory);
            if (checkedCategory) {
                checkedCategory.closest('.category-item').classList.add('selected');
                showSelectedCategory(checkedCategory);
            }
        });

        // Remove the old checkOnlyOne function since we're using selectCategory now
        // The selectCategory function handles the radio button behavior
    </script>
@endsection
