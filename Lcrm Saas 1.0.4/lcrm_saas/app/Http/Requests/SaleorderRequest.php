<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleorderRequest extends FormRequest
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
            'company_id' => 'required',
            'date' => 'required|date_format:"'.config('settings.date_format').'"',
            'exp_date' => 'required|date_format:"'.config('settings.date_format').'"',
            'payment_term' => 'required',
            'sales_team_id' => 'required',
            'grand_total' => 'required',
            'total' => 'required',
            'final_price' => 'required',
            'product_id' => 'required',
            'status' => 'required',
        ];
    }
}
