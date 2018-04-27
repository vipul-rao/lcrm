<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceReceivePayment extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    protected $table = 'invoice_receive_payments';
    protected $appends = ['invoice_payment_date'];

    public function date_time_format()
    {
        return config('settings.date_time_format');
    }

    public function setPaymentDateAttribute($payment_date)
    {
        if ($payment_date) {
            $this->attributes['payment_date'] = date('Y-m-d H:i:s', strtotime($payment_date));
        } else {
            $this->attributes['payment_date'] = '';
        }
    }

    public function getInvoicePaymentDateAttribute()
    {
        if ('0000-00-00 00:00' == $this->payment_date || '' == $this->payment_date) {
            return '';
        } else {
            return date($this->date_time_format(), strtotime($this->payment_date));
        }
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
