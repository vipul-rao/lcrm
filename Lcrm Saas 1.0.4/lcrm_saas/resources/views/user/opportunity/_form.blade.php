<div class="card bg-white">
    <div class="card-body">
        @if (isset($opportunity))
            {!! Form::model($opportunity, ['url' => $type . '/' . $opportunity->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
        @endif
        <div class="row">
            <div class="col-12">
                <div class="form-group required {{ $errors->has('opportunity') ? 'has-error' : '' }}">
                    {!! Form::label('opportunity', trans('opportunity.opportunity_name'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::text('opportunity', null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('opportunity', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group required {{ $errors->has('stages') ? 'has-error' : '' }}">
                    {!! Form::label('stages', trans('opportunity.stages'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::select('stages', $stages, null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('stages', ':message') }}</span>
                    </div>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('expected_revenue') ? 'has-error' : '' }}">
                        {!! Form::label('expected_revenue', trans('opportunity.expected_revenue'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('expected_revenue', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('expected_revenue', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('probability') ? 'has-error' : '' }}">
                        {!! Form::label('probability', trans('opportunity.probability'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::number('probability', null, ['class' => 'form-control','min'=>0]) !!}
                            <span class="help-block">{{ $errors->first('probability', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('assigned_partner_id') ? 'has-error' : '' }}">
                        {!! Form::label('assigned_partner_id', trans('opportunity.company_name'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('assigned_partner_id', $companies, null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('assigned_partner_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('customer_id') ? 'has-error' : '' }}">
                        {!! Form::label('customer_id', trans('opportunity.customer'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('customer_id', isset($agent_name)?$agent_name:[], null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('customer_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group required {{ $errors->has('sales_team_id') ? 'has-error' : '' }}">
                        {!! Form::label('sales_team_id', trans('opportunity.salesteam'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('sales_team_id', $salesteams, null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('sales_team_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('next_action') ? 'has-error' : '' }}">
                        {!! Form::label('next_action', trans('opportunity.next_action'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('next_action', isset($opportunity) ? $opportunity->next_action_date : null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('next_action', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('expected_closing') ? 'has-error' : '' }}">
                        {!! Form::label('expected_closing', trans('opportunity.expected_closing'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('expected_closing', isset($opportunity) ? $opportunity->expected_closing_date : null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('expected_closing', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group required {{ $errors->has('internal_notes') ? 'has-error' : '' }}">
                        {!! Form::label('internal_notes', trans('opportunity.internal_notes'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::textarea('internal_notes', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('internal_notes', ':message') }}</span>
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
    {{--<script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/plugins/rangePlugin.js"></script>--}}
    <script>
        $(document).ready(function(){
            $("#stages").select2({
                theme: 'bootstrap',
                placeholder: "Select Stage"
            });
            $("#customer_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('opportunity.customer') }}"
            });
            $("#assigned_partner_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('company.company_name') }}"
            });
            $("#sales_team_id").select2({
                theme: 'bootstrap',
                placeholder: "{{ trans('opportunity.salesteam') }}"
            });

            $('#stages').change(function () {
                var stage = $(this).val();
                if (stage == 'New' || stage == 'Lost' || stage == 'Dead') {
                    $('#probability').val(0);
                }
                if (stage == 'Qualification') {
                    $('#probability').val(20);
                }
                if (stage == 'Proposition') {
                    $('#probability').val(40);
                }
                if (stage == 'Negotiation') {
                    $('#probability').val(60);
                }
                if (stage == 'Won') {
                    $('#probability').val(100);
                }
            }).change();

            //datepickers initialization and logic
            var dateFormat = '{{ config('settings.date_format') }}';
            flatpickr('#next_action',{
                minDate: '{{ isset($opportunity) ? $opportunity->created_at : now() }}',
                dateFormat: dateFormat,
                disableMobile: "true",
                "plugins": [new rangePlugin({ input: "#expected_closing"})]
            });

        });

        //Stages Select
        $("#assigned_partner_id").change(function(){
            agentList($(this).val());
        });
        @if(old('assigned_partner_id'))
        agentList({{old('assigned_partner_id')}});
        @endif
        function agentList(id){
            $.ajax({
                type: "GET",
                url: '{{ url('opportunity/ajax_agent_list')}}',
                data: {'id': id, _token: '{{ csrf_token() }}' },
                success: function (data) {
                    $("#customer_id").empty();
                    $("#customer_id").select2({
                        theme:"bootstrap",
                        placeholder:"{{ trans('opportunity.customer') }}"
                    });
                    $.each(data, function (val, text) {
                        $('#customer_id').append($('<option></option>').val(val).html(text).attr('selected', val == "{{old('customer_id')}}" ? true : false));
                    });
                    @if(old('customer_id'))
                    $("#customer_id").append('<option value="">{{ trans('lead.agent_name') }}</option>');
                    @else
                    $("#customer_id").append('<option value="" selected>{{ trans('lead.agent_name') }}</option>');
                    @endif
                }
            });
        }
    </script>
@endsection
