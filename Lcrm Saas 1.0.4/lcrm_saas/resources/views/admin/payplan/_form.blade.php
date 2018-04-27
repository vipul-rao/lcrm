<div class="card">
    <div class="card-body">
        @if (isset($payplan))
            {!! Form::model($payplan, ['url' => $type . '/' . $payplan->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
        @endif
        <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
            {!! Form::label('name', trans('payplan.name'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('name', ':message') }}</span>
            </div>
        </div>
        @if (!isset($payplan))
            <div class="form-group required {{ $errors->has('amount') ? 'has-error' : '' }}">
                {!! Form::label('amount', trans('payplan.amount').' ('.trans('payplan.amount_info').')', ['class' => 'control-label required']) !!}
                <div class="controls">
                    {!! Form::text('amount', null, ['class' => 'form-control']) !!}
                    <span class="help-block">{{ $errors->first('amount', ':message') }}</span>
                </div>
            </div>
            <div class="form-group required {{ $errors->has('currency') ? 'has-error' : '' }}">
                {!! Form::label('currency', trans('payplan.currency'), ['class' => 'control-label required']) !!}
                <div class="controls">
                    {!! Form::select('currency', $currency,  $settings['currency']??null, ['id'=>'currency', 'class' => 'form-control']) !!}
                    <span class="help-block">{{ $errors->first('currency', ':message') }}</span>
                </div>
            </div>
            <div class="form-group required {{ $errors->has('interval') ? 'has-error' : '' }}">
                {!! Form::label('interval', trans('payplan.interval'), ['class' => 'control-label required']) !!}
                <div class="controls">
                    {!! Form::select('interval', $interval, null, ['id'=>'interval', 'class' => 'form-control']) !!}
                    <span class="help-block">{{ $errors->first('interval', ':message') }}</span>
                </div>
            </div>
            <div class="form-group required {{ $errors->has('interval_count') ? 'has-error' : '' }}">
                {!! Form::label('interval_count', trans('payplan.interval_count'), ['class' => 'control-label required']) !!}
                <div class="controls">
                    {!! Form::number('interval_count', 1, ['class' => 'form-control','data-fv-integer' => 'true','min'=>1]) !!}
                    <span class="help-block">{{ $errors->first('interval_count', ':message') }}</span>
                </div>
            </div>
        @endif
        <div class="form-group required {{ $errors->has('no_people') ? 'has-error' : '' }}">
            {!! Form::label('no_people', trans('payplan.no_people').' '.trans('payplan.no_people_info'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::number('no_people', null, ['class' => 'form-control','data-fv-integer' => 'true','min'=>0]) !!}
                <span class="help-block">{{ $errors->first('no_people', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('trial_period_days') ? 'has-error' : '' }}">
            {!! Form::label('trial_period_days', trans('payplan.trial_period_days'), ['class' => 'control-label']) !!}
            <div class="controls">
                {!! Form::number('trial_period_days', null, ['class' => 'form-control','data-fv-integer' => 'true','min'=>0]) !!}
                <span class="help-block">{{ $errors->first('trial_period_days', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('statement_descriptor') ? 'has-error' : '' }}">
            {!! Form::label('statement_descriptor', trans('payplan.statement_descriptor'), ['class' => 'control-label']) !!}
            <div class="controls">
                {!! Form::text('statement_descriptor', null ,['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('statement_descriptor', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('is_credit_card_required') ? 'has-error' : '' }}">
            {!! Form::label('card_type',trans('payplan.card_type'), ['class' => 'control-label required']) !!}
            <div class="input-group">
                <label>
                    <input type="radio" name="is_credit_card_required" value="1" class='icheckblue'
                           @if(old('is_credit_card_required', isset($payplan)&&$payplan->is_credit_card_required == 1?1:0) ==  1) checked @endif>
                    {{trans('payplan.with_card')}}
                </label>
                <label>
                    <input type="radio" name="is_credit_card_required" value="0" class='icheckblue'
                           @if(old('is_credit_card_required', isset($payplan)&&$payplan->is_credit_card_required == 0?0:1) ==  0) checked @endif>
                    {{trans('payplan.without_card')}}
                </label>
                <div class="w-100">
                    <span class="help-block">{{ $errors->first('is_credit_card_required', ':message') }}</span>
                </div>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('is_visible') ? 'has-error' : '' }}">
            {!! Form::label('card_visibility',trans('payplan.plan_visibility'), ['class' => 'control-label']) !!}
            <div class="input-group">
                <label>
                    {{ Form::checkbox('is_visible', '1', isset($payplan)?$payplan->is_visible:old('is_visible', true), ['class' => 'icheckblue']) }}
                    {{trans('payplan.is_visible')}}
                </label>
                <span class="help-block">{{ $errors->first('is_visible', ':message') }}</span>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}
                </button>
                <a href="{{ url($type) }}" class="btn btn-warning"><i
                            class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
            </div>
        </div>
        <!-- ./ form actions -->

        {!! Form::close() !!}
    </div>
</div>
@section('scripts')
    <script>
        $(document).ready(function () {
            $('.icheckblue').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $("#currency").select2({
                theme: "bootstrap",
                placeholder: "{{trans('payplan.currency')}}"
            });
            $("#interval").select2({
                theme: "bootstrap",
                placeholder: "{{trans('payplan.interval')}}"
            });
        });
    </script>
@stop
