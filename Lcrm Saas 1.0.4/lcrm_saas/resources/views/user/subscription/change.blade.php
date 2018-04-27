@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <div class="pull-right">
            @if($active_subscription->subscription_type=='stripe')
                <a href="{{url('update_card')}}" class="btn btn-warning m-b-10">{{trans('subscription.update_card')}}</a>
            @endif
            <a href="{{url($type)}}" class="btn btn-warning m-b-10"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">flag</i>
                {{ $title }}
            </h4>
            <span class="pull-right">
                <i class="fa fa-fw fa-chevron-up clickable"></i>
                <i class="fa fa-fw fa-times removecard clickable"></i>
            </span>
        </div>
        <div class="card-body">
            <div class=" table-responsive">
                <table id="data" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('payplan.name') }}</th>
                        <th>{{ trans('payplan.amount') }}</th>
                        <th>{{ trans('payplan.interval') }}</th>
                        <th>{{ trans('payplan.no_people') }}</th>
                        <th>{{ trans('payplan.description') }}</th>
                        <th>{{ trans('table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($settings['stripe_secret']!="" && $settings['stripe_publishable']!="")
                        @foreach($payment_plans_list as $key=>$item)
                        @if($item->is_visible)
                             <tr>
                                 <td>{{$item->name}}</td>
                                 <td>{{$item->amount/100}} {!! $item['currency'] !!}</td>
                                 <td>
                                     {{ ($item->interval_count==1?$item->interval_count.' '.$item->interval:$item->interval_count.' '.$item->interval.'s') }}
                                 </td>
                                 <td>{{$item->no_people}}</td>
                                 <td>{{$item->statement_descriptor}}</td>
                                 <td>
                                     @if($item->plan_id != $active_plan->plan_id)
                                         @if(isset($organization->staffWithUser) && (($organization->staffWithUser->count() + $unanswered_invites) > $item->no_people)
                                         && $item->no_people)
                                             <button class="btn btn-warning disabled" data-toggle="tooltip" title="Your Current number of users are {{($organization->staffWithUser->count() + $unanswered_invites)}} . It exceeds the no. of people in plan."><i
                                                         class="fa fa-check-square-o"></i> {{trans('subscription.activate')}}</button>
                                         @else
                                             @if($organization->subscription_type=='paypal')
                                                 @if($organization->subscriptions->first()->status=='Canceled')
                                                     {!! Form::open(['url' => 'payment/'.$item->id.'/paypal', 'method' => 'post']) !!}
                                                     <button type="submit" class="btn btn-warning"><i
                                                                 class="fa fa-check-square-o"></i> {{trans('subscription.activate')}}</button>
                                                     {!! Form::close() !!}
                                                 @else
                                                     {!! Form::open(['url' => $type.'/change_plan/'.$item->id, 'method' => 'post']) !!}
                                                     <button type="submit" class="btn btn-warning"><i
                                                                 class="fa fa-check-square-o"></i> {{trans('subscription.activate')}}</button>
                                                     {!! Form::close() !!}
                                                 @endif
                                             @else
                                                 {!! Form::open(['url' => $type.'/change_plan/'.$item->id, 'method' => 'post']) !!}
                                                 <button type="submit" class="btn btn-warning"><i
                                                             class="fa fa-check-square-o"></i> {{trans('subscription.activate')}}</button>
                                                 {!! Form::close() !!}
                                             @endif
                                         @endif
                                     @else
                                         @if((isset($organization)&&count($organization->subscriptions)&&$organization->subscriptions->first()->ends_at) ||
                                         ($organization->subscriptions->first()->status=='Canceled'))
                                             @if($organization->subscription_type=='paypal')
                                                 {!! Form::open(['url' => 'payment/'.$item->id.'/paypal', 'method' => 'post']) !!}
                                                 <button type="submit" class="btn btn-warning"><i
                                                             class="fa fa-check-square-o"></i> {{trans('subscription.activate')}}</button>
                                                 {!! Form::close() !!}
                                             @else
                                                 {!! Form::open(['url' => $type.'/change_plan/'.$item->id, 'method' => 'post']) !!}
                                                 <button type="submit" class="btn btn-warning"><i
                                                             class="fa fa-check-square-o"></i> {{trans('subscription.activate')}}</button>
                                                 {!! Form::close() !!}
                                             @endif
                                             <div class="badge badge-primary pull-left m-t-10">
                                                 {{ trans('subscription.previous_plan') }}
                                             </div>
                                         @else
                                             <button class="btn btn-success m-b-10">
                                                 {{ trans('subscription.current_plan') }}
                                             </button>
                                             @if($active_subscription->status=='Suspended')
                                                 {!! Form::open(['url' => 'resume_paypal_subscription/resume', 'method' => 'post']) !!}
                                                 <button type="submit" class="btn btn-success m-b-10"><i
                                                             class="fa fa-undo"></i> {{trans('subscription.reactivate')}}</button>
                                                 {!! Form::close() !!}
                                             @endif
                                         @endif
                                     @endif
                                 </td>
                             </tr>
                        @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
    function getCurrencySymbol($currencyCode, $locale = 'en_US')
    {
        $formatter = new \NumberFormatter($locale . '@currency=' . $currencyCode, \NumberFormatter::CURRENCY);
        return $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
    }
    ?>
    @if(isset($active_subscription->subscription_type) && $active_subscription->subscription_type=='paypal')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h4>{{ trans('subscription.recurring_payment_details') }}</h4>
                    </div>
                    <div class="card-body">
                        <div>
                            <strong>{{ trans('subscription.status') }}:</strong>
                            <span class="text-success">
                                {{ $recurring_payment_details['STATUS'] }}
                            </span>
                        </div>
                        <div>
                            {{ trans('subscription.customer') }} <strong>{{ $recurring_payment_details['SUBSCRIBERNAME'] }}</strong>
                        </div>
                        <div>
                            {{ trans('subscription.profile_start_date') }} <strong>{{ date( config('settings.date_time_format'),strtotime($recurring_payment_details['PROFILESTARTDATE'])) }}</strong>
                        </div>
                        <h5 class="m-t-20">{{ trans('subscription.payment_details') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>
                                        {{ trans('subscription.payment_type') }}
                                    </th>
                                    <th>
                                        {{ trans('subscription.Amount due each cycle') }}
                                    </th>
                                    <th>
                                        {{ trans('subscription.total_cycles') }}
                                    </th>
                                    <th>
                                        {{ trans('subscription.remaining_cycles') }}
                                    </th>
                                    <th>
                                        {{ trans('subscription.cycle_frequency') }}
                                    </th>
                                    <th>
                                        {{ trans('subscription.amount_received') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ trans('subscription.initial_payment') }}</td>
                                    <td>
                                        <?php
                                        echo getCurrencySymbol($recurring_payment_details['CURRENCYCODE']);
                                        ?>{{ $recurring_payment_details['OUTSTANDINGBALANCE'].' '.$recurring_payment_details['CURRENCYCODE'] }}
                                    </td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>
                                        <?php
                                        echo getCurrencySymbol($recurring_payment_details['CURRENCYCODE']);
                                        ?>{{ $recurring_payment_details['OUTSTANDINGBALANCE'].' '.$recurring_payment_details['CURRENCYCODE'] }}
                                    </td>
                                </tr>
                                @if(isset($recurring_payment_details['TRIALBILLINGPERIOD']))
                                    <tr>
                                        <td>
                                            {{ trans('subscription.trial_period') }}
                                        </td>
                                        <td>
                                            <?php
                                            echo getCurrencySymbol($recurring_payment_details['CURRENCYCODE']);
                                            ?>{{ $recurring_payment_details['TRIALAMT'].' '.$recurring_payment_details['CURRENCYCODE'] }}
                                        </td>
                                        <td>
                                            {{ $recurring_payment_details['TRIALTOTALBILLINGCYCLES'] }}
                                        </td>
                                        <td>
                                            0
                                        </td>
                                        <td>
                                            @if($recurring_payment_details['TRIALBILLINGFREQUENCY']==1)
                                                @if($recurring_payment_details['TRIALBILLINGPERIOD']=='Day')
                                                    {{ trans('subscription.daily') }}
                                                @else
                                                    {{ $recurring_payment_details['TRIALBILLINGPERIOD'].'ly' }}
                                                @endif
                                            @else
                                                {{ 'Every '.$recurring_payment_details['TRIALBILLINGFREQUENCY'].' '.$recurring_payment_details['TRIALBILLINGPERIOD'].'s' }}
                                            @endif
                                        </td>
                                        <td>
                                            <?php
                                            echo getCurrencySymbol($recurring_payment_details['CURRENCYCODE']);
                                            ?>{{ $recurring_payment_details['TRIALAMT'].' '.$recurring_payment_details['CURRENCYCODE'] }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>
                                        {{ trans('subscription.regular_recurring_payment') }}
                                    </td>
                                    <td>
                                        <?php
                                        echo getCurrencySymbol($recurring_payment_details['CURRENCYCODE']);
                                        ?>{{ $recurring_payment_details['REGULARAMT'].' '.$recurring_payment_details['CURRENCYCODE'] }}
                                    </td>
                                    <td>
                                        {{ trans('subscription.indefinite') }}
                                    </td>
                                    <td>
                                        {{ trans('subscription.indefinite') }}
                                    </td>
                                    <td>
                                        @if($recurring_payment_details['REGULARBILLINGFREQUENCY']==1)
                                            @if($recurring_payment_details['REGULARBILLINGPERIOD']=='Day')
                                                {{ trans('subscription.daily') }}
                                            @else
                                                {{ $recurring_payment_details['REGULARBILLINGPERIOD'].'ly' }}
                                            @endif
                                        @else
                                            {{ 'Every '.$recurring_payment_details['REGULARBILLINGFREQUENCY'].' '.$recurring_payment_details['REGULARBILLINGPERIOD'].'s' }}
                                        @endif
                                    </td>
                                    <td>
                                        <?php
                                        echo getCurrencySymbol($recurring_payment_details['CURRENCYCODE']);
                                        ?>
                                        {{ $recurring_payment_details['REGULARAMTPAID'].' '.$recurring_payment_details['CURRENCYCODE'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">
                                        <strong>Total</strong>
                                    </td>
                                    <td>
                                        <?php
                                        echo getCurrencySymbol($recurring_payment_details['CURRENCYCODE']);
                                        ?>{{ $recurring_payment_details['AGGREGATEAMT'].' '.$recurring_payment_details['CURRENCYCODE'] }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <h5 class="m-t-30">{{ trans('subscription.billing_details') }}</h5>
                        <div class="row">
                            <div class="col-12">

                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <th class="text-right">{{ trans('subscription.item_name') }}</th>
                                        <td>{{ $recurring_payment_details['DESC'] }}</td>
                                    </tr>
                                    @if(isset($recurring_payment_details['NEXTBILLINGDATE']))
                                        <tr>
                                            <th class="text-right">{{ trans('subscription.next_payment_due') }}</th>
                                            <td>{{ date( config('settings.date_time_format'),strtotime($recurring_payment_details['NEXTBILLINGDATE'])) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="text-right">Last payment due</th>
                                        <td>
                                            @if($recurring_payment_details['STATUS']=='Cancelled')
                                                {{ date( config('settings.date_time_format'),strtotime($recurring_payment_details['PROFILESTARTDATE'])) }}
                                            @elseif($recurring_payment_details['STATUS']=='Suspended')
                                                {{ trans('subscription.no_end_date') }}
                                            @else
                                                {{ trans('subscription.indefinite_continue_until_canceled') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-right">{{ trans('subscription.add_payments_that_failed_to_next_bill') }}</th>
                                        <td>No</td>
                                    </tr>
                                    <tr>
                                        <th class="text-right">{{ trans('subscription.this_profile_will_be_suspended_after') }}</th>
                                        <td>{{ trans('subscription.no_limit_failure') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
@section('scripts')
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
