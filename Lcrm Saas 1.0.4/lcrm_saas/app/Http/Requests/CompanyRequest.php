<?php

namespace App\Http\Requests;

use App\Repositories\CompanyRepositoryEloquent;
use App\Repositories\SettingsRepositoryEloquent;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    private $settingsRepository;

    private $companyRepository;

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
        $this->companyRepository = new CompanyRepositoryEloquent(app());
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
                    'name' => 'required|min:'.$minimum_characters.'|max:50',
                    'email' => 'required|email|unique:companies,email',
                    'address' => 'required',
                    'country_id' => 'required',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'phone' => 'required|regex:/^\d{5,15}?$/',
                    'website' => 'required|url',
                    'mobile' => 'regex:/^\d{5,15}?$/',
                    'fax' => 'regex:/^\d{5,15}?$/',
                    'company_avatar' => 'mimes:'.$allowed_extensions.'|image|max:'.$max_upload_file_size,
                ];

            case 'PUT':
            case 'PATCH':
                if (preg_match("/\/(\d+)$/", $this->url(), $mt)) {
                    $company = $this->companyRepository->find($mt[1]);
                }

                return [
                    'name' => 'required|min:'.$minimum_characters.'|max:50',
                    'phone' => 'required|regex:/^\d{5,15}?$/',
                    'mobile' => 'regex:/^\d{5,15}?$/',
                    'fax' => 'regex:/^\d{5,15}?$/',
                    'address' => 'required',
                    'website' => 'required|url',
                    'country_id' => 'required',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'email' => 'required|email|unique:companies,email,'.$company->id,
                    'company_avatar' => 'mimes:'.$allowed_extensions.'|image|max:'.$max_upload_file_size,
                ];

            default:
                break;
        }

        return [
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance();
    }

    public function messages()
    {
        return [
            'phone.regex' => 'Phone number can be only numbers',
            'mobile.regex' => 'Mobile number can be only numbers',
            'fax.regex' => 'Fax number can be only numbers with minimum 5',
        ];
    }
}
