<div class="tab-pane" id="gmaps">
    <div class="form-group required {{ $errors->has('google_maps_key') ? 'has-error' : '' }}">
        {!! Form::label('google_maps_key', trans('settings.google_maps_key'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','google_maps_key', old('google_maps_key', isset($settings['google_maps_key'])?$settings['google_maps_key']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('google_maps_key', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('latitude') ? 'has-error' : '' }}">
        {!! Form::label('latitude', trans('settings.latitude'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','latitude', old('latitude', isset($settings['latitude'])?$settings['latitude']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('latitude', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('longitude') ? 'has-error' : '' }}">
        {!! Form::label('longitude', trans('settings.longitude'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','longitude', old('longitude', isset($settings['longitude'])?$settings['longitude']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('longitude', ':message') }}</span>
        </div>
    </div>
</div>
