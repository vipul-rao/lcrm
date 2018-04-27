<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class Call extends Model
{
    use SoftDeletes, RevisionableTrait;

    protected $guarded = ['id'];
    protected $table = 'calls';
    protected $dates = ['deleted_at'];
    protected $appends = ['call_date'];

    public function lead()
    {
        return $this->morphedByMany(Lead::class, 'callables');
    }

    public function date_format()
    {
        return config('settings.date_format');
    }

    public function setDateAttribute($date)
    {
        $this->attributes['date'] = Carbon::createFromFormat($this->date_format(), $date)->format('Y-m-d');
    }

    public function getCallDateAttribute()
    {
        if ('0000-00-00' == $this->date || '' == $this->date) {
            return '';
        } else {
            return date($this->date_format(), strtotime($this->date));
        }
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'resp_staff_id');
    }

    public function opportunity()
    {
        return $this->morphedByMany(Opportunity::class, 'callables');
    }
}
