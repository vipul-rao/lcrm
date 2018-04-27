<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Salesteam extends Model implements Transformable
{
    use SoftDeletes,RevisionableTrait,TransformableTrait;

    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    protected $table = 'sales_teams';

    protected $casts = [
        'leads' => 'boolean',
        'quotations' => 'boolean',
        'opportunities' => 'boolean',
        'team_members' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function members(){
        return $this->belongsToMany(User::class,'sales_team_members');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function teamLeader()
    {
        return $this->belongsTo(User::class, 'team_leader');
    }

    public function actualInvoice()
    {
        return $this->hasMany(Invoice::class, 'sales_team_id')->where('invoice_date', 'LIKE', date('Y-m').'%');
    }
}
