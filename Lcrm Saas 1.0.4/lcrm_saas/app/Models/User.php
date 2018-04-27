<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Illuminate\Notifications\Notifiable;
use Prettus\Repository\Traits\TransformableTrait;

class User extends EloquentUser implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, Transformable
{
    use Authenticatable, Authorizable, CanResetPassword;

    use SoftDeletes;
    use TransformableTrait;
    use Notifiable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'email',
        'password',
        'last_name',
        'first_name',
        'permissions',
        'phone_number',
        'user_avatar',
        'user_id',
    ];

    protected $hidden = ['password', 'last_login', 'permissions', 'remember_token', 'updated_at', 'created_at', 'deleted_at'];

    protected $appends = ['full_name', 'avatar'];

    public function organizations()
    {
        return $this->belongsToMany(Organization::class)->withPivot('role_id', 'permissions');
    }

    public function salesTeams()
    {
        return $this->hasMany(Salesteam::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function calls()
    {
        return $this->hasMany(Call::class);
    }

    public function qtemplates()
    {
        return $this->hasMany(Qtemplate::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function authorized($permission = null)
    {
        return $this->hasAccess($permission);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function users()
    {
        return $this->hasMany(self::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoiceReceivePayments()
    {
        return $this->hasMany(InvoiceReceivePayment::class);
    }

    public function getAvatarAttribute()
    {
        $val = isset($this->attributes['user_avatar']) ? $this->attributes['user_avatar'] : null;
        if (empty($val)) {
            return asset('uploads/avatar').'/user.png';
        }

        $val = asset('uploads/avatar').'/'.$val;

        return $val;
    }

    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class, 'to');
    }

    public function supports()
    {
        return $this->hasMany(Support::class, 'to');
    }

    public function logins()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function invite()
    {
        return $this->hasMany(InviteUser::class);
    }
}
