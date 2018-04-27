<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Venturecraft\Revisionable\RevisionableTrait;

class Option extends Model
{
    use RevisionableTrait;

    protected $guarded = ['id'];
    protected $table = 'options';
    public $timestamps = false;

}
