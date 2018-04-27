<section id="nav_custom">
    <div class="container">
        <div class="row ">
            <div class="col-12  header_mtop" id="navicon_">
                <div class="">
                    <div class="d-none d-md-block">
                        <a href="#" class=" menuicon float-right "><img src="{{asset('front/images/header/menu1.png')}}" alt="menu_icon" class="menubar"></a>
                    </div>
                </div>
                <nav class="navbar navbar-expand-md navbar-light">
                    <a href="{{ url('/') }}" class="logo navbar-brand float-left text-center">
                        @if(isset($settings['site_logo']))
                            <img src="{{ asset($settings['site_logo']) }}"
                                 alt="{{ $settings['site_name'] }}" class="img-responsive site_logo m_auto">
                        @endif
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon res-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <ul class="navbar-nav  hide ml-auto wow  fadeInRight" data-wow-duration="0.5s" data-wow-delay="0s">
                            <li class="sub-menu-parent">
                                <a class="nav-item nav-link link-padding {{ Request::is('/') ? 'active':'' }}" href="{{ url('/') }}">
                                    {{ trans('frontend.home') }}
                                </a>
                            </li>
                            <li>
                                <a class="nav-item nav-link link-padding {{ Request::is('about_us') ? 'active':'' }}" href="{{ url('about_us') }}">
                                    {{ trans('frontend.about_us') }}
                                </a>
                            </li>
                            <li>
                                <a class="nav-item nav-link link-padding {{ Request::is('contactus') ? 'active':'' }}" href="{{ url('contactus') }}">
                                    {{ trans('contactus.contactus') }}
                                </a>
                            </li>
                            <li>
                                <a class="nav-item nav-link link-padding {{ Request::is('pricing') ? 'active':'' }}" href="{{ url('pricing') }}">
                                    {{ trans('frontend.pricing') }}
                                </a>
                            </li>
                            @if(isset($user))
                                <li class="dropdown show">
                                    <a class="btn dropdown-toggle menu2 text-left nav-item nav-link link-padding portfolio " id="dropdownMenuLink" data-toggle="dropdown">
                                        {{ $user->full_name }}
                                    </a>
                                    <ul id="portfolio"  class="dropdown-menu animated  fadeInUp" aria-labelledby="dropdownMenuLink">
                                        <li class="panel-body">
                                            <a class="dropdown-item font-weight-bold" href="#">
                                                <div class="user_name_max name_para text-center text-capitalize d-block">{{ $user->full_name }}</div>
                                            </a>
                                        </li>
                                        <li class="dropdown-divider"></li>
                                        <li class="panel-body">
                                            <a class="dropdown-item" href="{{ $user->inRole('admin') ? url('admin'):url('dashboard') }}">
                                                <i class="fa fa-fw fa-home"></i>
                                                {{trans('left_menu.dashboard')}}
                                            </a>
                                        </li>
                                        <li class="dropdown-divider"></li>
                                        <li class="panel-body">
                                            <a class="dropdown-item" href="{{ url('profile') }}">
                                                <i class="fa fa-fw fa-user"></i>
                                                {{trans('left_menu.my_profile')}}
                                            </a>
                                        </li>
                                        <li class="dropdown-divider"></li>
                                        <li class="panel-body">
                                            <a href="{{ url('logout') }}" class="text-danger dropdown-item">
                                                <i class="fa fa-fw fa-sign-out"></i>
                                                {{trans('left_menu.logout')}}
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @else
                                <li>
                                    <a class="nav-item   nav-link link-padding {{ Request::is('register') ? 'active':'' }}" href="{{ url('register') }}">
                                        {{ trans('frontend.sign_up') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-item   nav-link link-padding {{ Request::is('signin') ? 'active':'' }}" href="{{ url('signin') }}">
                                        {{ trans('frontend.login') }}
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a class="nav-item  nav-link link-padding close1 d-none d-md-block" href="#">
                                    <img src="{{ asset('front/images/header/close.png') }}" alt="menu_icon" >
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</section>

