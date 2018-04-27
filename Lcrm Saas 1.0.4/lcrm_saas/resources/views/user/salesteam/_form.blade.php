<div class="card">
    <div class="card-body">
        @if (isset($salesteam))
            {!! Form::model($salesteam, ['url' => $type . '/' . $salesteam->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
        @endif
        <div class="form-group required {{ $errors->has('salesteam') ? 'has-error' : '' }}">
            {!! Form::label('salesteam', trans('salesteam.salesteam'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::text('salesteam', null, ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('salesteam', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('invoice_target') ? 'has-error' : '' }}">
            {!! Form::label('invoice_target', trans('salesteam.invoice_target'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::text('invoice_target', null, ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('invoice_target', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('invoice_forecast') ? 'has-error' : '' }}">
            {!! Form::label('invoice_forecast', trans('salesteam.invoice_forecast'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::text('invoice_forecast', null, ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('invoice_forecast', ':message') }}</span>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('responsibility', trans('salesteam.responsibility'), ['class' => 'control-label']) !!}
            <div class="controls">
                <label>
                    {{ Form::checkbox('quotations','1',old('quotations'),['class'=>'icheck']) }}
                    {{trans('salesteam.quotations')}}
                </label>
                <label>
                    {{ Form::checkbox('leads','1',old('leads'),['class'=>'icheck']) }}
                    {{trans('salesteam.leads')}}
                </label>
                <label>
                    {{ Form::checkbox('opportunities','1',old('opportunities'),['class'=>'icheck']) }}
                    {{trans('salesteam.opportunities')}}
                </label>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('team_leader') ? 'has-error' : '' }}">
            {!! Form::label('team_leader', trans('salesteam.team_leader'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::select('team_leader', $staff, null, ['id'=>'team_leader', 'class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('team_leader', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('team_members') ? 'has-error' : '' }}">
            {!! Form::label('team_members', trans('salesteam.team_members'), ['class' => 'control-label required']) !!}
            <div class="controls">
                {!! Form::select('team_members[]', $staff, isset($salesteam)?$salesteam->members:null, ['id'=>'team_members', 'multiple'=>'multiple', 'class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('team_members', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('notes') ? 'has-error' : '' }}">
            {!! Form::label('notes', trans('salesteam.notes'), ['class' => 'control-label']) !!}
            <div class="controls">
                {!! Form::textarea('notes', null, ['class' => 'form-control']) !!}
                <span class="help-block">{{ $errors->first('notes', ':message') }}</span>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}
                </button>
                <a href="{{ route($type.'.index') }}" class="btn btn-warning"><i
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
            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            function MainStaffChange(){
                $("#team_leader").select2({
                    placeholder:"{{ trans('salesteam.team_leader') }}",
                    theme: 'bootstrap'
                }).on("change",function(){
                    var MainStaff=$(this).select2("val");
                    $("#team_members").find("option").prop('disabled',false);
                    $("#team_members").find("option").attr('selected',false);
                    $("#team_members").find("option[value='"+MainStaff+"']").attr('selected',true);
                    $("#team_members").select2({
                        placeholder:"{{ trans('salesteam.team_members') }}",
                        theme: 'bootstrap'
                    }).find("option[value='']").remove();
                });
            }
            MainStaffChange();
            $("#team_members").select2({
                placeholder:"{{ trans('salesteam.team_members') }}",
                theme: 'bootstrap'
            }).find("option:first").attr({
                selected:false
            });
        });
    </script>
@stop
