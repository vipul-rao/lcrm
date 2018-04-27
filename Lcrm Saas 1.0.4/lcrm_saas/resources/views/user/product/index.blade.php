@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop
{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header bg-white ">
                    <h4>{{ trans('product.product_status') }}</h4>
                </div>
                <div class="card-body">
                    <div id="product_chart" class="max_height_300"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header bg-white ">
                    <h4>{{ trans('product.products_by_month') }}</h4>
                </div>
                <div class="card-body">
                    <div id="products_by_month" class="max_height_300"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-header clearfix">
        @if($user->hasAccess(['products.write']) || $orgRole=='admin')
            <div class="pull-right">
                <a href="{{ $type.'/create' }}" class="btn btn-primary m-b-10">
                    <i class="fa fa-plus-circle"></i> {{ trans('product.create') }}</a>
                <a href="{{ $type.'/import' }}" class="btn btn-primary m-b-10">
                    <i class="fa fa-download"></i> {{ trans('product.import') }}</a>
            </div>
        @endif
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">layers</i>
                {{ $title }}
            </h4>
                                <span class="pull-right">
                                    <i class="fa fa-fw fa-chevron-up clickable"></i>
                                    <i class="fa fa-fw fa-times removecard clickable"></i>
                                </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="data" class="table table-striped table-bordered ">
                    <thead>
                    <tr>
                        <th>{{ trans('product.product_name') }}</th>
                        <th>{{ trans('product.category_id') }}</th>
                        <th>{{ trans('product.product_type') }}</th>
                        <th>{{ trans('product.status') }}</th>
                        <th>{{ trans('product.quantity_on_hand') }}</th>
                        <th>{{ trans('product.quantity_available') }}</th>
                        <th>{{ trans('table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop

{{-- Scripts --}}
@section('scripts')
    <script>
        var chart1 = c3.generate({
            bindto: '#product_chart',
            data: {
                columns: [
                        @foreach($statuses as $item)
                    ['{{$item['value']}}', {{$item['products']}}],
                    @endforeach
                ],
                type : 'donut',
                colors: {
                    @foreach($statuses as $item)
                    '{{$item['value']}}': '{{$item['color']}}',
                    @endforeach
                }
            }
        });
        setTimeout(function () {
            chart1.resize()
        }, 500);


        //products by month
        var productsData = [
            ['products'],
                @foreach($graphics as $item)
            [{{$item['products']}}],
            @endforeach
        ];
        var chart2 = c3.generate({
            bindto: '#products_by_month',
            data: {
                rows: productsData,
                type: 'bar'
            },
            color: {
                pattern: ['#3295ff']
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

        $(".sidebar-toggle").on("click",function () {
            setTimeout(function () {
                chart1.resize();
                chart2.resize();
            },200)
        });


    </script>
    <!-- Scripts -->
    @if(isset($type))
        <script type="text/javascript">
            var oTable;
            $(document).ready(function () {
                oTable = $('#data').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                        "columns":[
                            {"data":"product_name"},
                            {"data":"name"},
                            {"data":"product_type"},
                            {"data":"status"},
                            {"data":"quantity_on_hand"},
                            {"data":"quantity_available"},
                            {"data":"actions"},
                        ],
                    "ajax": "{{ url($type) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
                });
            });
        </script>
    @endif

@stop
