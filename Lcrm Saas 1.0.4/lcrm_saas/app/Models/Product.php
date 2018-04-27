<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Venturecraft\Revisionable\RevisionableTrait;

class Product extends Model
{
    use SoftDeletes,RevisionableTrait;

    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    protected $table = 'products';

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function invoiceProduct()
    {
        return $this->hasMany(InvoiceProduct::class, 'product_id');
    }

    public function quotationProduct()
    {
        return $this->hasMany(QuotationProduct::class, 'product_id');
    }

    public function qtemplates()
    {
        return $this->belongsToMany(Qtemplate::class, 'qtemplate_products');
    }

    public function salesOrderProduct()
    {
        return $this->hasMany(SaleorderProduct::class, 'product_id');
    }
}
