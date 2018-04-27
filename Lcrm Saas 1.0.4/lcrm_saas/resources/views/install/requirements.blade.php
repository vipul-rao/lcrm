@extends('layouts.install')
@section('content')
    @include('install.steps', ['steps' => ['welcome' => 'selected done', 'requirements' => 'selected']])
    <div class="content">
        @if (! $allLoaded)
            <div class="alert alert-danger">
                {!!trans('install.system_not_meet_requirements')!!}
            </div>
        @endif
        <div class="card">
            <div class="card-header bg-white">
                <h4>{{trans('install.system_requirements')}}</h4>
            </div>
            <div class="card-body">
                <div>
                    <ul class="list-group">
                        @foreach ($requirements as $extension => $loaded)
                            <li class="list-group-item d-flex justify-content-between align-items-center {{ ! $loaded ? 'list-group-item-danger' : '' }}">
                                {{ $extension }}
                                @if ($loaded)
                                    <span class="badge badge-success"><i class="fa fa-check"></i></span>
                                @else
                                    <span class="badge badge-danger"><i class="fa fa-times"></i></span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @if ($allLoaded)
                        <a class="btn btn-primary pull-right m-t-20" href="{{ url('install/permissions') }}">
                            {{trans('install.next')}}
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    @else
                        <a class="btn btn-info pull-right" href="{{ url('install/permissions') }}">
                            {{trans('install.refresh')}}
                            <i class="fa fa-refresh"></i></a>
                        <button class="btn btn-green pull-right" disabled>
                            {{trans('install.next')}}
                            <i class="fa fa-arrow-right"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop