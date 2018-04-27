<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                {{--logged_calls--}}
                @if($user->hasAccess(['logged_calls.read']))
                    <a href="{{ url('leadcall/' . $lead->id ) }}" class="btn btn-primary call-summary">
                        <i class="fa fa-phone"></i> <b>{{$lead->calls()->count()}}</b> {{ trans("table.calls") }}
                    </a>
                    @endif
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('company_name', trans('lead.company_name'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->company_name }}</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('function', trans('Function Type'), ['class' => 'control-label', 'placeholder'=>'select']) !!}
                    <div>{{ $lead->function }}</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('product_name', trans('lead.product_name'), ['class' => 'control-label' ]) !!}
                    <div>{{ $lead->product_name }}</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('internal_notes', trans('lead.additionl_info'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->internal_notes }}</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 m-t-10 mb-1">
                <h4 class="m-0">{{ trans('lead.personal_info') }}:</h4>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="form-group">
                    {!! Form::label('client_name', trans('lead.contact_name'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->title.' '.$lead->contact_name }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="form-group">
                    {!! Form::label('country_id', trans('lead.country'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->country->name ?? null }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="form-group">
                    {!! Form::label('state_id', trans('lead.state'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->state->name ?? null }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="form-group">
                    {!! Form::label('city_id', trans('lead.city'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->city->name ?? null }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="form-group">
                    {!! Form::label('phone', trans('lead.phone'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->phone }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="form-group">
                    {!! Form::label('mobile', trans('lead.mobile'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->mobile }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="form-group">
                    {!! Form::label('email', trans('lead.email'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->email }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="form-group">
                    {!! Form::label('priority', trans('lead.priority'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->priority }}</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('address', trans('lead.address'), ['class' => 'control-label']) !!}
                    <div>{{ $lead->address }}</div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="controls">
                @if (@$action == trans('action.show'))
                    <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                @else
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> {{trans('table.delete')}}</button>
                    <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                @endif
            </div>
        </div>
    </div>
</div>