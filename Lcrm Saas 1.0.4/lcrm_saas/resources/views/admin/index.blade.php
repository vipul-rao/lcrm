@extends('layouts.user')
@section('title')
    {{trans('dashboard.dashboard')}}
@stop
@section('content')
    @include('flash::message')
    <div class="row">
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{url('organizations')}}">
                <div class="card bg-primary">
                    <div class="card-body text-white">
                        <div class="row">
                            <div class="col-12">
                                <div class="pull-left">
                                    <p class="text-left user_font">{{trans('left_menu.organizations')}}</p>
                                    <div id="countno1" class="user_count">
                                        {{ $organizations }}
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <i class="material-icons user_icon pull-right">contacts</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{url('admin/payplan')}}">
                <div class="card bg-success">
                    <div class="card-body text-white">
                        <div class="row">
                            <div class="col-12">
                                <div class="pull-left">
                                    <p class="text-left user_font">{{trans('left_menu.payplans')}}</p>
                                    <div id="countno2" class="user_count">
                                        {{ $payplans }}
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <i class="material-icons user_icon pull-right">attach_money</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{url('admin/subscription')}}">
                <div class="card bg-warning">
                    <div class="card-body text-white">
                        <div class="row">
                            <div class="col-12">
                                <div class="pull-left">
                                    <p class="text-left user_font">{{trans('left_menu.subscription')}}</p>
                                    <div id="countno3" class="user_count">
                                        {{ $subscriptions }}
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <i class="material-icons user_icon pull-right">web</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h4>{{trans('dashboard.organizations')}}</h4>
                </div>
                <div class="card-body">
                    <div id='users'></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h4>{{trans('dashboard.payments')}}</h4>
                </div>
                <div class="card-body">
                    <div id="payments"></div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('scripts')
<script>
    var useOnComplete = false,
            useEasing = false,
            useGrouping = false;

    /*users chart*/
    $(function () {
        var data_users = [
            ['Organizations'],
                @foreach($graphics as $item)
            [{{$item['organizations']}}],
            @endforeach
        ];
        var users = c3.generate({
            bindto: '#users',
            data: {
                rows: data_users,
                type: 'area-spline'
            },
            color: {
                pattern: ['#fc4141']
            },
            axis: {
                x: {
                    tick: {
                        format: function (d) {
                            return formatMonth(d);
                        }
                    }
                }
            },
            legend: {
                show: true,
                position: 'bottom'
            },
            padding: {
                top: 10
            }
        });

        function formatMonth(d) {
            @foreach($graphics as $id => $item)
            if ('{{$id}}' == d) {
                return '{{$item['month']}}' + ' ' + '{{$item['year']}}'
            }
            @endforeach
        }

        setTimeout(function () {
            users.resize();
        }, 2000);

        setTimeout(function () {
            users.resize();
        }, 4000);

        setTimeout(function () {
            users.resize();
        }, 6000);
        $("[data-toggle='offcanvas']").click(function (e) {
            users.resize();
        });
    });

    /*payments chart*/
    $(function () {
        var data_payments = [
            ['Payments sum ($)','Subscriptions'],
                @foreach($graphics as $item)
            [{{$item['payments_sum']}},{{$item['subscriptions']}}],
            @endforeach
        ];
        var payments = c3.generate({
            bindto: '#payments',
            data: {
                rows: data_payments,
                type: 'area-spline'
            },
            color: {
                pattern: ['#3295ff','#2daf57']
            },
            axis: {
                x: {
                    tick: {
                        format: function (d) {
                            return formatMonth(d);
                        }
                    }
                }
            },
            legend: {
                show: true,
                position: 'bottom'
            },
            padding: {
                top: 10
            }
        });

        function formatMonth(d) {
            @foreach($graphics as $id => $item)
            if ('{{$id}}' == d) {
                return '{{$item['month']}}' + ' ' + '{{$item['year']}}'
            }
            @endforeach
        }

        setTimeout(function () {
            payments.resize();
        }, 2000);

        setTimeout(function () {
            payments.resize();
        }, 4000);

        setTimeout(function () {
            payments.resize();
        }, 6000);
        $("[data-toggle='offcanvas']").click(function (e) {
            payments.resize();
        });
    });
</script>
@stop
