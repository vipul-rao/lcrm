@extends('layouts.user')
{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    @include('flash::message')
    @if($errors->any())
        <div class="alert alert-danger">
            {{  trans('settings.mandatory_fields_valid') }}
        </div>
    @endif
    <div class="card" xmlns:v-bind="http://symfony.com/schema/routing">
        <div class="card-body">
            {!! Form::open(['url' => url('customers/setting'), 'method' => 'post', 'files'=> true]) !!}
            <div class="nav-tabs-custom" id="setting_tabs">
                <ul class="nav nav-tabs settings">
                    <li class="nav-item">
                        <a href="#europian_tax"
                           data-toggle="tab" title="{{ trans('settings.europian_tax') }}"><i
                                    class="fa fa-money md-24"></i></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="europian_tax">
                        <div class="alert alert-danger">
                            Vat is applied to all customers (Who don't have Vat Number).
                        </div>
                        @if(isset($europian_tax) && $europian_tax=='true')
                            <div class="form-group vat required {{ $errors->has('vat_number') ? 'has-error' : '' }}">
                                {!! Form::label('vat_number', trans('settings.vat_number'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {!! Form::input('text','vat_number', old('vat_number', isset($companySettings['vat_number'])?$companySettings['vat_number']:''), ['class' => 'form-control','placeholder'=>'Provide your Vat Number to avoid European Vat']) !!}
                                    <span class="help-block">{{ $errors->first('vat_number', ':message') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Form Actions -->
            <div class="form-group">
                <div class="controls">
                    <button type="submit" class="btn btn-success"><i
                                class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                </div>
            </div>
            <!-- ./ form actions -->

            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $(".select2").select2({
                theme:"bootstrap"
            });
//            $("#language").select2({
//                theme:'bootstrap'
//            });
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
            if('{{ isset($orgSettings['paypal_mode']) && !empty($orgSettings['paypal_mode']) }}'){
                if('{{ isset($orgSettings['paypal_mode']) && $orgSettings['paypal_mode']=='sandbox' }}'){
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
