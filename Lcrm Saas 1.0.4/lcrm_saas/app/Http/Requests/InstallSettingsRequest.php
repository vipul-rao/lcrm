<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class InstallSettingsRequest extends FormRequest
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
	        'first_name' => 'required|min:3|max:50',
	        'last_name' => 'required|min:3|max:50',
	        'email' => 'required|email',
	        'password' => 'required|min:6|confirmed',
            'site_name' => 'required|min:2',
            'site_email' => 'required|email'
        ];
    }
}
