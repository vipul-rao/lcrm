@extends('layouts.frontend.user')
@section('styles')
    <link href="{{ asset('css/login_register.css') }}" rel="stylesheet" type="text/css">
@stop
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="m-auto col-lg-4 col-md-6 col-sm-10 col-12 signin-form">
                    <div class="box-color text-color">
                        <h4 class="text-center text-white">{{trans('auth.forgot')}}</h4>
                        <div class="row">
                            <div class="col-12 m-t-20">
                                {!! Form::open(['url' => url('password'), 'method' => 'post']) !!}
                                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                    {!! Form::label(trans('auth.email')) !!} :
                                    <span>{{ $errors->first('email', ':message') }}</span>
                                    {!! Form::email('email', null, ['class' => 'form-control', 'required'=>'required', 'placeholder'=>'E-mail']) !!}
                                </div>
                                <input type="submit" class="btn btn-primary btn-block" value="{{trans('auth.send_reset')}}">
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <h5 class="text-center mt-3">
                            <a href="{{url('signin')}}" class="text-white">{{trans('auth.login')}}?</a>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop