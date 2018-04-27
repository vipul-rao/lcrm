<div class="tab-pane" id="pusher_configuration">
    <div class="form-group required {{ $errors->has('pusher_app_id') ? 'has-error' : '' }}">
        {!! Form::label('pusher_app_id', trans('settings.pusher_app_id'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','pusher_app_id', old('pusher_app_id', isset($settings['pusher_app_id'])?$settings['pusher_app_id']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('pusher_app_id', ':message') }}</span>
        </div>
    </div>

    <div class="form-group required {{ $errors->has('pusher_key') ? 'has-error' : '' }}">
        {!! Form::label('pusher_key', trans('settings.pusher_key'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','pusher_key', old('pusher_key', isset($settings['pusher_key'])?$settings['pusher_key']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('pusher_key', ':message') }}</span>
        </div>
    </div>

    <div class="form-group required {{ $errors->has('pusher_secret') ? 'has-error' : '' }}">
        {!! Form::label('pusher_secret', trans('settings.pusher_secret'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','pusher_secret', old('pusher_secret', isset($settings['pusher_secret'])?$settings['pusher_secret']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('pusher_secret', ':message') }}</span>
        </div>
    </div>
</div>
