<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPayPlanRequest extends FormRequest
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
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'amount' => 'required|integer',
                    'interval' => 'required',
                    'interval_count' => 'required',
                    'no_people' => 'required|integer',
                    'name' => 'required',
                    'currency' => 'required',
                    'statement_descriptor' => 'max:22|regex:/.*[a-zA-Z]+.*/',
                    'is_credit_card_required' => 'required',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'no_people' => 'required|integer',
                    'name' => 'required',
                    'statement_descriptor' => 'max:22|regex:/.*[a-zA-Z]+.*/',
                    'is_credit_card_required' => 'required',
                ];

            default:
                break;
        }

        return [
        ];
    }

    public function messages()
    {
        return [
            'statement_descriptor.regex' => 'The description contains atleast one alphabet',
            'is_credit_card_required.required' => 'Select if the plan requires card or not',
        ];
    }
}
