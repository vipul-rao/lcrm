<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class State extends Model implements Transformable
{
    use SoftDeletes,TransformableTrait;

    protected $dates = ['deleted_at'];
    protected $guarded  = ['id'];
    protected $table = 'states';
}
