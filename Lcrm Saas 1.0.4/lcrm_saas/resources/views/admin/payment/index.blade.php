@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <div class="row">
            <div class="col-md-12">
                @include('flash::message')
            </div>
        </div>
        <div class="pull-right">

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
                       <th>{{ trans('subscription.org_name') }}</th>
                       <th>{{ trans('subscription.org_email') }}</th>
                       <th>{{ trans('subscription.plan') }}</th>
                       <th>{{ trans('subscription.payment_due') }}</th>
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
                            {"data":"email"},
                            {"data":"generic_trial_plan"},
                            {"data":"trial_ends_at"},
                            {"data":"actions"}
                        ],
                    "ajax": "{{ url($type) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
                });
            });
        </script>
    @endif
@stop
