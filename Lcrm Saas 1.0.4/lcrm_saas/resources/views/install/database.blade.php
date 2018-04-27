@extends('layouts.install')
@section('content')
    @include('install.steps', ['steps' => [
        'welcome' => 'selected done',
        'requirements' => 'selected done',
        'permissions' => 'selected done',
        'database' => 'selected'
    ]])
    @include('layouts.messages')
    <div class="content">
        <div class="card">
            <div class="card-header bg-white">
                <h4>{{trans('install.database_info')}}</h4>
            </div>
            <div class="card-body">
                {!! Form::open(['url' =>  'install/database', 'method' => 'post']) !!}
                <div>
                    <div class="form-group">
                        <label for="host">{{trans('install.host')}}</label>
                        <input type="text" class="form-control" id="host" name="host" value="{{ old('host','localhost') }}">
                        <small>{{trans('install.host_info')}}</small>
                    </div>
                    <div class="form-group">
                        <label for="port">{{trans('install.port')}}</label>
                        <input type="text" class="form-control" id="port" name="port" value="{{ old('port','3306') }}">
                        <small>{{trans('install.port_info')}}</small>
                    </div>
                    <div class="form-group">
                        <label for="username">{{trans('install.username')}}</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username','root') }}">
                        <small>{{trans('install.username_info')}}</small>
                    </div>
                    <div class="form-group">
                        <label for="password">{{trans('install.password')}}</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small>{{trans('install.password_info')}}</small>
                    </div>
                    <div class="form-group">
                        <label for="database">{{trans('install.database')}}</label>
                        <input type="text" class="form-control" id="database" name="database"  value="{{ old('database') }}">
                        <small>{{trans('install.database_info2')}}</small>
                    </div>
                    <button class="btn btn-primary pull-right">
                        {{trans('install.next')}}
                        <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop
