@extends('frontend.conference.layouts.main')
@section('title')
    {{ $conference->society->sub_domain_name }} | Scientific Sessions
@endsection
@section('content')
    <div class="container">
        <div class="mb-2">
            <a href="{{ route('our-client.detail', $conference->society->slug) }}" class="back-btn mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to {{ $conference->society->abbreviation }}
            </a>
        </div>
    </div>
    <div class="search-panel position-relative mb-4">
        <input type="text" id="sessionSearch" class="form-control" placeholder="Search Scientific Sessions...">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <ul class="list-group position-absolute mt-2" id="suggestions" style="z-index: 1000;"></ul>
    </div>
 
    @foreach ($days as $dayIndex => $dayDate)
        <div class="container py-4 mb-5" style="background:#F1F4FC; border-radius:20px; padding: 45px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="section-title">Day {{ $dayIndex + 1 }}</h2>
                    <p class="span-text mt-4" style="color: black; font-weight: 500;">
                        {{ $dayDate->format('F d, Y') }}
                    </p>
                </div>
                <button class="btn btn-primary">
                    Export PDF <i class="fa-solid fa-download"></i>
                </button>
            </div>

            {{-- Tabs for Halls --}}
            <ul class="nav nav-tabs mb-3" role="tablist">
                @foreach ($halls as $hIndex => $hall)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $hIndex == 0 ? 'active' : '' }}"
                            id="hall{{ $dayIndex }}-{{ $hall->id }}-tab" data-bs-toggle="tab"
                            data-bs-target="#hall{{ $dayIndex }}-{{ $hall->id }}" type="button" role="tab">
                            {{ $hall->hall_name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            {{-- Tab content per hall --}}
            <div class="tab-content" id="hallTabsContent">
                @foreach ($halls as $hIndex => $hall)
                    @php
                        $dayKey = $days[$dayIndex]->format('Y-m-d');
                    @endphp

                    <div class="tab-pane fade {{ $hIndex == 0 ? 'show active' : '' }}"
                        id="hall{{ $dayIndex }}-{{ $hall->id }}" role="tabpanel">

                        <div class="accordion" id="accordionHall{{ $dayIndex }}-{{ $hall->id }}">
                            @if (isset($sessions[$dayKey][$hall->id]) && count($sessions[$dayKey][$hall->id]) > 0)
                                @foreach ($sessions[$dayKey][$hall->id] as $session)
                                    @php
                                        $title =
                                            $session->is_from_submission && $session->submission
                                                ? $session->submission->title
                                                : $session->topic;

                                        $author =
                                            $session->is_from_submission && $session->submission
                                                ? $session->submission->author_name
                                                : ($session->sessionChair
                                                    ? $session->sessionChair->fullName($session->sessionChair)
                                                    : '');
                                    @endphp

                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="sessionHeader{{ $session->id }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#sessionCollapse{{ $session->id }}" aria-expanded="false">
                                                <span class="session-title">{{ $title }}</span>
                                                <span class="session-time">
                                                    {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                                                    to {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}
                                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="sessionCollapse{{ $session->id }}" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionHall{{ $dayIndex }}-{{ $hall->id }}">
                                            <div class="accordion-body">
                                                <div class="session-item">
                                                    <div class="session-time">
                                                        {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                                                        to
                                                        {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}:
                                                    </div>
                                                    <div class="session-title">
                                                        {{ $title }}
                                                        @if ($author)
                                                            <span class="session-subtitle">{{ $author }}</span>
                                                        @endif
                                                    </div>
                                                    {{-- @if ($session->description)
                                                        <p>{{ $session->description }}</p>
                                                    @endif --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No sessions scheduled for this hall on this day.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    @endforeach


    <div class="td_height_80 td_height_lg_80"></div>

    <script>
        // Add this JavaScript to your blade file or external JS file

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('sessionSearch');
            const suggestionsBox = document.getElementById('suggestions');

            // Collect all sessions data
            const sessionsData = [];
            document.querySelectorAll('.accordion-item').forEach(item => {
                const titleElem = item.querySelector('.accordion-button .session-title');
                const timeElem = item.querySelector('.accordion-button .session-time');
                const subtitleElem = item.querySelector('.accordion-body .session-subtitle');

                if (titleElem) {
                    sessionsData.push({
                        element: item,
                        title: titleElem.textContent.trim(),
                        author: subtitleElem ? subtitleElem.textContent.trim() : '',
                        time: timeElem ? timeElem.textContent.trim() : '',
                        tabPane: item.closest('.tab-pane'),
                        tab: item.closest('.tab-pane') ? document.querySelector(
                            `[data-bs-target="#${item.closest('.tab-pane').id}"]`) : null
                    });
                }
            });

            // Search functionality
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();

                if (query.length === 0) {
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.style.display = 'none';
                    // Reset all visibility
                    document.querySelectorAll('.accordion-item').forEach(item => {
                        item.style.display = 'block';
                    });
                    return;
                }

                // Filter sessions
                const matches = sessionsData.filter(session =>
                    session.title.toLowerCase().includes(query) ||
                    session.author.toLowerCase().includes(query)
                );

                // Display suggestions
                if (matches.length > 0) {
                    suggestionsBox.innerHTML = matches.map(match => `
                <li class="list-group-item list-group-item-action" 
                    style="cursor: pointer;"
                    data-session-id="${match.element.id || ''}">
                    <strong>${highlightText(match.title, query)}</strong>
                    ${match.author ? `<br><small class="text-muted">by ${highlightText(match.author, query)}</small>` : ''}
                    <br><small class="text-primary">${match.time}</small>
                </li>
            `).join('');
                    suggestionsBox.style.display = 'block';

                    // Add click handlers to suggestions
                    suggestionsBox.querySelectorAll('li').forEach((li, index) => {
                        li.addEventListener('click', function() {
                            const match = matches[index];
                            navigateToSession(match);
                            suggestionsBox.style.display = 'none';
                            searchInput.value = match.title;
                        });
                    });
                } else {
                    suggestionsBox.innerHTML =
                        '<li class="list-group-item text-muted">No sessions found</li>';
                    suggestionsBox.style.display = 'block';
                }

                // Filter visible sessions
                document.querySelectorAll('.accordion-item').forEach(item => {
                    const session = sessionsData.find(s => s.element === item);
                    if (session) {
                        const isMatch = session.title.toLowerCase().includes(query) ||
                            session.author.toLowerCase().includes(query);
                        item.style.display = isMatch ? 'block' : 'none';
                    }
                });
            });

            // Helper function to highlight matching text
            function highlightText(text, query) {
                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<mark>$1</mark>');
            }

            // Navigate to session
            function navigateToSession(match) {
                // Activate the correct tab
                if (match.tab) {
                    const tab = new bootstrap.Tab(match.tab);
                    tab.show();
                }

                // Expand the accordion
                const collapseElement = match.element.querySelector('.accordion-collapse');
                if (collapseElement) {
                    const collapse = new bootstrap.Collapse(collapseElement, {
                        toggle: true
                    });
                }

                // Scroll to the session
                setTimeout(() => {
                    match.element.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    match.element.style.backgroundColor = '#fff3cd';
                    setTimeout(() => {
                        match.element.style.backgroundColor = '';
                    }, 2000);
                }, 300);
            }

            // Close suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.style.display = 'none';
                }
            });

            // PDF Export functionality
            document.querySelectorAll('.btn-primary').forEach(button => {
                if (button.textContent.includes('Export PDF')) {
                    button.addEventListener('click', function() {
                        const container = this.closest('.container');
                        exportToPDF(container);
                    });
                }
            });

            function exportToPDF(container) {
                // Get the day title
                const dayTitle = container.querySelector('.section-title').textContent;
                const dayDate = container.querySelector('.span-text').textContent;

                // Create printable content
                let printContent = `
            <html>
            <head>
                <title>${dayTitle} - ${dayDate}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1 { color: #333; }
                    h2 { color: #666; margin-top: 20px; }
                    .session { margin: 15px 0; padding: 10px; border-left: 3px solid #007bff; }
                    .session-time { font-weight: bold; color: #007bff; }
                    .session-title { font-size: 16px; margin: 5px 0; }
                    .session-subtitle { color: #666; font-style: italic; }
                    .hall-section { page-break-inside: avoid; margin-bottom: 30px; }
                </style>
            </head>
            <body>
                <h1>${dayTitle}</h1>
                <p>${dayDate}</p>
        `;

                // Get all tabs for this day
                const tabs = container.querySelectorAll('.nav-link');
                const tabPanes = container.querySelectorAll('.tab-pane');

                tabPanes.forEach((pane, index) => {
                    const hallName = tabs[index].textContent.trim();
                    printContent += `<div class="hall-section"><h2>${hallName}</h2>`;

                    const sessions = pane.querySelectorAll('.accordion-item');
                    sessions.forEach(session => {
                        const title = session.querySelector('.accordion-button .session-title')
                            ?.textContent || '';
                        const time = session.querySelector('.accordion-button .session-time')
                            ?.textContent.replace(/\s*\n\s*/g, ' ').trim() || '';
                        const subtitle = session.querySelector('.session-subtitle')?.textContent ||
                            '';

                        printContent += `
                    <div class="session">
                        <div class="session-time">${time.replace(/\s+/g, ' ')}</div>
                        <div class="session-title">${title}</div>
                        ${subtitle ? `<div class="session-subtitle">${subtitle}</div>` : ''}
                    </div>
                `;
                    });

                    printContent += '</div>';
                });

                printContent += '</body></html>';

                // Open print window
                const printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            }
        });
    </script>
@endsection
