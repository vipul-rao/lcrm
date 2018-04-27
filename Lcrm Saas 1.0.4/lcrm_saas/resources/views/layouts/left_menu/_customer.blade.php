<div class="nav_profile">
    <div class="media profile-left">
        <a class="pull-left profile-thumb" href="#">
            @if($user->user_avatar)
                <img src="{!! url('/').'/uploads/avatar/thumb_'.$user->user_avatar !!}" alt="img"
                     class="img-rounded"/>
            @else
                <img src="{{ url('uploads/avatar/user.png') }}" alt="img" class="img-rounded" />
            @endif
        </a>
        <div class="content-profile">
            <h4 class="media-heading text-capitalize user_name_max">{{ $user->full_name }}</h4>
            <ul class="icon-list">
                <li>
                    <a href="{{ url('customers/mailbox') }}#/m/inbox" title="Email">
                        <i class="fa fa-fw fa-envelope"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ url('customers/sales_order') }}" title="Sales Order">
                        <i class="fa fa-fw fa-usd"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ url('customers/invoice') }}" title="Invoices">
                        <i class="fa fa-fw fa-file-text"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div id="menu" role="navigation">
    <ul class="navigation">
        <li {!! (Request::is('customers') ? 'class="active"' : '') !!}>
            <a href="{{url('customers')}}">
                <span class="nav-icon">
                    <i class="material-icons text-primary">dashboard</i>
                </span>
                <span class="nav-text">{{trans('left_menu.dashboard')}}</span>
            </a>
        </li>
        <li {!! (Request::is('customers/quotation/*') || Request::is('customers/quotation') ? 'class="active"' : '') !!}>
            <a href="{{url('customers/quotation')}}">
                <span class="nav-icon">
                    <i class="material-icons text-info">assignment</i>
                </span>
                <span class="nav-text">{{trans('left_menu.quotations')}}</span>
            </a>
        </li>
        <li {!! (Request::is('customers/sales_order/*') || Request::is('customers/sales_order') ? 'class="active"' : '') !!}>
            <a href="{{url('customers/sales_order')}}">
                <span class="nav-icon">
                 <i class="material-icons text-warning">attach_money</i>
                </span>
                <span class="nav-text">{{trans('left_menu.sales_order')}}</span>
            </a>
        </li>
        <li class="menu-dropdown {!! (Request::is('customers/invoice/*') || Request::is('customers/invoice')  ||
            Request::is('customers/invoices_payment_log/*') || Request::is('customers/invoices_payment_log') ? 'active' : '') !!} ">
            <a>
                <span class="nav-caret pull-right">
                  <i class="fa fa-angle-right"></i>
                </span>
                <span class="nav-icon">
                    <i class="material-icons text-success">web</i>
                </span>
                <span class="nav-text">{{trans('left_menu.invoices')}}</span>
            </a>
            <ul class="nav-sub sub_menu">
                <li {!! (Request::is('customers/invoice/*') || Request::is('customers/invoice') ? 'class="active"' : '') !!}>
                    <a href="{{url('customers/invoice')}}" class="sub-li">
                        <i class="material-icons text-danger">receipt</i>
                        <span class="nav-text">{{trans('left_menu.invoices')}}</span>
                    </a>
                </li>
                <li {!! (Request::is('customers/invoices_payment_log/*') || Request::is('customers/invoices_payment_log') ? 'class="active"' : '') !!}>
                    <a href="{{url('customers/invoices_payment_log')}}" class="sub-li">
                        <i class="material-icons text-info">archive</i>
                        <span class="nav-text">{{trans('left_menu.payment_log')}}</span>
                    </a>
                </li>
            </ul>
        </li>
        <li {!! (Request::is('customers/setting/*') || Request::is('customers/setting') ? 'class="active"' : '') !!}>
            <a href="{{url('customers/setting')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-danger">settings</i>
                    </span>
                <span class="nav-text">{{trans('left_menu.settings')}}</span>
            </a>
        </li>
    </ul>
</div>
