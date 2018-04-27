<div class="card">
    <div class="card-body">
        @if(isset($qtemplate))
            <div class="row">
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="form-group">
                        {!! Form::label('quotation_template', trans('qtemplate.quotation_template'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {{ $qtemplate->quotation_template }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="form-group">
                        {!! Form::label('quotation_duration', trans('qtemplate.quotation_duration'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {{ $qtemplate->quotation_duration }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="control-label">{{trans('qtemplate.products')}}</label>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr class="detailes-tr">
                                <th>{{trans('qtemplate.product')}}</th>
                                <th>{{trans('qtemplate.description')}}</th>
                                <th>{{trans('qtemplate.quantity')}}</th>
                                <th>{{trans('qtemplate.unit_price')}}</th>
                                <th>{{trans('qtemplate.subtotal')}}</th>
                            </tr>
                            </thead>
                            <tbody id="InputsWrapper">
                            @if(isset($qtemplate) && $qtemplate->qTemplateProducts->count()>0)
                                @foreach($qtemplate->qTemplateProducts as $index => $variants)
                                    <tr class="remove_tr">
                                        <td>
                                        {{$variants->product_name}}
                                        <td>
                                            {{$variants->description}}
                                        </td>
                                        <td>
                                            {{isset($variants->pivot->quantity)?$variants->pivot->quantity:null}}
                                        </td>
                                        <td>
                                            {{isset($variants->pivot->price)?$variants->pivot->price:null}}
                                        </td>
                                        <td>
                                            {{isset($variants->pivot->quantity)?($variants->pivot->quantity*$variants->pivot->price):null}}
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
                        {!! Form::label('total', trans('qtemplate.total'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {{ $qtemplate->total }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="form-group">
                        {!! Form::label('tax_amount', trans('qtemplate.tax_amount'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {{ $qtemplate->tax_amount }}
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="form-group">
                        {!! Form::label('grand_total', trans('qtemplate.grand_total'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {{ $qtemplate->grand_total }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label" for="title">{{trans('qtemplate.terms_and_conditions')}}</label>
                        <div class="controls">
                            {{ $qtemplate->terms_and_conditions }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="controls">
                    @if (@$action == trans('action.show'))
                        <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.close')}}</a>
                    @else
                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> {{trans('table.delete')}}</button>
                        <a href="{{ url($type) }}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>