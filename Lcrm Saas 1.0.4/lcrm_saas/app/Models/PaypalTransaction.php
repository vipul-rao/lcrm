<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaypalTransaction extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'paypal_transactions';
}
