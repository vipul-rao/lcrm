<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('invoice.invoice_number')}}</label>
                    <div class="controls">
                        {{ $invoiceReceivePayment->invoice->invoice_number ?? null}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('invoice.invoice_date')}}</label>
                    <div class="controls">
                        {{ $invoiceReceivePayment->invoice->invoice_start_date ?? null}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('invoice.payment_date')}}</label>
                    <div class="controls">
                        {{ $invoiceReceivePayment->invoice_payment_date}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('invoices_payment_log.payment_method')}}</label>
                    <div class="controls">
                        {{ $invoiceReceivePayment->payment_method}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('payment_received', trans('invoice.payment_received'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoiceReceivePayment->payment_received }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    {!! Form::label('vat_amount', trans('quotation.vat_amount'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {{ $invoiceReceivePayment->vat_amount }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('invoices_payment_log.payment_number')}}</label>
                    <div class="controls">
                        {{ $invoiceReceivePayment->payment_number}}
                    </div>
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