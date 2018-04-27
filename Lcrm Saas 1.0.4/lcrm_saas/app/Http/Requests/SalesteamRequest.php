<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesteamRequest extends FormRequest
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
            'salesteam' => 'required',
            'invoice_target' => "required|regex:/^\d{1,11}(\.\d{1,2})?$/",
            'invoice_forecast' => "required|regex:/^\d{1,11}(\.\d{1,2})?$/",
            'team_leader' => 'required',
            'team_members' => 'required',
        ];
    }
}
