@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop
@section('styles')
    <style>
        @media print {
            .header,.left-aside,.breadcrumb,.print_btn {
                display: none;
            }
        }
    </style>
    @stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h4>{{ trans('paypal_transaction.transaction_details') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-9 m-t-10">
                            <h5>{{ trans('paypal_transaction.recurring_received') }}</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ date(config('settings.date_time_format'), strtotime($paypalTransactions['ORDERTIME'])) }}
                                </div>
                                <div class="col-md-6">
                                    {{'Transaction ID: '. $paypalTransactions['TRANSACTIONID'] }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 m-t-10">
                                    {{ trans('paypal_transaction.payment_status') }}
                                    <span class="text-success">{{ $paypalTransactions['PAYMENTSTATUS'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 text-lg-right m-t-10">
                            <span>{{ trans('paypal_transaction.gross_amount') }}</span>
                            <h4>
                                <?php
                                echo getCurrencySymbol($paypalTransactions['CURRENCYCODE']);
                                ?>{{ $paypalTransactions['AMT'].' '.$paypalTransactions['CURRENCYCODE'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 m-t-10">
                            <h5>We have no postal address on file</h5>
                            <h5 class="m-t-20">{{ trans('paypal_transaction.payment_details') }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 m-t-10">
                            <div class="row">
                                <div class="col-6">
                                    <strong>{{ trans('paypal_transaction.gross_amount') }}</strong>
                                </div>
                                <div class="col-6 text-right">
                                    <?php
                                    echo getCurrencySymbol($paypalTransactions['CURRENCYCODE']);
                                    ?>{{ $paypalTransactions['AMT'].' '.$paypalTransactions['CURRENCYCODE'] }}
                                </div>
                            </div>
                            <div class="row m-t-10">
                                <div class="col-6">
                                    <strong>{{ trans('paypal_transaction.payPal_fee') }}</strong>
                                </div>
                                <div class="col-6 text-right">
                                    <?php
                                    echo '-'.getCurrencySymbol($paypalTransactions['CURRENCYCODE']);
                                    ?>{{ $paypalTransactions['FEEAMT'].' '.$paypalTransactions['CURRENCYCODE'] }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>{{ trans('paypal_transaction.net_amount') }}</strong>
                                </div>
                                <div class="col-6 text-right">
                                    <?php
                                    echo getCurrencySymbol($paypalTransactions['CURRENCYCODE']);
                                    ?>{{ ($paypalTransactions['AMT'] - $paypalTransactions['FEEAMT']).' '.$paypalTransactions['CURRENCYCODE'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-t-30">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>{{ trans('paypal_transaction.recurring_payment_iD') }}</strong>
                        </div>
                        <div class="col-md-8">
                            <?php
                            $subscription_id = \App\Models\PaypalTransaction::where('txn_id',$paypalTransactions['TRANSACTIONID'])->first()->subscription_id;
                            $subscription = \App\Models\Subscription::find($subscription_id);
                            echo  $subscription->profile_id;
                            ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>{{ trans('paypal_transaction.reason') }}</strong>
                        </div>
                        <div class="col-md-8">
                            {{ trans('paypal_transaction.recurring') }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>{{ trans('paypal_transaction.paid_by') }}</strong>
                        </div>
                        <div class="col-md-8">
                            <div>
                                {{ $paypalTransactions['FIRSTNAME'].' '.$paypalTransactions['LASTNAME'] }}
                            </div>
                            <div>
                                The sender of this payment is <strong>{{ $paypalTransactions['PAYERSTATUS'] }}</strong>
                            </div>
                            <div>
                                {{ $paypalTransactions['EMAIL'] }}
                            </div>
                        </div>
                    </div>
                    <div class="row m-t-20">
                        <div class="col-md-4">
                            <strong>{{ trans('paypal_transaction.payment_sent_to') }}</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $paypalTransactions['RECEIVEREMAIL'] }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            Payments without a shipping address are not covered by PayPal's seller protection policies and programs
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Memo</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $paypalTransactions['SUBJECT'] }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 m-t-30">
                            <a class="btn btn-success text-white print_btn m-b-10" data-toggle="button" onclick="javascript:window.print();">
                                Print
                            </a>
                            <a href="{{ URL::previous() }}" class="btn btn-warning m-b-10 print_btn">
                                <i class="fa fa-arrow-left"></i>
                                {{trans('table.back')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#invoice_data').DataTable({
                "order": []
            });
            $('#events_data,#transactions_data').DataTable({
                "order": []
            });
        });
    </script>
@stop
