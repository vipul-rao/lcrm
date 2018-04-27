<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $guarded = ['id'];
    protected $table = 'user_login';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
