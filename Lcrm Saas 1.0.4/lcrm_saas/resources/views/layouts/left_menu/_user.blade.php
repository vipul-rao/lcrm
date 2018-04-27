<div class="nav_profile">
    <div class="media profile-left">
        <a class="pull-left profile-thumb" href="#">
            @if($user->user_avatar)
                <img src="{!! url('/').'/uploads/avatar/thumb_'.$user->user_avatar !!}" alt="img"
                     class="img-rounded"/>
            @else
                <img src="{{ url('uploads/avatar/user.png') }}" alt="img" class="img-rounded"/>
            @endif
        </a>
        <div class="content-profile">
            <h4 class="media-heading text-capitalize user_name_max">{{ $user->full_name }}</h4>
            <ul class="icon-list">
                <li>
                    <a href="{{ url('mailbox') }}#/m/inbox" title="Email">
                        <i class="fa fa-fw fa-envelope"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ url('sales_order') }}" title="Sales Order">
                        <i class="fa fa-fw fa-usd"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ url('invoice') }}" title="Invoices">
                        <i class="fa fa-fw fa-file-text"></i>
                    </a>
                </li>
                @if($orgRole=='admin')
                    <li>
                        <a href="{{ url('setting') }}" title="Settings">
                            <i class="fa fa-fw fa-cog"></i>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
