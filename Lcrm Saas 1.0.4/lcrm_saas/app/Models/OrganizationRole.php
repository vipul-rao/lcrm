<?php

namespace App\Models;

use Cartalyst\Sentinel\Roles\EloquentRole;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class OrganizationRole extends EloquentRole implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];
}
