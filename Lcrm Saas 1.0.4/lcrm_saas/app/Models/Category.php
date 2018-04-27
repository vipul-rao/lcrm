<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use \Venturecraft\Revisionable\RevisionableTrait;


class Category extends Model implements Transformable
{
    use SoftDeletes,RevisionableTrait,TransformableTrait;

    protected $guarded = ['id'];
    protected $table = 'categories';
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
