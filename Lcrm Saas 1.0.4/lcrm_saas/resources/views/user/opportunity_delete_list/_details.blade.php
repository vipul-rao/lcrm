<div class="card">
    <div class="card-body">
        @if (isset($opportunity))
            {!! Form::open(['url' => $type . '/' . $opportunity->id, 'method' => 'delete', 'class' => 'bf']) !!}
        @endif
        <div class="row">
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
        </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="controls">
                            @if (@$action == trans('action.show'))
                                <a href="{{ url($type) }}" class="btn btn-warning"><i
                                            class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                            @else
                                <button type="submit" class="btn btn-success"><i class="fa fa-undo"></i> {{trans('table.restore')}}
                                </button>
                                <a href="{{ url($type) }}" class="btn btn-warning"><i
                                            class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
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