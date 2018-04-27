<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceReceivePaymentRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'invoice_id' => 'required',
            'payment_date' => 'required',
            'payment_method' => 'required',
            'payment_received' => "required|regex:/^\d{1,6}(\.\d{1,2})?$/",
        ];
    }
}
