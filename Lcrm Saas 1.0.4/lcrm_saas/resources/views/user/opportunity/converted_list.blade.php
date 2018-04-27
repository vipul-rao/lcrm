@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        @if($user->hasAccess(['opportunities.write']) || $user->inRole('admin'))
            <div class="pull-right">
            </div>
        @endif
    </div>
    @if($errors->any())
        <div class="alert alert-danger">
            {{$errors->first()}}
        </div>
    @endif
    <div class="text-right">
        <a href="{{ url('opportunity') }}" class="btn btn-warning m-b-10"><i
                    class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">event_seat</i>
                {{ $title }}
            </h4>
            <span class="float-right">
                                    <i class="fa fa-fw fa-chevron-up clickable"></i>
                                    <i class="fa fa-fw fa-times removecard clickable"></i>
                                </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="data" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('opportunity.opportunity') }}</th>
                        <th>{{ trans('opportunity.company_name') }}</th>
                        <th>{{ trans('opportunity.customer') }}</th>
                        <th>{{ trans('opportunity.next_action') }}</th>
                        <th>{{ trans('opportunity.stages') }}</th>
                        <th>{{ trans('opportunity.expected_revenue') }}</th>
                        <th>{{ trans('opportunity.probability') }}</th>
                        <th>{{ trans('opportunity.salesteam') }}</th>
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
                        {"data": "opportunity"},
                        {"data": "company"},
                        {"data": "customer"},
                        {"data": "next_action"},
                        {"data": "stages"},
                        {"data": "expected_revenue"},
                        {"data": "probability"},
                        {"data": "salesteam"},
                        {"data": "actions"}
                    ],
                    "ajax": "{{ url($type) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
                });
            });
        </script>
    @endif
@stop