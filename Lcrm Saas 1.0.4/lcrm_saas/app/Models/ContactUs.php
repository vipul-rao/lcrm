<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Traits\TransformableTrait;
use Venturecraft\Revisionable\RevisionableTrait;

class ContactUs extends Model
{
    use SoftDeletes,RevisionableTrait,TransformableTrait;

    protected $guarded = ['id'];
    protected $table = 'contact_us';
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class,'from');
    }
}
