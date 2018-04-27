@extends('layouts.user')
@section('title')
    {{trans('dashboard.dashboard')}}
@stop

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="pull-right nopadmar text-info">{{trans('invoice.invoices_total')}}</h3>
                        </div>
                        <div class="col-md-6 ">
                            <div class="text-right counter-dash">
                                {{$organizationSettings['currency'] ?? null}} {{ $invoices_total_collection}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="pull-right nopadmar text-warning">{{trans('invoice.open_invoice')}}</h3>
                        </div>
                        <div class="col-md-6 ">
                            <div class="text-right counter-dash">
                                {{$organizationSettings['currency'] ?? null}} {{ $open_invoice_total}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="pull-right nopadmar text-success">{{trans('invoice.paid_invoice')}}</h3>
                        </div>
                        <div class="col-md-6 ">
                            <div class="text-right counter-dash">
                                {{$organizationSettings['currency'] ?? null}} {{ $paid_invoices_total}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h4>{{trans('dashboard.invoices_my_month')}}</h4>
                </div>
                <div class="card-body">
                    <div id="invoice1"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h4>{{trans('dashboard.quotations')}}</h4>
                </div>
                <div class="card-body">
                    <div id="quotation"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(function () {

            /*c3 invoice chart1*/
            var data1 = [
                ['Due by months'],
                    @foreach($data as $item)
                [{{$item['invoices']}}],
                @endforeach
            ];

            var data2 = [
                ['Quotations'],
                    @foreach($data as $item)
                [{{$item['quotations']}}],
                @endforeach
            ];

            var chart1 = c3.generate({
                bindto: '#invoice1',
                data: {
                    rows: data1,
                    type: 'spline'
                },
                color: {
                    pattern: ['#fc4141']
                },
                axis: {
                    x: {
                        tick: {
                            format: function (d) {
                                return formatMonthData(d);
                            }
                        }
                    },
                    y: {
                        tick: {
                            format: d3.format("$,")
                            //format: function (d) { return "Custom Format: " + d; }
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

            var chart2 = c3.generate({
                bindto: '#quotation',
                data: {
                    rows: data2,
                    type: 'spline'
                },
                color: {
                    pattern: ['#3295ff']
                },
                axis: {
                    x: {
                        tick: {
                            format: function (d) {
                                return formatMonthData(d);
                            }
                        }
                    },
                    y: {
                        tick: {
                            format: d3.format("")
                            //format: function (d) { return "Custom Format: " + d; }
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

            function formatMonthData(d) {

                @foreach($data as $id => $item)
                if({{$id}}==d)
                {
                    return '{{$item['month']}}'+' '+{{$item['year']}}
                }
                @endforeach
            }

            setTimeout(function () {
                chart1.resize();
            }, 2000);

            setTimeout(function () {
                chart1.resize();
            }, 4000);

            setTimeout(function () {
                chart1.resize();
            }, 6000);
            $("[data-toggle='offcanvas']").click(function (e) {
                chart1.resize();
            });


            setTimeout(function () {
                chart2.resize();
            }, 2000);

            setTimeout(function () {
                chart2.resize();
            }, 4000);

            setTimeout(function () {
                chart2.resize();
            }, 6000);
            $("[data-toggle='offcanvas']").click(function (e) {
                chart2.resize();
            });
            /*c3 invoice chart2 end*/

            /*sales progress*/


        })
    </script>
@stop
