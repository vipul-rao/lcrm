<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Venturecraft\Revisionable\RevisionableTrait;
use Prettus\Repository\Traits\TransformableTrait;

class OrganizationSetting extends Model implements Transformable
{
    use RevisionableTrait;
    use TransformableTrait;

    protected $guarded = ['id'];
    protected $table = 'organization_settings';
    public $timestamps = false;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
