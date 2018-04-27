<div class="card">
    <div class="card-body">
        @if (isset($call))
            {!! Form::model($call, ['url' => $type . '/' . $call->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
        @endif
        <div class="row">
            <div class="col-12">
                @if(Request::is('call/create'))
                    <div class="form-group {{ $errors->has('company_id') ? 'has-error' : '' }}">
                        {!! Form::label('company_id', trans('call.company'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('company_id', $companies, null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('company_id', ':message') }}</span>
                        </div>
                    </div>
                @endif
                @if (isset($call))
                    @if(is_int($call->company_id) && $call->company_id>0)
                        <div class="form-group {{ $errors->has('company_id') ? 'has-error' : '' }}">
                            {!! Form::label('company_id', trans('call.company'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::select('company_id', $companies, null, ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('company_id', ':message') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="form-group {{ $errors->has('company_name') ? 'has-error' : '' }}">
                            {!! Form::label('company_name', trans('call.company'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::text('company_name', $call->company_name, ['class' => 'form-control', 'readonly'=>'readonly']) !!}
                                <span class="help-block">{{ $errors->first('company_name', ':message') }}</span>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('date') ? 'has-error' : '' }}">
                    {!! Form::label('date', trans('call.date'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::text('date', isset($call) ? $call->call_date : null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('date', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('duration') ? 'has-error' : '' }}">
                    {!! Form::label('duration', trans('call.duration'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::input('number','duration',null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('duration', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('call_summary') ? 'has-error' : '' }}">
                    {!! Form::label('call_summary', trans('call.summary'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::text('call_summary', null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('call_summary', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('resp_staff_id') ? 'has-error' : '' }}">
                    {!! Form::label('resp_staff_id', trans('call.main_staff'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::select('resp_staff_id', $staffs, null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('resp_staff_id', ':message') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                <a href="{{ route($type.'.index') }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
            </div>
        </div>
        <!-- ./ form actions -->

        {!! Form::close() !!}
    </div>
</div>
@section('scripts')
    <script>
        $(document).ready(function() {
            $("#company_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('call.company') }}"
            });
            $("#resp_staff_id").select2({
                theme: "bootstrap",
                placeholder: "{{ trans('call.main_staff') }}"
            });

            var dateFormat = '{{ config('settings.date_format') }}';
            flatpickr("#date", {
                minDate: '{{ isset($call) ? $call->created_at : now() }}',
                dateFormat: dateFormat,
                disableMobile: "true",
            });
        });
    </script>
@endsection
