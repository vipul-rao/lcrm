<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Subscription extends Model implements Transformable
{
    use SoftDeletes,RevisionableTrait;
    use TransformableTrait;

    protected $dates = ['deleted_at', 'trial_ends_at', 'ends_at'];
    protected $guarded = ['id'];
    protected $table = 'subscriptions';
    protected $appends = ['ended_at'];

    public function date_time_format()
    {
        return config('settings.date_time_format');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function payplan()
    {
        return $this->belongsTo(PayPlan::class, 'stripe_plan', 'plan_id');
    }

    public function getEndedAtAttribute()
    {
        if ('0000-00-00' == $this->ends_at || '' == $this->ends_at) {
            return '';
        } else {
            return date($this->date_time_format(), strtotime($this->ends_at));
        }
    }

    public function getTrialEndsAtAttribute($trial_ends_at)
    {
        if ('0000-00-00' == $trial_ends_at || '' == $trial_ends_at) {
            return '';
        } else {
            return date($this->date_time_format(), strtotime($trial_ends_at));
        }
    }
    public function paypalTransactions()
    {
        return $this->hasMany(PaypalTransaction::class);
    }
}
