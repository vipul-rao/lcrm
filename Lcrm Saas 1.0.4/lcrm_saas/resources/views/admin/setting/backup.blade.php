<div class="tab-pane" id="backup_configuration">
    <backup-settings type="{{ $settings['backup_type']??'' }}" :options="{{ $backup_type }}" inline-template>
        <div>
            <div class="form-group required {{ $errors->has('backup_type') ? 'has-error' : '' }}">
                {!! Form::label('backup_type', trans('settings.backup_type'), ['class' => 'control-label']) !!}
                <div class="controls">
                    <select v-model="backup_type" name="backup_type" class="form-control">
                        <option v-for="option in options" :value="option.id">@{{ option.text }}</option>
                    </select>
                    <span class="help-block">{{ $errors->first('backup_type', ':message') }}</span>
                </div>
            </div>
            {{-- Dropbox --}}
            <div v-if="backup_type=='dropbox'">
                <div class="form-group required {{ $errors->has('disk_dbox_token') ? 'has-error' : '' }}">
                    {!! Form::label('disk_dbox_token', trans('settings.disk_dbox_token'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::text('disk_dbox_token', old('disk_dbox_token', isset($settings['disk_dbox_token'])?$settings['disk_dbox_token']:''), ['class' => 'form-control'])
                        !!}
                        <span class="help-block">{{ $errors->first('disk_dbox_token', ':message') }}</span>
                    </div>
                </div>
                <div class="form-group required {{ $errors->has('disk_dbox_app') ? 'has-error' : '' }}">
                    {!! Form::label('disk_dbox_app', trans('settings.disk_dbox_app'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::text('disk_dbox_app', old('disk_dbox_app', isset($settings['disk_dbox_app'])?$settings['disk_dbox_app']:''), ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('disk_dbox_app', ':message') }}</span>
                    </div>
                </div>
            </div>

            <div v-if="backup_type=='s3'">
                {{-- AWS S3 --}}
                <div class="form-group required {{ $errors->has('disk_aws_key') ? 'has-error' : '' }}">
                    {!! Form::label('disk_aws_key', trans('settings.disk_aws_key'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::text('disk_aws_key', old('disk_aws_key', isset($settings['disk_aws_key'])?$settings['disk_aws_key']:''), ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('disk_aws_key', ':message') }}</span>
                    </div>
                </div>

                <div class="form-group required {{ $errors->has('disk_aws_secret') ? 'has-error' : '' }}">
                    {!! Form::label('disk_aws_secret', trans('settings.disk_aws_secret'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::text('disk_aws_secret', old('disk_aws_secret', isset($settings['disk_aws_secret'])?$settings['disk_aws_secret']:''), ['class' => 'form-control'])
                        !!}
                        <span class="help-block">{{ $errors->first('disk_aws_secret', ':message') }}</span>
                    </div>
                </div>


                <div class="form-group required {{ $errors->has('disk_aws_bucket') ? 'has-error' : '' }}">
                    {!! Form::label('disk_aws_bucket', trans('settings.disk_aws_bucket'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::text('disk_aws_bucket', old('disk_aws_bucket', isset($settings['disk_aws_bucket'])?$settings['disk_aws_bucket']:''), ['class' => 'form-control'])
                        !!}
                        <span class="help-block">{{ $errors->first('site_nbucket', ':message') }}</span>
                    </div>
                </div>


                <div class="form-group required {{ $errors->has('disk_aws_region') ? 'has-error' : '' }}">
                    {!! Form::label('disk_aws_region', trans('settings.disk_aws_region'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::text('disk_aws_region', old('disk_aws_region',isset($settings['disk_aws_region'])?$settings['disk_aws_region']:''), ['class' => 'form-control'])
                        !!}
                        <span class="help-block">{{ $errors->first('site_nregion', ':message') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </backup-settings>
</div>
