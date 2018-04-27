<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Venturecraft\Revisionable\RevisionableTrait;

class ProductVariant extends Model
{
    use SoftDeletes, RevisionableTrait;

    protected $dates = ['deleted_at'];
    protected $guarded  = ['id'];
    protected $table = 'product_variants';
}
