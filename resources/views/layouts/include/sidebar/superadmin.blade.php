<nav class="pcoded-navbar">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu">


        <ul class="pcoded-item pcoded-left-item mt-4">
            <li class="{{ (request()->is('superadmin/dashboard*') || request()->is('/')) ? 'active' : '' }}">
                <a href="{{ route('superadmin.dashboard') }}">
                    <span class="pcoded-micon"><i class="ti-home"></i><b>D</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Dashboard</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>

        <ul class="pcoded-item pcoded-left-item">
            <li class="pcoded-hasmenu {{ request()->is('superadmin/users*') ? 'active  pcoded-trigger' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-user"></i></span>
                    <span class="pcoded-mtext"  data-i18n="nav.basic-components.main">Users</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ request()->is('superadmin/users/admin-users*') ? 'active' : '' }}">
                        <a href="{{ route('admin-users') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Admin Users</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                    <li class="{{ request()->is('superadmin/users/business-users*') ? 'active' : '' }}">
                        <a href="{{ route('business-users') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Business Users</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                    <li class="{{ request()->is('superadmin/users/consumer-users*') ? 'active' : '' }}">
                        <a href="{{ route('consumer-users') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Consumer Users</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>


        <ul class="pcoded-item pcoded-left-item">
            <li class="pcoded-hasmenu {{ request()->is('superadmin/interests*') ? 'active  pcoded-trigger' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-heart"></i></span>
                    <span class="pcoded-mtext"  data-i18n="nav.basic-components.main">Interests</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ (request()->is('superadmin/interests') || request()->is('superadmin/interests/add') || request()->is('superadmin/interests/edit')) ? 'active' : '' }}">
                        <a href="{{ route('interests') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Interests</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                    <li class="{{ request()->is('superadmin/interests/sub-interests*') ? 'active' : '' }}">
                        <a href="{{ route('sub-interests') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Sub Interests</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="pcoded-item pcoded-left-item">
			<li class="pcoded-hasmenu {{ request()->is('superadmin/posts*') || request()->is('superadmin/my-posts*') ? 'active  pcoded-trigger' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-comment-alt"></i></span>
                    <span class="pcoded-mtext"  data-i18n="nav.basic-components.main">Posts</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">

                    <li class="{{ request()->is('superadmin/posts') ||  request()->is('superadmin/posts/comments/*') ? 'active' : '' }}">
                        <a href="{{ route('users-posts') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i><b>P</b></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">All Posts</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                    <li class="{{ request()->is('superadmin/my-posts*') ? 'active' : '' }}">
                        <a href="{{ route('myposts') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i><b>P</b></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">My Posts</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                    <li class="{{ request()->is('superadmin/posts/archive-posts*') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.archive-posts') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Archived Posts</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                    <li class="{{ request()->is('superadmin/posts/flagged-posts*') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.flagged-posts') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Flagged Posts</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>

                    <li class="{{ request()->is('superadmin/posts/comment/flagged*') ? 'active' : '' }}">
                        <a href="{{ route('superadmin.flagged-comments') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Flagged Comments</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="pcoded-item pcoded-left-item">
            <li class="{{ request()->is('superadmin/profanity-words*') ? 'active' : '' }}">
                <a href="{{ route('superadmin.profanity-words') }}">
                    <span class="pcoded-micon"><i class="ti-na"></i><b>P</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Profanity Words</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>

            <li class="{{ request()->is('superadmin/advertisment*') ? 'active' : '' }}">
                <a href="{{ route('advertisment') }}">
                    <span class="pcoded-micon"><i class="ti-gallery"></i><b>A</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Advertisment</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>

            <li class="{{ request()->is('superadmin/offer-codes*') ? 'active' : '' }}">
                <a href="{{ route('offer-codes') }}">
                    <span class="pcoded-micon"><i class="ti-wand"></i><b>O</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Offer Codes</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="{{ request()->is('superadmin/notifications*') ? 'active' : '' }}">
                <a href="{{ route('notifications') }}">
                    <span class="pcoded-micon"><i class="ti-email"></i><b>N</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Notifications</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
			<!--
            <li class="{{ request()->is('superadmin/fitness-status*') ? 'active' : '' }}">
                <a href="{{ route('fitness-status') }}">
                    <span class="pcoded-micon"><i class="ti-pulse"></i><b>F</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Fitness Status</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
			-->

        </ul>


            <ul class="pcoded-item pcoded-left-item">

                <li class="pcoded-hasmenu {{ request()->is('superadmin/settings*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="ti-settings"></i></span>
                        <span class="pcoded-mtext"  data-i18n="nav.basic-components.main">Settings</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->is('superadmin/settings/edit-profile*') ? 'active' : '' }}">
                            <a href="{{ url('superadmin/settings/edit-profile') }}">
                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Edit Profile</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        <li class="{{ request()->is('superadmin/settings/change-password*') ? 'active' : '' }}">
                            <a href="{{ url('superadmin/settings/change-password') }}">
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



            <div class="pcoded-navigatio-lavel" data-i18n="nav.category.forms" menu-title-theme="theme1">Get Fit</div>
            <ul class="pcoded-item pcoded-left-item" item-border="true" item-border-style="none" subitem-border="true">
                <li class="{{ request()->is('superadmin/get-fit/workout-type*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('workout-types') }}">
                        <span class="pcoded-micon"><i class="ti-layers"></i><b>WT</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Workout Type</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
                <li class="{{ request()->is('superadmin/get-fit/equipment*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('equipment') }}">
                        <span class="pcoded-micon"><i class='ti-layers'></i><b>EQ</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Equipments</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
                 <li class="{{ request()->is('superadmin/get-fit/plan-goal*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('plan-goals') }}">
                        <span class="pcoded-micon"><i class="fa fa-regular fa-bullseye"></i><b>EQ</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Plan Goal</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
                <li class="{{ request()->is('superadmin/get-fit/plan-sport*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('plan-sports') }}">
                        <span class="pcoded-micon"><i class="ti-basketball"></i><b>EQ</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Plan Sport</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
                {{-- <li class="{{ request()->is('superadmin/get-fit/on-demand-services*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('on-demand-services') }}">
                        <span class="pcoded-micon"><i class="ti-layers"></i><b>ODS</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">On Demand Service</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li> --}}
            </ul>
    </div>
</nav>