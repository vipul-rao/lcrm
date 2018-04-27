<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('company_id', trans('quotation.company_id'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->companies->name ?? null }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('sales_team_id', trans('quotation.sales_team_id'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->salesTeam->salesteam ?? null  }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('quotation.date')}}</label>
                    <div class="controls">
                        {{ $quotation->start_date }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('quotation.exp_date')}}</label>
                    <div class="controls">
                        {{ $quotation->expire_date }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('payment_term', trans('quotation.payment_term'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->payment_term }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('invoice_number', trans('quotation.status'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->status }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label class="control-label">{{trans('quotation.products')}}</label>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr class="detailes-tr">
                            <th>{{trans('quotation.product')}}</th>
                            <th>{{trans('quotation.description')}}</th>
                            <th>{{trans('quotation.quantity')}}</th>
                            <th>{{trans('quotation.unit_price')}}</th>
                            <th>{{trans('quotation.subtotal')}}</th>
                        </tr>
                        </thead>
                        <tbody id="InputsWrapper">
                        @if(isset($quotation)&& $quotation->quotationProducts->count()>0)
                            @foreach($quotation->quotationProducts as $index => $variants)
                                <tr class="remove_tr">
                                    <td>
                                        {{$variants->product_name}}
                                    </td>
                                    <td>
                                        {{$variants->description}}
                                    </td>
                                    <td>
                                        {{$variants->pivot->quantity}}
                                    </td>
                                    <td>
                                        {{$variants->pivot->price}}
                                    </td>
                                    <td>
                                        {{$variants->pivot->quantity*$variants->pivot->price}}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('quotation.total'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->total}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('quotation.discount').' (%)', ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->discount}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('quotation.grand_total'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->grand_total}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('quotation.tax_amount'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->tax_amount}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('quotation.vat_amount'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->vat_amount}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('quotation.final_price'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->final_price}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('quotation_duration', trans('qtemplate.terms_and_conditions'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $quotation->terms_and_conditions }}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="controls">
                <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
            </div>
        </div>
    </div>
</div>