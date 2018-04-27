<div class="card bg-white">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    @if($user->hasAccess(['logged_calls.read']) || $orgRole=='admin')
                        <a href="{{ url('opportunitycall/' . $opportunity->id ) }}" class="btn btn-primary">
                            <i class="fa fa-phone"></i>
                            <b>{{$opportunity->calls()->count()}}</b> {{ trans("table.calls") }}
                        </a>
                    @endif
                    @if($user->hasAccess(['meetings.read']) || $orgRole=='admin')
                        <a href="{{ url('opportunitymeeting/' . $opportunity->id ) }}" class="btn btn-primary">
                            <i class="fa fa-users"></i>
                            <b>{{$opportunity->meetings()->count()}}</b> {{ trans("opportunity.meetings") }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('product_name', trans('opportunity.opportunity_name'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $opportunity->opportunity }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('stages', trans('opportunity.stages'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $opportunity->stages }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('stages', trans('opportunity.expected_revenue'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $opportunity->expected_revenue }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('probability', trans('opportunity.probability'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $opportunity->probability }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('company_name', trans('company.company_name'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $opportunity->companies->name ?? null }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('customer', trans('lead.customer'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $opportunity->customer->full_name ?? null }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('sales_team_id', trans('opportunity.sales_team_id'), ['class' => 'control-label']) !!}
                    <div>
                        {{ $opportunity->salesTeam->salesteam ?? null }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group ">
                    {!! Form::label('additional_info', trans('opportunity.next_action'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $opportunity->next_action_date }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group ">
                    {!! Form::label('additional_info', trans('opportunity.expected_closing'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $opportunity->expected_closing_date }}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group ">
                    {!! Form::label('internal_notes', trans('opportunity.internal_notes'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $opportunity->internal_notes }}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                @if(@$action == 'lost')
                    {!! Form::model($opportunity, ['url' => $type . '/' . $opportunity->id.'/opportunity_archive/', 'id' => 'opportunity_lost','method' => 'post', 'files'=> true]) !!}
                    <div class="form-group {{ $errors->has('lost_reason') ? 'has-error' : '' }}">
                        {!! Form::label('lost_reason', trans('opportunity.lost_reason'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('lost_reason', $lost_reason, null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('lost_reason', ':message') }}</span>
                        </div>
                    </div>
                    <div class="controls">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}
                        </button>
                        <a href="{{ url($type) }}" class="btn btn-warning"><i
                                    class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                    </div>
                    {!! Form::close() !!}
                @endif
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <div class="controls">
                        @if (@$action == trans('action.show'))
                            <a href="{{ url($type) }}" class="btn btn-warning"><i
                                        class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                        @elseif (@$action == 'won')
                            @if($user->hasAccess(['quotations.write']) || $orgRole=='admin')
                                <a href="{{ url($type . '/'.$opportunity->id.'/convert_to_quotation/') }}"
                                   class="btn btn-primary" target="">{{trans('opportunity.convert_to_quotation')}}</a>
                            @endif
                            <a href="{{ url($type) }}" class="btn btn-warning"><i
                                        class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                        @elseif (@$action == 'lost')
                        @else
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> {{trans('table.delete')}}
                            </button>
                            <a href="{{ url($type) }}" class="btn btn-warning"><i
                                        class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
    <script>
        $(document).ready(function(){
            $("#lost_reason").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('opportunity.lost_reason') }}"
            });
        });
    </script>
@endsection