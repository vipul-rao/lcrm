<div class="tab-pane" id="stripe_settings">
    <div class="alert alert-danger">
        Don't change these values as this will cause problems with already existing plans, subscriptions. Only enter initialize them once.
    </div>
    <div class="form-group required {{ $errors->has('stripe_publishable') ? 'has-error' : '' }}">
        {!! Form::label('stripe_publishable', trans('settings.stripe_publishable'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','stripe_publishable', old('stripe_publishable', isset($settings['stripe_publishable'])?$settings['stripe_publishable']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('stripe_publishable', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('stripe_secret') ? 'has-error' : '' }}">
        {!! Form::label('stripe_secret', trans('settings.stripe_secret'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','stripe_secret', old('stripe_secret', isset($settings['stripe_secret'])?$settings['stripe_secret']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('stripe_secret', ':message') }}</span>
        </div>
    </div>
</div>
