@extends('layouts.install')
@section('content')
    @include('install.steps', ['steps' => [
        'welcome' => 'selected done',
        'requirements' => 'selected done',
        'permissions' => 'selected done',
        'database' => 'selected done',
        'installation' => 'selected done',
        'complete' => 'selected error'
    ]])
    <div class="content">
        <div class="card">
            <div class="card-header bg-white">
                <h4>
                    {{trans('install.whoops')}}
                </h4>
            </div>
            <div class="card-body">
                <p><strong>{!! trans('install.something_wrong')!!}</strong></p>
                <p>{!! trans('install.check_log') !!}</p>
                <a class="btn btn-primary pull-right" href="{{ url('install') }}">
                    <i class="fa fa-undo"></i>
                    {{trans('install.try_again')}}
                </a>
            </div>
        </div>
    </div>
@stop