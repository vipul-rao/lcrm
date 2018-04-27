@extends('layouts.install')

@section('content')

    @include('install.steps', ['steps' => [
        'welcome' => 'selected done',
        'requirements' => 'selected done',
        'permissions' => 'selected done',
        'database' => 'selected done',
        'installation' => 'selected'
    ]])

    <div class="content">
        <div class="card">
            <div class="card-header bg-white">
                <h4>{{trans('install.installation')}}</h4>
            </div>
            <div class="card-body">
                {!! Form::open(['url' => 'install/install']) !!}
                <div>
                    <p>{{trans('install.ready_to_install')}}</p>
                    <button class="btn btn-primary pull-right" data-toggle="loader" data-loading-text="Installing" type="submit">
                        <i class="fa fa-play"></i>
                        {{trans('install.install')}}
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop