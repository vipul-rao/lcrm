@extends('layouts.frontend.user')
@section('styles')
    <link href="{{ asset('css/login_register.css') }}" rel="stylesheet" type="text/css">
@stop
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                @foreach($payment_plans_list as $item)
                    @if($item->is_visible==1)
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                            <div class="pay_plan">
                                <div class="card">
                                    @if(collect($payment_plans_list)->max('organizations') == $item->organizations && $item->organizations > 0)
                                        <div class="badges badge_left">
                                            <div class="badge_content badge_purple bg-purple">Trending</div>
                                        </div>
                                    @endif
                                    <div class="card-header bg-primary text-center text-white">
                                        <input type="hidden" class="plan_id" value="{{$item->id}}">
                                        <h4 class="text-white text-capitalize">{{ $item->name }}</h4>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mt-2">
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
                                        <div class="mt-3">
                                            <h4 class="text-primary">{{ trans('payplan.user_access') }}</h4>
                                        </div>
                                        <div class="mt-2 text_light">
                                            {{ ($item->no_people!==0?$item->no_people : trans('payplan.unlimited')) }} {{ trans('payplan.members') }}
                                        </div>
                                        <div class="mt-3">
                                            <h4 class="text-primary">{{ trans('payplan.trials') }}</h4>
                                        </div>
                                        <div class="mt-2 text_light">
                                            {{ isset($item->trial_period_days)?$item->trial_period_days .' '.trans('payplan.days_free_trial'): trans('payplan.none') }}
                                        </div>
                                        <div class="mt-3">
                                            <h4 class="text-primary">{{ trans('payplan.description') }}</h4>
                                        </div>
                                        <div class="mt-2 text_light">
                                            {{ isset($item->statement_descriptor) ? $item->statement_descriptor : trans('payplan.none') }}
                                        </div>
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
