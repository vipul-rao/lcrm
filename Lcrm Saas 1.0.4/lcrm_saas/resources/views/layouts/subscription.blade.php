<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
    @include('layouts.header._meta')
    @include('layouts.header._assets')

    @yield('styles')
</head>
<body>
{{--  <div id="app">  --}}
<div id="app">
    <header class="header">
        <nav class="navbar navbar-static-top" role="navigation">
            <div class="w-100">
                <a href="{{ url('/') }}" class="logo navbar-brand float-sm-left text-center">
                    @if(isset($settings['site_logo']))
                        <img src="{{ asset($settings['site_logo']) }}"
                             alt="{{ $settings['site_name'] }}" class="img-responsive site_logo m_auto">
                    @endif
                </a>
                <div class="navbar-right m-t-5">
                    <div class="float-right">
                        <div class="dropdown">
                            <a id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="float-left">
                                    @if($user->user_avatar)
                                        <img src="{!! url('/').'/uploads/avatar/thumb_'.$user->user_avatar !!}" alt="img"
                                             class="img-rounded img-responsive user_avatar mr-1"/>
                                    @else
                                        <img src="{{ url('uploads/avatar/user.png') }}" alt="img"
                                             class="img-rounded img-responsive user_avatar mr-1"/>
                                    @endif
                                </div>
                                <div class="float-right">
                                    <p class="user_name_max text-capitalize mt-1">
                                        {{$user->full_name}}
                                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                                    </p>
                                </div>
                            </a>
                            <div class="dropdown-menu user_dropdown_menu" aria-labelledby="dropdownMenuButton">
                                <div class="dropdown-item">
                                    <p class="user_name_max name_para text-center text-capitalize d-block">{{ $user->full_name }}</p>
                                </div>
                                <a class="dropdown-item" href="{{ $user->inRole('admin') ? url('admin'):url('dashboard') }}">
                                    <i class="fa fa-fw fa-home"></i>
                                    {{trans('left_menu.dashboard')}}
                                </a>
                                <a class="dropdown-item" href="{{ url('profile') }}">
                                    <i class="fa fa-fw fa-user"></i>
                                    {{trans('left_menu.my_profile')}}
                                </a>
                                <a class="dropdown-item" href="{{ url('setting') }}">
                                    <i class="fa fa-fw fa-gear"></i>
                                    {{trans('left_menu.settings')}}
                                </a>
                                <a href="{{ url('logout') }}" class="text-danger dropdown-item">
                                    <i class="fa fa-fw fa-sign-out"></i>
                                    {{trans('left_menu.logout')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="wrapper">
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="right-side">
            <div class="right-content">
                <div class="breadcrumb">
                    <h2 class="m-t-20 m-b-10 text-uppercase">{{ $title or trans('dashboard.welcome_to_lcrm_saas') }}</h2>
                </div>
                <!-- Content -->
                <div class="content">
                    @yield('content')
                </div>
            <!-- /.content -->
            </div>
        </aside>
        <!-- /.right-side -->
    </div>
</div>
{{--  </div>  --}}
<!-- /.right-side -->
<!-- ./wrapper -->
<!-- global js -->
@include('layouts._assets_footer')
@yield('scripts')
<script>
    $('.left-side').addClass('collapse-left');
    $('.right-side').addClass('strech');
    </script>
</body>
</html>
