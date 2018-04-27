<?php

namespace App\Http\Requests;

use App\Repositories\SettingsRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    private $settingsRepository;

    private $userRepository;

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
        $this->settingsRepository = new SettingsRepositoryEloquent(app());

        $minimum_characters = $this->settingsRepository->getKey('minimum_characters');
        $max_upload_file_size = $this->settingsRepository->getKey('max_upload_file_size');

        switch ($this->method()) {
            case 'GET':

                return [];

            case 'DELETE':

                return [];

            case 'PUT':
            case 'PATCH':
            if (preg_match("/\/(\d+)$/", $this->url(), $mt)) {
                $user = $this->userRepository->find($mt[1]);
            }

                return [
                    'first_name' => 'required|min:'.$minimum_characters.'|max:50|alpha_dash',
                    'last_name' => 'required|min:'.$minimum_characters.'|max:50|alpha_dash',

                    'email' => 'required|email|unique:users,email,'.$user->id,
                    'password' => 'min:6|confirmed',
                    'phone_number' => 'required|regex:/^\d{5,15}?$/',
                    'user_avatar_file' => 'image|max:'.$max_upload_file_size,
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
        ];
    }
}
