<div class="tab-pane" id="paypal_settings">
    <div class="form-group required {{ $errors->has('paypal_mode') ? 'has-error' : '' }}">
        {!! Form::label('paypal_mode', trans('settings.paypal_mode'), ['class' => 'control-label']) !!}
        <div class="controls">
            <div class="form-inline">
                <div class="radio">
                    <div class="form-inline">
                        {!! Form::radio('paypal_mode', 'sandbox',(isset($settings['paypal_mode']) && $settings['paypal_mode']=='sandbox')?true:false,['class' => 'icheck sandbox'])  !!}
                        {!! Form::label('true', trans('settings.sandbox'),['class'=>'ml-1 mr-2'])  !!}
                    </div>
                </div>
                <div class="radio">
                    <div class="form-inline">
                        {!! Form::radio('paypal_mode', 'live', (isset($settings['paypal_mode']) && $settings['paypal_mode']=='live')?true:false,['class' => 'icheck live'])  !!}
                        {!! Form::label('false', trans('settings.live'),['class'=>'ml-1 mr-2']) !!}
                    </div>
                </div>
            </div>
            <span class="help-block">{{ $errors->first('paypal_mode', ':message') }}</span>
        </div>
    </div>
    <div class="paypal_sandbox">
        <div class="form-group required {{ $errors->has('paypal_sandbox_username') ? 'has-error' : '' }}">
            {!! Form::label('paypal_sandbox_username', trans('settings.paypal_sandbox_username'), ['class' => 'control-label']) !!}
            <div class="controls">
                {!! Form::input('text','paypal_sandbox_username', old('paypal_sandbox_username', (isset($settings['paypal_sandbox_username'])?$settings['paypal_sandbox_username']:"")), ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('paypal_sandbox_username', ':message') }}</span>
            </div>
        </div>

        <div class="form-group required {{ $errors->has('paypal_sandbox_password') ? 'has-error' : '' }}">
            {!! Form::label('paypal_sandbox_password', trans('settings.paypal_sandbox_password'), ['class' => 'control-label']) !!}
            <div class="controls">
                {!! Form::input('text','paypal_sandbox_password', old('paypal_sandbox_password', (isset($settings['paypal_sandbox_password'])?$settings['paypal_sandbox_password']:"")), ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('paypal_sandbox_password', ':message') }}</span>
            </div>
        </div>

        <div class="form-group required {{ $errors->has('paypal_sandbox_signature') ? 'has-error' : '' }}">
            {!! Form::label('paypal_sandbox_signature', trans('settings.paypal_sandbox_signature'), ['class' => 'control-label']) !!}
            <div class="controls">
                {!! Form::input('text','paypal_sandbox_signature', old('paypal_sandbox_signature', (isset($settings['paypal_sandbox_signature'])?$settings['paypal_sandbox_signature']:"")), ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('paypal_sandbox_signature', ':message') }}</span>
            </div>
        </div>
    </div>
    <div class="paypal_live">
        <div class="form-group required {{ $errors->has('paypal_live_username') ? 'has-error' : '' }}">
            {!! Form::label('paypal_live_username', trans('settings.paypal_live_username'), ['class' => 'control-label']) !!}
            <div class="controls">
                {!! Form::input('text','paypal_live_username', old('paypal_live_username', (isset($settings['paypal_live_username'])?$settings['paypal_live_username']:"")), ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('paypal_live_username', ':message') }}</span>
            </div>
        </div>

        <div class="form-group required {{ $errors->has('paypal_live_password') ? 'has-error' : '' }}">
            {!! Form::label('paypal_live_password', trans('settings.paypal_live_password'), ['class' => 'control-label']) !!}
            <div class="controls">
                {!! Form::input('text','paypal_live_password', old('paypal_live_password', (isset($settings['paypal_live_password'])?$settings['paypal_live_password']:"")), ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('paypal_live_password', ':message') }}</span>
            </div>
        </div>

        <div class="form-group required {{ $errors->has('paypal_live_signature') ? 'has-error' : '' }}">
            {!! Form::label('paypal_live_signature', trans('settings.paypal_live_signature'), ['class' => 'control-label']) !!}
            <div class="controls">
                {!! Form::input('text','paypal_live_signature', old('paypal_live_signature', (isset($settings['paypal_live_signature'])?$settings['paypal_live_signature']:"")), ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('paypal_live_signature', ':message') }}</span>
            </div>
        </div>
    </div>
</div>