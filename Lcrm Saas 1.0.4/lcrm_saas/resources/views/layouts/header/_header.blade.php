<nav class="navbar navbar-static-top">
    <div class="w-100">
        <a href="{{ url('/') }}" class="logo navbar-brand float-sm-left text-center">
            @if(isset($settings['site_logo']))
                <img src="{{ asset($settings['site_logo']) }}"
                     alt="{{ $settings['site_name'] }}" class="img-responsive site_logo m_auto">
            @endif
        </a>
        <div class="navbar-btn float-left">
            <a href="" class="sidebar-toggle m-t-5" data-toggle="offcanvas" role="button">
                <i class="fa fa-bars"></i>
            </a>
        </div>
        <div class="navbar-right m-t-5">
            @include("layouts.header._header-right")
        </div>
    </div>
</nav>