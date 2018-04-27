@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <div class="pull-right">
        @if($can_edit==1 && ($user->hasAccess(['staff.write']) || $orgRole=='admin'))
                <a href="{{ $type.'/create' }}" class="btn btn-primary m-b-10">
                    <i class="fa fa-plus-circle"></i> {{ trans('table.new') }}</a>
        @endif
        @if($orgRole=='admin')
                <a href="{{ $type.'/invite' }}" class="btn btn-warning m-b-10">
                    <i class="fa fa-envelope"></i> {{ trans('staff.invite') }}</a>
        @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">people_outline</i>
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
                        <th>{{ trans('customer.full_name') }}</th>
                        <th>{{ trans('customer.email') }}</th>
                        <th>{{ trans('customer.register') }}</th>
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
                            {"data":"full_name"},
                            {"data":"email"},
                            {"data":"created_at"},
                            {"data":"actions"},
                        ],
                    "ajax": "{{ url($type) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
                });
                $('div.dataTables_length select').select2({
                    theme:"bootstrap"
                });
            });
        </script>
    @endif

@stop
