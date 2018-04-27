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
            <a href="{{ url('logout') }}" class="text-danger dropdown-item">
                <i class="fa fa-fw fa-sign-out"></i>
                {{trans('left_menu.logout')}}
            </a>
        </div >
    </div>
</div>
<div class="float-right mr-3">
    @if(!$user->inRole('admin'))
        <div class="dropdown messages-menu">
            <mail-notifications url="{{ url('/') }}"></mail-notifications>
        </div>
    @else
        <div class="dropdown messages-menu">
            <support-notifications url="{{ url('/') }}"></support-notifications>
        </div>
    @endif
</div>
