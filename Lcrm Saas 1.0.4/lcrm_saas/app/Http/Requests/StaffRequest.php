<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\SettingsRepositoryEloquent;

class StaffRequest extends FormRequest
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

        $minimum_characters = 3;
        $max_upload_file_size = '1000';

        $minimum_characters = $this->settingsRepository->getKey('minimum_characters');
        $max_upload_file_size = $this->settingsRepository->getKey('max_upload_file_size');

        switch ($this->method()) {
            case 'GET':

                return [];

            case 'DELETE':

                return [];

            case 'POST':
                return [
                    'last_name' => 'required|min:'.$minimum_characters.'|max:50|alpha',
                    'first_name' => 'required|min:'.$minimum_characters.'|max:50|alpha',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:6|confirmed',
                    'phone_number' => 'required|regex:/^\d{5,15}?$/',
                    'user_avatar_file' => 'image|max:'.$max_upload_file_size,
                ];

            case 'PUT':
            case 'PATCH':
                $user_edit = $this->userRepository->find($this->route('staff'));

                return [
                    'last_name' => 'required|min:'.$minimum_characters.'|max:50|alpha',
                    'first_name' => 'required|min:'.$minimum_characters.'|max:50|alpha',
                    'email' => 'required|email|unique:users,email,'.$user_edit->id,
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
