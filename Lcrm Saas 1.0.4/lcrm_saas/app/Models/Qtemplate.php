<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Venturecraft\Revisionable\RevisionableTrait;

class Qtemplate extends Model
{
    use SoftDeletes,RevisionableTrait;

    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    protected $table = 'qtemplates';

    public function setImmediatePaymentAttribute($val)
    {
        if (is_null($val))
            $val = 0;

        $this->attributes['immediate_payment'] = $val;
    }

    public function qTemplateProducts()
    {
        return $this->belongsToMany(Product::class, 'qtemplate_products')->withPivot('quantity','price');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
