@extends('layouts.install')
@section('content')
    @include('install.steps', ['steps' => [
        'welcome' => 'selected done',
        'requirements' => 'selected done',
        'permissions' => 'selected'
    ]])
    <div class="content">
        @if (! $allGranted)
            <div class="alert alert-danger">
                {!!trans('install.system_not_meet_requirements')!!}
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-white">
                <h4>{{trans('install.permissions')}}</h4>
            </div>
            <div class="card-body">
                <div>
                    <ul class="list-group">
                        @foreach($folders as $path => $isWritable)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $path }}
                                @if ($isWritable)
                                    <div>
                                        <span class="badge badge-success"><i class="fa fa-check"></i></span>
                                        <span class="badge badge-primary m-l-20">775</span>
                                    </div>
                                @else
                                    <div>
                                        <span class="badge badge-danger"><i class="fa fa-times"></i></span>
                                        <span class="badge badge-primary m-l-20">775</span>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @if ($allGranted)
                        <a class="btn btn-primary pull-right m-t-20" href="{{ url('install/database') }}">
                            {{trans('install.next')}}
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    @else
                        <a class="btn btn-primary pull-right" href="{{ url('install/permissions') }}">
                            {{trans('install.refresh')}}
                            <i class="fa fa-refresh"></i></a>
                        <button class="btn btn-primary pull-right" disabled>
                            {{trans('install.next')}}
                            <i class="fa fa-arrow-right"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop