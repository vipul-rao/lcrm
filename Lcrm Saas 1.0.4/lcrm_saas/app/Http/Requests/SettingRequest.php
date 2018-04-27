<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'site_name' => 'required',
            'phone_number' => 'regex:/^\d{5,15}?$/',
            'site_email' => 'required',
	        'allowed_extensions' => 'required',
	        'max_upload_file_size' => 'required',
	        'minimum_characters' => 'required',
	        'date_format' => 'required',
	        'time_format' => 'required',
	        'currency' => 'required',
            'email_driver' => 'required',
            'email_host' => 'required_if:email_driver,smtp',
            'email_port' => 'required_if:email_driver,smtp',
            'email_username' => 'required_if:email_driver,smtp',
            'email_password' => 'required_if:email_driver,smtp',
            'paypal_sandbox_username' => 'required_if:paypal_mode,sandbox',
            'paypal_sandbox_password' => 'required_if:paypal_mode,sandbox',
            'paypal_sandbox_signature' => 'required_if:paypal_mode,sandbox',
            'paypal_live_username' => 'required_if:paypal_mode,live',
            'paypal_live_password' => 'required_if:paypal_mode,live',
            'paypal_live_signature' => 'required_if:paypal_mode,live',
        ];
    }
}
