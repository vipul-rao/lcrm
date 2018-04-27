<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpportunityRequest extends FormRequest
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
            'opportunity' => 'required',
            'customer_id' => 'required',
            'sales_team_id' => 'required',
            'next_action' => 'required|date_format:"'.config('settings.date_format').'"',
            'expected_closing' => 'required|date_format:"'.config('settings.date_format').'"',
            'expected_revenue' => 'required|numeric',
            'stages' => 'required',
            'assigned_partner_id' => 'required',
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance();
    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'Phone number can be only numbers',
            'assigned_partner_id.required' => 'The company name field is required',
            'customer_id.required' => 'The customer field is required',
            'sales_team_id.required' => 'The sales team field is required',
        ];
    }
}
