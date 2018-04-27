<div class="card">
    <div class="card-body">
        @if (isset($product))
            {!! Form::model($product, ['url' => $type . '/' . $product->id, 'method' => 'put', 'files'=> true]) !!}
        @else
            {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true]) !!}
        @endif
        <div class="row">
            <div class="col-12">
                <div class="form-group required {{ $errors->has('product_image_file') ? 'has-error' : '' }}">
                    {!! Form::label('product_image_file', trans('product.product_image'), ['class' => 'control-label']) !!}
                    <div class="controls row">
                        <div class="col-sm-6 col-lg-4">
                            <div class="row">
                                @if(isset($product->product_image))
                                    <image-upload name="product_image_file" old-image="{{ url('uploads/products/thumb_'.$product->product_image) }}"></image-upload>
                                    @else
                                    <image-upload name="product_image_file"></image-upload>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <span class="help-block">{{ $errors->first('company_avatar_file', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('product_name') ? 'has-error' : '' }}">
                        {!! Form::label('product_name', trans('product.product_name'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('product_name', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('product_name', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('category_id') ? 'has-error' : '' }}">
                        {!! Form::label('category_id', trans('product.category_id'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('category_id', $categories, null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('category_id', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('product_type') ? 'has-error' : '' }}">
                        {!! Form::label('product_type', trans('product.product_type'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::select('product_type', $product_types, (isset($product)?$product->product_type:null), ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('product_type', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('status') ? 'has-error' : '' }}">
                        {!! Form::label('status', trans('product.status'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::select('status', $statuses, (isset($product)?$product->status:null), ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('status', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('quantity_on_hand') ? 'has-error' : '' }}">
                        {!! Form::label('quantity_on_hand', trans('product.quantity_on_hand'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::input('number','quantity_on_hand', null, ['class' => 'form-control','min' => 0]) !!}
                            <span class="help-block">{{ $errors->first('quantity_on_hand', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group required {{ $errors->has('quantity_available') ? 'has-error' : '' }}">
                        {!! Form::label('quantity_available', trans('product.quantity_available'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::input('number','quantity_available', null, ['class' => 'form-control','min' => 0]) !!}
                            <span class="help-block">{{ $errors->first('quantity_available', ':message') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('description') ? 'has-error' : '' }}">
                        {!! Form::label('description', trans('product.description'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('description', ':message') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group required {{ $errors->has('sale_price') ? 'has-error' : '' }}">
                        {!! Form::label('sale_price', trans('product.sale_price'), ['class' => 'control-label required']) !!}
                        <div class="controls">
                            {!! Form::text('sale_price', null, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('sale_price', ':message') }}</span>
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
    <script>
        $(document).ready(function () {
            $("#category_id").select2({
                theme: 'bootstrap',
                placeholder:'{{trans('product.category_id')}}'
            });
            $("#product_type").select2({
                theme: 'bootstrap',
                placeholder:'{{trans('product.product_type')}}'
            });
            $("#status").select2({
                theme: 'bootstrap',
                placeholder:'{{trans('product.status')}}'
            });
        });
    </script>
@endsection
