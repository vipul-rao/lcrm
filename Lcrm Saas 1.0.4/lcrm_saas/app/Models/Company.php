<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;


class Company extends Model implements Transformable
{
    use SoftDeletes,RevisionableTrait;
    use TransformableTrait;


    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function contactPerson()
    {
        return $this->belongsTo(User::class, 'main_contact_person');
    }

    public function salesTeam()
    {
        return $this->belongsTo(Salesteam::class, 'sales_team_id');
    }

    public function cities()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
