<div class="card">
	<div class="card-body">
		@if (isset($payment))
			{!! Form::model($payment, ['url' => $type . '/' . $payment->id, 'method' => 'put','files'=> true]) !!}
		@endif
				<div class="row">
					<div class="col-12">
						<h2>{{trans('payplan.payplans')}}</h2>
						<div class="form-group required {{ $errors->has('plan_id') ? 'has-error' : '' }}">
							<span class="help-block">{{ $errors->first('plan_id', ':message') }}</span>
						</div>
					</div>
					@foreach($payment_plans_list as $item)
						<div class="col-md-4 col-sm-6 col-12">
							<div class="pay_plan @if($payment->generic_trial_plan==$item->id) active @endif
							@if(isset($payment->staffWithUser) && (($payment->staffWithUser->count() + $unanswered_invites) <= $item->no_people)
                                        && $item->no_people) plan_allowed @else plan_not_allowed @endif">
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
											{{ ($item->no_people!==0?$item->no_people : trans('payplan.unlimited')) }} {{ trans('payplan.members') }}
										</div>
										<div class="m-t-20 text-primary">
											<h4>{{ trans('payplan.trials') }}</h4>
										</div>
										<div class="m-t-10 text_light">
											{{ isset($item->trial_period_days)?$item->trial_period_days .' '.trans('payplan.days_free_trial'): trans('payplan.none')}}
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
					<input type="hidden" value="{{$payment->generic_trial_plan}}" name="plan_id" class="payplan_id">
					<div class="col-md-6 plan_duration">
						<div class="form-group required {{ $errors->has('duration') ? 'has-error' : '' }}">
							{!! Form::label('duration', trans('organizations.duration_to_add'), ['class' => 'control-label required'])
                            !!}
							<div class="controls">
								{!! Form::number('duration', null, ['class' => 'form-control','data-fv-integer' => 'true', 'min'=>1]) !!}
								<span class="help-block">{{ $errors->first('duration', ':message') }}</span>
							</div>
						</div>
					</div>
				</div>
			<div class="row">
				<div class="col-md-12">
					<!-- Form Actions -->
					<div class="form-group">
						<div class="controls">
							<button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
							<a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
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
		    $(".pay_plan.plan_allowed").on("click",function(){
		        $(".pay_plan.plan_allowed").removeClass("active");
		       $(this).addClass('active');
		       $(".payplan_id").val($(this).find(".plan_id").val())
			});
		    $(".plan_not_allowed").on("click",function(){
		        var count = {{ $payment->staffWithUser->count() + $unanswered_invites }}
		       alert("Your current number of users are "+count+'(including staff invitations). It exceeds the number of people in the plan.');
			});
		})
	</script>
	@endsection