<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Venturecraft\Revisionable\RevisionableTrait;

class Lead extends Model implements Transformable
{
    use SoftDeletes, CallableTrait, MeetableTrait,RevisionableTrait,TransformableTrait;

    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    protected $table = 'leads';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function salesTeam()
    {
        return $this->belongsTo(Salesteam::class, 'sales_team_id');
    }

    public function customerCompany()
    {
        return $this->belongsTo(Company::class, 'customer_id');
    }

    public function salesPerson()
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }
}
