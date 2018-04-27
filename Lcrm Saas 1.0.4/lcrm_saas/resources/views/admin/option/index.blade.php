@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <label class="radio-inline">
                            <input type='radio' id='category' name='category' checked value='__' class='icheck'/> All
                        </label>
                        @foreach($categories as $key => $value)
                            <label class="radio-inline">
                                <input type='radio' id='category' name='category' value='{{$key}}' class='icheck'/> {{$value}}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="pull-right">
            <a href="{{ url($type.'/create') }}" class="btn btn-primary m-b-10">
                <i class="fa fa-plus-circle"></i> {{ trans('option.create') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h4 class="float-left">
                <i class="material-icons">dashboard</i>
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
                        <th>{{ trans('option.category') }}</th>
                        <th>{{ trans('option.title') }}</th>
                        <th>{{ trans('option.value') }}</th>
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
        $(document).ready(function () {
            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
        });
    </script>
    <script>
        var oTable;
        $(document).ready(function () {
            oTable = $('#data').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [],
                "columns": [
                    {"data": "category"},
                    {"data": "title"},
                    {"data": "value"},
                    {"data": "actions"},
                ],
                "ajax": "{{ url($type) }}" + ((typeof $('#data').attr('data-id') != "undefined") ? "/" + $('#id').val() + "/" + $('#data').attr('data-id') : "/data")
            });
        });
        $('input[type=radio]').on('ifChecked', function (event) {
            oTable.ajax.url('{!! url($type.'/data') !!}/' + $(this).val());
            oTable.ajax.reload();
        });
    </script>
@stop
