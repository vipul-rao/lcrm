@extends('layouts.install')
@section('content')
    @include('install.steps', ['steps' => [
        'welcome' => 'selected done',
        'requirements' => 'selected done',
        'permissions' => 'selected done',
        'database' => 'selected done',
        'installation' => 'selected done',
        'complete' => 'selected'
    ]])
    <div class="content">
        <div class="card">
            <div class="card-header bg-white">
                <h4>
                    {{trans('install.complete2')}}
                </h4>
            </div>
            <div class="card-body">
                <div>
                    <p><strong>{{trans('install.well_done')}}</strong></p>
                    <p>{{trans('install.successfully')}}</p>

                    @if (is_writable(base_path()))
                        <p>{!!trans('install.final_info')!!}</p>
                    @endif
                    <a class="btn btn-primary pull-right" href="{{ url('/signin') }}">
                        <i class="fa fa-sign-in"></i>
                        {{trans('install.login')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
