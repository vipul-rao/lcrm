@extends('layouts.user')
@section('title')
    {{trans('dashboard.dashboard')}}
@stop
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/jquery-jvectormap.css') }}">
@stop
@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{url('product')}}" class="text-default">
                <div class="card">
                    <div class="card-body">
                        <div class="cnts ">
                            <div class="row">
                                <div class="col-4 col-sm-2">
                                    <i class="material-icons md-36 mar-top text-left text-warning box_icons">layers</i>
                                </div>
                                <div class="col-8 col-sm-10">
                                    <p class="text-right nopadmar user_font mb-0">{{trans('left_menu.products')}}</p>
                                    <div id="countno2" class="text-right counter-dash user_count">
                                        {{ $products }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{url('opportunity')}}" class="text-default">
                <div class="card">
                    <div class="card-body">
                        <div class="">
                            <div class="cnts ">
                                <div class="row">
                                    <div class="col-4 col-sm-2">
                                        <i class="material-icons md-36 mar-top text-left text-danger box_icons">chrome_reader_mode</i>
                                    </div>
                                    <div class="col-8 col-sm-10">
                                        <p class="text-right nopadmar user_font mb-0">{{trans('left_menu.opportunities')}}</p>
                                        <div id="countno3" class="text-right counter-dash user_count">
                                            {{ $opportunities }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <a href="{{ url('customer') }}" class="text-default">
                <div class="card">
                    <div class="card-body">
                        <div class="cnts">
                            <div class="row">
                                <div class="col-4 col-sm-2">
                                    <i class="material-icons md-36 mar-top text-left text-info box_icons">supervisor_account</i>
                                </div>
                                <div class="col-8 col-sm-10">
                                    <p class="text-right nopadmar user_font mb-0">{{trans('left_menu.customers')}}</p>
                                    <div id="countno4" class="text-right counter-dash user_count">
                                        {{ $customers }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4>{{trans('dashboard.opportunities_leads')}}</h4>
                </div>
                <div class="card-body">
                    <div id='chart'></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h4>{{trans('dashboard.opportunities')}}</h4>
                </div>
                <div class="card-body">
                    <div id="sales"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h4>{{trans('dashboard.customers_map')}}</h4>
                </div>
                <div class="card-body">
                    <div class="world" style="height:350px; width:100%;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <meta name="_token" content="{{ csrf_token() }}">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="float-left">
                        <i class="livicon" data-name="inbox" data-size="18" data-color="white" data-hc="white"
                           data-l="true"></i>
                        {{ trans('task.my_task_list') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="list_of_items vertical_scroll max_height_350">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p></p>
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/d3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/c3.min.js')}}"></script>
    <script src="{{ asset('js/todolist.js') }}"></script>
    <script>

        /*c3 line chart*/
        $(function () {

            var data = [
                ['Opportunities', 'Leads'],
                    @foreach($opportunity_leads as $item)
                [{{$item['opportunities']}}, {{$item['leads']}}],
                @endforeach
            ];

//c3 customisation
            var chart1 = c3.generate({
                bindto: '#chart',
                data: {
                    rows: data,
                    type: 'area-spline'
                },
                color: {
                    pattern: ['#fc4141', '#3295ff']
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
            $(".sidebar-toggle").on("click",function () {
                setTimeout(function () {
                    chart1.resize();
                },200)
            });

            function formatMonth(d) {

                @foreach($opportunity_leads as $id => $item)
                if ('{{$id}}' == d) {
                    return '{{$item['month']}}' + ' ' + '{{$item['year']}}'
                }
                @endforeach
            }

            setTimeout(function () {
                chart.resize();
            }, 2000);

            setTimeout(function () {
                chart.resize();
            }, 4000);

            setTimeout(function () {
                chart.resize();
            }, 6000);
            $("[data-toggle='offcanvas']").click(function (e) {
                chart.resize();
            });
            /*c3 line chart end*/

            /*c3 pie chart*/
            var chart = c3.generate({
                bindto: '#sales',
                data: {
                    columns: [
                        ['New', {{$opportunity_new}}],
                        ['Qualification', {{$opportunity_qualification}}],
                        ['Proposition', {{$opportunity_proposition}}],
                        ['Negotiation', {{$opportunity_negotiation}}],
                        ['Won', {{$opportunity_won}}],
                        ['Loss', {{$opportunity_loss}}]
                    ],
                    type: 'pie',
                    colors: {
                        'New': '#3295ff',
                        'Qualification': '#6f42c1',
                        'Proposition': '#17a2b8',
                        'Negotiation': '#fcb410',
                        'Won': '#2daf57',
                        'Loss': '#fc4141'
                    },
                    labels: true
                }
            });
            /*c3 pie chart end*/
            // c3 chart end


            var useOnComplete = false,
                    useEasing = false,
                    useGrouping = false;

            var world=$('.world').vectorMap(
                    {
                        map: 'world_mill_en',
                        markers: [
                                @foreach($customers_world as $item)
                            {
                                latLng: [{{$item['latitude']}}, {{$item['longitude']}}], name: '{{$item['city']}}'
                            },
                            @endforeach
                        ],
                        normalizeFunction: 'polynomial',
                        backgroundColor: 'transparent',
                        regionsSelectable: true,
                        markersSelectable: true,
                        regionStyle: {
                            initial: {
                                fill: 'rgba(120,130,140,0.2)'
                            },
                            hover: {
                                fill: '#2c6c4c',
                                stroke: '#fff'
                            }
                        },
                        markerStyle: {
                            initial: {
                                fill: '#2daf57',
                                stroke: '#fff',
                                r: 10
                            },
                            hover: {
                                fill: '#0cc2aa',
                                stroke: '#fff',
                                r: 15
                            }
                        }
                    }
            );
                            $(".sidebar-toggle").on("click",function () {
                                setTimeout(function () {
                                    world.resize();
                                },200)
                            });

        });
    </script>

@stop