<div id="menu" role="navigation">
    <ul class="navigation">
        <li {!! (Request::is('dashboard') ? 'class="active"' : '') !!}>
            <a href="{{url('dashboard')}}">
                <span class="nav-icon">
                    <i class="material-icons text-primary">dashboard</i>
                </span>
                <span class="nav-text"> {{trans('left_menu.dashboard')}}</span>
            </a>
        </li>
        @if(isset($user) && isset($orgRole) && $orgRole=='admin')
            <li {!! (Request::is('support*') || Request::is('support') ? 'class="active"' : '') !!}>
                <a href="{{url('support#/s/tickets')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-warning">phone</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.support')}}</span>
                </a>
            </li>
        @endif
        @if(isset($user) && ($user->hasAccess(['opportunities.read']) || isset($orgRole) && $orgRole=='admin'))
            <li {!! (Request::is('opportunity*') || Request::is('opportunity') ? 'class="active"' : '') !!}>
                <a href="{{url('opportunity')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-danger">event_seat</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.opportunities')}}</span>
                </a>
            </li>
        @endif
        @if(isset($user) && ($user->hasAccess(['leads.read']) || isset($orgRole) && $orgRole=='admin'))
            <li {!! (Request::is('lead*') || Request::is('lead') ? 'class="active"' : '') !!}>
                <a href="{{url('lead')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-info">thumb_up</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.leads')}}</span>
                </a>
            </li>
        @endif
        @if(isset($user) && ($user->hasAccess(['quotations.read']) || isset($orgRole) && $orgRole=='admin'))
            <li {!! (Request::is('quotation/*') || Request::is('quotation')
            || Request::is('quotation_delete_list/*') || Request::is('quotation_delete_list')
            || Request::is('quotation_converted_list') || Request::is('quotation_invoice_list') ? 'class="active"' : '') !!}>
                <a href="{{url('quotation')}}">
                    <i class="material-icons text-primary">receipt</i>
                    <span class="nav-text">{{trans('left_menu.quotations')}}</span>
                </a>
            </li>
        @endif
        @if(isset($user) && ($user->hasAccess(['invoices.read']) || isset($orgRole) && $orgRole=='admin'))
            <li class="menu-dropdown {!! (Request::is('invoice/*') || Request::is('invoice')
             || Request::is('invoice_delete_list*') || Request::is('invoice_delete_list')
             || Request::is('invoices_payment_log/*') || Request::is('invoices_payment_log')
             || Request::is('paid_invoice*') || Request::is('paid_invoice')
            ? 'active':'') !!}">
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
                    <li {!! (Request::is('invoice/*') || Request::is('invoice') || Request::is('invoice_delete_list*') || Request::is('invoice_delete_list')
                 || Request::is('paid_invoice*') || Request::is('paid_invoice') ? 'class="active"' : '') !!}>
                        <a href="{{url('invoice')}}" class="sub-li">
                            <i class="material-icons text-danger">receipt</i>
                            <span class="nav-text">{{trans('left_menu.invoices')}}</span>
                        </a>
                    </li>
                    <li {!! (Request::is('invoices_payment_log/*') || Request::is('invoices_payment_log') ? 'class="active"' : '') !!}>
                        <a href="{{url('invoices_payment_log')}}" class="sub-li">
                            <i class="material-icons text-info">archive</i>
                            <span class="nav-text">{{trans('left_menu.payment_log')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @if(isset($user) && ($user->hasAccess(['sales_team.read']) || isset($orgRole) && $orgRole=='admin'))
            <li {!! (Request::is('salesteam/*') || Request::is('salesteam') ? 'class="active"' : '') !!}>
                <a href="{{url('salesteam')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-danger">group</i>
                    </span>
                    <span class="nav-text"> {{trans('left_menu.salesteam')}}</span>
                </a>
            </li>
        @endif
        @if(isset($user) && ($user->hasAccess(['logged_calls.read']) || isset($orgRole) && $orgRole=='admin'))
            <li {!! (Request::is('call/*') || Request::is('call') ? 'class="active"' : '') !!}>
                <a href="{{url('call')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-primary">phone</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.calls')}}</span>
                </a>
            </li>
        @endif
        @if(isset($user) && ($user->hasAccess(['sales_orders.read']) || isset($orgRole) && $orgRole=='admin'))
            <li {!! (Request::is('sales_order/*') || Request::is('sales_order') ? 'class="active"' : '') !!}>
                <a href="{{url('sales_order')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-warning">attach_money</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.sales_order')}}</span>
                </a>
            </li>
        @endif
        @if(isset($user) && ($user->hasAccess(['products.read']) || isset($orgRole) && $orgRole=='admin'))
            <li class="menu-dropdown {!! (Request::is('product/*') || Request::is('product')  || Request::is('category/*') || Request::is('category') ? 'active' : '') !!}">
                <a>
                    <span class="nav-caret pull-right">
                        <i class="fa fa-angle-right"></i>
                    </span>
                    <span class="nav-icon">
                        <i class="material-icons text-primary">shopping_basket</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.products')}}</span>
                </a>
                <ul class="nav-sub sub_menu">
                    <li {!! (Request::is('product/*') || Request::is('product') ? 'class="active"' : '') !!}>
                        <a href="{{url('product')}}" class="sub-li">
                            <i class="material-icons text-danger">layers</i>
                            <span class="nav-text">{{trans('left_menu.products')}}</span>
                        </a>
                    </li>
                    <li {!! (Request::is('category/*') || Request::is('category') ? 'class="active"' : '') !!}>
                        <a href="{{url('category')}}" class="sub-li">
                            <i class="material-icons text-info">gamepad</i>
                            <span class="nav-text">{{trans('left_menu.category')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        <li {!! (Request::is('calendar/*') || Request::is('calendar') ? 'class="active"' : '') !!}>
            <a href="{{url('calendar')}}">
                <span class="nav-icon">
                    <i class="material-icons text-danger">event_note</i>
                </span>
                <span class="nav-text">{{trans('left_menu.calendar')}}</span>
            </a>
        </li>
        @if(isset($user) && ($user->hasAccess(['customers.read']) || isset($orgRole) && $orgRole=='admin'))
            <li class="menu-dropdown  {!! (Request::is('customer/*') || Request::is('customer') || Request::is('company/*') || Request::is('company') ? 'active' : '') !!} ">
                <a>
                    <span class="nav-caret pull-right">
                        <i class="fa fa-angle-right"></i>
                    </span>
                    <span class="nav-icon">
                        <i class="material-icons text-info">person_pin</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.customers')}}</span>
                </a>
                <ul class="nav-sub sub_menu">
                    <li {!! (Request::is('company/*') || Request::is('company') ? 'class="active"' : '') !!}>
                        <a href="{{url('company')}}" class="sub-li">
                            <i class="material-icons text-warning">flag</i>
                            <span class="nav-text">{{trans('left_menu.company')}}</span>
                        </a>
                    </li>
                    <li {!! (Request::is('customer/*') || Request::is('customer') ? 'class="active"' : '') !!}>
                        <a href="{{url('customer')}}" class="sub-li">
                            <i class="material-icons text-success">person</i>
                            <span class="nav-text">{{trans('left_menu.contact_person')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @if(isset($user) && ($user->hasAccess(['meetings.read']) || isset($orgRole) && $orgRole=='admin'))
            <li {!! (Request::is('meeting/*') || Request::is('meeting') ? 'class="active"' : '') !!}>
                <a href="{{url('meeting')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-success">radio</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.meetings')}}</span>
                </a>
            </li>
        @endif
        <li {!! (Request::is('task') ? 'class="active"' : '') !!}>
            <a href="{{url('/task')}}">
                <span class="nav-icon">
                    <i class="material-icons text-warning">event</i>
                </span>
                <span class="nav-text"> {{trans('left_menu.tasks')}}</span>
            </a>
        </li>
        @if(isset($user) && $user->hasAccess(['staff.read']) || isset($orgRole) && $orgRole=='admin')
            <h4 class="mar-5 border-b">Configuration</h4>
            <li {!! (Request::is('staff/*') || Request::is('staff') ? 'class="active"' : '') !!}>
                <a href="{{url('staff')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-primary">people_outline</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.staff')}}</span>
                </a>
            </li>
        @endif
        @if(isset($user) && isset($orgRole) && $orgRole=='admin')
            <li {!! (Request::is('email_template/*') || Request::is('email_template') ? 'class="active"' : '') !!}>
                <a href="{{url('email_template')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-success">email</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.email_template')}}</span>
                </a>
            </li>
            <li {!! (Request::is('qtemplate/*') || Request::is('qtemplate') ? 'class="active"' : '') !!}>
                <a href="{{url('qtemplate')}}">
                    <i class="material-icons text-primary">image</i>
                    <span class="nav-text">{{trans('left_menu.quotation_template')}}</span>
                </a>
            </li>
            <li {!! (Request::is('subscription*') || Request::is('subscription') ? 'class="active"' : '') !!}>
                <a href="{{url('subscription')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-info">web</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.subscription')}}</span>
                </a>
            </li>
            <li {!! (Request::is('setting/*') || Request::is('setting') ? 'class="active"' : '') !!}>
                <a href="{{url('setting')}}">
                    <span class="nav-icon">
                        <i class="material-icons text-danger">settings</i>
                    </span>
                    <span class="nav-text">{{trans('left_menu.settings')}}</span>
                </a>
            </li>
            @if($organization->subscription_type=='paypal')
                <li {!! (Request::is('paypal_transactions/*') || Request::is('paypal_transactions') ? 'class="active"' : '') !!}>
                    <a href="{{url('paypal_transactions')}}">
                        <span class="nav-icon">
                            <i class="material-icons text-primary">payment</i>
                        </span>
                        <span class="nav-text">{{trans('left_menu.paypal_transactions')}}</span>
                    </a>
                </li>
            @endif
        @endif
    </ul>
</div>
