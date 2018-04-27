<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|min:3|max:50|alpha_dash',
            'phone_number' => 'required|regex:/^\d{5,15}?$/',
            'email' => 'required|email|unique:organizations,email',
            'owner_first_name' => 'required|min:3|max:50|alpha_dash',
            'owner_last_name' => 'required|min:3|max:50|alpha_dash',
            'owner_phone_number' => 'required|regex:/^\d{5,15}?$/',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|min:6|confirmed',
            'owner_password_confirmation' => 'required|same:owner_password',
        ];
    }
}
