<?php

namespace App\Http\Requests;

use App\Repositories\SettingsRepositoryEloquent;
use Illuminate\Foundation\Http\FormRequest;

class AdminSubscriptionRequest extends FormRequest
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

        return [
            'user_id' => 'required',
            'payment_method' => 'required',
            'payment_received' => "required|regex:/^\d{1,6}(\.\d{1,2})?$/",
            'ends_at' => 'required|date_format:"'.$this->settingsRepository->getKey('date_format').'"',
        ];
    }
}
