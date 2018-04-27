<div class="nav_profile">
	<div class="media profile-left">
		<a class="pull-left profile-thumb" href="#">
			@if(isset($user)&&$user->user_avatar)
			<img src="{!! url('/').'/uploads/avatar/thumb_'.$user->user_avatar !!}" alt="img" class="img-rounded" /> @else
			<img src="{{ url('uploads/avatar/user.png') }}" alt="img" class="img-rounded" /> @endif
		</a>
		<div class="content-profile">
			<h4 class="media-heading text-capitalize user_name_max">{{ isset($user)?$user->full_name:'' }}</h4>
			<ul class="icon-list">
				<li>
					<a href="{{ url('admin/support') }}#/s/tickets" title="Support">
						<i class="fa fa-fw fa-envelope"></i>
					</a>
				</li>
				<li>
					<a href="{{ url('admin/setting') }}" title="Settings">
						<i class="fa fa-fw fa-cog"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<div id="menu">
	<ul class="navigation">
		<li {!! (Request::is( 'admin') ? 'class="active"' : '') !!}>
			<a href="{{url('admin')}}">
				<span class="nav-icon">
					<i class="material-icons text-primary">dashboard</i>
				</span>
				<span class="nav-text"> {{trans('left_menu.dashboard')}}</span>
			</a>
		</li>
		<li {!! (Request::is( 'admin/support*') || Request::is( 'admin/support') ? 'class="active"' : '') !!}>
			<a href="{{url('admin/support#/s/tickets')}}">
				<span class="nav-icon">
					<i class="material-icons text-warning">phone</i>
				</span>
				<span class="nav-text">{{trans('left_menu.support')}}</span>
			</a>
		</li>
		<li {!! (Request::is( 'organizations*') || Request::is( 'organizations') ? 'class="active"' : '') !!}>
			<a href="{{url('organizations')}}">
				<span class="nav-icon">
					<i class="material-icons text-danger">event_seat</i>
				</span>
				<span class="nav-text">{{trans('left_menu.organizations')}}</span>
			</a>
		</li>
		@if(isset($settings['stripe_secret']) && $settings['stripe_secret']!=='' && isset($settings['stripe_publishable']) && $settings['stripe_publishable']!=='')
		<li {!! (Request::is( 'admin/payplan*') || Request::is( 'admin/payplan') ? 'class="active"' : '') !!}>
			<a href="{{url('admin/payplan')}}">
				<span class="nav-icon">
					<i class="material-icons text-success">attach_money</i>
				</span>
				<span class="nav-text">{{trans('left_menu.payplan')}}</span>
			</a>
		</li>
		@endif
		<li {!! (Request::is( 'admin/subscription*') || Request::is( 'admin/subscription') ? 'class="active"' : '') !!}>
			<a href="{{url('admin/subscription')}}">
				<span class="nav-icon">
					<i class="material-icons text-info">web</i>
				</span>
				<span class="nav-text">{{trans('left_menu.subscription')}}</span>
			</a>
		</li>
		<li {!! (Request::is( 'admin/payment*') || Request::is( 'admin/payment') ? 'class="active"' : '') !!}>
			<a href="{{url('admin/payment')}}">
				<span class="nav-icon">
					<i class="material-icons text-primary">web</i>
				</span>
				<span class="nav-text">{{trans('left_menu.payment')}}</span>
			</a>
		</li>
		<li {!! (Request::is( 'admin/option/*') || Request::is( 'admin/option') ? 'class="active"' : '') !!}>
			<a href="{{url('admin/option')}}">
				<span class="nav-icon">
					<i class="material-icons text-warning">dashboard</i>
				</span>
				<span class="nav-text">{{trans('left_menu.options')}}</span>
			</a>
		</li>
		<li {!! (Request::is( 'admin/contactus*') || Request::is( 'admin/contactus') ? 'class="active"' : '') !!}>
			<a href="{{url('admin/contactus')}}">
				<span class="nav-icon">
					<i class="material-icons text-success">phone</i>
				</span>
				<span class="nav-text">{{trans('left_menu.contacts')}}</span>
			</a>
		</li>
		<li {!! (Request::is( 'admin/setting/*') || Request::is( 'admin/setting') ? 'class="active"' : '') !!}>
			<a href="{{url('admin/setting')}}">
				<span class="nav-icon">
					<i class="material-icons text-danger">settings</i>
				</span>
				<span class="nav-text">{{trans('left_menu.settings')}}</span>
			</a>
		</li>
		<li {!! (Request::is( 'admin/backup/*') || Request::is( 'admin/backup') ? 'class="active"' : '') !!}>
			<a href="{{url('admin/backup')}}">
				<span class="nav-icon">
					<i class="material-icons text-primary">backup</i>
				</span>
				<span class="nav-text">{{trans('left_menu.backup')}}</span>
			</a>
		</li>
	</ul>
</div>