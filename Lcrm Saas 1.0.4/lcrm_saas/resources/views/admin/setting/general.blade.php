<div class="tab-pane active" id="general_configuration">
    <div class="form-group required {{ $errors->has('site_logo_file') ? 'has-error' : '' }} ">
        {!! Form::label('site_logo_file', trans('settings.site_logo'), ['class' => 'control-label required']) !!}
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <image-upload name="site_logo_file" old-image="{{ url((isset($settings['site_logo'])?$settings['site_logo']:'logo.png')) }}"></image-upload>
                </div>
            </div>
        </div>
        <span class="help-block">{{ $errors->first('site_logo_file', ':message') }}</span>
    </div>
    <div class="form-group required {{ $errors->has('site_name') ? 'has-error' : '' }}">
        {!! Form::label('site_name', trans('settings.site_name'), ['class' => 'control-label required']) !!}
        <div class="controls">
            {!! Form::text('site_name', old('site_name', isset($settings['site_name'])?$settings['site_name']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('site_name', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('site_email') ? 'has-error' : '' }}">
        {!! Form::label('site_email', trans('settings.site_email'), ['class' => 'control-label required']) !!}
        <div class="controls">
            {!! Form::text('site_email', old('site_email', isset($settings['site_email'])?$settings['site_email']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('site_email', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('phone_number') ? 'has-error' : '' }}">
        {!! Form::label('phone_number', trans('settings.phone_number'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::text('phone_number', old('phone_number', isset($settings['phone_number'])?$settings['phone_number']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('phone_number', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('address') ? 'has-error' : '' }}">
        {!! Form::label('address', trans('settings.address'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::textarea('address', old('address', isset($settings['address'])?$settings['address']:''), ['class' => 'form-control','rows'=>5]) !!}
            <span class="help-block">{{ $errors->first('address', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('currency') ? 'has-error' : '' }}">
        {!! Form::label('currency', trans('settings.currency'), ['class' => 'control-label required']) !!}
        <div class="controls">
            {!! Form::select('currency', $currency, old('currency', $settings['currency']??null), ['id'=>'currency','class' => 'form-control select2']) !!}
            <span class="help-block">{{ $errors->first('currency', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('allowed_extensions') ? 'has-error' : '' }}">
        {!! Form::label('allowed_extensions', trans('settings.allowed_extensions'), ['class' => 'control-label required']) !!}
        <div class="controls">
            {!! Form::text('allowed_extensions', old('allowed_extensions',isset($settings['allowed_extensions'])?$settings['allowed_extensions']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('allowed_extensions', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('max_upload_file_size') ? 'has-error' : '' }}">
        {!! Form::label('max_upload_file_size', trans('settings.max_upload_file_size'), ['class' => 'control-label required']) !!}
        <div class="controls">
            {!! Form::select('max_upload_file_size', $max_upload_file_size, old('max_upload_file_size',isset($settings['max_upload_file_size'])?$settings['max_upload_file_size']:''), ['id'=>'max_upload_file_size','class' => 'form-control select2']) !!}
            <span class="help-block">{{ $errors->first('max_upload_file_size', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('minimum_characters') ? 'has-error' : '' }}">
        {!! Form::label('minimum_characters', trans('settings.minimum_characters'), ['class' => 'control-label required']) !!}
        <div class="controls">
            {!! Form::text('minimum_characters', old('minimum_characters', isset($settings['minimum_characters'])?$settings['minimum_characters']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('minimum_characters', ':message') }}</span>
        </div>
    </div>
    <div class="form-group required {{ $errors->has('date_format') ? 'has-error' : '' }}">
        {!! Form::label('date_format', trans('settings.date_format'), ['class' => 'control-label required']) !!}
        <div class="controls">
            <div class="radio">
                {!! Form::radio('date_format', 'F j,Y',(isset($settings['date_format'])&&$settings['date_format']=='F j,Y')?true:false,['class' => 'icheck'])  !!}
                {!! Form::label('true', date('F j,Y'))  !!}
            </div>
            <div class="radio">
                {!! Form::radio('date_format', 'Y-d-m',(isset($settings['date_format'])&&$settings['date_format']=='Y-d-m')?true:false,['class' => 'icheck'])  !!}
                {!! Form::label('date_format', date('Y-d-m'))  !!}
            </div>
            <div class="radio">
                {!! Form::radio('date_format', 'd.m.Y.',(isset($settings['date_format'])&&$settings['date_format']=='d.m.Y.')?true:false,['class' => 'icheck'])  !!}
                {!! Form::label('date_format', date('d.m.Y.'))  !!}
            </div>
            <div class="radio">
                {!! Form::radio('date_format', 'd.m.Y',(isset($settings['date_format'])&&$settings['date_format']=='d.m.Y')?true:false,['class' => 'icheck'])  !!}
                {!! Form::label('date_format', date('d.m.Y'))  !!}
            </div>
            <div class="radio">
                {!! Form::radio('date_format', 'd/m/Y',(isset($settings['date_format'])&&$settings['date_format']=='d/m/Y')?true:false,['class' => 'icheck'])  !!}
                {!! Form::label('date_format', date('d/m/Y'))  !!}
            </div>
            <div class="radio">
                {!! Form::radio('date_format', 'm/d/Y',(isset($settings['date_format'])&&$settings['date_format']=='m/d/Y')?true:false,['class' => 'icheck'])  !!}
                {!! Form::label('date_format', date('m/d/Y'))  !!}
            </div>
            <div class="form-inline">
                {!! Form::label('custom_format', trans('settings.custom_format'))  !!}
                {!! Form::input('text','date_format_custom', (isset($settings['date_format'])?$settings['date_format']:"d-m-Y"), ['class' => 'form-control']) !!}
            </div>
        </div>
        <span class="help-block">{{ $errors->first('date_format', ':message') }}</span>
    </div>
    <a href="{{url('http://php.net/manual/en/function.date.php')}}">{!! trans('settings.date_time_format') !!}</a>
    <div class="form-group required {{ $errors->has('time_format') ? 'has-error' : '' }}">
        {!! Form::label('time_format', trans('settings.time_format'), ['class' => 'control-label required']) !!}
        <div class="controls">
            <div class="radio">
                {!! Form::radio('time_format', 'g:i a',(isset($settings['time_format'])&&$settings['time_format']=='g:i a')?true:false,['class' => 'icheck'])  !!}
                {!! Form::label('time_format', date('g:i a'))  !!}
            </div>
            <div class="radio">
                {!! Form::radio('time_format', 'g:i A',(isset($settings['time_format'])&&$settings['time_format']=='g:i A')?true:false,['class' => 'icheck'])  !!}
                {!! Form::label('time_format', date('g:i A'))  !!}
            </div>
            <div class="radio">
                {!! Form::radio('time_format', 'H:i',(isset($settings['time_format'])&&$settings['time_format']=='H:i')?true:false,['class' => 'icheck'])  !!}
                {!! Form::label('time_format', date('H:i'))  !!}
            </div>
            <div class="form-inline">
                {!! Form::label('custom_format', trans('settings.custom_format'))  !!}
                {!! Form::input('text','time_format_custom', isset($settings['time_format'])?$settings['time_format']:"", ['class' => 'form-control']) !!}
            </div>
        </div>
        <span class="help-block">{{ $errors->first('date_format', ':message') }}</span>
    </div>
    <div class="form-group required {{ $errors->has('language') ? 'has-error' : '' }}">
        {!! Form::label('language', trans('option.language'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::select('language', $languages, old('language',isset($settings['language'])?$settings['language']:''), ['class' => 'form-control select2']) !!}
            <span class="help-block">{{ $errors->first('language', ':message') }}</span>
        </div>
    </div>
</div>
