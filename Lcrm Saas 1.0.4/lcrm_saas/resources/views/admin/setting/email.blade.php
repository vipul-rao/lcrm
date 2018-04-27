<div class="tab-pane" id="email_configuration">
    <div class="form-group required {{ $errors->has('email_driver') ? 'has-error' : '' }}">
        {!! Form::label('email_driver', trans('settings.email_driver'), ['class' => 'control-label required']) !!}
        <div class="controls">
            <div class="form-inline">
                <div class="radio">
                    <div class="form-inline">
                        {!! Form::radio('email_driver', 'mail',(isset($settings['email_driver'])&&$settings['email_driver']=='mail')?true:false,['id'=>'mail','class' => 'email_driver icheck'])  !!}
                        {!! Form::label('true', 'MAIL',['class'=>'ml-1 mr-2'])  !!}
                    </div>
                </div>
                <div class="radio">
                    <div class="form-inline">
                        {!! Form::radio('email_driver', 'smtp', (isset($settings['email_driver'])&&$settings['email_driver']=='smtp')?true:false,['id'=>'smtp','class' => 'email_driver icheck'])  !!}
                        {!! Form::label('false', 'SMTP',['class'=>'ml-1']) !!}
                    </div>
                </div>
            </div>
            <span class="help-block">{{ $errors->first('email_driver', ':message') }}</span>
        </div>
    </div>
    <div class="form-group smtp required {{ $errors->has('email_host') ? 'has-error' : '' }}">
        {!! Form::label('email_host', trans('settings.email_host'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','email_host', old('email_host', isset($settings['email_host'])?$settings['email_host']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('email_host', ':message') }}</span>
        </div>
    </div>
    <div class="form-group smtp required {{ $errors->has('email_port') ? 'has-error' : '' }}">
        {!! Form::label('email_port', trans('settings.email_port'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','email_port', old('email_port', isset($settings['email_port'])?$settings['email_port']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('email_port', ':message') }}</span>
        </div>
    </div>
    <div class="form-group smtp required {{ $errors->has('email_username') ? 'has-error' : '' }}">
        {!! Form::label('email_username', trans('settings.email_username'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','email_username', old('email_username', isset($settings['email_username'])?$settings['email_username']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('email_username', ':message') }}</span>
        </div>
    </div>
    <div class="form-group smtp required {{ $errors->has('email_password') ? 'has-error' : '' }}">
        {!! Form::label('email_password', trans('settings.email_password'), ['class' => 'control-label']) !!}
        <div class="controls">
            {!! Form::input('text','email_password', old('email_password', isset($settings['email_password'])?$settings['email_password']:''), ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('email_password', ':message') }}</span>
        </div>
    </div>
</div>
@section('scripts')
    <script>
        $(document).ready(function($) {
            $(".select2").select2({
                theme:"bootstrap"
            });
            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });

            $("#smtp").on("ifChecked",function(){
                $('.smtp').show();
            });
            $("#smtp").on("ifUnchecked",function(){
                $(".smtp").hide();
            });
            if($("#smtp").closest(".iradio_minimal-blue").hasClass("checked")){
                $('.smtp').show();
            }else{
                $('.smtp').hide();
            }

            $("input[name='date_format']").on('ifChecked', function () {
                if ("date_format_custom_radio" != $(this).attr("id"))
                    $("input[name='date_format_custom']").val($(this).val()).siblings('.example').text($(this).siblings('span').text());
            });
            $("input[name='date_format_custom']").focus(function () {
                $("#date_format_custom_radio").attr("checked", "checked");
            });

            $("input[name='time_format']").on('ifChecked', function () {
                if ("time_format_custom_radio" != $(this).attr("id"))
                    $("input[name='time_format_custom']").val($(this).val()).siblings('.example').text($(this).siblings('span').text());
            });
            $("input[name='time_format_custom']").focus(function () {
                $("#time_format_custom_radio").attr("checked", "checked");
            });
            $("input[name='date_format_custom'], input[name='time_format_custom']").on('ifChecked', function () {
                var format = $(this);
                format.siblings('img').css('visibility', 'visible');
                $.post(ajaxurl, {
                    action: 'date_format_custom' == format.attr('name') ? 'date_format' : 'time_format',
                    date: format.val()
                }, function (d) {
                    format.siblings('img').css('visibility', 'hidden');
                    format.siblings('.example').text(d);
                });
            });
            $(".paypal_live,.paypal_sandbox").hide();
            if('{{ isset($settings['paypal_mode']) && !empty($settings['paypal_mode']) }}'){
                if('{{ isset($settings['paypal_mode']) && $settings['paypal_mode']=='sandbox' }}'){
                    $(".paypal_sandbox").show();
                }else{
                    $(".paypal_live").show();
                }
            }
            if('{{ old('paypal_mode')=='sandbox' }}'){
                $(".paypal_sandbox").show();
                $(".paypal_live").hide();
            }
            if('{{ old('paypal_mode')=='live' }}'){
                $(".paypal_sandbox").hide();
                $(".paypal_live").show();
            }
            $(".sandbox").on("ifChecked",function(){
                $(".paypal_sandbox").show();
                $(".paypal_live").hide();
            });
            $(".sandbox").on("ifUnchecked",function(){
                $(".paypal_sandbox").hide();
                $(".paypal_live").show();
            });
        });
    </script>
@stop
