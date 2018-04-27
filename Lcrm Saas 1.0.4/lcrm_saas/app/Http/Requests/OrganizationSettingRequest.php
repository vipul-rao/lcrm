<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class OrganizationSettingRequest extends FormRequest
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
            'site_logo_file' => 'image|max:2000',
            'pdf_logo_file' => 'image|max:2000',
            'site_name' => 'required',
            'site_email' => 'required',
            'date_format' => 'required',
            'time_format' => 'required',
            'currency' => 'required',
            'sales_tax' => 'required|numeric',
            'payment_term1' => 'required|numeric',
            'payment_term2' => 'required|numeric',
            'payment_term3' => 'required|numeric',
            'opportunities_reminder_days' => 'required|numeric',
            'invoice_reminder_days' => 'required|numeric',
            'quotation_prefix' => 'required',
            'quotation_start_number' => 'required|numeric',
            'sales_prefix' => 'required',
            'sales_start_number' => 'required|numeric',
            'invoice_prefix' => 'required',
            'invoice_start_number' => 'required|numeric',
            'invoice_payment_prefix' => 'required',
            'invoice_payment_start_number' => 'required|numeric',
            'paypal_sandbox_username' => 'required_if:paypal_mode,sandbox',
            'paypal_sandbox_password' => 'required_if:paypal_mode,sandbox',
            'paypal_sandbox_signature' => 'required_if:paypal_mode,sandbox',
            'paypal_live_username' => 'required_if:paypal_mode,live',
            'paypal_live_password' => 'required_if:paypal_mode,live',
            'paypal_live_signature' => 'required_if:paypal_mode,live',
        ];
    }
}
