@extends('layouts.user')
{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop
@section('content')

<div class="card">
<div class="card-body">
    <div class="alert alert-warning">
        <p>
            {{ trans('subscription.extend_reason') }}
        </p>
    </div>
    @if (isset($subscription))
        {!! Form::model($subscription, ['url' => $type . '/' . $subscription->id.'/extend', 'method' => 'post', 'files'=> true]) !!}
    @else
        {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
    @endif
    <div class="form-group required {{ $errors->has('duration') ? 'has-error' : '' }}">
        {!! Form::label('duration', trans('subscription.duration'), ['class' => 'control-label required']) !!}
        <div class="controls">
            {!! Form::text('duration', null, ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('duration', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('reason') ? 'has-error' : '' }}">
        {!! Form::label('reason', trans('subscription.reason'), ['class' => 'control-label required']) !!}
        <div class="controls">
            {!! Form::textarea('reason', null, ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('reason', ':message') }}</span>
        </div>
    </div>
    <!-- Form Actions -->
    <div class="form-group">
        <div class="controls">
            <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
            <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
        </div>
    </div>
    <!-- ./ form actions -->
    {!! Form::close() !!}
</div>
</div>
@endsection
