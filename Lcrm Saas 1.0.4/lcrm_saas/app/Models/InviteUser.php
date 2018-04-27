<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class InviteUser extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'invite_user';
    protected $fillable = ['code', 'email', 'user_id', 'organization_id', 'claimed_at'];
}
