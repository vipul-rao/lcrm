<?php

namespace App\Http\Requests;

use App\Repositories\SettingsRepositoryEloquent;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    private $settingsRepository;

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
        $this->settingsRepository = new SettingsRepositoryEloquent(app());

        $minimum_characters = $this->settingsRepository->getKey('minimum_characters');
        return [
            'last_name' => 'required|min:'.$minimum_characters.'|max:50|alpha',
            'first_name' => 'required|min:'.$minimum_characters.'|max:50|alpha',
            'password' => 'required|min:6|confirmed',
            'phone_number' => 'required|regex:/^\d{5,15}?$/',
        ];
    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'Phone number can be only numbers',
        ];
    }
}
