@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">assignment</i>
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
