@extends('layouts.user')
@section('content')
    <div class="card">
        <div class="card-body">
            {!! Form::model($user, ['url' => url('account/'.$user->id), 'method' => 'put', 'files'=> true]) !!}
            <div class="row">
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('user_avatar_file') ? 'has-error' : '' }}">
                        {!! Form::label('user_avatar_file', trans('profile.avatar'), ['class' => 'control-label']) !!}
                        <div class="controls row">
                            <div class="col-sm-6 col-lg-4">
                                <div class="row">
                                    @if(isset($user->user_avatar))
                                        <image-upload name="user_avatar_file" old-image="{{ url('uploads/avatar/thumb_'.$user->user_avatar) }}"></image-upload>
                                    @else
                                        <image-upload name="user_avatar_file" old-image="{{ url('uploads/avatar/user.png') }}"></image-upload>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <span class="help-block">{{ $errors->first('user_avatar_file', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('first_name') ? 'has-error' : '' }}">
                        {!! Form::label('first_name', trans('profile.first_name'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('first_name', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('last_name') ? 'has-error' : '' }}">
                        {!! Form::label('last_name', trans('profile.last_name'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('last_name', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                        {!! Form::label('phone_number', trans('staff.phone_number'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('phone_number', null, ['class' => 'form-control','data-fv-integer' => 'true']) !!}
                            <span class="help-block">{{ $errors->first('phone_number', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                        {!! Form::label('email', trans('profile.email'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('email', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('password') ? 'has-error' : '' }}">
                        {!! Form::label('password', trans('profile.password'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::password('password', ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('password', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                        {!! Form::label('password_confirmation', trans('profile.password_confirmation'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('password_confirmation', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="controls">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                    <a href="{{ url('/profile') }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@stop
