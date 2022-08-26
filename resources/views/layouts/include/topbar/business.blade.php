<nav class="navbar header-navbar pcoded-header">
               <div class="navbar-wrapper">
                   <div class="navbar-logo">
                       <a class="mobile-menu" id="mobile-collapse" href="javascript:void(0)">
                           <i class="ti-menu"></i>
                       </a>
                       <a href="{{ url('business/dashboard') }}">
                           <img class="img-fluid custom-logo" src="{{ asset('backend/assets/images/logo.png') }}" alt="Theme-Logo" />
                       </a>
                       <a class="mobile-options">
                           <i class="ti-more"></i>
                       </a>
                   </div>

                   <div class="navbar-container container-fluid">
                       <ul class="nav-left">
                           <li>
                               <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                           </li>
                           <li>
                               <a href="javascript:void(0)" onclick="javascript:toggleFullScreen()">
                                   <i class="ti-fullscreen"></i>
                               </a>
                           </li>
                       </ul>
                       <ul class="nav-right">
                           @php
                                $avtar_url = !empty(Auth::user()->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH').Auth::user()->avtar) : asset('backend/assets/images/no-avtar.png');
                           @endphp
                           <li class="user-profile header-notification">
                               <a href="javascript:void(0)">
                                   <span class="profile-wrap">
                                    <img src="{{ $avtar_url }}" class="img-radius" alt="User-Profile-Image">
                                   </span>
                                   <span>{{ Auth::user()->name }}</span>
                                   <i class="ti-angle-down"></i>
                               </a>
                               <ul class="show-notification profile-notification">
                                   <li>
                                       <a href="{{ url('business/settings/edit-profile') }}">
                                           <i class="ti-user"></i> Edit Profile
                                       </a>
                                   </li>

                                   <li>
                                       <a href="{{ url('business/settings/change-password') }}">
                                           <i class="ti-key"></i> Change Password
                                       </a>
                                   </li>
                                   <li>
                                       <a class="logoutLink" id="logoutLink" data-url="{{ route('logout')}}" href="javascript:void(0)">
                                            <i class="ti-shift-left-alt"></i> {{ __('Logout') }}
                                        </a>
                                   </li>
                               </ul>
                           </li>
                       </ul>
                   </div>
               </div>
           </nav>

