@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <div class="pull-right">
            <a href="{{ url($type.'/create') }}" class="btn btn-primary m-b-10">
                <i class="fa fa-plus-circle"></i> {{ trans('payplan.create') }}</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">flag</i>
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
                        <th>{{ trans('payplan.name') }}</th>
                        <th>{{ trans('payplan.amount') }}</th>
                        <th>{{ trans('payplan.currency') }}</th>
                        <th>{{ trans('payplan.no_people') }}</th>
                        <th>{{ trans('payplan.interval') }}</th>
                        <th>{{ trans('payplan.trial_period_days') }}</th>
                        <th>{{ trans('payplan.active_organizations') }}</th>
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
                    "columns":[
                        {"data":"name"},
                        {"data":"amount"},
                        {"data":"currency"},
                        {"data":"no_people"},
                        {"data":"interval"},
                        {"data":"trial_period_days"},
                        {"data":"organizations"},
                        {"data":"actions"}
                    ],
                    "ajax": "{{ url($type) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
                });
            });
        </script>
    @endif

@stop
