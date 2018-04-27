<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
    @include('layouts.header._meta')
    @include('layouts.header._assets')

    @yield('styles')
</head>
<body>
<div id="app">
<header class="header">
@include('layouts.header._header')
</header>
<div class="wrapper">
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="left-aside">
        <!-- sidebar: style can be found in sidebar-->
        <section class="sidebar">
            <div role="navigation">
                @if($menu_role=='admin')
                    @include('layouts.left_menu._admin')
                @elseif($menu_role=='user')
                    @include('layouts.left_menu._user')
                @elseif($menu_role=='customer')
                    @include('layouts.left_menu._customer')
                @endif
            </div>
            <!-- menu -->
        </section>
        <!-- /.sidebar -->
    </aside>
    <div class="right-aside">
        <div class="breadcrumb">
            <h2 class="m-t-20 m-b-10 text-uppercase">{{ $title or trans('dashboard.welcome_to_lcrm_saas') }}</h2>
        </div>
        <!-- Notifications -->

        <!-- Content -->
        <div class="content">

            @yield('content')

        </div>
        <!-- /.content -->
    </div>
    <!-- /.right-side -->
</div>
<!-- /.right-side -->
<!-- ./wrapper -->
</div>
<!-- global js -->
@include('layouts._assets_footer')
@include('layouts.pusherjs')
@yield('scripts')

</body>
</html>
