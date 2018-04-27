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
                    <h4>{{ trans('invoice.invoice_details_for_current_month') }}</h4>
                </div>
                <div class="card-body">
                    <div id="invoice-chart" style="width:100%; height:300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>{{trans('invoice.invoices_total')}}</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="number c-red">{{$organizationSettings['currency'] ?? null}} {{ $invoices_total_collection}} </h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <h4>{{trans('invoice.open_invoice')}}</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="number c-green">{{$organizationSettings['currency'] ?? null}} {{$open_invoice_total}} </h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h4>{{trans('invoice.overdue_invoice')}}</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="number c-green">{{$organizationSettings['currency'] ?? null}} {{$overdue_invoices_total}} </h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4>{{trans('invoice.paid_invoice')}}</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="number c-green">{{$organizationSettings['currency'] ?? null}} {{$paid_invoices_total}} </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">web</i>
                {{ $title }}
            </h4>
                                <span class="pull-right">
                                    <i class="fa fa-fw fa-chevron-up clickable"></i>
                                    <i class="fa fa-fw fa-times removecard clickable"></i>
                                </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="data" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('invoice.invoice_number') }}</th>
                        <th>{{ trans('invoice.company_id') }}</th>
                        <th>{{ trans('invoice.invoice_date') }}</th>
                        <th>{{ trans('invoice.due_date') }}</th>
                        <th>{{ trans('invoice.total') }}</th>
                        <th>{{ trans('invoice.unpaid_amount') }}</th>
                        <th>{{ trans('invoice.status') }}</th>
                        <th>{{ trans('invoice.expired') }}</th>
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

        /*invoice chart*/

        var chart = c3.generate({
            bindto: '#invoice-chart',
            data: {
                columns: [
                    ['Open invoice', {{$open_invoice_total}}],
                    ['Overdue invoice', {{$overdue_invoices_total}}],
                    ['Paid invoice', {{$paid_invoices_total}}]
                ],
                type : 'donut',
                colors: {
                    'Open invoice': '#3295ff',
                    'Overdue invoice': '#fc4141',
                    'Paid invoice': '#A0D468'
                }
            }
        });
        setTimeout(function () {
            chart.resize()
        }, 500);
        //c3 customisation

        /* invoice chart end*/
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
                        {"data":"invoice_number"},
                        {"data":"company_id"},
                        {"data":"invoice_date"},
                        {"data":"due_date"},
                        {"data":"final_price"},
                        {"data":"unpaid_amount"},
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
