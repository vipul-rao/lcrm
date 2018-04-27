<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Venturecraft\Revisionable\RevisionableTrait;
use Prettus\Repository\Traits\TransformableTrait;

class CompanySetting extends Model implements Transformable
{
    use RevisionableTrait;
    use TransformableTrait;

    protected $guarded = ['id'];
    protected $table = 'company_settings';
    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
