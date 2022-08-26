<nav class="pcoded-navbar">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu">


        <ul class="pcoded-item pcoded-left-item mt-4">
            <li class="{{ (request()->is('business/dashboard*') || request()->is('/')) ? 'active' : '' }}">
                <a href="{{ route('business.dashboard') }}">
                    <span class="pcoded-micon"><i class="ti-home"></i><b>D</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Dashboard</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
            <li class="{{ (request()->is('business/users/employee-users*') || request()->is('/')) ? 'active' : '' }}">
                <a href="{{ route('employee-users') }}">
                    <span class="pcoded-micon"><i class="ti-user"></i><b>E</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Employee</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>

        <ul class="pcoded-item pcoded-left-item">
			<li class="pcoded-hasmenu {{ request()->is('business/products*') ? 'active  pcoded-trigger' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-package"></i></span>
                    <span class="pcoded-mtext"  data-i18n="nav.basic-components.main">Products</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ request()->is('business/products') || request()->is('business/products/add') || request()->is('business/products/add-gallery*') || request()->is('business/products/edit*')   ? 'active' : '' }}">
                        <a href="{{ route('business.products') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Products</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                    <li class="{{ request()->is('business/products/product-category*') ? 'active' : '' }}">
                        <a href="{{ route('business.product-category') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Product Category</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                    <li class="{{ request()->is('business/products/import-products*') ? 'active' : '' }}">
                        <a href="{{ route('business.product-import') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Import Products</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
					<li class="{{ request()->is('business/products/archive-products*') ? 'active' : '' }}">
                        <a href="{{ route('business.archive-products') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Archived Products</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>


        <ul class="pcoded-item pcoded-left-item">
			<li class="pcoded-hasmenu {{ request()->is('business/posts*') ? 'active  pcoded-trigger' : '' }}">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="ti-comment-alt"></i></span>
                    <span class="pcoded-mtext"  data-i18n="nav.basic-components.main">Posts</span>
                    <span class="pcoded-mcaret"></span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="{{ request()->is('business/posts') || request()->is('business/posts/add') || request()->is('business/posts/edit/*') ||  request()->is('business/posts/comments/*') ? 'active' : '' }}">
                        <a href="{{ route('business.posts') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i><b>P</b></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Posts</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                    <li class="{{ request()->is('business/posts/archive-posts*') ? 'active' : '' }}">
                        <a href="{{ route('business.archive-posts') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Archived Posts</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
					<!--
                    <li class="{{ request()->is('business/posts/comment/flagged*') ? 'active' : '' }}">
                        <a href="{{ route('business.flagged-comments') }}">
                            <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                            <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Flagged Comments</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
					-->
                </ul>
            </li>
        </ul>

        <ul class="pcoded-item pcoded-left-item">
			<li class="{{ request()->is('business/orders*') ? 'active' : '' }}">
                <a href="{{ route('orders') }}">
                    <span class="pcoded-micon"><i class="ti-truck"></i><b>O</b></span>
                    <span class="pcoded-mtext" data-i18n="nav.dash.main">Orders</span>
                    <span class="pcoded-mcaret"></span>
                </a>
            </li>
        </ul>


            <ul class="pcoded-item pcoded-left-item">

                <li class="pcoded-hasmenu {{ request()->is('business/settings*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="ti-settings"></i></span>
                        <span class="pcoded-mtext" data-i18n="nav.basic-components.main">Settings</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->is('business/settings/edit-profile*') ? 'active' : '' }}">
                            <a href="{{ url('business/settings/edit-profile') }}">
                                <span class="pcoded-micon"><i class="ti-angle-right"></i></span>
                                <span class="pcoded-mtext" data-i18n="nav.basic-components.breadcrumbs">Edit Profile</span>
                                <span class="pcoded-mcaret"></span>
                            </a>
                        </li>
                        <li class="{{ request()->is('business/settings/change-password*') ? 'active' : '' }}">
                            <a href="{{ url('business/settings/change-password') }}">
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
                <li class="{{ request()->is('business/get-fit/exercises*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('business.exercises') }}">
                        <span class="pcoded-micon"><i class="ti-layers"></i><b>EX</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Exercises</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
                <li class="{{ request()->is('business/get-fit/workout*') && !request()->is('business/get-fit/workout-plan*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('business.workout') }}">
                        <span class="pcoded-micon"><i class="ti-layers"></i><b>WO</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Workout</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
               <!--  <li class="{{ request()->is('business/get-fit/workout-plan*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('business.workout-plan') }}">
                        <span class="pcoded-micon"><i class="ti-layers"></i><b>WP</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Workout Plan</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li> -->
            </ul>


             <?php /*<div class="pcoded-navigatio-lavel" data-i18n="nav.category.forms" menu-title-theme="theme1">Get Fit</div>
            <ul class="pcoded-item pcoded-left-item" item-border="true" item-border-style="none" subitem-border="true">
                <li class="{{ request()->is('superadmin/get-fit/workout-category*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('workout-categories') }}">
                        <span class="pcoded-micon"><i class="ti-layers"></i><b>WC</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Program</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
                <li class="{{ request()->is('superadmin/get-fit/workout-category*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('workout-categories') }}">
                        <span class="pcoded-micon"><i class="ti-layers"></i><b>WC</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Workout</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
                <li class="{{ request()->is('business/exercise*') ? 'active  pcoded-trigger' : '' }}">
                    <a href="{{ route('exercise') }}">
                        <span class="pcoded-micon"><i class="ti-layers"></i><b>ODS</b></span>
                        <span class="pcoded-mtext" data-i18n="nav.form-components.main">Exercise</span>
                        <span class="pcoded-mcaret"></span>
                    </a>
                </li>
            </ul> */ ?>
    </div>
</nav>
