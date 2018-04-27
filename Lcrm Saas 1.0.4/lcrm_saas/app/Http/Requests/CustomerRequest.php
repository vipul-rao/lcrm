<?php

namespace App\Http\Requests;

use App\Repositories\CustomerRepositoryEloquent;
use App\Repositories\SettingsRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $this->userRepository = new UserRepositoryEloquent(app());
        $this->customerRepository = new CustomerRepositoryEloquent(app());
        $this->settingsRepository = new SettingsRepositoryEloquent(app());

        $minimum_characters = $this->settingsRepository->getKey('minimum_characters');
        $max_upload_file_size = $this->settingsRepository->getKey('max_upload_file_size');
        $allowed_extensions = $this->settingsRepository->getKey('allowed_extensions');

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'title' => 'required',
                    'first_name' => 'required|min:'.$minimum_characters.'|max:50|alpha',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:6|confirmed',
                    'password_confirmation' => 'required|same:password',
                    'phone_number' => 'required|regex:/^\d{5,15}?$/',
                    'company_id' => 'required',
                    'mobile' => 'regex:/^\d{5,15}?$/',
                    'user_avatar' => 'mimes:'.$allowed_extensions.'|image|max:'.$max_upload_file_size,
                ];

            case 'PUT':
            case 'PATCH':
                if (preg_match("/\/(\d+)$/", $this->url(), $mt)) {
                    $customer = $this->customerRepository->find($mt[1]);
                    $user = $customer->user;
                }
                $user = isset($user) ? $user : '';

                return [
                    'title' => 'required',
                    'first_name' => 'required|min:'.$minimum_characters.'|max:50|alpha',
                    'email' => 'required|email|unique:users,email,'.$user->id,
                    'password' => 'min:6|confirmed',
                    'company_id' => 'required',
                    'phone_number' => 'required|regex:/^\d{5,15}?$/',
                    'mobile' => 'regex:/^\d{5,15}?$/',
                    'user_avatar' => 'mimes:'.$allowed_extensions.'|image|max:'.$max_upload_file_size,
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
            'phone_number.regex' => 'Phone number can be only numbers',
            'mobile.regex' => 'Mobile number can be only numbers',
            'fax.regex' => 'Fax number can be only numbers',
        ];
    }
}
