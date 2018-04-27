@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <meta name="_token" content="{{ csrf_token() }}">
    <div class="row">
        <div class="col-md-6">
            <div class="card todolist">
                <div class="card-header bg-white">
                    <h4 class="float-left">
                        <i class="livicon" data-name="medal" data-size="18" data-color="white" data-hc="white"
                           data-l="true"></i>
                        {{trans('task.tasks')}}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="todolist_list adds">
                        {!! Form::open(['class'=>'form', 'id'=>'main_input_box']) !!}
                        {!! Form::hidden('task_from_user',$user->id, ['id'=>'task_from_user']) !!}
                        <div class="form-group">
                            {!! Form::label('task_description', trans('task.description')) !!}
                            {!! Form::text('task_description', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('task_deadline', trans('task.deadline')) !!}
                            {!! Form::text('task_deadline', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('user_id', trans('task.user')) !!}
                            {!! Form::select('user_id', $users , null, ['class' => 'form-control']) !!}
                        </div>
                        {!!  Form::hidden('full_name', $user->full_name, ['id'=> 'full_name'])!!}
                        <button type="submit" class="btn btn-primary add_button">
                            Send
                        </button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="float-left">
                        <i class="livicon" data-name="inbox" data-size="18" data-color="white" data-hc="white"
                           data-l="true"></i>
                        {{ trans('task.my_task_list') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="list_of_items vertical_scroll max_height_300">
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- Scripts --}}
@section('scripts')
    <script src="{{ asset('js/todolist.js') }}"></script>
    <script>
        $(document).ready(function () {
            $("#user_id").select2({
                theme: "bootstrap",
                placeholder: "{{trans('task.user')}}"
            });

            var dateFormat = '{{ config('settings.date_format') }}';
            flatpickr("#task_deadline", {
                minDate: '{{  now() }}',
                dateFormat: dateFormat,
            });
        });
        $('.icheckgreen').iCheck({
            checkboxClass: 'icheckbox_minimal-green',
            radioClass: 'iradio_minimal-green'
        });
    </script>
@stop
