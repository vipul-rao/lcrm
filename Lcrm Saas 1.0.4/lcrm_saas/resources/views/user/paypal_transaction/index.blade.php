@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
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
                    <h4>Transactions</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="transactions_data" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Payment</th>
                                <th>Gross</th>
                                <th>Fee</th>
                                <th>Net</th>
                                <th>{{ trans('table.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($paypalTransactions))
                                @foreach($paypalTransactions as $transaction)
                                <tr>
                                    <td>{{ date(config('settings.date_time_format'),strtotime($transaction['ORDERTIME'])) }}</td>
                                    <td>
                                        @if($transaction['TRANSACTIONTYPE']=='recurring_payment')
                                            Payment from
                                        @else
                                            Recurring payment from
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction['FIRSTNAME'].' '.$transaction['LASTNAME'] }}
                                    </td>
                                    <td>
                                        {{ $transaction['PAYMENTSTATUS'] }}
                                    </td>
                                    <td>
                                        <?php
                                        echo getCurrencySymbol($transaction['CURRENCYCODE']);
                                        ?>{{ $transaction['AMT'].' '.$transaction['CURRENCYCODE'] }}
                                    </td>
                                    <td>
                                        <?php
                                        echo getCurrencySymbol($transaction['CURRENCYCODE']);
                                        ?>{{ $transaction['FEEAMT'].' '.$transaction['CURRENCYCODE'] }}
                                    </td>
                                    <td>
                                        <?php
                                        echo getCurrencySymbol($transaction['CURRENCYCODE']);
                                        ?>{{ ($transaction['AMT'] - $transaction['FEEAMT']).' '.$transaction['CURRENCYCODE'] }}
                                    </td>
                                    <th>
                                        <a href="{{ url('activity/payment/'.$transaction['TRANSACTIONID']) }}">
                                            <i class="fa fa-fw fa-eye text-primary"></i>
                                        </a>
                                    </th>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
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
            $('#transactions_data').DataTable({
//                "order": []
            });
        });
    </script>
@stop
