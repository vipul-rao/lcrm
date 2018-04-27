<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('invoice_number', trans('invoice.invoice_number'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->invoice_number }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('customer', trans('invoice.company_id'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->companies->name ?? null}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('sales_team_id', trans('invoice.sales_team_id'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ is_null($invoice->salesTeam)?"":$invoice->salesTeam->salesteam }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('invoice_date', trans('invoice.invoice_date'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->invoice_start_date }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('due_date', trans('invoice.due_date'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->invoice_due_date }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('payment_term', trans('invoice.payment_term'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->payment_term }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('status', trans('invoice.status'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ is_null($invoice->status)?"":$invoice->status }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label class="control-label">{{trans('invoice.products')}}</label>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr class="detailes-tr">
                            <th>{{trans('invoice.product')}}</th>
                            <th>{{trans('invoice.description')}}</th>
                            <th>{{trans('invoice.quantity')}}</th>
                            <th>{{trans('invoice.unit_price')}}</th>
                            <th>{{trans('invoice.subtotal')}}</th>
                        </tr>
                        </thead>
                        <tbody id="InputsWrapper">
                        @if(isset($invoice) && $invoice->invoiceProducts->count()>0)
                            @foreach($invoice->invoiceProducts as $index => $variants)
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
                    {!! Form::label('total', trans('invoice.total'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->total}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('invoice.discount').' (%)', ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->discount}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('invoice.grand_total'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->grand_total}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('invoice.tax_amount').' ('.$sales_tax.'%)', ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->tax_amount}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('quotation.vat_amount'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->vat_amount}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('invoice.final_price'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->final_price}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('total', trans('invoice.unpaid_amount'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoice->unpaid_amount}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="controls">
                        <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>