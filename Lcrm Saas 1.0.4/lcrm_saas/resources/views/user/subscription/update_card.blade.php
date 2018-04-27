@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <div class="pull-right">
            <a href="{{url($type)}}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6 col-lg-4 m-t-30">
            <div class="card">
                <div class="card-header bg-primary text-center text-white">
                    <h3 class="m-t-0">{{ trans('subscription.current_card') }}</h3>
                </div>
                <div class="card-body text-center">
                    <div class="m-t-10">
                        <h4 class="text-primary">
                            {{ trans('subscription.card_brand') }}
                        </h4>
                        <div class="m-t-10">
                            {{ $organization->card_brand }}
                        </div>
                    </div>
                    <div class="m-t-20">
                        <h4 class="text-primary">
                            {{ trans('subscription.card_last_four') }}
                        </h4>
                        <div class="m-t-10">
                            {{ $organization->card_last_four }}
                        </div>
                    </div>
                    @if($settings['stripe_secret']!="" && $settings['stripe_publishable']!="")
                        <div class="m-t-20">
                            {!! Form::open(['url' => url('update_card'), 'method' => 'post']) !!}
                            <script
                                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                    data-key="{!! $settings['stripe_publishable'] !!}"
                                    data-image="{{asset($settings['app_logo'])}}"
                                    data-name="{{ config('app.name') }}"
                                    data-panel-label="Update"
                                    data-description="{{ trans('subscription.update_card') }}"
                                    data-label="{{ trans('subscription.update_card') }}"
                                    data-locale="auto">
                            </script>
                            {!! Form::close() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
