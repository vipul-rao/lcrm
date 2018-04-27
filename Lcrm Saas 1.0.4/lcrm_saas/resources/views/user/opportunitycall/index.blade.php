@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        @if($user->hasAccess(['logged_calls.write']) || $orgRole=='admin')
            <div class="pull-right">
                <a href="{{ url($type.'/'.$opportunity->id.'/create') }}" class="btn btn-primary m-b-10">
                    <i class="fa fa-plus-circle"></i> {{ trans('call.create') }}</a>
            </div>
        @endif
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="fa fa-fw fa-bell-o"></i>
                {{ $title }}
            </h4>
                                <span class="pull-right">
                                    <i class="fa fa-fw fa-chevron-up clickable"></i>
                                    <i class="fa fa-fw fa-times removecard clickable"></i>
                                </span>
        </div>
        <div class="card-body">
            <input type="hidden" id="id" value="{{$opportunity->id}}">
            <div class="table-responsive">
            <table id="data" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>{{ trans('call.company_name') }}</th>
                    <th>{{ trans('call.date') }}</th>
                    <th>{{ trans('call.summary') }}</th>
                    <th>{{ trans('call.duration') }}</th>
                    <th>{{ trans('call.main_staff') }}</th>
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
                    columns:[
                        {"data":"company_id"},
                        {"data":"date"},
                        {"data":"call_summary"},
                        {"data":"duration"},
                        {"data":"resp_staff_id"},
                        {"data":"actions"}
                    ],
                    "ajax": "{{ url($type.'/'.$opportunity->id) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
                });
                $('div.dataTables_length select').select2({
                    theme:"bootstrap"
                });
            });
        </script>
    @endif
@stop

