<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">

        <a href="index.html" class="app-brand-link">
            {{-- <span class="app-brand-logo demo">
                <span class="text-primary">
                    <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                            fill="currentColor" />
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                            d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                            fill="currentColor" />
                    </svg>
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-3">Vuexy</span> --}}
            <span class="d-flex justify-content-center" style="width: 200px">
                @if (current_user()->type == 2 && current_user()->societies->value('logo'))
                    <img src="{{ asset('storage/society/logo/' . current_user()->societies->value('logo')) }}"
                        height="65">
                @else
                    <img src="{{ asset('default-image/logo.png') }}" height="65">
                @endif
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <li class="menu-item {{ request()->segment(1) == 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link ">
                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>


        <!-- e-commerce-app menu start -->
        @if (is_super_admin() || current_user()->type == 3)
            <li class="menu-item {{ request()->segment(1) == 'society' ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-shopping-cart"></i>
                    <div data-i18n="Society">Society</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->segment(1) == 'society' ? 'active' : '' }}">
                        <a href="{{ route('society.index') }}" class="menu-link">
                            <div data-i18n="Society">Society</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @if (is_super_admin())
            <li class="menu-item {{ request()->segment(1) == 'name-prefix' ? 'active' : '' }}">
                <a href="{{ route('name-prefix.index') }}" class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-label"></i>
                    <div data-i18n="Name Prefix">Name Prefix</div>
                </a>
            </li>
            <li class="menu-item {{ request()->segment(1) == 'institution' ? 'active' : '' }}">
                <a href="{{ route('institution.index') }}" class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-circle-letter-i"></i>
                    <div data-i18n="Institution">Institution</div>
                </a>
            </li>
            <li class="menu-item {{ request()->segment(1) == 'designation' ? 'active' : '' }}">
                <a href="{{ route('designation.index') }}" class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-circle-letter-d"></i>
                    <div data-i18n="Designation">Designation</div>
                </a>
            </li>
            <li class="menu-item {{ request()->segment(1) == 'department' ? 'active' : '' }}">
                <a href="{{ route('department.index') }}" class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-circle-letter-d"></i>
                    <div data-i18n="Department">Department</div>
                </a>
            </li>
            <li class="menu-item {{ request()->segment(1) == 'permission' ? 'active' : '' }}">
                <a href="{{ route('permission.index') }}" class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-circle-letter-p"></i>
                    <div data-i18n="Permission">Permission</div>
                </a>
            </li>
        @endif
        {{-- @if (current_user()->type == 3)
            <li class="menu-item {{ request()->segment(1) == 'my-society' ? 'active' : '' }}">
                <a href="{{ route('my-society.index') }}" class="menu-link ">
                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                    <div data-i18n="My Society">My Society</div>
                </a>
            </li>
        @endif --}}
    </ul>
</aside>

<div class="menu-mobile-toggler d-xl-none rounded-1">
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
        <i class="ti tabler-menu icon-base"></i>
        <i class="ti tabler-chevron-right icon-base"></i>
    </a>
</div>
