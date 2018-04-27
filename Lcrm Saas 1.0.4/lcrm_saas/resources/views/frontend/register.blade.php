@extends('layouts.frontend.user')
@section('styles')
    <link href="{{ asset('css/login_register.css') }}" rel="stylesheet" type="text/css">
@stop
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="m-auto col-lg-4 col-md-6 col-sm-10 col-12 signin-form">
                    <div class="box-color">
                        <h4 class="text-center text-white">{{trans('auth.signup')}}</h4>
                        <div class="m-t-15">
                            {!! Form::open(['url' => url('register'), 'method' => 'post']) !!}
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="text-white">{{trans('organizations.organization_details')}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
                                        {!! Form::label('name', trans('organizations.name'), ['class' => 'control-label required']) !!}
                                        <div class="controls">
                                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                            <span class="help-block">{{ $errors->first('name', ':message') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group required {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                                        {!! Form::label('phone_number', trans('organizations.phone_number'), ['class' => 'control-label required']) !!}
                                        <div class="controls">
                                            {!! Form::text('phone_number', null, ['class' => 'form-control','data-fv-integer' => 'true']) !!}
                                            <span class="help-block">{{ $errors->first('phone_number', ':message') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                                        {!! Form::label('email', trans('organizations.email'), ['class' => 'control-label required']) !!}
                                        <div class="controls">
                                            {!! Form::email('email', null, ['class' => 'form-control']) !!}
                                            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="text-white">{{trans('organizations.organization_owner_detals')}}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group has-feedback {{ $errors->has('owner_first_name') ? 'has-error' : '' }}">
                                        {!! Form::label('owner_first_name', trans('organizations.owner_first_name'), ['class' => 'control-label required']) !!}
                                        {!! Form::text('owner_first_name', null, ['class' => 'form-control']) !!}
                                        <span class="help-block">{{ $errors->first('owner_first_name', ':message') }}</span>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('owner_last_name') ? 'has-error' : '' }}">
                                        {!! Form::label('owner_last_name', trans('organizations.owner_last_name'), ['class' => 'control-label required']) !!}
                                        {!! Form::text('owner_last_name', null, ['class' => 'form-control']) !!}
                                        <span class="help-block">{{ $errors->first('owner_last_name', ':message') }}</span>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('owner_phone_number') ? 'has-error' : '' }}">
                                        {!! Form::label('owner_phone_number', trans('organizations.owner_phone_number'), ['class' => 'control-label required']) !!}
                                        {!! Form::text('owner_phone_number', null, ['class' => 'form-control']) !!}
                                        <span class="help-block">{{ $errors->first('owner_phone_number', ':message') }}</span>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('owner_email') ? 'has-error' : '' }}">
                                        {!! Form::label('owner_email', trans('organizations.owner_email'), ['class' => 'control-label required']) !!}
                                        {!! Form::text('owner_email', null, ['class' => 'form-control']) !!}
                                        <span class="help-block">{{ $errors->first('owner_email', ':message') }}</span>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('owner_password') ? 'has-error' : '' }}">
                                        {!! Form::label('owner_password', trans('organizations.password'), ['class' => 'control-label required']) !!}
                                        {!! Form::password('owner_password', ['class' => 'form-control']) !!}
                                        <span class="help-block">{{ $errors->first('owner_password', ':message') }}</span>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('owner_password_confirmation') ? 'has-error' : '' }}">
                                        {!! Form::label('owner_password_confirmation', trans('organizations.password_confirmation'), ['class' => 'control-label required']) !!}
                                        {!! Form::password('owner_password_confirmation', ['class' => 'form-control']) !!}
                                        <span class="help-block">{{ $errors->first('owner_password_confirmation', ':message') }}</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">{{trans('auth.register')}}</button>
                            {!! Form::close() !!}
                            <div class="text-center mt-3">
                                <span class="text-white">Already have an account?</span>
                                <a href="{{url('signin')}}" class="text-primary _600 text-white">{{trans('organizations.login')}} </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
