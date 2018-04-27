<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CallRequest extends FormRequest
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
        return [
            'date' => 'required',
            'call_summary' => 'required',
            'resp_staff_id' => 'required',
            'duration' => 'required|integer|min:1',
        ];
    }
}
