<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="image_upload thumbnail" >
                    @if(isset($product->product_image) && $product->product_image!="")
                        <img src="{{ url('uploads/products/thumb_'.$product->product_image) }}"
                             alt="Image" class="ima-responsive" width="300">
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('product.product_name')}}</label>
                    <div class="controls">
                        {{ $product->product_name }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('product.category_id')}}</label>
                    <div class="controls">
                        {{ $product->category->name ?? null }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('product.product_type')}}</label>
                    <div class="controls">
                        {{ $product->product_type }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('product.status')}}</label>
                    <div class="controls">
                        {{ $product->status }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('product.quantity_on_hand')}}</label>
                    <div class="controls">
                        {{ $product->quantity_on_hand }}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('product.quantity_available')}}</label>
                    <div class="controls">
                        {{ $product->quantity_available }}
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('product.sale_price')}}</label>
                    <div class="controls">
                        {{ $product->sale_price }}
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="title">{{trans('product.description')}}</label>
                    <div class="controls">
                        {{ $product->description }}
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