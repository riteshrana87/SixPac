<nav class="pcoded-navbar">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu">


        <ul class="pcoded-item pcoded-left-item mt-4">
            <li class="{{ (request()->is('admin/dashboard*') || request()->is('/')) ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <span class="pcoded-micon"><i class="ti-home"></i><b>D</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Dashboard</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            {{-- <li>
                <a href="{{ route('admin.dashboard') }}">
                    <span class="pcoded-micon"><i class="ti-user"></i><b>U</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Users</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li> --}}
        </ul>


            <ul class="pcoded-item pcoded-left-item">

                <li class="pcoded-hasmenu {{ request()->is('admin/settings*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="ti-settings"></i></span>
                        <span class="pcoded-mtext"  data-i18n="nav.basic-components.main">Settings</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->is('admin/settings/edit-profile*') ? 'active' : '' }}">
                            <a href="{{ url('admin/settings/edit-profile') }}">
                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Edit Profile</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        <li class="{{ request()->is('admin/settings/change-password*') ? 'active' : '' }}">
                            <a href="{{ url('admin/settings/change-password') }}">
                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Change Password</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        <li>
                            <a class="logoutLink" id="logoutLink" data-url="{{ route('logout')}}" href="javascript:void(0)">
                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Logout</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
    </div>
</nav>
