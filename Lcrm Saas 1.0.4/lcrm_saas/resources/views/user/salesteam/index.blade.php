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
                    <h4>{{ trans('salesteam.invoice_taget_vs_actual_invoice') }}</h4>
                </div>
                <div class="card-body">
                    <div id="invoice_target_by_month" class="max_height_300"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header bg-white ">
                    <h4>{{ trans('salesteam.sales_teams_by_month') }}</h4>
                </div>
                <div class="card-body">
                    <div id="salesteam_by_month" class="max_height_300"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-header clearfix">
        @if($user->hasAccess(['sales_team.write']) || $orgRole=='admin')
            <div class="pull-right">
                <a href="{{ request()->url() }}/import" class="btn btn-primary m-b-10">
                    <i class="fa fa-download"></i> {{ trans('table.import') }}
                </a>
                <a href="{{ url($type.'/create') }}" class="btn btn-primary m-b-10">
                    <i class="fa fa-plus-circle"></i> {{ trans('salesteam.new') }}
                </a>
            </div>
        @endif
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">groups</i>{{ $title }}
            </h4>
                                <span class="pull-right">
                                    <i class="fa fa-fw fa-chevron-up clickable"></i>
                                    <i class="fa fa-fw fa-times removecard clickable"></i>
                                </span>
        </div>
        <div class="card-body">
             <div class="table-responsive">
            <table id="data" class="table table-bordered table-hover ">
                <thead>
                <tr>
                    <th>{{ trans('salesteam.salesteam') }}</th>
                    <th>{{ trans('salesteam.invoice_target') }}</th>
                    <th>{{ trans('salesteam.invoice_forecast') }}</th>
                    <th>{{ trans('salesteam.actual_invoice') }}</th>
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

        //invoice target by month
        var data1 = [
            ['Invoice Target','Actual Invoice'],
                @foreach($graphics as $item)
            [{{$item['invoice_target']}}, {{$item['actual_invoice']}}],
            @endforeach
        ];
        var chart1 = c3.generate({
            bindto: '#invoice_target_by_month',
            data: {
                rows: data1,
                type: 'bar'
            },
            color: {
                pattern: ['#3295ff','#fc4141']
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

        var data2 = [
            ['Sales Teams'],
                @foreach($graphics as $item)
            [{{$item['salesteams']}}],
            @endforeach
        ];
        var chart2 = c3.generate({
            bindto: '#salesteam_by_month',
            data: {
                rows: data2,
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
                        {"data":"salesteam"},
                        {"data":"target"},
                        {"data":"invoice_forecast"},
                        {"data":"actual_invoice"},
                        {"data":"actions"},
                    ],
                    "ajax": "{{ url($type) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
                });
            });
        </script>
    @endif

@stop
