<div class="card">
	<div class="card-body">
		@if (isset($organization)) {!! Form::model($organization, ['url' => $type . '/' . $organization->id, 'method' => 'put','files'=> true]) !!}
		@else {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!} @endif
		<div class="row">
			<div class="col-md-6">
				<h2>{{trans('organizations.organization_details')}}</h2>
				<div class="form-group required {{ $errors->has('user_avatar_file') ? 'has-error' : '' }}">
					{!! Form::label('user_avatar_file', trans('organizations.organization_avatar'), ['class' => 'control-label']) !!}
					<div class="row">
						@if(isset($organization->logo))
							<image-upload name="organization_avatar_file" old-image="{{ url('uploads/organizations/thumb_'.$organization->logo) }}"></image-upload>
						@else
							<image-upload name="organization_avatar_file"></image-upload>
						@endif
					</div>
					<span class="help-block">{{ $errors->first('organization_avatar_file', ':message') }}</span>
				</div>
				<div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
					{!! Form::label('name', trans('organizations.name'), ['class' => 'control-label required']) !!}
					<div class="controls">
						{!! Form::text('name', null, ['class' => 'form-control']) !!}
						<span class="help-block">{{ $errors->first('name', ':message') }}</span>
					</div>
				</div>
				<div class="form-group required {{ $errors->has('phone_number') ? 'has-error' : '' }}">
					{!! Form::label('phone_number', trans('organizations.phone_number'), ['class' => 'control-label required']) !!}
					<div class="controls">
						{!! Form::text('phone_number', null, ['class' => 'form-control','data-fv-integer' => 'true']) !!}
						<span class="help-block">{{ $errors->first('phone_number', ':message') }}</span>
					</div>
				</div>
				<div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
					{!! Form::label('email', trans('organizations.email'), ['class' => 'control-label required']) !!}
					<div class="controls">
						{!! Form::email('email', null, ['class' => 'form-control']) !!}
						<span class="help-block">{{ $errors->first('email', ':message') }}</span>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<h2>{{trans('organizations.organization_owner_detals')}}</h2>
				<div class="form-group required {{ $errors->has('owner_first_name') ? 'has-error' : '' }}">
					{!! Form::label('owner_first_name', trans('organizations.owner_first_name'), ['class' => 'control-label required']) !!}
					<div class="controls">
						{!! Form::text('owner_first_name', null, ['class' => 'form-control']) !!}
						<span class="help-block">{{ $errors->first('owner_first_name', ':message') }}</span>
					</div>
				</div>
				<div class="form-group required {{ $errors->has('owner_last_name') ? 'has-error' : '' }}">
					{!! Form::label('owner_last_name', trans('organizations.owner_last_name'), ['class' => 'control-label required']) !!}
					<div class="controls">
						{!! Form::text('owner_last_name', null, ['class' => 'form-control']) !!}
						<span class="help-block">{{ $errors->first('owner_last_name', ':message') }}</span>
					</div>
				</div>

				<div class="form-group required {{ $errors->has('owner_phone_number') ? 'has-error' : '' }}">
					{!! Form::label('owner_phone_number', trans('organizations.owner_phone_number'), ['class' => 'control-label required'])
                    !!}
					<div class="controls">
						{!! Form::text('owner_phone_number', null, ['class' => 'form-control','data-fv-integer' => 'true']) !!}
						<span class="help-block">{{ $errors->first('owner_phone_number', ':message') }}</span>
					</div>
				</div>
				<div class="form-group required {{ $errors->has('owner_email') ? 'has-error' : '' }}">
					{!! Form::label('owner_email', trans('organizations.owner_email'), ['class' => 'control-label required']) !!}
					<div class="controls">
						{!! Form::email('owner_email', null, ['class' => 'form-control']) !!}
						<span class="help-block">{{ $errors->first('owner_email', ':message') }}</span>
					</div>
				</div>
				<div class="form-group required {{ $errors->has('owner_password') ? 'has-error' : '' }}">
					{!! Form::label('owner_password', trans('organizations.owner_password'), ['class' => 'control-label required']) !!}
					<div class="controls">
						{!! Form::password('owner_password', ['class' => 'form-control']) !!}
						<span class="help-block">{{ $errors->first('owner_password', ':message') }}</span>
					</div>
				</div>
				<div class="form-group required {{ $errors->has('owner_password_confirmation') ? 'has-error' : '' }}">
					{!! Form::label('owner_password_confirmation', trans('organizations.owner_password_confirmation'), ['class' => 'control-label
                    required']) !!}
					<div class="controls">
						{!! Form::password('owner_password_confirmation', ['class' => 'form-control']) !!}
						<span class="help-block">{{ $errors->first('owner_password_confirmation', ':message') }}</span>
					</div>
				</div>
			</div>
		</div>
			@if(!isset($organization))
				<div class="row">
					<div class="col-12">
						<h2>{{trans('payplan.payplans')}}</h2>
						<div class="form-group required {{ $errors->has('plan_id') ? 'has-error' : '' }}">
							<span class="help-block">{{ $errors->first('plan_id', ':message') }}</span>
						</div>
					</div>
					@foreach($payment_plans_list as $item)
						<div class="col-md-4 col-sm-6 col-12">
							<div class="pay_plan">
								<div class="card">
									@if(collect($payment_plans_list)->max('organizations') == $item->organizations && $item->organizations > 0)
										<div class="badges badge_left">
											<div class="badge_content badge_purple bg-purple">Trending</div>
										</div>
									@endif
									<div class="card-header bg-primary text-center text-white">
										<input type="hidden" class="plan_id" value="{{$item->id}}">
										<h4>{{ $item->name }}</h4>
									</div>
									<div class="card-body text-center">
										<div class="m-t-10">
                                        <span class="font_28">
                                            @if($item->currency==="usd")
												<sup>$</sup>
											@else
												<sup>&euro;</sup>
											@endif
											{{ ($item->amount/100)}}
                                        </span>
											<span class="font_18"> / </span>
											<span class="text_light">
												{{ ($item->interval_count==1?$item->interval_count.' '.$item->interval:$item->interval_count.' '.$item->interval.'s') }}
											</span>
										</div>
										<div class="m-t-20 text-primary">
											<h4>{{ trans('payplan.user_access') }}</h4>
										</div>
										<div class="m-t-10 text_light">
											{{ ($item->no_people!==0?$item->no_people : trans('payplan.unlimited'))}} {{ trans('payplan.members') }}
										</div>
										<div class="m-t-20 text-primary">
											<h4>{{ trans('payplan.trials') }}</h4>
										</div>
										<div class="m-t-10 text_light">
											{{ isset($item->trial_period_days)?$item->trial_period_days .' '.trans('payplan.days_free_trial'): trans('payplan.none') }}
										</div>
										<div class="m-t-20 text-primary">
											<h4>{{ trans('payplan.description') }}</h4>
										</div>
										<div class="m-t-10 text_light">
											{{ isset($item->statement_descriptor) ? $item->statement_descriptor : trans('payplan.none') }}
										</div>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
				<div class="row">
					<input type="hidden" value="" name="plan_id" class="payplan_id">
					<div class="col-md-6 plan_duration">
						<div class="form-group required {{ $errors->has('duration') ? 'has-error' : '' }}">
							{!! Form::label('duration', trans('organizations.duration'), ['class' => 'control-label required'])
                            !!}
							<div class="controls">
								{!! Form::number('duration', null, ['class' => 'form-control','data-fv-integer' => 'true', 'min'=>1]) !!}
								<span class="help-block">{{ $errors->first('duration', ':message') }}</span>
							</div>
						</div>
					</div>
				</div>
				@endif
			<div class="row">
				<div class="col-md-12">
					<!-- Form Actions -->
					<div class="form-group">
						<div class="controls">
							<button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
							<a href="{{ route($type.'.index') }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
						</div>
					</div>
					<!-- ./ form actions -->
				</div>
			</div>
		{!! Form::close() !!}
	</div>
</div>
@section('scripts')
	<script>
		$(document).ready(function(){
		    $(".plan_duration").hide();
		    $(".pay_plan").on("click",function(){
		        $(".pay_plan").removeClass("active");
		       $(this).addClass('active');
                $(".plan_duration").show();
		       $(".payplan_id").val($(this).find(".plan_id").val())
			});
		})
	</script>
	@endsection