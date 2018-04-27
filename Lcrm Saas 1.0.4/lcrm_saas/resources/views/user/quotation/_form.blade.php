<div class="card">
    <div class="card-body">
        @if (isset($quotation))
            {!! Form::model($quotation, ['url' => $type . '/' . $quotation->id, 'method' => 'put', 'files'=> true, 'id'=>'form']) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true, 'id'=>'form']) !!}
        @endif
        <div class="row">
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('company_id') ? 'has-error' : '' }}">
                    {!! Form::label('company_id', trans('quotation.company_id'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::select('company_id', isset($companies)?$companies:[''=>trans('quotation.company_id')], null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('company_id', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('sales_team_id') ? 'has-error' : '' }}">
                    {!! Form::label('sales_team_id', trans('quotation.sales_team_id'), ['class' => 'control-label  required']) !!}
                    <div class="controls">
                        {!! Form::select('sales_team_id', $salesteams, (isset($quotation)?$quotation->sales_team_id:null), ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('sales_team_id', ':message') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group required {{ $errors->has('qtemplate_id') ? 'has-error' : '' }}">
                    {!! Form::label('qtemplate_id', trans('quotation.quotation_template'), ['class' => 'control-label']) !!}
                    <div class="controls">
                        {!! Form::select('qtemplate_id', $qtemplates, (isset($quotation)?$quotation->qtemplate_id:null), ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('qtemplate_id', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('date') ? 'has-error' : '' }}">
                    {!! Form::label('date', trans('quotation.date'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::text('date', isset($quotation) ? $quotation->start_date : null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('date', ':message') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('exp_date') ? 'has-error' : '' }}">
                    {!! Form::label('exp_date', trans('quotation.exp_date'), ['class' => 'control-label required']) !!}
                    <div class="controls">
                        {!! Form::text('exp_date', isset($quotation) ? $quotation->expire_date : null, ['class' => 'form-control']) !!}
                        <span class="help-block">{{ $errors->first('exp_date', ':message') }}</span>
                    </div>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('payment_term') ? 'has-error' : '' }}">
                        {!! Form::label('payment_term', trans('quotation.payment_term'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            <select name="payment_term" class="form-control" id="payment_term">
                                <option value>{{trans('quotation.payment_term')}}</option>
                                @if($payment_term1!='0' && $payment_term1!='')
                                    <option value="{{$payment_term1.' '.trans('quotation.days')}}"
                                            @if(isset($quotation) && $payment_term1.' '.trans('quotation.days') == $quotation->payment_term) selected @endif>{{$payment_term1}} {{trans('quotation.days')}}</option>
                                @endif
                                @if($payment_term2!='0' && $payment_term2!='')
                                    <option value="{{$payment_term2.' '.trans('quotation.days')}}"
                                            @if(isset($quotation) && $payment_term2.' '.trans('quotation.days') == $quotation->payment_term) selected @endif>{{$payment_term2}} {{trans('quotation.days')}}</option>
                                @endif
                                @if($payment_term3!='0' && $payment_term3!='')
                                    <option value="{{$payment_term3.' '.trans('quotation.days')}}"
                                            @if(isset($quotation) && $payment_term3.' '.trans('quotation.days')== $quotation->payment_term) selected @endif>{{$payment_term3}} {{trans('quotation.days')}}</option>
                                @endif
                                <option value="0 {{trans('quotation.days')}}"
                                        @if(isset($quotation) && $quotation->payment_term.' '.trans('quotation.days')==0) selected @endif>{{trans('quotation.immediate_payment')}}</option>
                            </select>
                            <span class="help-block">{{ $errors->first('payment_term', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                        {!! Form::label('status', trans('quotation.status'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            <div class="input-group">
                                <label>
                                    <input type="radio" name="status" value="{{trans('quotation.draft_quotation')}}"
                                           class='icheckblue'
                                           @if(isset($quotation) && old('status',$quotation->status)==trans('quotation.draft_quotation') || old('status')==trans('quotation.draft_quotation'))  checked @endif>
                                    {{trans('quotation.draft_quotation')}}
                                </label>
                                <label>
                                    <input type="radio" name="status" value="{{trans('quotation.send_quotation')}}"
                                           class='icheckblue'
                                           @if(isset($quotation) && old('status',$quotation->status)==trans('quotation.send_quotation') || old('status')==trans('quotation.send_quotation')) checked @endif>
                                    {{trans('quotation.send_quotation')}}
                                </label>
                            </div>

                            <span class="help-block">{{ $errors->first('status', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-0 {{ $errors->has('product_id') ? 'has-error' : '' }}">
                    <label class="control-label required">{{trans('qtemplate.products')}}
                        <span>{!! $errors->first('products') !!}</span>
                    </label>
                    <div class="controls">
                        <span class="help-block">{{ $errors->first('product_id', ':message') }}</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr style="font-size: 12px;">
                            <th>{{trans('quotation.product')}}</th>
                            <th>{{trans('quotation.description')}}</th>
                            <th>{{trans('quotation.quantity')}}</th>
                            <th>{{trans('quotation.unit_price')}}</th>
                            <th>{{trans('quotation.subtotal')}}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="InputsWrapper">
                        @if(isset($quotation)&& $quotation->quotationProducts->count()>0)
                            @foreach($quotation->quotationProducts as $index => $variants)
                                <tr class="remove_tr">
                                    <td>
                                        <input type="hidden" name="product_id[]" id="product_id{{$index}}"
                                               value="{{$variants->pivot->product_id}}"
                                               readOnly>
                                        <select name="product_list" id="product_list{{$index}}" class="form-control product_list"
                                                data-search="true" onchange="product_value({{$index}});">
                                            <option value=""></option>
                                            @foreach( $products as $product)
                                                <option value="{{ $product->id . '_' . $product->description. '_' . $product->quantity_on_hand.'_'.$product->sale_price}}"
                                                        @if($product->id == $variants->pivot->product_id) selected="selected" @endif>
                                                    {{ $product->product_name}}</option>
                                            @endforeach
                                        </select>
                                    <td><textarea name=description[]" id="description{{$index}}" rows="2"
                                                  class="form-control resize_vertical" readOnly>{{$variants->description}}</textarea>
                                    </td>
                                    <td><input type="number" name="quantity[]" id="quantity{{$index}}" min="1"
                                               value="{{$variants->pivot->quantity}}"
                                               class="form-control number"
                                               onkeyup="product_price_changes('quantity{{$index}}','price{{$index}}','sub_total{{$index}}');">
                                    </td>
                                    <td><input type="text" name="price[]" id="price{{$index}}"
                                               value="{{$variants->pivot->price}}"
                                               class="form-control" readonly></td>
                                    <input type="hidden" name="taxes[]" id="taxes{{$index}}"
                                           value="{{ floatval($sales_tax) }}" class="form-control"></td>
                                    <td><input type="text" name="sub_total[]" id="sub_total{{$index}}"
                                               value="{{$variants->pivot->quantity*$variants->pivot->price}}"
                                               class="form-control" readOnly></td>
                                    <td><a href="javascript:void(0)" class="delete removeclass"><i
                                                    class="fa fa-fw fa-trash fa-lg text-danger"></i></a></td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <button type="button" id="AddMoreFile"
                        class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> {{trans('quotation.add_product')}}
                </button>
                <div class="row">&nbsp;</div>
            </div>
        </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('total') ? 'has-error' : '' }}">
                        {!! Form::label('total', trans('quotation.total'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('total', null, ['class' => 'form-control','readonly']) !!}
                            <span class="help-block">{{ $errors->first('total', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('discount') ? 'has-error' : '' }}">
                        {!! Form::label('discount', trans('quotation.discount').' (%)', ['class' => 'control-label']) !!}
                        <div class="controls">
                            <input type="text" name="discount" id="discount"
                                   value="{{(isset($quotation)?$quotation->discount:"0.00")}}"
                                   class="form-control number"
                                   onkeyup="update_total_price();">
                            <span class="help-block">{{ $errors->first('discount', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group required {{ $errors->has('grand_total') ? 'has-error' : '' }}">
                        {!! Form::label('grand_total', trans('quotation.grand_total'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('grand_total', null, ['class' => 'form-control','readonly']) !!}
                            <span class="help-block">{{ $errors->first('grand_total', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('tax_amount') ? 'has-error' : '' }}">
                        {!! Form::label('tax_amount', trans('quotation.tax_amount').' ('.$sales_tax.'%)', ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('tax_amount', null, ['class' => 'form-control','readonly']) !!}
                            <span class="help-block">{{ $errors->first('tax_amount', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required {{ $errors->has('vat_amount') ? 'has-error' : '' }}">
                        {!! Form::label('vat_amount', trans('quotation.vat_amount').' ('.$taxRate.'%)', ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('vat_amount', null, ['class' => 'form-control','readonly']) !!}
                            <span class="help-block">{{ $errors->first('vat_amount', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('final_price') ? 'has-error' : '' }}">
                        {!! Form::label('final_price', trans('quotation.final_price'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('final_price', null, ['class' => 'form-control','readonly']) !!}
                            <span class="help-block">{{ $errors->first('final_price', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('terms_and_conditions') ? 'has-error' : '' }}">
                        {!! Form::label('terms_and_conditions', trans('qtemplate.terms_and_conditions'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::textarea('terms_and_conditions', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('terms_and_conditions', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                <a href="{{URL::previous()}}" class="btn btn-warning"><i class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
            </div>
        </div>
        <!-- ./ form actions -->
        {!! Form::close() !!}
    </div>
</div>

@section('scripts')
    <script>
        $(document).ready(function(){
            $("#company_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('quotation.company_id') }}"
            });
            $("#sales_team_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('quotation.sales_team_id') }}"
            });
            $("#qtemplate_id").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('quotation.quotation_template') }}"
            });
            $("#recipients").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('quotation.recipients') }}"
            });
            $(".product_list").select2({
                theme:"bootstrap",
                placeholder:"Product"
            });
            @if(old('payment_term'))
            $("#payment_term").find("option[value='"+'{{old("payment_term")}}'+"']").attr('selected',true);
            @endif
            $("#payment_term").select2({
                theme:"bootstrap",
                placeholder:"{{ trans('quotation.payment_term') }}"
            });
        });
        $(function () {
            update_total_price();
            $('#qtemplate_id').change(function () {
                if ($(this).val() > 0) {
                    $.ajax({
                        type: "GET",
                        url: '{{url("quotation/ajax_qtemplates_products")}}/' + $(this).val(),
                        success: function (data) {
                            content_data = '';
                            $.each(data, function (i, item) {
                                content_data += makeContent(FieldCount, item);
                                FieldCount++;
                            });
                            $("#InputsWrapper").html(content_data);
                            update_total_price();
                        }
                    });
                }
                setTimeout(function(){
                    $(".product_list").select2({
                        theme:"bootstrap",
                        placeholder:"Product"
                    })
                },200);
            });
        });
        function product_value(FieldCount) {
            var all_Val = $("#product_list" + FieldCount).val();
            var res = all_Val.split("_");
            $('#product_id' + FieldCount).val(res[0]);
            $('#description' + FieldCount).val(res[1]);
            $('#quantity' + FieldCount).val(res[2]);
            $('#price' + FieldCount).val(res[3]);
            var quantity=$('#quantity'+FieldCount).val();
            var price=$('#price'+FieldCount).val();
            $('#sub_total' + FieldCount).val(price*quantity);
            update_total_price();
        }
        function product_price_changes(quantity, product_price, sub_total_id) {
            var no_quantity = $("#" + quantity).val();
            var no_product_price = $("#" + product_price).val();

            var sub_total = parseFloat(no_quantity * no_product_price);

            var tax_amount = 0;
            tax_amount = (sub_total * {{floatval($sales_tax)}}) / 100;
            $('#taxes').val(tax_amount.toFixed(2));

            $('#' + sub_total_id).val(sub_total);
            update_total_price();

        }

        function update_total_price() {
            var sub_total = 0;
            $('#total').val(0);
            $('#tax_amount').val(0);
            $('#vat_amount').val(0);
            $('#grand_total').val(0);
            $('#final_price').val(0);
            $('input[name^="sub_total"]').each(function () {
                sub_total += parseFloat($(this).val());
                $('#total').val(sub_total.toFixed(2));


                var discount = $("#discount").val();
                var discount_amount = (sub_total * discount) / 100;

                var grand_total = 0;
                grand_total = sub_total - discount_amount;
                $('#grand_total').val(grand_total.toFixed(2));

                var tax_per = '{{floatval($sales_tax)}}';
                var tax_amount = 0;
                tax_amount = (grand_total * tax_per) / 100;
                $('#tax_amount').val(tax_amount.toFixed(2));

                var vat_per = '{{floatval($taxRate)}}';
                var vat_amount = 0;
                vat_amount = (grand_total * vat_per) / 100;
                $('#vat_amount').val(vat_amount.toFixed(2));

                var final_price=0;
                final_price = grand_total+tax_amount+vat_amount;
                $('#final_price').val(final_price.toFixed(2));
            });

        }

        function makeContent(number, item) {
            item = item || '';

            var content = '';
            content += '<tr class="remove_tr"><td>';
            content += '<input type="hidden" name="product_id[]" id="product_id' + number + '" value="' + ((typeof item.pivot == 'undefined') ? '' : item.pivot.product_id) + '" readOnly>';
            content += '<select name="product_list" id="product_list' + number + '" class="form-control product_list" data-search="true" onchange="product_value(' + number + ');">' +
                '<option value=""></option>';
            @foreach( $products as $product)
                content += '<option value="{{ $product->id . '_' . $product->description.'_'.$product->quantity_on_hand.'_'.$product->sale_price}}"';
            if ((typeof item.pivot == 'undefined') ? '' : item.pivot.product_id =={{$product->id}}) {
                content += 'selected';
            }
            content += '>' +
                '{{ $product->product_name}}</option>';
            @endforeach

                content += '</select>' +
                '<td><textarea name=description[]" id="description' + number + '" rows="2" class="form-control resize_vertical" readOnly>' + ((typeof item.description == 'undefined') ? '' : item.description) + '</textarea>' +
                '</td>' +
                '<td><input type="number" min="1" name="quantity[]" id="quantity' + number + '" value="' + ((typeof item.pivot == 'undefined') ? '' : item.pivot.quantity) + '" class="form-control number" onkeyup="product_price_changes(\'quantity' + number + '\',\'price' + number + '\',\'sub_total' + number + '\');"></td>' +
                '<td><input type="text" name="price[]" id="price' + number + '" value="' + ((typeof item.pivot == 'undefined') ? '' : item.pivot.price) + '" class="form-control" readOnly>' +
                '<input type="hidden" name="taxes[]" id="taxes' + number + '" value="{{floatval($sales_tax)}}" class="form-control" readOnly></td>' +
                '<td><input type="text" name="sub_total[]" id="sub_total' + number + '" value="' + ((typeof item.pivot == 'undefined') ? '' : item.pivot.quantity*item.pivot.price) + '" class="form-control" readOnly></td>' +
                '<td><a href="javascript:void(0)" class="delete removeclass" title="{{ trans('table.delete') }}"><i class="fa fa-fw fa-trash fa-lg text-danger"></i></a></td>' +
                '</tr>';
            return content;
        }

        var MaxInputs = 50; //maximum input boxes allowed
        var InputsWrapper = $("#InputsWrapper"); //Input boxes wrapper ID
        var AddButton = $("#AddMoreFile"); //Add button ID

        var x = InputsWrapper.length; //initlal text box count
        var FieldCount = @if(isset($quotation)&& $quotation->quotationProducts->count()>0) {{$quotation->quotationProducts->count()}} @else 1 @endif; //to keep track of text box added


        $("#total").val("0");

        $(AddButton).click(function (e)  //on add input button click
        {

            setTimeout(function(){
                $(".product_list").select2({
                    theme:"bootstrap",
                    placeholder:"Product"
                });
                quantityChange();
            });
            if (x <= MaxInputs) //max input box allowed
            {
                FieldCount++; //text box added increment
                content = makeContent(FieldCount);
                $(InputsWrapper).append(content);
                x++; //text box increment

                $('.number').keypress(function (event) {
                    if (event.which < 46
                        || event.which > 59) {
                        event.preventDefault();
                    } // prevent if not number/dot

                    if (event.which == 46
                        && $(this).val().indexOf('.') != -1) {
                        event.preventDefault();
                    } // prevent if already dot
                });
            }
            //            $('#surveyForm').formValidation('addField', $option);

            return false;
        });

        quantityChange();
        function quantityChange(){
            $(".number").bind("keyup change click",function(){
                var no_quantity = $(this).val();
                var no_product_price = $(this).closest("tr").find("input[name='price[]']").val();
                var sub_total = parseFloat(no_quantity * no_product_price);
                var tax_amount = 0;
                tax_amount = (sub_total * {{floatval($sales_tax)}}) / 100;
                $('#taxes').val(tax_amount.toFixed(2));
                $(this).closest("tr").find("input[name='sub_total[]']").val(sub_total);
                update_total_price();
            });
        }
        $(InputsWrapper).on("click", ".removeclass", function (e) { //user click on remove text
            @if(!isset($quotation))
            if (x > 0) {
                $(this).parent().parent().remove(); //remove text box
                x--; //decrement textbox
            }
            @else
            $(this).parent().parent().remove(); //remove text box
            x--; //decrement textbox
            @endif
            update_total_price();
            return false;
        });

        $('#qtemplate').on('keyup keypress', function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });

        var dateFormat = '{{ config('settings.date_format') }}';
        flatpickr('#date',{
            minDate: '{{ isset($quotation) ? $quotation->created_at : now() }}',
            dateFormat: dateFormat,
            disableMobile: "true",
            "plugins": [new rangePlugin({ input: "#exp_date"})]
        });

        function create_pdf(quotation_id) {
            $.ajax({
                type: "GET",
                url: "{{url('quotation' )}}/" + quotation_id + "/ajax_create_pdf",
                data: {'_token': '{{csrf_token()}}'},
                success: function (msg) {
                    if (msg != '') {
                        $("#pdf_url").attr("href", msg);
                        var index = msg.lastIndexOf("/") + 1;
                        var filename = msg.substr(index);
                        $("#pdf_url").html(filename);
                        $("#quotation_pdf").val(filename);
                    }
                }
            });
        }
        $("#send_quotation").submit(function (e) {
            e.preventDefault();
            $.post( "{{url('quotation/send_quotation')}}",
                $('#send_quotation').serialize()
            )
                .done(function( msg ) {
                    $('body,html').animate({scrollTop: 0}, 200);
                    $("#sendby_ajax").html(msg);
                    setTimeout(function(){
                        $("#sendby_ajax").hide();
                    },5000);
                    $("#modal-send_by_email").modal('hide');
                });
        });
        $("#modal-send_by_email").on('hide.bs.modal', function () {
            $("#recipients").find("option").attr('selected',false);
            $("#recipients").select2({
                placeholder:"{{ trans('quotation.recipients') }}",
                theme: 'bootstrap'
            });
        });
        $('.icheckblue').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
    </script>
@endsection