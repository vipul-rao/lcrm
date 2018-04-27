<div class="card">
    <div class="card-body">
        @if (isset($meeting))
            {!! Form::model($meeting, ['url' => $type . '/' . $opportunity->id. '/' . $meeting->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type. '/' . $opportunity->id, 'method' => 'post', 'files'=> true]) !!}
        @endif
        <div class="row">
            <div class="col-12">
                <div class="form-group required {{ $errors->has('meeting_subject') ? 'has-error' : '' }}">
                    {!! Form::label('meeting_subject', trans('meeting.meeting_subject'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::text('meeting_subject', null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('meeting_subject', ':message') }}</span>
                    </div>
                </div>  
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('company_attendees') ? 'has-error' : '' }}">
                    {!! Form::label('company_attendees', trans('meeting.company_attendees'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::select('company_attendees[]', $customers, (isset($company_attendees)?$company_attendees:null), ['id'=>'attendees','multiple'=>'multiple', 'class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('company_attendees', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('responsible_id') ? 'has-error' : '' }}">
                    {!! Form::label('responsible_id', trans('meeting.responsible'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::select('responsible_id', $staffs, isset($meeting)?$meeting->responsible_id:$opportunity->sales_person_id, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('responsible_id', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('staff_attendees') ? 'has-error' : '' }}">
                    {!! Form::label('staff_attendees', trans('meeting.staff_attendees'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::select('staff_attendees[]', $staffs, (isset($staff_attendees)?$staff_attendees:null), ['id'=>'staff_attendees','multiple'=>'multiple', 'class' => 'form-control select2']) !!}
                        <span class="help-block">{{ $errors->first('staff_attendees', ':message') }}</span>
                    </div>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('starting_date') ? 'has-error' : '' }}">
                        {!! Form::label('starting_date', trans('meeting.starting_date'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('starting_date', isset($meeting) ? $meeting->meeting_starting_date : null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('starting_date', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('ending_date') ? 'has-error' : '' }}">
                        {!! Form::label('ending_date', trans('meeting.ending_date'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('ending_date', isset($meeting) ? $meeting->meeting_ending_date : null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('ending_date', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('location') ? 'has-error' : '' }}">
                        {!! Form::label('location', trans('meeting.location'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('location', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('location', ':message') }}</span>
                        </div>
                    </div>
                    <div class="form-group required {{ $errors->has('meeting_description') ? 'has-error' : '' }}">
                        {!! Form::label('meeting_description', trans('meeting.meeting_description'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::textarea('meeting_description', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('meeting_description', ':message') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" value="1" class="icheckblue" name="all_day"
                                   @if(isset($meeting) && $meeting->all_day==1)checked @endif><i
                                    class="primary"></i> {{trans('meeting.all_day')}}
                        </label>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('privacy') ? 'has-error' : '' }}">
                    {!! Form::label('privacy', trans('meeting.privacy'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::select('privacy', $privacy, null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('privacy', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('show_time_as') ? 'has-error' : '' }}">
                    {!! Form::label('show_time_as', trans('meeting.show_time_as'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::select('show_time_as', $show_times, null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('show_time_as', ':message') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                <a href="{{ url($type.'/'.$opportunity->id) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
            </div>
        </div>
        <!-- ./ form actions -->

        {!! Form::close() !!}
    </div>
</div>
@section('scripts')
    <script>
        $(document).ready(function () {
            $("#privacy").select2({
                theme:'bootstrap'
            });
            $("#show_time_as").select2({
                theme:'bootstrap'
            });
            $("#attendees").select2({
                placeholder:"{{ trans('meeting.company_attendees') }}",
                theme: 'bootstrap'
            });
            function MainStaffChange(){
                $("#responsible_id").select2({
                    placeholder:"{{ trans('meeting.responsible') }}",
                    theme: 'bootstrap'
                }).on("change",function(){
                    var MainStaff=$(this).select2("val");
                    $("#staff_attendees").find("option").prop('disabled',false);
                    $("#staff_attendees").find("option").attr('selected',false);
                    $("#staff_attendees").find("option[value='"+MainStaff+"']").attr('selected',true);
                    $("#staff_attendees").select2({
                        placeholder:"{{ trans('meeting.staff_attendees') }}",
                        theme: 'bootstrap'
                    });
                });
            }
            MainStaffChange();
            $("#staff_attendees").select2({
                placeholder:"{{ trans('meeting.staff_attendees') }}",
                theme: 'bootstrap'
            }).find("option:first").attr({
                selected:false
            });
            var MainStaff=$("#responsible_id").select2("val");
            $("#staff_attendees").find("option[value='"+MainStaff+"']").attr('selected',true);
            $("#staff_attendees").select2({
                placeholder:"{{ trans('meeting.staff_attendees') }}",
                theme: 'bootstrap'
            });

            @if(isset($meeting))
            if($(".icheckbox_minimal-blue").hasClass('checked')){
                $("#show_time_as").find("option:contains('Free')").remove();
            }
            @endif
            $('#all_day').on('ifChecked', function(event){
                $("#show_time_as").find("option:contains('Busy')").attr('selected',true);
                $("#show_time_as").find("option:contains('Free')").remove();
            });
            $('#all_day').on('ifUnchecked', function(event){
                $("#show_time_as").prepend('<option value="Free" selected>{{ trans("Free") }}</option>');
                $("#show_time_as").find("option:contains('Busy')").attr('selected',false);
            });


            var dateTimeFormat = '{{ config('settings.date_format').' H:i' }}';
            flatpickr('#starting_date',{
                minDate: '{{ isset($meeting) ? $meeting->created_at : now() }}',
                dateFormat: dateTimeFormat,
                enableTime: true,
                disableMobile: "true",
                "plugins": [new rangePlugin({ input: "#ending_date"})]
            });
            $('.icheckblue').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
        });
    </script>
@stop