@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white ">
                    <h4>Quotations</h4>
                </div>
                <div class="card-body">
                    <div id="quotations" class="max_height_300"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-header clearfix">
        <div class="pull-right">
            <a href="{{ url($type.'/draft_quotations') }}" class="btn btn-primary m-b-10">{{trans('quotation.draft_quotations')}}</a>
            <a href="{{ url('quotation_invoice_list') }}" class="btn btn-primary m-b-10">{{ trans('quotation.quotation_invoice_list') }}</a>
            <a href="{{ url('quotation_converted_list') }}" class="btn btn-primary m-b-10">{{ trans('quotation.converted_list') }}</a>
            <a href="{{ url('quotation_delete_list') }}" class="btn btn-primary m-b-10">{{ trans('quotation.delete_list') }}</a>
            @if($user->hasAccess(['quotations.write']) || $orgRole=='admin')
                <a href="{{ $type.'/create' }}" class="btn btn-primary m-b-10">
                    <i class="fa fa-plus-circle"></i> {{ trans('quotation.create') }}</a>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">receipt</i>
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
                    <th>{{ trans('quotation.quotations_number') }}</th>
                    <th>{{ trans('quotation.company_id') }}</th>
                    <th>{{ trans('quotation.sales_team_id') }}</th>
                    <th>{{ trans('quotation.date') }}</th>
                    <th>{{ trans('quotation.exp_date') }}</th>
                    <th>{{ trans('quotation.total') }}</th>
                    <th>{{ trans('quotation.payment_term') }}</th>
                    <th>{{ trans('quotation.status') }}</th>
                    <th>{{ trans('quotation.expired') }}</th>
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
        var data1 = [
            ['Quotations','Draft Quotations','Sales Order Converted List','Invoice Converted List','Deleted List'],
                @foreach($graphics as $item)
            [ {{ $item['send_quotation'] }}, {{ $item['draft_quotation'] }}, {{ $item['salesorder_list'] }}
                , {{ $item['invoice_list'] }}, {{ $item['delete_list'] }} ],
            @endforeach
        ];
        var chart1 = c3.generate({
            bindto: '#quotations',
            data: {
                rows: data1,
                type: 'bar'
            },
            color: {
                pattern: ['#3295ff','#2daf57','#fc4141','#fcb410','#17a2b8']
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
            },200)
        });
    </script>
    @if(isset($type))
        <script type="text/javascript">
            var oTable;
            $(document).ready(function () {
                oTable = $('#data').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "columns":[
                        {"data":"quotations_number"},
                        {"data":"company_id"},
                        {"data":"sales_team_id"},
                        {"data":"date"},
                        {"data":"exp_date"},
                        {"data":"final_price"},
                        {"data":"payment_term"},
                        {"data":"status"},
                        {"data":"expired"},
                        {"data":"actions"}
                    ],
                    "ajax": "{{ url($type) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
                });
            });
        </script>
    @endif

@stop
