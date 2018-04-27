<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('subscription.org_name')}}</label>
                    <div class="controls">
                        {{ isset($subscription->organization->name)?$subscription->organization->name:null }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('organizations.email')}}</label>
                    <div class="controls">
                        {{ is($subscription->organization)?$subscription->organization->email:null }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('subscription.pay_plan')}}</label>
                    <div class="controls">
                        {{ $subscription->name }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('subscription.end_subscription')}}</label>
                    <div class="controls">
                        {{$subscription->ended_at ? $subscription->ended_at : trans('subscription.subscription_active')}}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="controls">
                @if (@$action == trans('action.show'))
                    <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                @else
                    <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> {{trans('table.delete')}}</button>
                @endif
            </div>
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
@if(isset($subscription->subscription_type) && isset($recurring_payment_details) && $subscription->subscription_type=='paypal')
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