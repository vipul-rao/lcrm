<div class="tab-pane" id="europian_tax">
    <div class="form-group required {{ $errors->has('europian_tax') ? 'has-error' : '' }}">
        <div class="alert alert-danger">
            {{ trans('settings.vat_applied_to_all_users') }}
        </div>
        {!! Form::label('email_driver', trans('settings.europian_tax'), ['class' => 'control-label']) !!}
        <div class="controls">
            <div class="form-inline">
                <div class="radio">
                    <div class="form-inline">
                        {!! Form::radio('europian_tax', 'true',(isset($settings['europian_tax'])&&$settings['europian_tax']=='true')?true:false,['id'=>'europian_tax_true','class' => 'europian_tax icheck'])  !!}
                        {!! Form::label('true', 'TRUE',['class'=>'ml-1 mr-2'])  !!}
                    </div>
                </div>
                <div class="radio">
                    <div class="form-inline">
                        {!! Form::radio('europian_tax', 'false', (isset($settings['europian_tax'])&&$settings['europian_tax']=='false')?true:false,['id'=>'europian_tax_false','class' => 'europian_tax icheck'])  !!}
                        {!! Form::label('false', 'FALSE',['class'=>'ml-1']) !!}
                    </div>
                </div>
            </div>
            <span class="help-block">{{ $errors->first('europian_tax', ':message') }}</span>
        </div>
    </div>
</div>