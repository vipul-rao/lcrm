@extends('layouts.subscription')

{{-- Web site Title --}}
@section('title')
{{ $title }}
@stop

{{-- Content --}}

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row">
                @foreach($payment_plans_list as $item)
                    @if($item->is_visible==1)
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="pay_plan">
                                <div class="card">
                                    {!! Form::open(['url' => url('change_generic_plan/stripe'), 'method' => 'post']) !!}
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
                                            <h4>{{ trans('payplan.description') }}</h4>
                                        </div>
                                        <div class="m-t-10 text_light">
                                            {{ isset($item->statement_descriptor) ? $item->statement_descriptor : 'None' }}
                                        </div>
                                        <div class="form-inline m-t-10 justify-content-center">
                                            <div class="radio">
                                                <div class="form-inline">
                                                    {!! Form::radio('subscription_type', 'stripe',(isset($organization->subscription_type)&&$organization->subscription_type=='stripe')?true:false,['id'=>'stripe','class' => 'stripe icheck'])  !!}
                                                    {!! Form::label('false', 'Stripe',['class'=>'ml-1 mr-2'])  !!}
                                                </div>
                                            </div>
                                            <div class="radio">
                                                <div class="form-inline">
                                                    {!! Form::radio('subscription_type', 'paypal', (isset($organization->subscription_type)&&$organization->subscription_type=='paypal')?true:false,['id'=>'paypal','class' => 'paypal icheck'])  !!}
                                                    {!! Form::label('false', 'Paypal',['class'=>'ml-1']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="subscribe_with_stripe">
                                            @if($stripe_secret!="" && $stripe_publishable!="")
                                                @if(isset($organization->staffWithUser) && (($organization->staffWithUser->count() + $unanswered_invites) > $item->no_people)
                                                                                     && $item->no_people)
                                                    <button class="btn btn-primary m-t-20" disabled>Subscribe</button>
                                                @else
                                                    {!! Form::hidden('pay_plan', $item['id']) !!}
                                                    <div class="m-t-20 text-center">
                                                        <script
                                                                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                                                data-key="{!!$stripe_publishable !!}"
                                                                data-image="{{asset($settings['app_logo'])}}"
                                                                data-amount="{!! $item['amount'] !!}"
                                                                data-name="{!! $item['name'] !!}"
                                                                data-description="{!! $item['statement_descriptor'] !!}"
                                                                data-label="Subscribe"
                                                                data-locale="auto">
                                                        </script>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                    <div class="subscribe_with_paypal text-center m-b-15">
                                        @if(isset($paypal_mode))
                                            @if(isset($organization->staffWithUser) && (($organization->staffWithUser->count() + $unanswered_invites) > $item->no_people)
                                                && $item->no_people)
                                                <button class="btn btn-primary" disabled>Paypal</button>
                                            @else
                                                {!! Form::open(['url' => url('change_generic_plan/'.$item->id.'/paypal'), 'method' => 'post']) !!}
                                                <button type="submit" class="btn btn-primary">Paypal</button>
                                                {!! Form::close() !!}
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@stop

{{-- page level scripts --}}
@section('scripts')
    <script>
        $(document).ready(function(){
            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $(".subscribe_with_stripe,.subscribe_with_paypal").hide();
            var stripe = $(".iradio_minimal-blue.checked #stripe").val();
            var paypal = $(".iradio_minimal-blue.checked #paypal").val();
            if(stripe == 'stripe'){
                $(".subscribe_with_stripe").show();
            }
            if(paypal == 'paypal'){
                $(".subscribe_with_paypal").show();
            }
            $(".stripe").on("ifChecked",function(){
                $(this).closest(".card").find('.subscribe_with_stripe').show();
                $(this).closest(".card").find(".subscribe_with_paypal").hide();
            });
            $(".paypal").on("ifChecked",function(){
                $(this).closest(".card").find(".subscribe_with_stripe").hide();
                $(this).closest(".card").find(".subscribe_with_paypal").show();
            });

        });
    </script>
@stop
