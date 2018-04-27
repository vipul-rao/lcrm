@extends('layouts.frontend.user')
@section('styles')
    <link href="{{ asset('css/login_register.css') }}" rel="stylesheet" type="text/css">
@stop
@section('content')
    <div class="content">
        <div class="container">
            <div class="content">
                <div class="row">
                    <div class="m-auto col-lg-4 col-md-6 col-sm-10 col-12 signin-form">
                        <div class="box-color">
                            <h4 class="text-center text-white">{{trans('auth.login')}}</h4>
                            <div class="row">
                                <div class="col-12 m-t-20">
                                    {!! Form::open(['url' => url('signin'), 'method' => 'post', 'name' => 'form']) !!}
                                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                        {!! Form::label(trans('auth.email')) !!} :
                                        {!! Form::email('email', null, ['class' => 'form-control', 'required'=>'required', 'placeholder'=>'E-mail', 'autofocus'=>true]) !!}
                                        <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                                    </div>
                                    <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                                        {!! Form::label(trans('auth.password')) !!} :
                                        {!! Form::password('password', ['class' => 'form-control', 'required'=>'required', 'placeholder'=>'Password']) !!}
                                        <span class="help-block">{{ $errors->first('password', ':message') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="remember" value="remember" name="remember">
                                            <i class="primary"></i> {{trans('auth.keep_login')}}
                                        </label>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="submit" class="btn btn-primary btn-block">{{trans('auth.login')}}</button>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{url('register')}}" class="btn btn-success btn-block">{{trans('auth.signup')}}</a>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                            <hr class="separator">
                            <div class="text-center">
                                <h5><a href="{{url('forgot')}}" class="forgot_pw _600">{{trans('auth.forgot')}}?</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
