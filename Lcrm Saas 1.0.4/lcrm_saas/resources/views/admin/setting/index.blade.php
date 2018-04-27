@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
{{--  {{$settings}}  --}}
@include('flash::message')
    <div class="card">
        <div class="card-body">
            {!! Form::open(['url' => url('admin/setting'), 'method' => 'post', 'files'=> true]) !!}

            <div class="nav-tabs-custom" id="setting_tabs">
                <ul class="nav nav-tabs settings">
                    <li class="nav-item">
                        <a class="active" href="#general_configuration"
                           data-toggle="tab" title="{{ trans('settings.general_configuration') }}"><i
                                    class="material-icons md-24">build</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#email_configuration"
                           data-toggle="tab" title="{{ trans('settings.email_configuration') }}"><i
                                    class="material-icons md-24">email</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#pusher_configuration"
                           data-toggle="tab" title="{{ trans('settings.pusher_configuration') }}"><i
                                    class="material-icons md-24">receipt</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#paypal_settings"
                           data-toggle="tab" title="{{ trans('settings.paypal_settings') }}"><i
                                    class="material-icons md-24">payment</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#stripe_settings"
                           data-toggle="tab" title="{{ trans('settings.stripe_settings') }}"><i
                                    class="material-icons md-24">vpn_key</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#gmaps"
                           data-toggle="tab" title="{{ trans('settings.google_maps') }}"><i
                                    class="material-icons md-24">place</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#backup_configuration"
                           data-toggle="tab" title="{{ trans('settings.backup_configuration') }}"><i
                                    class="material-icons md-24">backup</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#europian_tax"
                           data-toggle="tab" title="{{ trans('settings.europian_tax') }}"><i
                                    class="fa fa-money md-24"></i></a>
                    </li>
                </ul>
                <div class="tab-content m-t-20">
                    @include('admin.setting.general')
                    @include('admin.setting.email')
                    @include('admin.setting.pusher')
                    @include('admin.setting.paypal')
                    @include('admin.setting.stripe')
                    @include('admin.setting.backup')
                    @include('admin.setting.gmaps')
                    @include('admin.setting.europian_tax')

                </div>
            </div>
            <!-- Form Actions -->
            <div class="form-group">
                <div class="controls">
                    <button type="submit" class="btn btn-success"><i
                                class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                </div>
            </div>
            <!-- ./ form actions -->
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('scripts')

@stop
