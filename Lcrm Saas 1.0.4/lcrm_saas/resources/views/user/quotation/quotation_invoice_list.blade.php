@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        @if($user->hasAccess(['opportunities.write']) || $orgRole=='admin')
            <div class="pull-right">
            </div>
        @endif
    </div>
    @include('flash::message')
    <div class="text-right">
        <a href="{{ url('quotation') }}" class="btn btn-warning m-b-10"><i
                    class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">event_seat</i>
                {{ $title }}
            </h4>
            <span class="pull-right">
                                    <i class="fa fa-fw fa-chevron-up clickable"></i>
                                    <i class="fa fa-fw fa-times removecard clickable"></i>
                                </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="data" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('quotation.quotations_number') }}</th>
                        <th>{{ trans('quotation.company_id') }}</th>
                        <th>{{ trans('quotation.sales_team_id') }}</th>
                        <th>{{ trans('quotation.total') }}</th>
                        <th>{{ trans('quotation.payment_term') }}</th>
                        <th>{{ trans('quotation.status') }}</th>
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
    <!-- Scripts -->
    @if(isset($type))
        <script type="text/javascript">
            var oTable;
            $(document).ready(function () {
                oTable = $('#data').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "columns": [
                        {"data": "quotations_number"},
                        {"data": "company_id"},
                        {"data": "sales_team_id"},
                        {"data": "final_price"},
                        {"data": "payment_term"},
                        {"data": "status"},
                        {"data": "actions"}
                    ],
                    "ajax": "{{ url($type) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
                });
            });
        </script>
    @endif
@stop