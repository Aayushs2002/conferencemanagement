<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">

        <a href="{{ route('conference.openConferencePortal', [request()->segment(2), request()->segment(4)]) }}"
            class="app-brand-link">
            <span class="d-flex justify-content-center" style="width: 200px">
                <img src="{{ asset('storage/society/logo/' . getSociety(request()->segment(2))->logo) }}" height="65">

            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <li class="menu-item {{ request()->segment(5) == 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('conference.openConferencePortal', [request()->segment(2), request()->segment(4)]) }}"
                class="menu-link ">
                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        @php
            $conference = getConference(request()->segment(4));
        @endphp
        {{-- @if (current_user()->type != 3) --}}
        @if (auth()->user()->hasAnyConferencePermission($conference, [
                    'View Conference Registration',
                    'Registration And Invitation',
                    'Exceptional Case',
                    'View Pass Setting',
                    'View Certificate Setting',
                ]))
            <li
                class="menu-item {{ request()->segment(5) == 'conference-registration' && request()->segment(1) != 'my-society' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-registered"></i>

                    <div data-i18n="Conference Registration">Conference Registration</div>
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Conference Registration'))
                        <li class="menu-item {{ request()->segment(6) == 'registrant' ? 'active' : '' }}">
                            <a href="{{ route('conference.conference-registration.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Registrant">Registrant</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'Registration And Invitation'))
                        <li
                            class="menu-item {{ request()->segment(6) == 'registration-or-invitation' ? 'active' : '' }}">
                            <a href="{{ route('conference.conference-registration.registrationOrInvitation', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Registration/Invitation">Registration/Invitation</div>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'Exceptional Case'))
                        <li
                            class="menu-item {{ request()->segment(6) == 'register-for-exceptional-case' ? 'active' : '' }}">
                            <a href="{{ route('conference.conference-registration.registerForExceptionalCase', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Exceptional Case">Exceptional Case</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Pass Setting'))
                        <li class="menu-item {{ request()->segment(6) == 'pass-setting' ? 'active' : '' }}">
                            <a href="{{ route('pass-setting.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Pass Setting">Pass Setting</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Certificate Setting'))
                        <li class="menu-item {{ request()->segment(6) == 'conference-certificate' ? 'active' : '' }}">
                            <a href="{{ route('conference-certificate.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Certificate Setting">Certificate Setting</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        {{-- @endif --}}

        @if (current_user()->type == 3)
            <li
                class="menu-item {{ request()->segment(5) == 'conference-registration' && request()->segment(1) == 'my-society' ? 'active' : '' }}">
                <a href="{{ checkRegistration(request()->segment(4)) ? route('my-society.conference.index', [request()->segment(2), request()->segment(4)]) : route('my-society.conference.create', [request()->segment(2), request()->segment(4)]) }}"
                    class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-building-skyscraper"></i>
                    <div data-i18n="My Conference Registration">My Conference Registration</div>
                </a>
            </li>

            <li
                class="menu-item  {{ request()->segment(5) == 'workshop-registration' && request()->segment(1) == 'my-society' ? 'active' : '' }}">
                <a href="{{ route('my-society.conference.workshop.index', [request()->segment(2), request()->segment(4)]) }}"
                    class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-building-skyscraper"></i>
                    <div data-i18n="My Workshop Registration">My Workshop Registration</div>
                </a>
            </li>
            <li
                class="menu-item  {{ request()->segment(5) == 'submission' && request()->segment(1) == 'my-society' ? 'active' : '' }}">
                <a href="{{ route('my-society.conference.submission.index', [request()->segment(2), request()->segment(4)]) }}"
                    class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-building-skyscraper"></i>
                    <div data-i18n="My Submission">My Submission</div>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasAnyConferencePermission($conference, [
                    'View Submission',
                    'View Submission Setting',
                    'View Category/Major Track',
                ]))
            <li
                class="menu-item {{ request()->segment(5) == 'submission' && request()->segment(1) != 'my-society' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-file-description"></i>

                    <div data-i18n="Submission">Submission</div>
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Submission'))
                        <li
                            class="menu-item {{ request()->segment(5) == 'submission' &&request()->segment(6) == '' && request()->segment(1) != 'my-society' ? 'active' : '' }}">
                            <a href="{{ route('submission.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Submission">Submission</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Submission Setting'))
                        <li class="menu-item {{ request()->segment(6) == 'submission-setting' ? 'active' : '' }}">
                            <a href="{{ route('submission.setting', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Submission Setting">Submission Setting</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Category/Major Track'))
                        <li
                            class="menu-item {{ request()->segment(6) == 'submission-cateogry-majortrack' ? 'active' : '' }}">
                            <a href="{{ route('submission.category-majortrack.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Category/Major Track">Category/Major Track</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        {{-- <li class="menu-item {{ request()->segment(1) == 'hotel' ? 'active' : '' }}">
            <a href="{{ route('my-society.conference.index', [request()->segment(2), request()->segment(4)]) }}"
                class="menu-link ">
                <i class="menu-icon icon-base ti tabler-building-skyscraper"></i>
                <div data-i18n="Submission">Submission</div>
            </a>
        </li> --}}
        {{-- @if (current_user()->type != 3) --}}
        @if (auth()->user()->hasAnyConferencePermission($conference, [
                    'View Scientific Session',
                    'View Scientific Session Category',
                    'View Scientific Session Hall',
                ]))
            <li class="menu-item {{ request()->segment(5) == 'scientific-session' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-calendar-clock"></i>

                    <div data-i18n="Schedule Plan">Schedule Plan</div>
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Scientific Session'))
                        <li
                            class="menu-item {{ request()->segment(5) == 'scientific-session' && request()->segment(6) == '' ? 'active' : '' }}">
                            <a href="{{ route('scientific-session.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Scientific Session">Scientific Session</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Scientific Session Category'))
                        <li class="menu-item {{ request()->segment(6) == 'category' ? 'active' : '' }}">
                            <a href="{{ route('category.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Category">Category</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Scientific Session Hall'))
                        <li class="menu-item {{ request()->segment(6) == 'hall' ? 'active' : '' }}">
                            <a href="{{ route('hall.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Hall">Hall</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (auth()->user()->hasAnyConferencePermission($conference, [
                    'View Workshop',
                    'Regster New User',
                    'Regster User in Exceptional Case',
                    'View Workshop Pass Setting',
                ]))
            <li class="menu-item {{ request()->segment(5) == 'workshop' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-wash-dry-w"></i>

                    <div data-i18n="Workshop">Workshop</div>
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Workshop'))
                        <li
                            class="menu-item {{ request()->segment(5) == 'workshop' && request()->segment(6) == '' ? 'active' : '' }}">
                            <a href="{{ route('workshop.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Workshop">Workshop</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'Regster New User'))
                        <li class="menu-item {{ request()->segment(7) == 'register-for-new-user' ? 'active' : '' }}">
                            <a href="{{ route('workshop.workshop-registration.registerForNewUser', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Register New User">Register New User</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'Regster User in Exceptional Case'))
                        <li
                            class="menu-item {{ request()->segment(7) == 'register-for-exceptional-case' ? 'active' : '' }}">
                            <a href="{{ route('workshop.workshop-registration.registerForExceptionalCase', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Exceptional Case">Exceptional Case</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Workshop Pass Setting'))
                        <li class="menu-item {{ request()->segment(6) == 'workshop-pass-settings' ? 'active' : '' }}">
                            <a href="{{ route('workshop-pass-settings.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Pass Setting">Pass Setting</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (auth()->user()->hasAnyConferencePermission($conference, ['View Committee', 'View Committee Designation']))
            <li class="menu-item {{ request()->segment(5) == 'committee' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-circle-letter-c"></i>

                    <div data-i18n="Committee">Committee</div>
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Committee'))
                        <li
                            class="menu-item {{ request()->segment(5) == 'committee' && request()->segment(6) == '' ? 'active' : '' }}">
                            <a href="{{ route('committee.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Committee">Committee</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Committee Designation'))
                        <li class="menu-item {{ request()->segment(6) == 'committe-designation' ? 'active' : '' }}">
                            <a href="{{ route('committe-designation.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Committee Designation">Committee Designation</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (auth()->user()->hasAnyConferencePermission($conference, ['View Sponsor', 'View Sponsor Category']))
            <li class="menu-item {{ request()->segment(5) == 'sponsor' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-circle-letter-s"></i>

                    <div data-i18n="Sponsor">Sponsor</div>
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Sponsor'))
                        <li
                            class="menu-item {{ request()->segment(5) == 'sponsor' && request()->segment(6) == '' ? 'active' : '' }}">
                            <a href="{{ route('sponsor.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Sponsor">Sponsor</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Sponsor Category'))
                        <li class="menu-item {{ request()->segment(6) == 'sponsor-category' ? 'active' : '' }}">
                            <a href="{{ route('sponsor-category.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Sponsor Category">Sponsor Category</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (auth()->user()->hasAnyConferencePermission($conference, ['View Faq', 'View Faq Category']))
            <li class="menu-item {{ request()->segment(5) == 'faq' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-circle-letter-f"></i>

                    <div data-i18n="Faq">Faq</div>
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Faq'))
                        <li
                            class="menu-item {{ request()->segment(5) == 'faq' && request()->segment(6) == '' ? 'active' : '' }}">
                            <a href="{{ route('faq.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Faq">Faq</div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Faq Category'))
                        <li class="menu-item {{ request()->segment(6) == 'faq-category' ? 'active' : '' }}">
                            <a href="{{ route('faq-category.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Faq Category">Faq Category</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (auth()->user()->hasConferencePermissionBlade($conference, 'View User'))
            <li class="menu-item {{ request()->segment(5) == 'user' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-users"></i>

                    <div data-i18n="User">User</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->segment(6) == 'signup-users' ? 'active' : '' }}">
                        <a href="{{ route('signup-user.index', [request()->segment(2), request()->segment(4)]) }}"
                            class="menu-link">
                            <div data-i18n="Signed Up User">Signed Up User</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @if (auth()->user()->hasAnyConferencePermission($conference, ['View Role']))
            <li class="menu-item {{ request()->segment(5) == 'roles' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-users"></i>

                    <div data-i18n="User Management">User Management</div>
                </a>
                <ul class="menu-sub">
                    @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Role'))
                        <li class="menu-item {{ request()->segment(5) == 'roles' ? 'active' : '' }}">
                            <a href="{{ route('roles.index', [request()->segment(2), request()->segment(4)]) }}"
                                class="menu-link">
                                <div data-i18n="Roles">Roles</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Download'))
            <li class="menu-item {{ request()->segment(5) == 'download' ? 'active' : '' }}">
                <a href="{{ route('download.index', [request()->segment(2), request()->segment(4)]) }}"
                    class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-download"></i>
                    <div data-i18n="Download">Download</div>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasConferencePermissionBlade($conference, 'View News/Notice'))
            <li class="menu-item {{ request()->segment(5) == 'notice' ? 'active' : '' }}">
                <a href="{{ route('notice.index', [request()->segment(2), request()->segment(4)]) }}"
                    class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-news"></i>
                    <div data-i18n="News/Notice">News/Notice</div>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasConferencePermissionBlade($conference, 'View Hotel'))
            <li class="menu-item {{ request()->segment(5) == 'hotel' ? 'active' : '' }}">
                <a href="{{ route('hotel.index', [request()->segment(2), request()->segment(4)]) }}"
                    class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-building-skyscraper"></i>
                    <div data-i18n="Accomodation">Accomodation</div>
                </a>
            </li>
        @endif
        {{-- @endif --}}


    </ul>
</aside>

<div class="menu-mobile-toggler d-xl-none rounded-1">
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
        <i class="ti tabler-menu icon-base"></i>
        <i class="ti tabler-chevron-right icon-base"></i>
    </a>
</div>
