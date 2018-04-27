@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <div class="pull-right">
            @if($user->hasAccess(['meetings.read']) || $orgRole=='admin')
                <a href="{{ url($type.'/'.$opportunity->id.'/calendar') }}" class="btn btn-success m-b-10">
                    <i class="fa fa-calendar"></i> {{ trans('opportunity.calendar') }}</a>
            @endif
            @if($user->hasAccess(['meetings.write']) || $orgRole=='admin')
                <a href="{{ url($type.'/'.$opportunity->id.'/create') }}" class="btn btn-primary m-b-10">
                    <i class="fa fa-plus-circle"></i> {{ trans('meeting.create_meeting') }}</a>
            @endif
        </div>
    </div>
    <input type="hidden" id="id" value="{{$opportunity->id}}">
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

            <table id="data" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>{{ trans('meeting.meeting_subject') }}</th>
                    <th>{{ trans('meeting.company_name') }}</th>
                    <th>{{ trans('meeting.starting_date') }}</th>
                    <th>{{ trans('meeting.ending_date') }}</th>
                    <th>{{ trans('meeting.responsible') }}</th>
                    <th>{{ trans('table.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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
                        {"data":"meeting_subject"},
                        {"data":"company_name"},
                        {"data":"starting_date"},
                        {"data":"ending_date"},
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
