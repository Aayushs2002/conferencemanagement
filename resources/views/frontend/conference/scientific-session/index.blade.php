@extends('frontend.conference.layouts.main')
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

    {{-- <div class="container py-4 mb-5" style="background:#F1F4FC; border-radius:20px; padding: 45px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title">Day 1</h2>
                <p class="span-text mt-4" style="color: black; font-weight: 500;">November 9, 2024</p>
            </div>
            <button class="btn btn-primary">
                Export PDF <i class="fa-solid fa-download"></i>
            </button>
        </div>

        <!-- Bootstrap Nav Tabs -->
        <ul class="nav nav-tabs mb-3" id="hallTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="common-tab" data-bs-toggle="tab" data-bs-target="#common" type="button"
                    role="tab">Common Hall</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallA-tab" data-bs-toggle="tab" data-bs-target="#hallA" type="button"
                    role="tab">Hall A</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallB-tab" data-bs-toggle="tab" data-bs-target="#hallB" type="button"
                    role="tab">Hall B</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallC-tab" data-bs-toggle="tab" data-bs-target="#hallC" type="button"
                    role="tab">Hall C</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallD-tab" data-bs-toggle="tab" data-bs-target="#hallD" type="button"
                    role="tab">Hall D</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallE-tab" data-bs-toggle="tab" data-bs-target="#hallE" type="button"
                    role="tab">Hall E</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="lobby-tab" data-bs-toggle="tab" data-bs-target="#lobby" type="button"
                    role="tab">Lobby</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="hallTabsContent">
            <div class="tab-pane fade show active" id="common" role="tabpanel" aria-labelledby="common-tab">
                <div class="accordion" id="accordionDay1">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="inauguralHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#inauguralCollapse" aria-expanded="false">
                                <span class="session-title">Inaugural Session</span>
                                <span class="session-time">
                                    8:00 AM to 10:00 AM
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                </span>
                            </button>
                        </h2>
                        <div id="inauguralCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionDay1">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">8:00 AM to 8:30 AM :</div>
                                    <div class="session-title">Registration</div>
                                </div>
                                <div class="session-item">
                                    <div class="session-time">8:30 AM to 9:30 AM :</div>
                                    <div class="session-title">Inauguration</div>
                                </div>
                                <div class="session-item">
                                    <div class="session-time">9:30 AM to 9:45 AM :</div>
                                    <div class="session-title">
                                        Prof. Dr. Roshana Amatya Oration - Occupational wellbeing in Anesthesia
                                        <span class="session-subtitle">Fauzia Khan</span>
                                    </div>
                                </div>
                                <div class="session-item">
                                    <div class="session-time">9:45 AM to 10:00 AM :</div>
                                    <div class="session-title">Break</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thematic Session -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="thematicHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#thematicCollapse" aria-expanded="false">
                                <span class="session-title">Thematic Session</span>
                                <span class="session-time">
                                    8:00 AM to 10:00 AM
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                </span>
                            </button>
                        </h2>
                        <div id="thematicCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionDay1">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">8:00 AM to 10:00 AM :</div>
                                    <div class="session-title">Thematic Discussions</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keynote Address -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="keynoteHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#keynoteCollapse" aria-expanded="false">
                                <span class="session-title">Keynote Address</span>
                                <span class="session-time">
                                    10:00 AM to 11:00 AM
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                </span>
                            </button>
                        </h2>
                        <div id="keynoteCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionDay1">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">10:00 AM to 11:00 AM :</div>
                                    <div class="session-title">Keynote Speech by Dr. John Doe</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Introduction to SAFOCON 2025 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="introHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#introCollapse" aria-expanded="false">
                                <span class="session-title">Introduction to SAFOCON 2025</span>
                                <span class="session-time">
                                    11:00 AM to 11:30 AM
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                </span>
                            </button>
                        </h2>
                        <div id="introCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionDay1">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">11:00 AM to 11:30 AM :</div>
                                    <div class="session-title">Overview of SAFOCON 2025</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="tab-content" id="hallTabsContent">
            <!-- Hall A -->
            <div class="tab-pane fade show active" id="hallA" role="tabpanel" aria-labelledby="hallA-tab">
                <div class="accordion" id="accordionHallA">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallAHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallACollapse1" aria-expanded="false">
                                <span class="session-title">Inaugural Session</span>
                                <span class="session-time">8:00 AM to 10:00 AM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallACollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallA">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">8:00 AM to 8:30 AM :</div>
                                    <div class="session-title">Registration</div>
                                </div>
                                <div class="session-item">
                                    <div class="session-time">8:30 AM to 9:30 AM :</div>
                                    <div class="session-title">Inauguration</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hall B -->
            <div class="tab-pane fade" id="hallB" role="tabpanel" aria-labelledby="hallB-tab">
                <div class="accordion" id="accordionHallB">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallBHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallBCollapse1" aria-expanded="false">
                                <span class="session-title">Keynote Session</span>
                                <span class="session-time">10:00 AM to 11:00 AM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallBCollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallB">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">10:00 AM to 11:00 AM :</div>
                                    <div class="session-title">Keynote Speech by Dr. Jane Smith</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hall C -->
            <div class="tab-pane fade" id="hallC" role="tabpanel" aria-labelledby="hallC-tab">
                <div class="accordion" id="accordionHallC">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallCHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallCCollapse1" aria-expanded="false">
                                <span class="session-title">Workshop Session</span>
                                <span class="session-time">11:00 AM to 12:30 PM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallCCollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallC">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">11:00 AM to 12:30 PM :</div>
                                    <div class="session-title">Hands-on Workshop</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hall D -->
            <div class="tab-pane fade" id="hallD" role="tabpanel" aria-labelledby="hallD-tab">
                <div class="accordion" id="accordionHallD">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallDHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallDCollapse1" aria-expanded="false">
                                <span class="session-title">Panel Discussion</span>
                                <span class="session-time">1:00 PM to 2:30 PM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallDCollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallD">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">1:00 PM to 2:30 PM :</div>
                                    <div class="session-title">Panel Discussion on Trends</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hall E -->
            <div class="tab-pane fade" id="hallE" role="tabpanel" aria-labelledby="hallE-tab">
                <div class="accordion" id="accordionHallE">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallEHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallECollapse1" aria-expanded="false">
                                <span class="session-title">Networking Session</span>
                                <span class="session-time">2:30 PM to 3:30 PM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallECollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallE">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">2:30 PM to 3:30 PM :</div>
                                    <div class="session-title">Networking & Coffee Break</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lobby -->
            <div class="tab-pane fade" id="lobby" role="tabpanel" aria-labelledby="lobby-tab">
                <div class="accordion" id="accordionLobby">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="lobbyHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#lobbyCollapse1" aria-expanded="false">
                                <span class="session-title">Welcome & Info Desk</span>
                                <span class="session-time">8:00 AM to 9:00 AM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="lobbyCollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionLobby">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">8:00 AM to 9:00 AM :</div>
                                    <div class="session-title">Guest Registration & Information</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="container py-4" style="background:#F1F4FC; border-radius:20px; padding: 45px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="section-title">Day 2</h2>
                <p class="span-text mt-4" style="color: black; font-weight: 500;">November 8, 2024</p>
            </div>
            <button class="btn btn-primary">
                Export PDF <i class="fa-solid fa-download"></i>
            </button>
        </div>

        <!-- Bootstrap Nav Tabs -->
        <ul class="nav nav-tabs mb-3" id="hallTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="common-tab" data-bs-toggle="tab" data-bs-target="#common"
                    type="button" role="tab">Common Hall</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallA-tab" data-bs-toggle="tab" data-bs-target="#hallA" type="button"
                    role="tab">Hall A</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallB-tab" data-bs-toggle="tab" data-bs-target="#hallB" type="button"
                    role="tab">Hall B</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallC-tab" data-bs-toggle="tab" data-bs-target="#hallC" type="button"
                    role="tab">Hall C</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallD-tab" data-bs-toggle="tab" data-bs-target="#hallD" type="button"
                    role="tab">Hall D</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hallE-tab" data-bs-toggle="tab" data-bs-target="#hallE" type="button"
                    role="tab">Hall E</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="lobby-tab" data-bs-toggle="tab" data-bs-target="#lobby" type="button"
                    role="tab">Lobby</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="hallTabsContent">
            <div class="tab-pane fade show active" id="common" role="tabpanel" aria-labelledby="common-tab">
                <div class="accordion" id="accordionDay1">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="inauguralHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#inauguralCollapse" aria-expanded="false">
                                <span class="session-title">Inaugural Session</span>
                                <span class="session-time">
                                    8:00 AM to 10:00 AM
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                </span>
                            </button>
                        </h2>
                        <div id="inauguralCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionDay1">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">8:00 AM to 8:30 AM :</div>
                                    <div class="session-title">Registration</div>
                                </div>
                                <div class="session-item">
                                    <div class="session-time">8:30 AM to 9:30 AM :</div>
                                    <div class="session-title">Inauguration</div>
                                </div>
                                <div class="session-item">
                                    <div class="session-time">9:30 AM to 9:45 AM :</div>
                                    <div class="session-title">
                                        Prof. Dr. Roshana Amatya Oration - Occupational wellbeing in Anesthesia
                                        <span class="session-subtitle">Fauzia Khan</span>
                                    </div>
                                </div>
                                <div class="session-item">
                                    <div class="session-time">9:45 AM to 10:00 AM :</div>
                                    <div class="session-title">Break</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thematic Session -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="thematicHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#thematicCollapse" aria-expanded="false">
                                <span class="session-title">Thematic Session</span>
                                <span class="session-time">
                                    8:00 AM to 10:00 AM
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                </span>
                            </button>
                        </h2>
                        <div id="thematicCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionDay1">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">8:00 AM to 10:00 AM :</div>
                                    <div class="session-title">Thematic Discussions</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keynote Address -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="keynoteHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#keynoteCollapse" aria-expanded="false">
                                <span class="session-title">Keynote Address</span>
                                <span class="session-time">
                                    10:00 AM to 11:00 AM
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                </span>
                            </button>
                        </h2>
                        <div id="keynoteCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionDay1">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">10:00 AM to 11:00 AM :</div>
                                    <div class="session-title">Keynote Speech by Dr. John Doe</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Introduction to SAFOCON 2025 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="introHeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#introCollapse" aria-expanded="false">
                                <span class="session-title">Introduction to SAFOCON 2025</span>
                                <span class="session-time">
                                    11:00 AM to 11:30 AM
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                </span>
                            </button>
                        </h2>
                        <div id="introCollapse" class="accordion-collapse collapse" data-bs-parent="#accordionDay1">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">11:00 AM to 11:30 AM :</div>
                                    <div class="session-title">Overview of SAFOCON 2025</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="tab-content" id="hallTabsContent">
            <!-- Hall A -->
            <div class="tab-pane fade show active" id="hallA" role="tabpanel" aria-labelledby="hallA-tab">
                <div class="accordion" id="accordionHallA">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallAHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallACollapse1" aria-expanded="false">
                                <span class="session-title">Inaugural Session</span>
                                <span class="session-time">8:00 AM to 10:00 AM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallACollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallA">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">8:00 AM to 8:30 AM :</div>
                                    <div class="session-title">Registration</div>
                                </div>
                                <div class="session-item">
                                    <div class="session-time">8:30 AM to 9:30 AM :</div>
                                    <div class="session-title">Inauguration</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hall B -->
            <div class="tab-pane fade" id="hallB" role="tabpanel" aria-labelledby="hallB-tab">
                <div class="accordion" id="accordionHallB">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallBHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallBCollapse1" aria-expanded="false">
                                <span class="session-title">Keynote Session</span>
                                <span class="session-time">10:00 AM to 11:00 AM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallBCollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallB">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">10:00 AM to 11:00 AM :</div>
                                    <div class="session-title">Keynote Speech by Dr. Jane Smith</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hall C -->
            <div class="tab-pane fade" id="hallC" role="tabpanel" aria-labelledby="hallC-tab">
                <div class="accordion" id="accordionHallC">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallCHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallCCollapse1" aria-expanded="false">
                                <span class="session-title">Workshop Session</span>
                                <span class="session-time">11:00 AM to 12:30 PM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallCCollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallC">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">11:00 AM to 12:30 PM :</div>
                                    <div class="session-title">Hands-on Workshop</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hall D -->
            <div class="tab-pane fade" id="hallD" role="tabpanel" aria-labelledby="hallD-tab">
                <div class="accordion" id="accordionHallD">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallDHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallDCollapse1" aria-expanded="false">
                                <span class="session-title">Panel Discussion</span>
                                <span class="session-time">1:00 PM to 2:30 PM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallDCollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallD">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">1:00 PM to 2:30 PM :</div>
                                    <div class="session-title">Panel Discussion on Trends</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hall E -->
            <div class="tab-pane fade" id="hallE" role="tabpanel" aria-labelledby="hallE-tab">
                <div class="accordion" id="accordionHallE">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="hallEHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#hallECollapse1" aria-expanded="false">
                                <span class="session-title">Networking Session</span>
                                <span class="session-time">2:30 PM to 3:30 PM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="hallECollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionHallE">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">2:30 PM to 3:30 PM :</div>
                                    <div class="session-title">Networking & Coffee Break</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lobby -->
            <div class="tab-pane fade" id="lobby" role="tabpanel" aria-labelledby="lobby-tab">
                <div class="accordion" id="accordionLobby">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="lobbyHeader1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#lobbyCollapse1" aria-expanded="false">
                                <span class="session-title">Welcome & Info Desk</span>
                                <span class="session-time">8:00 AM to 9:00 AM <i
                                        class="fa-solid fa-chevron-down arrow-icon"></i></span>
                            </button>
                        </h2>
                        <div id="lobbyCollapse1" class="accordion-collapse collapse" data-bs-parent="#accordionLobby">
                            <div class="accordion-body">
                                <div class="session-item">
                                    <div class="session-time">8:00 AM to 9:00 AM :</div>
                                    <div class="session-title">Guest Registration & Information</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> --}}

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
